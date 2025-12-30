<?php
require_once __DIR__ . '/../model/database/database.php';
require_once __DIR__ . '/../model/entities/Jugador.php';

require_once __DIR__ . '/../model/dao/JugadorDAO.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';


class JugadorController
{
    private $jugadorDAO;
    private $equipoDAO;
    private $db;


    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->jugadorDAO = new JugadorDAO($this->db);
        $this->equipoDAO = new EquipoDAO($this->db);
    }

    public function handleRequest()
    {
        $action = $_GET['action'] ?? 'list';
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
            case 'listJugador':
            default:
                $this->listJugadores();
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
                    header("Location: /practicas/public/index.php?createdJugador=success&id=" . $result);
                    exit();
                } else {
                    header("Location: /practicas/public/index.php?createdJugador=error");
                    exit();
                }
            } catch (Exception $e) {
                header("Location: /practicas/public/index.php?createdJugador=error&msg=" . urlencode($e->getMessage()));
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
                $id = $_POST['id'] ?? '';
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
                        $jugador = new Jugador($id, $nombre_completo, $equipo_id, $valor, $partidos, $goles, $asistencias);
                        $rowsAffected = $this->jugadorDAO->update($jugador);
                        if ($rowsAffected > 0) {
                            header("Location: /practicas/public/index.php?updatedJugador=success");
                            exit();
                        } else {
                            $message = "No se ha podido actualizar el jugador.";
                        }
                    }
                }
            } catch (Exception $e) {
                $message = "Error: " . $e->getMessage();
            }
        }
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            try {
                $jugador = $this->jugadorDAO->findById($_GET['id']);
                if (!$jugador) {
                    $message = "No se ha encontrado ningún jugador con este ID.";
                }
            } catch (Exception $e) {
                $message = "Error buscando el jugador: " . $e->getMessage();
            }
        }
        include __DIR__ . '/../vista/crudJugadores/updateJugadores.php';
    }

    private function deleteJugador()
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            try {
                $id = $_GET['id'];
                $rowsAffected = $this->jugadorDAO->delete($id);
                if ($rowsAffected > 0) {
                    header("Location: /practicas/public/index.php?deletedJugador=success&id=" . $id);
                    exit();
                } else {
                    header("Location: /practicas/public/index.php?deletedJugador=error");
                    exit();
                }
            } catch (Exception $e) {
                header("Location: /practicas/public/index.php?deletedJugador=error&msg=" . urlencode($e->getMessage()));
                exit();
            }
        } else {
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
                $message = "Error buscando el jugador: " . $e->getMessage();
            }
        } else {
            $message = "ID no proporcionado";
            header("HTTP/1.0 400 Bad Request");
        }
        include __DIR__ . '/../vista/singleJugador.php';
    }

    private function listJugadores()
    {
        try {
            $jugadores = $this->jugadorDAO->findAll();
        } catch (Exception $e) {
            $jugadores = [];
            $message = "Error obteniendo jugadores: " . $e->getMessage();
        }
        include __DIR__ . '/../vista/crudJugadores/listJugadores.php';
    }
}
?>