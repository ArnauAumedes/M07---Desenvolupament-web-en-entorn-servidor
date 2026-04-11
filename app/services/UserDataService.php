<?php

require_once __DIR__ . '/InternalApiClient.php';
require_once __DIR__ . '/../model/entities/User.php';
require_once __DIR__ . '/../model/dao/UserDAO.php';

class UserDataService
{
    private $userDAO;
    private string $activeSource = 'bdd';

    public function __construct(PDO $db)
    {
        $this->userDAO = new UserDAO($db);
    }

    public function getAll(string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        return $this->userDAO->findAll();
    }

    public function findByName(string $name, string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        return $this->userDAO->findByName($name);
    }

    public function getById(int $id, ?string $source = null)
    {
        $source = $this->resolveSource($source);
        return $this->userDAO->findById($id);
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

    private function resolveSource(?string $source): string
    {
        if ($source !== null) {
            $this->activeSource = $source;
            return $source;
        }

        return $this->activeSource;
    }
}
