<?php
/**
 * JugadorController.php
 * Controlador para la gestión de jugadores
 * Autor: Arnau Aumedes Jimenez
 *
 * Este archivo implementa la lógica de creación, actualización, borrado, visualización y listado de jugadores.
 * Cada método está documentado y el código incluye comentarios explicativos.
 */
require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/entities/Jugador.php';
require_once __DIR__ . '/../model/dao/JugadorDAO.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';
require_once __DIR__ . '/../model/components/CookieHelper.php';
require_once __DIR__ . '/../services/DataSourceResolver.php';
require_once __DIR__ . '/../services/JugadorDataService.php';
require_once __DIR__ . '/../services/EquipoDataService.php';


class JugadorController
{
    private $jugadorDAO;
    private $jugador;
    private $equipoDAO;
    private $db;
    private $jugadorDataService;
    private $equipoDataService;
    private $currentSource = 'bdd';


    public function __construct()
    {
        $database = Database::getInstance();
        $this->db = $database->getConnection();
        $this->jugadorDAO = new JugadorDAO($this->db);
        $this->equipoDAO = new EquipoDAO($this->db);
        $this->jugador = new Jugador();
        $this->jugadorDataService = new JugadorDataService($this->db);
        $this->equipoDataService = new EquipoDataService($this->db);
    }

    public function handleRequest()
    {
        $this->currentSource = DataSourceResolver::resolve();
        $action = $_POST['action'] ?? ($_GET['action'] ?? 'mejores-valorados');
        switch ($action) {
            case 'createJugador':
                $this->createJugador();
                break;
            case 'updateJugador':
                $this->updateJugador();
                break;
            case 'deleteJugador':
                $this->deleteJugador();
                break;
            case 'viewJugador':
                $this->viewJugador();
                break;
            case 'pichichis':
                $this->listJugadores('pichichis', function ($jugador) {
                    return $jugador->getGoles();
                });
                break;
            case 'asistencias':
                $this->listJugadores('asistencias', function ($jugador) {
                    return $jugador->getAsistencias();
                });
                break;
            default:
                $this->listJugadores('mejores-valorados', function ($jugador) {
                    return $this->jugadorDataService->getSumaGolesAsistencias((int) $jugador->getId(), $this->currentSource);
                });
                break;
        }
    }

    private function createJugador()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $nombre_completo = $_POST['nombre_completo'] ?? '';
                $equipo_id = $_POST['equipo_id'] ?? '';
                $valor = $_POST['valor'] ?? '';
                $partidos = (int) ($_POST['partidos'] ?? 0);
                $goles = (int) ($_POST['goles'] ?? 0);
                $asistencias = (int) ($_POST['asistencias'] ?? 0);
                // Validaciones básicas
                $error_jugador = '';
                if (empty($nombre_completo) || empty($equipo_id) || $valor === '') {
                    $error_jugador = 'Todos los campos son obligatorios.';
                    include __DIR__ . '/../vista/crudJugadores/createJugadores.php';
                    return;
                }
                // Validar que ningún valor sea menor que 0
                if ($valor < 0 || $partidos < 0 || $goles < 0 || $asistencias < 0) {
                    $error_jugador = 'Ningún valor puede ser menor que 0.';
                    include __DIR__ . '/../vista/crudJugadores/createJugadores.php';
                    return;
                }
                // Comprobar que el equipo existe
                $equipo = $this->equipoDAO->findById($equipo_id);
                if (!$equipo) {
                    $error_jugador = 'El equipo seleccionado no existe.';
                    include __DIR__ . '/../vista/crudJugadores/createJugadores.php';
                    return;
                }
                $jugador = new Jugador(null, $nombre_completo, $equipo_id, $valor, $partidos, $goles, $asistencias);
                $result = $this->jugadorDAO->create($jugador);
                if ($result) {
                    header("Location: index.php?createdJugador=success&id=" . $result);
                    exit();
                } else {
                    header("Location: index.php?createdJugador=error");
                    exit();
                }
            } catch (Exception $e) {
                error_log('Error creando jugador: ' . $e->getMessage());
                header("Location: index.php?createdJugador=error");
                exit();
            }
        } else {
            include __DIR__ . '/../vista/crudJugadores/createJugadores.php';
        }
    }

    private function updateJugador()
    {
        $jugador = null;
        $message = "";
        $error_jugador = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                    if (session_status() === PHP_SESSION_NONE)
                        session_start();
                    $user_id = $_SESSION['user']['user_id'] ?? null;
                    $isAdmin = $_SESSION['user']['isAdmin'] ?? 0;
                $id = $_POST['id'] ?? null;
                $nombre_completo = $_POST['nombre_completo'] ?? '';
                $equipo_id = $_POST['equipo_id'] ?? '';
                $valor = $_POST['valor'] ?? '';
                $partidos = (int) ($_POST['partidos'] ?? 0);
                $goles = (int) ($_POST['goles'] ?? 0);
                $asistencias = (int) ($_POST['asistencias'] ?? 0);
                if (empty($nombre_completo) || empty($equipo_id) || $valor === '') {
                    $error_jugador = 'Todos los campos son obligatorios.';
                } else {
                    // Comprobar que el equipo existe
                    $equipo = $this->equipoDAO->findById($equipo_id);
                    if (!$equipo) {
                        $error_jugador = 'El equipo seleccionado no existe.';
                    } else {
                            // Permiso: solo creador del equipo o admin
                            if ($user_id === null || ($equipo->getCreadorId() !== $user_id && !$isAdmin)) {
                                $error_jugador = 'No tienes permiso para editar jugadores de este equipo.';
                            } else {
                        $jugador = new Jugador($id, $nombre_completo, $equipo_id, $valor, $partidos, $goles, $asistencias);
                        $rowsAffected = $this->jugadorDAO->update($jugador);
                        if ($rowsAffected > 0) {
                            header("Location: index.php?updatedJugador=success");
                            exit();
                        } else {
                            $message = "No se ha podido actualizar el jugador.";
                        }
                            }
                    }
                }
            } catch (Exception $e) {
                error_log('Error actualizando jugador: ' . $e->getMessage());
                $message = "Error interno del servidor.";
            }
        }
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            try {
                $jugador = $this->jugadorDAO->findById($_GET['id']);
                if (!$jugador) {
                    $message = "No se ha encontrado ningún jugador con este ID.";
                }
            } catch (Exception $e) {
                error_log('Error cargando jugador para update: ' . $e->getMessage());
                $message = "Error interno del servidor.";
            }
        }
        include __DIR__ . '/../vista/crudJugadores/updateJugadores.php';
    }

    private function deleteJugador()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !empty($_POST['id'])) {
            try {
                    if (!$this->isValidCsrfToken($_POST['csrf_token'] ?? null)) {
                        throw new Exception('Invalid CSRF token');
                    }
                    if (session_status() === PHP_SESSION_NONE)
                        session_start();
                    $user_id = $_SESSION['user']['user_id'] ?? null;
                    $isAdmin = $_SESSION['user']['isAdmin'] ?? 0;
                $id = $_POST['id'];
                    // Obtener equipo del jugador
                    $jugador = $this->jugadorDAO->findById($id);
                    if ($jugador) {
                        $equipo = $this->equipoDAO->findById($jugador->getEquipoId());
                        if ($user_id === null || ($equipo && $equipo->getCreadorId() !== $user_id && !$isAdmin)) {
                            header("Location: index.php?deletedJugador=error");
                            exit();
                        }
                    }
                $rowsAffected = $this->jugadorDAO->delete($id);
                if ($rowsAffected > 0) {
                    header("Location: index.php?deletedJugador=success&id=" . $id);
                    exit();
                } else {
                    header("Location: index.php?deletedJugador=error");
                    exit();
                }
            } catch (Exception $e) {
                error_log('Error eliminando jugador: ' . $e->getMessage());
                header("Location: index.php?deletedJugador=error");
                exit();
            }
        } else {
            $csrfToken = $this->getCsrfToken();
            $idPrefill = $_GET['id'] ?? '';
            include __DIR__ . '/../vista/crudJugadores/deleteJugadores.php';
        }
    }

    private function viewJugador()
    {
        $jugador = null;
        $message = "";
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            try {
                $jugador = $this->jugadorDAO->findById($_GET['id']);
                if (!$jugador) {
                    $message = "No se ha encontrado ningún jugador con este ID.";
                    header("HTTP/1.0 404 Not Found");
                }
            } catch (Exception $e) {
                error_log('Error visualizando jugador: ' . $e->getMessage());
                $message = "Error interno del servidor.";
            }
        } else {
            $message = "ID no proporcionado";
            header("HTTP/1.0 400 Bad Request");
        }
        if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {    
            include __DIR__ . '/../vista/crudJugadores/singleJugador.php';
            exit;
        }
     }

    /**
     * Lista los jugadores de forma paginada y opcionalmente ordenada.
     * Incluye la vista especificada para mostrar los jugadores.
     *
     * @param string $vista Nombre del archivo de vista a incluir (sin extensión ni ruta completa)
     * @param callable|null $ordenCallback Función de callback para ordenar los jugadores (opcional)
     * @return void
     */
    private function listJugadores($vista = 'mejores-valorados', $ordenCallback = null)
    {
        require_once __DIR__ . '/../model/dao/UserDAO.php';
        $source = $this->currentSource;
        
        // Usar CookieHelper para obtener la página actual (GET o cookie)
        $page = CookieHelper::getPagePreference('page', 'page_preference', 1);
        $limit = CookieHelper::getLimitPreference('limit', 'limit_preference', 10);

        try {
            $order = CookieHelper::getOrderPreference('order', 'order_preference') ?? 'desc';
            if (!in_array(strtolower($order), ['asc', 'desc'], true)) {
                $order = 'desc';
            }

            // Calcular total de jugadores y páginas
            $totalJugadores = $this->jugadorDAO->countAll();
            $totalPages = max(1, ceil($totalJugadores / $limit));
            
            // Ajustar la página si excede el total de páginas
            if ($page > $totalPages) {
                $page = 1;
                CookieHelper::set('page_preference', $page);
            }

            $offset = ($page - 1) * $limit;

            if ($source === 'bdd') {
                if ($vista === 'pichichis') {
                    $jugadores = $this->jugadorDAO->getPichichisPaginados($limit, $offset, $order);
                } elseif ($vista === 'asistencias') {
                    $jugadores = $this->jugadorDAO->getAsistenciasPaginados($limit, $offset, $order);
                } elseif ($vista === 'mejores-valorados') {
                    $jugadores = $this->jugadorDAO->getMejoresValoradosPaginados($limit, $offset, $order);
                } else {
                    $jugadores = $this->jugadorDAO->getJugadoresPaginados($limit, $offset);
                }
            } else {
                $jugadores = $this->jugadorDataService->getAll($source);
                if ($ordenCallback !== null) {
                    $jugadores = $this->jugadorDataService->sortByValue($jugadores, $ordenCallback, $order);
                }
                $jugadores = array_slice($jugadores, $offset, $limit);
            }

        } catch (Exception $e) {
            $jugadores = [];
            error_log('Error listando jugadores: ' . $e->getMessage());
            $message = "Error interno del servidor.";
            $totalPages = 1;
            $page = 1;
            $limit = 5;
        }
        $jugadorDAO = $this->jugadorDataService;
        $equipoDAO = $this->equipoDataService;
        include __DIR__ . "/../vista/osm/{$vista}.php";
    }

    private function getCsrfToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    private function isValidCsrfToken($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['csrf_token']) && is_string($token) && hash_equals($_SESSION['csrf_token'], $token);
    }
}
?>