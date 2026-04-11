<?php

require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/dao/UserDAO.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';
require_once __DIR__ . '/ApiResponse.php';

class UserApiController
{
    private $userDAO;
    private $equipoDAO;

    public function __construct()
    {
        $database = new Database();
        $db = $database->getConnection();
        $this->userDAO = new UserDAO($db);
        $this->equipoDAO = new EquipoDAO($db);
    }

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

    private function list(): void
    {
        $users = $this->userDAO->findAll();
        $payload = [];

        foreach ($users as $user) {
            $payload[] = $this->serializeUser($user);
        }

        ApiResponse::success('Usuarios obtenidos correctamente', $payload, [
            'resource' => 'usuarios',
            'count' => count($payload),
        ]);
    }

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
}
