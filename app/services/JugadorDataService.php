<?php

require_once __DIR__ . '/InternalApiClient.php';
require_once __DIR__ . '/../model/entities/Jugador.php';
require_once __DIR__ . '/../model/dao/JugadorDAO.php';

class JugadorDataService
{
    private $jugadorDAO;
    private string $activeSource = 'bdd';

    public function __construct(PDO $db)
    {
        $this->jugadorDAO = new JugadorDAO($db);
    }

    public function getAll(string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        return $this->jugadorDAO->findAll();
    }

    public function findByName(string $name, string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        return $this->jugadorDAO->findByName($name);
    }

    public function getById(int $id, ?string $source = null)
    {
        $source = $this->resolveSource($source);
        return $this->jugadorDAO->findById($id);
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
        return (int) $this->jugadorDAO->getSumaGolesAsistencias($jugadorId);
    }

    public function getMediaPorPartidoJugador(int $jugadorId, string $atributo, ?string $source = null): float
    {
        $source = $this->resolveSource($source);
        return (float) $this->jugadorDAO->getMediaPorPartidoJugador($jugadorId, $atributo);
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
