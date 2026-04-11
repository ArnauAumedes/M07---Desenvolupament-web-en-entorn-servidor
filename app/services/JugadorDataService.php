<?php

/**
 * JugadorDataService.php
 * Servicio de datos de jugadores con interfaz unificada para bdd/api.
 * Autor: Arnau Aumedes Jimenez
 */

require_once __DIR__ . '/InternalApiClient.php';
require_once __DIR__ . '/../model/entities/Jugador.php';
require_once __DIR__ . '/../model/dao/JugadorDAO.php';

class JugadorDataService
{
    private $jugadorDAO;

    /** @var string Fuente activa para operaciones sin source explicito. */
    private string $activeSource = 'bdd';

    /**
     * Inicializa el servicio de jugadores.
     *
     * @param PDO $db Conexion PDO de la aplicacion.
     * @return void
     */
    public function __construct(PDO $db)
    {
        $this->jugadorDAO = new JugadorDAO($db);
    }

    /**
     * Obtiene todos los jugadores.
     *
     * @param string $source Fuente de datos (bdd o api).
     * @return array Listado de jugadores.
     */
    public function getAll(string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        return $this->jugadorDAO->findAll();
    }

    /**
     * Busca jugadores por nombre.
     *
     * @param string $name Texto a buscar.
     * @param string $source Fuente de datos (bdd o api).
     * @return array Listado filtrado de jugadores.
     */
    public function findByName(string $name, string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        return $this->jugadorDAO->findByName($name);
    }

    /**
     * Obtiene un jugador por id.
     *
     * @param int $id Identificador del jugador.
     * @param string|null $source Fuente de datos o null para fuente activa.
     * @return mixed Entidad Jugador o null.
     */
    public function getById(int $id, ?string $source = null)
    {
        $source = $this->resolveSource($source);
        return $this->jugadorDAO->findById($id);
    }

    /**
     * Alias de compatibilidad para getById.
     *
     * @param mixed $id Identificador de jugador.
     * @return mixed Entidad Jugador o null.
     */
    public function findById($id)
    {
        return $this->getById((int) $id, null);
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
     * Devuelve la suma de goles y asistencias de un jugador.
     *
     * @param int $jugadorId Identificador del jugador.
     * @param string|null $source Fuente de datos o null para fuente activa.
     * @return int Suma de goles y asistencias.
     */
    public function getSumaGolesAsistencias(int $jugadorId, ?string $source = null): int
    {
        $source = $this->resolveSource($source);
        return (int) $this->jugadorDAO->getSumaGolesAsistencias($jugadorId);
    }

    /**
     * Devuelve la media por partido de un atributo del jugador.
     *
     * @param int $jugadorId Identificador del jugador.
     * @param string $atributo Atributo a calcular (goles o asistencias).
     * @param string|null $source Fuente de datos o null para fuente activa.
     * @return float Media del atributo por partido.
     */
    public function getMediaPorPartidoJugador(int $jugadorId, string $atributo, ?string $source = null): float
    {
        $source = $this->resolveSource($source);
        return (float) $this->jugadorDAO->getMediaPorPartidoJugador($jugadorId, $atributo);
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
