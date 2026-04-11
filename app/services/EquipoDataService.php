<?php

require_once __DIR__ . '/InternalApiClient.php';
require_once __DIR__ . '/../model/entities/Equipo.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';

class EquipoDataService
{
    private $equipoDAO;
    private InternalApiClient $apiClient;
    private array $statsById = [];
    private string $activeSource = 'bdd';

    public function __construct(PDO $db)
    {
        $this->equipoDAO = new EquipoDAO($db);
        $this->apiClient = new InternalApiClient();
    }

    public function getAll(string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        if ($source === 'api') {
            return $this->fetchFromApi();
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
            return (int) ($this->statsById[$equipoId]['puntos'] ?? 0);
        }

        return (int) $this->equipoDAO->getPuntos($equipoId);
    }

    public function getValorEquipo(int $equipoId, ?string $source = null): float
    {
        $source = $this->resolveSource($source);
        if ($source === 'api') {
            return (float) ($this->statsById[$equipoId]['valor_total'] ?? 0);
        }

        return (float) $this->equipoDAO->getValorEquipo($equipoId);
    }

    public function getCantidadJugadores(int $equipoId, ?string $source = null): int
    {
        $source = $this->resolveSource($source);
        if ($source === 'api') {
            return (int) ($this->statsById[$equipoId]['cantidad_jugadores'] ?? 0);
        }

        return (int) $this->equipoDAO->getCantidadJugadores($equipoId);
    }

    public function getMediaValorJugadores(int $equipoId, ?string $source = null): float
    {
        $source = $this->resolveSource($source);
        if ($source === 'api') {
            return (float) ($this->statsById[$equipoId]['valor_promedio'] ?? 0);
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
        $response = $this->apiClient->get('equipos');
        $rows = $response['payload']['data'] ?? [];

        $equipos = [];
        $this->statsById = [];

        foreach ($rows as $row) {
            $id = (int) ($row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $equipo = new Equipo(
                $id,
                (string) ($row['equip'] ?? ''),
                isset($row['entrenador']) ? (int) $row['entrenador'] : null,
                (string) ($row['escudo'] ?? ''),
                (int) ($row['jugados'] ?? 0),
                (int) ($row['ganados'] ?? 0),
                (int) ($row['empatados'] ?? 0),
                (int) ($row['perdidos'] ?? 0),
                (int) ($row['objetivo'] ?? 0),
                isset($row['creador_id']) ? (int) $row['creador_id'] : null,
            );

            $equipos[] = $equipo;
            $this->statsById[$id] = [
                'puntos' => (int) ($row['puntos'] ?? 0),
                'valor_total' => (float) ($row['valor_total'] ?? 0),
                'cantidad_jugadores' => (int) ($row['cantidad_jugadores'] ?? 0),
                'valor_promedio' => (float) ($row['valor_promedio'] ?? 0),
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
