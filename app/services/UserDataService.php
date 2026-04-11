<?php

require_once __DIR__ . '/InternalApiClient.php';
require_once __DIR__ . '/../model/entities/User.php';
require_once __DIR__ . '/../model/dao/UserDAO.php';

class UserDataService
{
    private $userDAO;
    private InternalApiClient $apiClient;
    private string $activeSource = 'bdd';

    public function __construct(PDO $db)
    {
        $this->userDAO = new UserDAO($db);
        $this->apiClient = new InternalApiClient();
    }

    public function getAll(string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        if ($source === 'api') {
            return $this->fetchFromApi();
        }

        return $this->userDAO->findAll();
    }

    public function findByName(string $name, string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        if ($source !== 'api') {
            return $this->userDAO->findByName($name);
        }

        $name = trim($name);
        $users = $this->getAll('api');
        if ($name === '') {
            return $users;
        }

        return array_values(array_filter($users, function ($user) use ($name) {
            return stripos($user->getUsername(), $name) !== false;
        }));
    }

    public function getById(int $id, ?string $source = null)
    {
        $source = $this->resolveSource($source);
        if ($source !== 'api') {
            return $this->userDAO->findById($id);
        }

        foreach ($this->getAll('api') as $user) {
            if ((int) $user->getId() === $id) {
                return $user;
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

    private function fetchFromApi(): array
    {
        $response = $this->apiClient->get('usuarios');
        $rows = $response['payload']['data'] ?? [];

        $users = [];
        foreach ($rows as $row) {
            $id = (int) ($row['user_id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            $users[] = new User(
                $id,
                (string) ($row['username'] ?? ''),
                (string) ($row['email'] ?? ''),
                null,
                (int) ($row['active'] ?? 1),
                (int) ($row['isAdmin'] ?? 0),
                $row['created_at'] ?? null,
            );
        }

        return $users;
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
