<?php

require_once __DIR__ . '/InternalApiClient.php';
require_once __DIR__ . '/FootballApiService.php';
require_once __DIR__ . '/../model/entities/Equipo.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';

class EquipoDataService
{
    private $equipoDAO;
    private InternalApiClient $apiClient;
    private FootballApiService $footballApiService;
    private array $statsById = [];
    private string $activeSource = 'bdd';

    public function __construct(PDO $db)
    {
        $this->equipoDAO = new EquipoDAO($db);
        $this->apiClient = new InternalApiClient();
        $this->footballApiService = new FootballApiService();
    }

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

    public function findById($id)
    {
        return $this->getById((int) $id, null);
    }

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

    public function sortByValue(array $items, callable $valueCallback, string $order = 'desc'): array
    {
        usort($items, function ($a, $b) use ($valueCallback, $order) {
            $valueA = $valueCallback($a);
            $valueB = $valueCallback($b);
            return strtolower($order) === 'asc' ? ($valueA <=> $valueB) : ($valueB <=> $valueA);
        });
        return $items;
    }

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

    private function resolveSource(?string $source): string
    {
        if ($source !== null) {
            $this->activeSource = $source;
            return $source;
        }

        return $this->activeSource;
    }
}
