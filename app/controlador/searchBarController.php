<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../model/dao/EquipoDAO.php';
require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/components/CookieHelper.php';

class SearchBarController
{
    private $db;
    private $equipoDAO;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->equipoDAO = new EquipoDAO($this->db);
    }

    public function handleRequest()
    {
        $action = $_GET['action'] ?? 'search';
        $tipo = $_GET['tipo'];
        switch ($tipo) {
            case 'valor':
                $this->searchEquiposValor();
                break;
            default:
                $this->searchEquiposClasificacion();
                break;
        }
    }

    public function searchEquiposClasificacion()
    {
        header('Content-Type: text/html; charset=UTF-8');
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page = CookieHelper::getPagePreference('page', 'page_preference', 1);
        $limit = CookieHelper::getLimitPreference('limit', 'limit_preference', 5);
        $offset = ($page - 1) * $limit;
        $order = CookieHelper::getOrderPreference('order', 'order_preference', 'desc');

        if ($search === '') {
            $equipos = $this->equipoDAO->findAll();
        } else {
            $equipos = $this->equipoDAO->findByName($search);
        }
        $equipos = $this->equipoDAO->ordenarPorValor($equipos, function ($equipo) {
            return $this->equipoDAO->getPuntos($equipo->getId());
        }, $order);
        $equipos = array_slice($equipos, $offset, $limit);
        if (empty($equipos)) {
            echo '<tr><td colspan="8" class="text-center">No s\'han trobat equips</td></tr>';
            exit;
        }
        foreach ($equipos as $index => $equipo) {
            echo '<tr onclick="window.location=\'/practicas/index.php?action=view&id=' . urlencode($equipo->getId()) . '\'" style="cursor:pointer;">';
            echo '<td class="align-middle fs-4 fw-bold">' . ($offset + $index + 1) . '</td>';
            echo '<td class="align-middle">' . htmlspecialchars($equipo->getId()) . '</td>';
            echo '<td class="align-middle d-flex align-items-center gap-2">';
            echo '<img src="' . htmlspecialchars($equipo->getEscudo()) . '" alt="' . htmlspecialchars($equipo->getEquip()) . '" style="height:32px; margin-right:8px;">';
            echo '<div>';
            echo '<span class="fw-bold text-uppercase">' . htmlspecialchars($equipo->getEquip()) . '</span><br>';
            echo '<span class="text-muted club-usuario" style="font-size:0.95em;">';
            if ($equipo->getUserId() === null || $equipo->getUserId() === "") {
                echo 'no tiene entrenador';
            } else {
                echo htmlspecialchars($equipo->getUserId());
            }
            echo '</span>';
            echo '</div>';
            echo '</td>';
            echo '<td class="text-center align-middle">' . htmlspecialchars($equipo->getJugados()) . '</td>';
            echo '<td class="text-center align-middle">' . htmlspecialchars($equipo->getGanados()) . '</td>';
            echo '<td class="text-center align-middle">' . htmlspecialchars($equipo->getEmpatados()) . '</td>';
            echo '<td class="text-center align-middle">' . htmlspecialchars($equipo->getPerdidos()) . '</td>';
            if (method_exists($this->equipoDAO, 'getPuntos')) {
                $puntos = $this->equipoDAO->getPuntos($equipo->getId());
            } else {
                $puntos = '';
            }
            echo '<td class="text-center align-middle">' . htmlspecialchars($puntos) . '</td>';
            echo '</tr>';
        }
        exit;
    }

    public function searchEquiposValor()
    {
        header('Content-Type: text/html; charset=UTF-8');
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page = CookieHelper::getPagePreference('page', 'page_preference', 1);
        $limit = CookieHelper::getLimitPreference('limit', 'limit_preference', 5);
        $offset = ($page - 1) * $limit;
        $order = CookieHelper::getOrderPreference('order', 'order_preference', 'desc');

        if ($search === '') {
            $equipos = $this->equipoDAO->findAll();
        } else {
            $equipos = $this->equipoDAO->findByName($search);
        }
        $equipos = $this->equipoDAO->ordenarPorValor($equipos, function ($equipo) {
            return $this->equipoDAO->getValorEquipo($equipo->getId());
        }, $order);
        $equipos = array_slice($equipos, $offset, $limit);
        if (empty($equipos)) {
            echo '<tr><td colspan="6" class="text-center">No s\'han trobat equips</td></tr>';
            exit;
        }
        foreach ($equipos as $index => $equipo) {
            $equipoId = $equipo->getId();
            $valorTotal = $this->equipoDAO->getValorEquipo($equipoId);
            $cantidadJugadores = $this->equipoDAO->getCantidadJugadores($equipoId);
            $valorPromedio = $cantidadJugadores > 0 ? $this->equipoDAO->getMediaValorJugadores($equipoId) : 0;
            echo '<tr onclick="window.location=\'/practicas/index.php?action=view&id=' . urlencode($equipo->getId()) . '\'" style="cursor:pointer;">';
            echo '<td class="align-middle fs-4 fw-bold">' . ($offset + $index + 1) . '</td>';
            echo '<td class="align-middle">' . htmlspecialchars($equipo->getId()) . '</td>';
            echo '<td class="align-middle d-flex align-items-center gap-2">';
            echo '<img src="' . htmlspecialchars($equipo->getEscudo()) . '" alt="' . htmlspecialchars($equipo->getEquip()) . '" style="height:32px; margin-right:8px;">';
            echo '<span class="fw-bold text-uppercase">' . htmlspecialchars($equipo->getEquip()) . '</span>';
            echo '</td>';
            echo '<td class="text-center align-middle" style="font-weight:bold; color:#2c3e50;">' . number_format($valorTotal, 2) . ' €</td>';
            echo '<td class="text-center align-middle">' . $cantidadJugadores . '</td>';
            echo '<td class="text-center align-middle">' . number_format($valorPromedio, 2) . ' €</td>';
            echo '</tr>';
        }
        exit;
    }
}
$controller = new SearchBarController();
$controller->handleRequest();
?>