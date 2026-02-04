<?php
require_once __DIR__ . '/../model/dao/UserDAO.php';
require_once __DIR__ . '/../model/entities/User.php';
require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/components/CookieHelper.php';

class SearchBarControllerUser
{
    private $db;
    private $userDAO;
    private $user;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userDAO = new UserDAO($this->db);
    }

    public function handleRequest()
    {
        $action = $_GET['action'] ?? 'search';
        switch ($action) {
            default:
                $this->searchUsers();
                break;
        }
    }

    public function searchUsers()
    {
        header('Content-Type: text/html; charset=UTF-8');
        $search = isset($_GET['q']) ? $_GET['q'] : '';
        $page = CookieHelper::getPagePreference('page', 'page_preference', 1);
        $limit = CookieHelper::getLimitPreference('limit', 'limit_preference', 5);
        $offset = ($page - 1) * $limit;
        $order = CookieHelper::getOrderPreference('order', 'order_preference', 'desc');
        // Realizar la búsqueda
        if ($search === '') {
            $users = $this->userDAO->findAll();
        } else {
            $users = $this->userDAO->findByName($search);
        }
        // Ordenar los usuarios
        $users = $this->userDAO->ordenarPorValor($users, function ($user) {
            return $user->getUsername();
        }, $order);
        // Aplicar paginación
        $users = array_slice($users, $offset, $limit);
        if (empty($users)) {
            echo '<tr><td colspan="6" class="text-center">No s\'han trobat usuaris</td></tr>';
            exit;
        }
        require_once __DIR__ . '/../model/dao/EquipoDAO.php';
        $equipoDAO = new EquipoDAO($this->db);
        $posicion = 1 + $offset;
        foreach ($users as $user) {
            // Obtener equipos del usuario (entrenador)
            $equipos = $equipoDAO->findByEntrenadorId($user->getId());
            if (!empty($equipos)) {
                foreach ($equipos as $equipo) {
                    echo '<tr>';
                    // ENTRENADOR
                    echo '<td class="align-middle fw-bold">' . htmlspecialchars($user->getUsername()) . '</td>';
                    // EQUIPO
                    echo '<td class="align-middle d-flex align-items-center gap-2">';
                    echo '<img src="' . htmlspecialchars($equipo->getEscudo()) . '" alt="' . htmlspecialchars($equipo->getEquip()) . '" style="height:32px; margin-right:8px;">';
                    echo '<span class="fw-bold text-uppercase">' . htmlspecialchars($equipo->getEquip()) . '</span>';
                    echo '</td>';
                    // OBJETIVO
                    echo '<td class="text-center align-middle">' . htmlspecialchars($equipo->getObjetivo()) . '</td>';
                    // POSICIÓN
                    echo '<td class="text-center align-middle">' . $posicion . '</td>';
                    // DIFERENCIA
                    $dif = $equipoDAO->getDiferenciaObjetivoPosicion($equipo->getObjetivo(), $posicion);
                    echo '<td class="text-center align-middle">';
                    echo '<span class="fw-bold" style="color: ' . htmlspecialchars($dif['color'] ?? '#000') . ';">';
                    echo ($dif['simbolo'] ?? '') . ($dif['valor'] ?? '');
                    echo '</span>';
                    echo '</td>';
                    // FECHA CREACIÓN
                    echo '<td class="text-center align-middle">' . htmlspecialchars($user->getCreatedAt()) . '</td>';
                    echo '</tr>';
                    $posicion++;
                }
            } else {
                // Usuario sin equipo
                echo '<tr>';
                echo '<td class="align-middle fw-bold">' . htmlspecialchars($user->getUsername()) . '</td>';
                echo '<td class="align-middle text-muted" colspan="4">Sin equipo</td>';
                echo '<td class="text-center align-middle">' . htmlspecialchars($user->getCreatedAt()) . '</td>';
                echo '</tr>';
                $posicion++;
            }
        }
        exit;
    }
}
$controller = new SearchBarControllerUser();
$controller->handleRequest();
?>