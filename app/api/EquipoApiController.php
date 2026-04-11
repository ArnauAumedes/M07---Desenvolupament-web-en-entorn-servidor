<?php

/**
 * EquipoApiController.php
 * Controlador de lectura para el recurso equipos de la API interna.
 * Autor: Arnau Aumedes Jimenez
 */

require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';
require_once __DIR__ . '/ApiResponse.php';

class EquipoApiController
{
    private $equipoDAO;

    /**
     * Inicializa la conexion y el DAO de equipos.
     *
     * @return void
     */
    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->equipoDAO = new EquipoDAO($db);
    }

    /**
     * Gestiona la peticion HTTP del recurso equipos.
     *
     * @param string $method Metodo HTTP recibido.
     * @param int|null $id Identificador del equipo o null para listado.
     * @return void
     */
    public function handle(string $method, ?int $id = null): void
    {
        if ($method !== 'GET') {
            ApiResponse::methodNotAllowed('Metodo no permitido para equipos', ['GET']);
            return;
        }

        if ($id === null) {
            $this->list();
            return;
        }

        $this->show($id);
    }

    /**
     * Devuelve el listado de equipos con soporte de limit y order.
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

        $equipos = $this->equipoDAO->findAll();
        $payload = [];

        foreach ($equipos as $equipo) {
            $payload[] = $this->serializeEquipo($equipo);
        }

        usort($payload, function ($a, $b) use ($order) {
            return $order === 'desc'
                ? ((int) $b['id'] <=> (int) $a['id'])
                : ((int) $a['id'] <=> (int) $b['id']);
        });

        if ($limit !== null) {
            $payload = array_slice($payload, 0, $limit);
        }

        ApiResponse::success('Equipos obtenidos correctamente', $payload, [
            'resource' => 'equipos',
            'count' => count($payload),
            'order' => $order,
            'limit' => $limit,
        ]);
    }

    /**
     * Devuelve un equipo concreto por id.
     *
     * @param int $id Identificador del equipo.
     * @return void
     */
    private function show(int $id): void
    {
        $equipo = $this->equipoDAO->findById($id);
        if ($equipo === null) {
            ApiResponse::notFound('Equipo no encontrado');
            return;
        }

        ApiResponse::success('Equipo obtenido correctamente', $this->serializeEquipo($equipo), [
            'resource' => 'equipos',
        ]);
    }

    /**
     * Serializa una entidad Equipo al formato JSON de la API.
     *
     * @param mixed $equipo Entidad de dominio Equipo.
     * @return array Array serializado del equipo.
     */
    private function serializeEquipo($equipo): array
    {
        $equipoId = (int) $equipo->getId();
        $ganados = (int) $equipo->getGanados();
        $empatados = (int) $equipo->getEmpatados();

        $cantidadJugadores = $this->equipoDAO->getCantidadJugadores($equipoId);

        return [
            'id' => $equipoId,
            'equip' => (string) $equipo->getEquip(),
            'entrenador' => $equipo->getEntrenador(),
            'escudo' => (string) $equipo->getEscudo(),
            'jugados' => (int) $equipo->getJugados(),
            'ganados' => $ganados,
            'empatados' => $empatados,
            'perdidos' => (int) $equipo->getPerdidos(),
            'objetivo' => (int) $equipo->getObjetivo(),
            'creador_id' => $equipo->getCreadorId() !== null ? (int) $equipo->getCreadorId() : null,
            'puntos' => ($ganados * 3) + $empatados,
            'valor_total' => (float) $this->equipoDAO->getValorEquipo($equipoId),
            'cantidad_jugadores' => (int) $cantidadJugadores,
            'valor_promedio' => $cantidadJugadores > 0 ? (float) $this->equipoDAO->getMediaValorJugadores($equipoId) : 0.0,
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
