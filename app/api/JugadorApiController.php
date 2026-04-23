<?php

/**
 * JugadorApiController.php
 * Controlador de lectura para el recurso jugadores de la API interna.
 * Autor: Arnau Aumedes Jimenez
 */

require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/dao/JugadorDAO.php';
require_once __DIR__ . '/ApiResponse.php';

class JugadorApiController
{
    private $jugadorDAO;

    /**
     * Inicializa la conexion y el DAO de jugadores.
     *
     * @return void
     */
    public function __construct()
    {
        $database = Database::getInstance();
        $db = $database->getConnection();
        $this->jugadorDAO = new JugadorDAO($db);
    }

    /**
     * Gestiona la peticion HTTP del recurso jugadores.
     *
     * @param string $method Metodo HTTP recibido.
     * @param int|null $id Identificador del jugador o null para listado.
     * @return void
     */
    public function handle(string $method, ?int $id = null): void
    {
        if ($method !== 'GET') {
            ApiResponse::methodNotAllowed('Metodo no permitido para jugadores', ['GET']);
            return;
        }

        if ($id === null) {
            $this->list();
            return;
        }

        $this->show($id);
    }

    /**
     * Devuelve el listado de jugadores con soporte de limit y order.
     *
     * @return void
     */
    private function list(): void
    {
        $validation = $this->validateListParams();
        if ($validation !== null) {
            ApiResponse::validationError('Error de validacion', $validation);
            return;
        }

        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : null;
        $order = isset($_GET['order']) ? strtolower((string) $_GET['order']) : 'asc';

        $jugadores = $this->jugadorDAO->findAll();
        $payload = [];

        foreach ($jugadores as $jugador) {
            $payload[] = $this->serializeJugador($jugador);
        }

        usort($payload, function ($a, $b) use ($order) {
            return $order === 'desc'
                ? ((int) $b['id'] <=> (int) $a['id'])
                : ((int) $a['id'] <=> (int) $b['id']);
        });

        if ($limit !== null) {
            $payload = array_slice($payload, 0, $limit);
        }

        ApiResponse::success('Jugadores obtenidos correctamente', $payload, [
            'resource' => 'jugadores',
            'count' => count($payload),
            'order' => $order,
            'limit' => $limit,
        ]);
    }

    /**
     * Devuelve un jugador concreto por id.
     *
     * @param int $id Identificador del jugador.
     * @return void
     */
    private function show(int $id): void
    {
        $jugador = $this->jugadorDAO->findById($id);
        if ($jugador === null) {
            ApiResponse::notFound('Jugador no encontrado');
            return;
        }

        ApiResponse::success('Jugador obtenido correctamente', $this->serializeJugador($jugador), [
            'resource' => 'jugadores',
        ]);
    }

    /**
     * Serializa una entidad Jugador al formato JSON de la API.
     *
     * @param mixed $jugador Entidad de dominio Jugador.
     * @return array Array serializado del jugador.
     */
    private function serializeJugador($jugador): array
    {
        $jugadorId = (int) $jugador->getId();
        $partidos = (int) $jugador->getPartidos();
        $goles = (int) $jugador->getGoles();
        $asistencias = (int) $jugador->getAsistencias();

        return [
            'id' => $jugadorId,
            'nombre_completo' => (string) $jugador->getNombreCompleto(),
            'equipo_id' => (int) $jugador->getEquipoId(),
            'valor' => (float) $jugador->getValor(),
            'partidos' => $partidos,
            'goles' => $goles,
            'asistencias' => $asistencias,
            'suma_goles_asistencias' => $goles + $asistencias,
            'media_goles_por_partido' => $partidos > 0 ? ($goles / $partidos) : 0,
            'media_asistencias_por_partido' => $partidos > 0 ? ($asistencias / $partidos) : 0,
        ];
    }

    /**
     * Valida parametros de listado recibidos por query string.
     *
     * @return array|null Lista de errores o null si la entrada es valida.
     */
    private function validateListParams(): ?array
    {
        $errors = [];

        if (isset($_GET['limit'])) {
            $rawLimit = (string) $_GET['limit'];
            if (!ctype_digit($rawLimit)) {
                $errors[] = 'limit debe ser un entero entre 1 y 100';
            } else {
                $limit = (int) $rawLimit;
                if ($limit < 1 || $limit > 100) {
                    $errors[] = 'limit debe ser un entero entre 1 y 100';
                }
            }
        }

        if (isset($_GET['order'])) {
            $order = strtolower(trim((string) $_GET['order']));
            if (!in_array($order, ['asc', 'desc'], true)) {
                $errors[] = 'order debe ser asc o desc';
            }
        }

        return empty($errors) ? null : $errors;
    }
}
