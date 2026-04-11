<?php

/**
 * EquipoDataService.php
 * Servicio de datos de equipos con soporte dual bdd/api y fallback controlado.
 * Autor: Arnau Aumedes Jimenez
 */

require_once __DIR__ . '/InternalApiClient.php';
require_once __DIR__ . '/FootballApiService.php';
require_once __DIR__ . '/../model/entities/Equipo.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';

class EquipoDataService
{
    private $equipoDAO;
    private InternalApiClient $apiClient;
    private FootballApiService $footballApiService;

    /** @var array Estadisticas calculadas/cacheadas por id de equipo. */
    private array $statsById = [];

    /** @var string Fuente activa para operaciones sin source explicito. */
    private string $activeSource = 'bdd';

    /**
     * Inicializa DAO y servicios auxiliares para equipos.
     *
     * @param PDO $db Conexion PDO de la aplicacion.
     * @return void
     */
    public function __construct(PDO $db)
    {
        $this->equipoDAO = new EquipoDAO($db);
        $this->apiClient = new InternalApiClient();
        $this->footballApiService = new FootballApiService();
    }

    /**
     * Obtiene todos los equipos segun la fuente seleccionada.
     *
     * @param string $source Fuente de datos (bdd o api).
     * @return array Listado de entidades Equipo.
     */
    public function getAll(string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        if ($source === 'api') {
            try {
                return $this->fetchFromApi();
            } catch (Throwable $e) {
                error_log('[equipo-data-service] fallback a BDD por error provider: ' . $e->getMessage());
                return $this->equipoDAO->findAll();
            }
        }

        return $this->equipoDAO->findAll();
    }

    /**
     * Busca equipos por nombre segun la fuente activa.
     *
     * @param string $name Texto a buscar.
     * @param string $source Fuente de datos (bdd o api).
     * @return array Listado de entidades Equipo filtrado.
     */
    public function findByName(string $name, string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        if ($source !== 'api') {
            return $this->equipoDAO->findByName($name);
        }

        $name = trim($name);
        $equipos = $this->getAll('api');
        if ($name === '') {
            return $equipos;
        }

        return array_values(array_filter($equipos, function ($equipo) use ($name) {
            return stripos($equipo->getEquip(), $name) !== false;
        }));
    }

    /**
     * Obtiene un equipo por id en la fuente indicada.
     *
     * @param int $id Identificador del equipo.
     * @param string|null $source Fuente de datos o null para fuente activa.
     * @return mixed Entidad Equipo o null si no existe.
     */
    public function getById(int $id, ?string $source = null)
    {
        $source = $this->resolveSource($source);
        if ($source !== 'api') {
            return $this->equipoDAO->findById($id);
        }

        foreach ($this->getAll('api') as $equipo) {
            if ((int) $equipo->getId() === $id) {
                return $equipo;
            }
        }

        return null;
    }

    /**
     * Alias de compatibilidad para getById.
     *
     * @param mixed $id Identificador del equipo.
     * @return mixed Entidad Equipo o null.
     */
    public function findById($id)
    {
        return $this->getById((int) $id, null);
    }

    /**
     * Busca equipos asociados a un entrenador.
     *
     * @param int $entrenadorId Identificador del entrenador.
     * @param string|null $source Fuente de datos o null para fuente activa.
     * @return array Listado de equipos del entrenador.
     */
    public function findByEntrenador(int $entrenadorId, ?string $source = null): array
    {
        $source = $this->resolveSource($source);
        if ($source !== 'api') {
            return $this->equipoDAO->findByEntrenador($entrenadorId);
        }

        return array_values(array_filter($this->getAll('api'), function ($equipo) use ($entrenadorId) {
            return (int) ($equipo->getEntrenador() ?? 0) === $entrenadorId;
        }));
    }

    /**
     * Ordena una coleccion por callback de valor.
     *
     * @param array $items Coleccion a ordenar.
     * @param callable $valueCallback Callback para extraer valor de orden.
     * @param string $order Orden asc o desc.
     * @return array Coleccion ordenada.
     */
    public function sortByValue(array $items, callable $valueCallback, string $order = 'desc'): array
    {
        usort($items, function ($a, $b) use ($valueCallback, $order) {
            $valueA = $valueCallback($a);
            $valueB = $valueCallback($b);
            return strtolower($order) === 'asc' ? ($valueA <=> $valueB) : ($valueB <=> $valueA);
        });
        return $items;
    }

    /**
     * Devuelve los puntos de un equipo.
     *
     * @param int $equipoId Identificador del equipo.
     * @param string|null $source Fuente de datos o null para fuente activa.
     * @return int Puntos del equipo.
     */
    public function getPuntos(int $equipoId, ?string $source = null): int
    {
        $source = $this->resolveSource($source);
        if ($source === 'api') {
            if (isset($this->statsById[$equipoId]['puntos'])) {
                return (int) $this->statsById[$equipoId]['puntos'];
            }
            return (int) $this->equipoDAO->getPuntos($equipoId);
        }

        return (int) $this->equipoDAO->getPuntos($equipoId);
    }

    /**
     * Devuelve el valor total de plantilla de un equipo.
     *
     * @param int $equipoId Identificador del equipo.
     * @param string|null $source Fuente de datos o null para fuente activa.
     * @return float Valor total del equipo.
     */
    public function getValorEquipo(int $equipoId, ?string $source = null): float
    {
        $source = $this->resolveSource($source);
        if ($source === 'api') {
            if (isset($this->statsById[$equipoId]['valor_total'])) {
                return (float) $this->statsById[$equipoId]['valor_total'];
            }
            return (float) $this->equipoDAO->getValorEquipo($equipoId);
        }

        return (float) $this->equipoDAO->getValorEquipo($equipoId);
    }

    /**
     * Devuelve la cantidad de jugadores de un equipo.
     *
     * @param int $equipoId Identificador del equipo.
     * @param string|null $source Fuente de datos o null para fuente activa.
     * @return int Cantidad de jugadores.
     */
    public function getCantidadJugadores(int $equipoId, ?string $source = null): int
    {
        $source = $this->resolveSource($source);
        if ($source === 'api') {
            if (isset($this->statsById[$equipoId]['cantidad_jugadores'])) {
                return (int) $this->statsById[$equipoId]['cantidad_jugadores'];
            }
            return (int) $this->equipoDAO->getCantidadJugadores($equipoId);
        }

        return (int) $this->equipoDAO->getCantidadJugadores($equipoId);
    }

    /**
     * Devuelve el valor promedio por jugador de un equipo.
     *
     * @param int $equipoId Identificador del equipo.
     * @param string|null $source Fuente de datos o null para fuente activa.
     * @return float Valor promedio de jugadores.
     */
    public function getMediaValorJugadores(int $equipoId, ?string $source = null): float
    {
        $source = $this->resolveSource($source);
        if ($source === 'api') {
            if (isset($this->statsById[$equipoId]['valor_promedio'])) {
                return (float) $this->statsById[$equipoId]['valor_promedio'];
            }
            return (float) $this->equipoDAO->getMediaValorJugadores($equipoId);
        }

        return (float) $this->equipoDAO->getMediaValorJugadores($equipoId);
    }

    /**
     * Calcula la diferencia entre objetivo y posicion actual para mostrar en UI.
     *
     * @param int $objetivo Posicion objetivo del equipo.
     * @param int $posicionActual Posicion actual en la tabla.
     * @return array Datos de diferencia con valor, simbolo y color.
     */
    public function getDiferenciaObjetivoPosicion(int $objetivo, int $posicionActual): array
    {
        $diferencia = $objetivo - $posicionActual;
        if ($diferencia > 0) {
            return ['valor' => $diferencia, 'simbolo' => '+', 'color' => '#11461D'];
        }
        if ($diferencia < 0) {
            return ['valor' => abs($diferencia), 'simbolo' => '-', 'color' => '#75151E'];
        }
        return ['valor' => 0, 'simbolo' => '', 'color' => 'text-secondary'];
    }

    /**
     * Obtiene equipos desde provider externo y construye entidades internas.
     *
     * @return array Listado de equipos normalizado para la aplicacion.
     */
    private function fetchFromApi(): array
    {
        $competitionCode = (string) (getenv('FOOTBALL_DEFAULT_COMPETITION') ?: 'PL');
        $rows = $this->footballApiService->getTeams(['competition' => $competitionCode]);
        $standingsById = $this->footballApiService->getStandings($competitionCode);

        $equipos = [];
        $this->statsById = [];

        foreach ($rows as $row) {
            $id = (int) ($row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $standing = $standingsById[$id] ?? [];
            $jugados = (int) ($standing['jugados'] ?? 0);
            $ganados = (int) ($standing['ganados'] ?? 0);
            $empatados = (int) ($standing['empatados'] ?? 0);
            $perdidos = (int) ($standing['perdidos'] ?? 0);
            $position = (int) ($standing['position'] ?? 0);
            $objetivo = $position > 0 ? $position : 0;

            $equipo = new Equipo(
                $id,
                (string) ($row['name'] ?? ''),
                null,
                (string) ($row['logo'] ?? ''),
                $jugados,
                $ganados,
                $empatados,
                $perdidos,
                $objetivo,
                null,
            );

            $equipos[] = $equipo;
            $this->statsById[$id] = [
                'puntos' => (int) ($standing['puntos'] ?? (($ganados * 3) + $empatados)),
                'valor_total' => 0.0,
                'cantidad_jugadores' => 0,
                'valor_promedio' => 0.0,
            ];
        }

        return $equipos;
    }

    /**
     * Resuelve la fuente efectiva de una operacion.
     *
     * @param string|null $source Fuente explicita o null.
     * @return string Fuente efectiva.
     */
    private function resolveSource(?string $source): string
    {
        if ($source !== null) {
            $this->activeSource = $source;
            return $source;
        }

        return $this->activeSource;
    }
}
