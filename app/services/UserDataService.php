<?php

/**
 * UserDataService.php
 * Servicio de datos de usuarios con interfaz unificada para bdd/api.
 * Autor: Arnau Aumedes Jimenez
 */

require_once __DIR__ . '/InternalApiClient.php';
require_once __DIR__ . '/../model/entities/User.php';
require_once __DIR__ . '/../model/dao/UserDAO.php';

class UserDataService
{
    private $userDAO;

    /** @var string Fuente activa para operaciones sin source explicito. */
    private string $activeSource = 'bdd';

    /**
     * Inicializa el servicio de usuarios.
     *
     * @param PDO $db Conexion PDO de la aplicacion.
     * @return void
     */
    public function __construct(PDO $db)
    {
        $this->userDAO = new UserDAO($db);
    }

    /**
     * Obtiene todos los usuarios.
     *
     * @param string $source Fuente de datos (bdd o api).
     * @return array Listado de usuarios.
     */
    public function getAll(string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        return $this->userDAO->findAll();
    }

    /**
     * Busca usuarios por nombre.
     *
     * @param string $name Texto a buscar.
     * @param string $source Fuente de datos (bdd o api).
     * @return array Listado filtrado de usuarios.
     */
    public function findByName(string $name, string $source = 'bdd'): array
    {
        $this->activeSource = $source;
        return $this->userDAO->findByName($name);
    }

    /**
     * Obtiene un usuario por id.
     *
     * @param int $id Identificador del usuario.
     * @param string|null $source Fuente de datos o null para fuente activa.
     * @return mixed Entidad User o null.
     */
    public function getById(int $id, ?string $source = null)
    {
        $source = $this->resolveSource($source);
        return $this->userDAO->findById($id);
    }

    /**
     * Alias de compatibilidad para getById.
     *
     * @param mixed $id Identificador de usuario.
     * @return mixed Entidad User o null.
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
