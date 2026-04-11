<?php

require_once __DIR__ . '/InternalApiClient.php';
require_once __DIR__ . '/../model/entities/Jugador.php';
require_once __DIR__ . '/../model/dao/JugadorDAO.php';

class JugadorDataService
{
    private $jugadorDAO;
    private InternalApiClient $apiClient;
    private array $statsById = [];
    private string $activeSource = 'bdd';

    public function __construct(PDO $db)
    {
        $this->jugadorDAO = new JugadorDAO($db);
        $this->apiClient = new InternalApiClient();
    }

    public function getAll(string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        if ($source === 'api') {
            return $this->fetchFromApi();
        }

        return $this->jugadorDAO->findAll();
    }

    public function findByName(string $name, string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        if ($source !== 'api') {
            return $this->jugadorDAO->findByName($name);
        }

        $name = trim($name);
        $jugadores = $this->getAll('api');
        if ($name === '') {
            return $jugadores;
        }

        return array_values(array_filter($jugadores, function ($jugador) use ($name) {
            return stripos($jugador->getNombreCompleto(), $name) !== false;
        }));
    }

    public function getById(int $id, ?string $source = null)
    {
        $source = $this->resolveSource($source);
        if ($source !== 'api') {
            return $this->jugadorDAO->findById($id);
        }

        foreach ($this->getAll('api') as $jugador) {
            if ((int) $jugador->getId() === $id) {
                return $jugador;
            }
        }

        return null;
    }

    public function findById($id)
    {
        return $this->getById((int) $id, null);
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

    public function getSumaGolesAsistencias(int $jugadorId, ?string $source = null): int
    {
        $source = $this->resolveSource($source);
        if ($source === 'api') {
            return (int) ($this->statsById[$jugadorId]['suma_goles_asistencias'] ?? 0);
        }

        return (int) $this->jugadorDAO->getSumaGolesAsistencias($jugadorId);
    }

    public function getMediaPorPartidoJugador(int $jugadorId, string $atributo, ?string $source = null): float
    {
        $source = $this->resolveSource($source);
        if ($source === 'api') {
            if ($atributo === 'goles') {
                return (float) ($this->statsById[$jugadorId]['media_goles_por_partido'] ?? 0);
            }
            if ($atributo === 'asistencias') {
                return (float) ($this->statsById[$jugadorId]['media_asistencias_por_partido'] ?? 0);
            }
            throw new InvalidArgumentException('Atributo no valido para media: ' . $atributo);
        }

        return (float) $this->jugadorDAO->getMediaPorPartidoJugador($jugadorId, $atributo);
    }

    private function fetchFromApi(): array
    {
        $response = $this->apiClient->get('jugadores');
        $rows = $response['payload']['data'] ?? [];

        $jugadores = [];
        $this->statsById = [];

        foreach ($rows as $row) {
            $id = (int) ($row['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $jugador = new Jugador(
                $id,
                (string) ($row['nombre_completo'] ?? ''),
                (int) ($row['equipo_id'] ?? 0),
                (float) ($row['valor'] ?? 0),
                (int) ($row['partidos'] ?? 0),
                (int) ($row['goles'] ?? 0),
                (int) ($row['asistencias'] ?? 0),
            );

            $jugadores[] = $jugador;
            $this->statsById[$id] = [
                'suma_goles_asistencias' => (int) ($row['suma_goles_asistencias'] ?? 0),
                'media_goles_por_partido' => (float) ($row['media_goles_por_partido'] ?? 0),
                'media_asistencias_por_partido' => (float) ($row['media_asistencias_por_partido'] ?? 0),
            ];
        }

        return $jugadores;
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
