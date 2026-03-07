<?php
/**
 * SearchBarControllerJugador.php
 * Controlador para la búsqueda de jugadores en la tabla de pichichis, asistencias y mejores valorados
 * Autor: Arnau Aumedes Jimenez
 */
require_once __DIR__ . '/../model/dao/JugadorDAO.php';
require_once __DIR__ . '/../model/entities/Jugador.php';
require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/components/CookieHelper.php';

class SearchBarControllerJugador
{
    private $db;
    private $jugadorDAO;
    private $jugador;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->jugadorDAO = new JugadorDAO($this->db);
        $this->jugador = new Jugador();
    }

    public function handleRequest()
    {
        $action = $_GET['action'] ?? 'search';
        $tipo = $_GET['tipo'];
        switch ($tipo) {
            case 'pichichis':
                $this->searchJugadoresPichichis();
                break;
            case 'asistencias':
                $this->searchJugadoresAsistencias();
                break;
            default:
                $this->searchJugadoresValoracion();
                break;
        }
    }

    public function searchJugadoresPichichis()
    {
        header('Content-Type: text/html; charset=UTF-8');
        // Agafar els paràmetres de cerca, paginació i ordre des de les cookies
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page = CookieHelper::getPagePreference('page', 'page_preference', 1);
        $limit = CookieHelper::getLimitPreference('limit', 'limit_preference', 5);
        $offset = ($page - 1) * $limit;
        $order = CookieHelper::getOrderPreference('order', 'order_preference', 'desc');
        // Realitzar la cerca
        if ($search === '') {
            $jugadores = $this->jugadorDAO->findAll();
        } else {
            $jugadores = $this->jugadorDAO->findByName($search);
        }
        // Ordenar los jugadores
        $jugadores = $this->jugadorDAO->ordenarPorValor($jugadores, function ($jugador) {
            return $jugador->getGoles();
        }, $order);
        // Aplicar paginació
        $jugadores = array_slice($jugadores, $offset, $limit);
        if (empty($jugadores)) {
            echo '<tr><td colspan="6" class="text-center">No se han encontrado jugadores</td></tr>';
            exit;
        }
        // Mostrar els resultats
        require_once __DIR__ . '/../model/dao/EquipoDAO.php';
        $equipoDAO = new EquipoDAO($this->db);
        foreach ($jugadores as $index => $jugador) {
            $equipo = $equipoDAO->findById($jugador->getEquipoId());
            $mediaGoles = method_exists($this->jugadorDAO, 'getMediaPorPartidoJugador')
                ? $this->jugadorDAO->getMediaPorPartidoJugador($jugador->getId(), 'goles')
                : ($jugador->getPartidos() > 0 ? $jugador->getGoles() / $jugador->getPartidos() : 0);
            echo '<tr onclick="window.location=\'index.php?action=viewJugador&id=' . urlencode($jugador->getId()) . '\'" style="cursor:pointer;">';
            // ID JUGADOR
            echo '<td class="align-middle">' . htmlspecialchars($jugador->getId()) . '</td>';
            // NOMBRE
            echo '<td class="align-middle fw-bold text-uppercase">' . htmlspecialchars($jugador->getNombreCompleto()) . '</td>';
            // CLUB
            echo '<td class="align-middle d-flex align-items-center gap-2">';
            if ($equipo) {
                echo '<img src="' . htmlspecialchars($equipo->getEscudo()) . '" alt="' . htmlspecialchars($equipo->getEquip()) . '" style="height:32px; margin-right:8px;">';
                echo '<span class="fw-bold text-uppercase">' . htmlspecialchars($equipo->getEquip()) . '</span><br>';
                echo '<span class="text-muted club-usuario" style="font-size:0.95em;">';
                if ($equipo->getEntrenador() === null || $equipo->getEntrenador() === "") {
                    echo 'no tiene entrenador';
                } else {
                    echo htmlspecialchars($equipo->getEntrenador());
                }
                echo '</span>';
            }
            echo '</td>';
            // PARTIDOS
            echo '<td class="text-center align-middle">' . htmlspecialchars($jugador->getPartidos()) . '</td>';
            // GOLES
            echo '<td class="text-center align-middle">' . htmlspecialchars($jugador->getGoles()) . '</td>';
            // GOLES/PARTIDO
            echo '<td class="text-center align-middle" style="font-weight:bold;">' . str_replace('.', "'", number_format($mediaGoles, 2)) . '</td>';
            echo '</tr>';
        }
        exit;
    }

    public function searchJugadoresAsistencias()
    {
        header('Content-Type: text/html; charset=UTF-8');
        // Agafar els paràmetres de cerca, paginació i ordre des de les cookies
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page = CookieHelper::getPagePreference('page', 'page_preference', 1);
        $limit = CookieHelper::getLimitPreference('limit', 'limit_preference', 5);
        $offset = ($page - 1) * $limit;
        $order = CookieHelper::getOrderPreference('order', 'order_preference', 'desc');
        // Realitzar la cerca
        if ($search === '') {
            $jugadores = $this->jugadorDAO->findAll();
        } else {
            $jugadores = $this->jugadorDAO->findByName($search);
        }
        $jugadores = $this->jugadorDAO->ordenarPorValor($jugadores, function ($jugador) {
            return $jugador->getAsistencias();
        }, $order);
        $jugadores = array_slice($jugadores, $offset, $limit);
        if (empty($jugadores)) {
            echo '<tr><td colspan="6" class="text-center">No se han encontrado jugadores</td></tr>';
            exit;
        }
        require_once __DIR__ . '/../model/dao/EquipoDAO.php';
        $equipoDAO = new EquipoDAO($this->db);
        foreach ($jugadores as $index => $jugador) {
            $equipo = $equipoDAO->findById($jugador->getEquipoId());
            $mediaAsistencias = method_exists($this->jugadorDAO, 'getMediaPorPartidoJugador')
                ? $this->jugadorDAO->getMediaPorPartidoJugador($jugador->getId(), 'asistencias')
                : ($jugador->getPartidos() > 0 ? $jugador->getAsistencias() / $jugador->getPartidos() : 0);
            echo '<tr onclick="window.location=\'index.php?action=viewJugador&id=' . urlencode($jugador->getId()) . '\'" style="cursor:pointer;">';
            // ID JUGADOR
            echo '<td class="align-middle">' . htmlspecialchars($jugador->getId()) . '</td>';
            // NOMBRE
            echo '<td class="align-middle fw-bold text-uppercase">' . htmlspecialchars($jugador->getNombreCompleto()) . '</td>';
            // CLUB
            echo '<td class="align-middle d-flex align-items-center gap-2">';
            if ($equipo) {
                echo '<img src="' . htmlspecialchars($equipo->getEscudo()) . '" alt="' . htmlspecialchars($equipo->getEquip()) . '" style="height:32px; margin-right:8px;">';
                echo '<span class="fw-bold text-uppercase">' . htmlspecialchars($equipo->getEquip()) . '</span><br>';
                echo '<span class="text-muted club-usuario" style="font-size:0.95em;">';
                if ($equipo->getEntrenador() === null || $equipo->getEntrenador() === "") {
                    echo 'no tiene entrenador';
                } else {
                    echo htmlspecialchars($equipo->getEntrenador());
                }
                echo '</span>';
            }
            echo '</td>';
            // PARTIDOS
            echo '<td class="text-center align-middle">' . htmlspecialchars($jugador->getPartidos()) . '</td>';
            // ASISTENCIAS
            echo '<td class="text-center align-middle">' . htmlspecialchars($jugador->getAsistencias()) . '</td>';
            // ASISTENCIAS/PARTIDO
            echo '<td class="text-center align-middle" style="font-weight:bold;">' . str_replace('.', "'", number_format($mediaAsistencias, 2)) . '</td>';
            echo '</tr>';
        }
        exit;
    }

    public function searchJugadoresValoracion()
    {
        header('Content-Type: text/html; charset=UTF-8');
        $search = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page = CookieHelper::getPagePreference('page', 'page_preference', 1);
        $limit = CookieHelper::getLimitPreference('limit', 'limit_preference', 5);
        $offset = ($page - 1) * $limit;
        $order = CookieHelper::getOrderPreference('order', 'order_preference', 'desc');

        if ($search === '') {
            $jugadores = $this->jugadorDAO->findAll();
        } else {
            $jugadores = $this->jugadorDAO->findByName($search);
        }
        $jugadores = $this->jugadorDAO->ordenarPorValor($jugadores, function ($jugador) {
            return $this->jugadorDAO->getSumaGolesAsistencias($jugador->getId());
        }, $order);
        $jugadores = array_slice($jugadores, $offset, $limit);
        if (empty($jugadores)) {
            echo '<tr><td colspan="7" class="text-center">No se han encontrado jugadores</td></tr>';
            exit;
        }
        require_once __DIR__ . '/../model/dao/EquipoDAO.php';
        $equipoDAO = new EquipoDAO($this->db);
        foreach ($jugadores as $index => $jugador) {
            $equipo = $equipoDAO->findById($jugador->getEquipoId());
            echo '<tr onclick="window.location=\'index.php?action=viewJugador&id=' . urlencode($jugador->getId()) . '\'" style="cursor:pointer;">';
            // ID JUGADOR
            echo '<td class="align-middle">' . htmlspecialchars($jugador->getId()) . '</td>';
            // NOMBRE
            echo '<td class="align-middle fw-bold text-uppercase">' . htmlspecialchars($jugador->getNombreCompleto()) . '</td>';
            // CLUB
            echo '<td class="align-middle d-flex align-items-center gap-2">';
            if ($equipo) {
                echo '<img src="' . htmlspecialchars($equipo->getEscudo()) . '" alt="' . htmlspecialchars($equipo->getEquip()) . '" style="height:32px; margin-right:8px;">';
                echo '<span class="fw-bold text-uppercase">' . htmlspecialchars($equipo->getEquip()) . '</span><br>';
                echo '<span class="text-muted club-usuario" style="font-size:0.95em;">';
                if ($equipo->getEntrenador() === null || $equipo->getEntrenador() === "") {
                    echo 'no tiene entrenador';
                } else {
                    echo htmlspecialchars($equipo->getEntrenador());
                }
                echo '</span>';
            }
            echo '</td>';
            // PARTIDOS
            echo '<td class="text-center align-middle">' . htmlspecialchars($jugador->getPartidos()) . '</td>';
            // GOLES
            echo '<td class="text-center align-middle">' . htmlspecialchars($jugador->getGoles()) . '</td>';
            // ASISTENCIAS
            echo '<td class="text-center align-middle">' . htmlspecialchars($jugador->getAsistencias()) . '</td>';
            // GOLES + ASISTENCIAS
            if (method_exists($this->jugadorDAO, 'getSumaGolesAsistencias')) {
                $suma = $this->jugadorDAO->getSumaGolesAsistencias($jugador->getId());
            } else {
                $suma = $jugador->getGoles() + $jugador->getAsistencias();
            }
            echo '<td class="text-center align-middle" style="font-weight:bold;">' . htmlspecialchars($suma) . '</td>';
            echo '</tr>';
        }
        exit;
    }
}
$controller = new SearchBarControllerJugador();
$controller->handleRequest();
?>