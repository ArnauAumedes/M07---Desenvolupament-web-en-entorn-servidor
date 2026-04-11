<?php
/**
 * SearchBarControllerEquipo.php
 * Controlador para la búsqueda de equipos en la tabla de clasificación y valoración
 * Autor: Arnau Aumedes Jimenez
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../model/dao/EquipoDAO.php';
require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/components/CookieHelper.php';
require_once __DIR__ . '/../services/DataSourceResolver.php';
require_once __DIR__ . '/../services/EquipoDataService.php';

class SearchBarControllerEquipo
{
    private $db;
    private $equipoDAO;
    private $equipoDataService;
    private $source = 'bdd';

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->equipoDAO = new EquipoDAO($this->db);
        $this->equipoDataService = new EquipoDataService($this->db);
    }

    public function handleRequest()
    {
        $action = $_GET['action'] ?? 'search';
        $this->source = DataSourceResolver::resolve();
        $tipo = $_GET['tipo'] ?? 'clasificacion';
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
            $equipos = $this->equipoDataService->getAll($this->source);
        } else {
            $equipos = $this->equipoDataService->findByName($search, $this->source);
        }
        $equipos = $this->equipoDataService->sortByValue($equipos, function ($equipo) {
            return $this->equipoDataService->getPuntos((int) $equipo->getId(), $this->source);
        }, $order);
        $equipos = array_slice($equipos, $offset, $limit);
        if (empty($equipos)) {
            echo '<tr><td colspan="8" class="text-center">No s\'han trobat equips</td></tr>';
            exit;
        }
        foreach ($equipos as $index => $equipo) {
            // ID EQUIPO
            echo '<tr onclick="window.location=\'index.php?action=view&id=' . urlencode($equipo->getId()) . '\'" style="cursor:pointer;">';
            // POSICIÓN
            echo '<td class="align-middle fs-4 fw-bold">' . ($offset + $index + 1) . '</td>';
            // CLUB
            echo '<td class="align-middle">' . htmlspecialchars($equipo->getId()) . '</td>';
            echo '<td class="align-middle d-flex align-items-center gap-2">';
            echo '<img src="' . htmlspecialchars($equipo->getEscudo()) . '" alt="' . htmlspecialchars($equipo->getEquip()) . '" style="height:32px; margin-right:8px;">';
            echo '<div>';
            echo '<span class="fw-bold text-uppercase">' . htmlspecialchars($equipo->getEquip()) . '</span><br>';
            echo '<span class="text-muted club-usuario" style="font-size:0.95em;">';
            if ($equipo->getEntrenador() === null || $equipo->getEntrenador() === "") {
                echo 'no tiene entrenador';
            } else {
                echo htmlspecialchars($equipo->getEntrenador());
            }
            echo '</span>';
            echo '</div>';
            echo '</td>';
            // JUGADOS: GANADOS, EMPATADOS, PERDIDOS
            echo '<td class="text-center align-middle">' . htmlspecialchars($equipo->getJugados()) . '</td>';
            echo '<td class="text-center align-middle">' . htmlspecialchars($equipo->getGanados()) . '</td>';
            echo '<td class="text-center align-middle">' . htmlspecialchars($equipo->getEmpatados()) . '</td>';
            echo '<td class="text-center align-middle">' . htmlspecialchars($equipo->getPerdidos()) . '</td>';
            // PUNTOS
            $puntos = $this->equipoDataService->getPuntos((int) $equipo->getId(), $this->source);
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
            $equipos = $this->equipoDataService->getAll($this->source);
        } else {
            $equipos = $this->equipoDataService->findByName($search, $this->source);
        }
        $equipos = $this->equipoDataService->sortByValue($equipos, function ($equipo) {
            return $this->equipoDataService->getValorEquipo((int) $equipo->getId(), $this->source);
        }, $order);
        $equipos = array_slice($equipos, $offset, $limit);
        if (empty($equipos)) {
            echo '<tr><td colspan="6" class="text-center">No s\'han trobat equips</td></tr>';
            exit;
        }
        foreach ($equipos as $index => $equipo) {
            $equipoId = $equipo->getId();
            $valorTotal = $this->equipoDataService->getValorEquipo((int) $equipoId, $this->source);
            $cantidadJugadores = $this->equipoDataService->getCantidadJugadores((int) $equipoId, $this->source);
            $valorPromedio = $this->equipoDataService->getMediaValorJugadores((int) $equipoId, $this->source);
            // ID EQUIPO
            echo '<tr onclick="window.location=\'index.php?action=view&id=' . urlencode($equipo->getId()) . '\'" style="cursor:pointer;">';
            // POSICIÓN
            echo '<td class="align-middle fs-4 fw-bold">' . ($offset + $index + 1) . '</td>';
            // CLUB
            echo '<td class="align-middle">' . htmlspecialchars($equipo->getId()) . '</td>';
            echo '<td class="align-middle d-flex align-items-center gap-2">';
            echo '<img src="' . htmlspecialchars($equipo->getEscudo()) . '" alt="' . htmlspecialchars($equipo->getEquip()) . '" style="height:32px; margin-right:8px;">';
            echo '<span class="fw-bold text-uppercase">' . htmlspecialchars($equipo->getEquip()) . '</span>';
            echo '</td>';
            // VALOR TOTAL
            echo '<td class="text-center align-middle" style="font-weight:bold; color:#2c3e50;">' . number_format($valorTotal, 2) . ' €</td>';
            // CANTIDAD DE JUGADORES
            echo '<td class="text-center align-middle">' . $cantidadJugadores . '</td>';
            // VALOR PROMEDIO
            echo '<td class="text-center align-middle">' . number_format($valorPromedio, 2) . ' €</td>';
            echo '</tr>';
        }
        exit;
    }
}
$controller = new SearchBarControllerEquipo();
$controller->handleRequest();
?>