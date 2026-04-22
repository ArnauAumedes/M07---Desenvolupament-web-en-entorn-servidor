<?php

/**
 * UserApiController.php
 * Controlador de lectura para el recurso usuarios de la API interna.
 * Autor: Arnau Aumedes Jimenez
 */

require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/dao/UserDAO.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';
require_once __DIR__ . '/ApiResponse.php';

class UserApiController
{
    private $userDAO;
    private $equipoDAO;

    /**
     * Inicializa la conexion y los DAOs necesarios del recurso usuarios.
     *
     * @return void
     */
    public function __construct()
    {
        $database = Database::getInstance();
        $db = $database->getConnection();
        $this->userDAO = new UserDAO($db);
        $this->equipoDAO = new EquipoDAO($db);
    }

    /**
     * Gestiona la peticion HTTP del recurso usuarios.
     *
     * @param string $method Metodo HTTP recibido.
     * @param int|null $id Identificador del usuario o null para listado.
     * @return void
     */
    public function handle(string $method, ?int $id = null): void
    {
        if ($method !== 'GET') {
            ApiResponse::methodNotAllowed('Metodo no permitido para usuarios', ['GET']);
            return;
        }

        if ($id === null) {
            $this->list();
            return;
        }

        $this->show($id);
    }

    /**
     * Devuelve el listado de usuarios con soporte de limit y order.
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

        $users = $this->userDAO->findAll();
        $payload = [];

        foreach ($users as $user) {
            $payload[] = $this->serializeUser($user);
        }

        usort($payload, function ($a, $b) use ($order) {
            return $order === 'desc'
                ? ((int) $b['user_id'] <=> (int) $a['user_id'])
                : ((int) $a['user_id'] <=> (int) $b['user_id']);
        });

        if ($limit !== null) {
            $payload = array_slice($payload, 0, $limit);
        }

        ApiResponse::success('Usuarios obtenidos correctamente', $payload, [
            'resource' => 'usuarios',
            'count' => count($payload),
            'order' => $order,
            'limit' => $limit,
        ]);
    }

    /**
     * Devuelve un usuario concreto por id.
     *
     * @param int $id Identificador del usuario.
     * @return void
     */
    private function show(int $id): void
    {
        $user = $this->userDAO->findById($id);
        if ($user === null) {
            ApiResponse::notFound('Usuario no encontrado');
            return;
        }

        ApiResponse::success('Usuario obtenido correctamente', $this->serializeUser($user), [
            'resource' => 'usuarios',
        ]);
    }

    /**
     * Serializa una entidad User al formato JSON de la API.
     *
     * @param mixed $user Entidad de dominio User.
     * @return array Array serializado del usuario.
     */
    private function serializeUser($user): array
    {
        $userId = (int) $user->getId();
        $equipos = $this->equipoDAO->findByEntrenador($userId);

        return [
            'user_id' => $userId,
            'username' => (string) $user->getUsername(),
            'email' => (string) $user->getEmail(),
            'active' => $user->isActive() ? 1 : 0,
            'isAdmin' => $user->isAdmin() ? 1 : 0,
            'created_at' => $user->getCreatedAt(),
            'equipos_count' => count($equipos),
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
