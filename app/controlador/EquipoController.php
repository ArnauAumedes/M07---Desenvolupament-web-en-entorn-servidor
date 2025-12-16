<?php
require_once __DIR__ . '/../model/database/database.php';
require_once __DIR__ . '/../model/entities/Equipo.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';

class EquipoController
{
	private $equipoDAO;
	private $db;

	public function __construct()
	{
		$database = new Database();
		$this->db = $database->getConnection();
		$this->equipoDAO = new EquipoDAO($this->db);
	}

	public function handleRequest()
	{
		$action = $_GET['action'] ?? 'list';
		switch ($action) {
			case 'create':
				$this->createEquipo();
				break;
			case 'update':
				$this->updateEquipo();
				break;
			case 'delete':
				$this->deleteEquipo();
				break;
			case 'view':
				$this->viewEquipo();
				break;
			case 'list':
			default:
				$this->listEquipos();
				break;
		}
	}

	private function createEquipo()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			try {
				if (session_status() === PHP_SESSION_NONE) session_start();
				$user_id = $_SESSION['user']['user_id'] ?? null;
				if ($user_id === null) {
					throw new Exception('User not authenticated');
				}
				$equip = $_POST['equip'] ?? '';
				$escudo = $_POST['escudo'] ?? '';
				$jugados = $_POST['jugados'] ?? 0;
				$ganados = $_POST['ganados'] ?? 0;
				$empatados = $_POST['empatados'] ?? 0;
				$perdidos = $_POST['perdidos'] ?? 0;
				$puntos = $_POST['puntos'] ?? 0;
				$gf_gc = $_POST['gf_gc'] ?? '';
				$equipo = new Equipo(null, $equip, $user_id, $escudo, $jugados, $ganados, $empatados, $perdidos, $puntos, $gf_gc);
				$result = $this->equipoDAO->create($equipo);
				if ($result) {
					header("Location: /practicas/public/index.php?created=success&id=" . $result);
					exit();
				} else {
					header("Location: /practicas/public/index.php?created=error");
					exit();
				}
			} catch (Exception $e) {
				header("Location: /practicas/public/index.php?created=error&msg=" . urlencode($e->getMessage()));
				exit();
			}
		} else {
			include __DIR__ . '/../vista/createEquipo.php';
		}
	}

	private function updateEquipo()
	{
		$equipo = null;
		$message = "";
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			try {
				if (session_status() === PHP_SESSION_NONE) session_start();
				$user_id = $_SESSION['user']['user_id'] ?? null;
				if ($user_id === null) {
					throw new Exception('User not authenticated');
				}
				$id = $_POST['id'] ?? '';
				$equip = $_POST['equip'] ?? '';
				$escudo = $_POST['escudo'] ?? '';
				$jugados = $_POST['jugados'] ?? 0;
				$ganados = $_POST['ganados'] ?? 0;
				$empatados = $_POST['empatados'] ?? 0;
				$perdidos = $_POST['perdidos'] ?? 0;
				$puntos = $_POST['puntos'] ?? 0;
				$gf_gc = $_POST['gf_gc'] ?? '';
				$equipo = new Equipo($id, $equip, $user_id, $escudo, $jugados, $ganados, $empatados, $perdidos, $puntos, $gf_gc);
				$rowsAffected = $this->equipoDAO->update($equipo);
				if ($rowsAffected > 0) {
					header("Location: /practicas/public/index.php?updated=success");
					exit();
				} else {
					$message = "No s'ha pogut actualitzar l'equip";
				}
			} catch (Exception $e) {
				$message = "Error: " . $e->getMessage();
			}
		}
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			try {
				$equipo = $this->equipoDAO->findById($_GET['id']);
				if (!$equipo) {
					$message = "No s'ha trobat cap equip amb aquest ID";
				}
			} catch (Exception $e) {
				$message = "Error cercant l'equip: " . $e->getMessage();
			}
		}
		include __DIR__ . '/../vista/updateEquipo.php';
	}

	private function deleteEquipo()
	{
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			try {
				$id = $_GET['id'];
				$rowsAffected = $this->equipoDAO->delete($id);
				if ($rowsAffected > 0) {
					header("Location: /practicas/public/index.php?deleted=success&id=" . $id);
					exit();
				} else {
					header("Location: /practicas/public/index.php?deleted=error");
					exit();
				}
			} catch (Exception $e) {
				header("Location: /practicas/public/index.php?deleted=error&msg=" . urlencode($e->getMessage()));
				exit();
			}
		} else {
			header("Location: /practicas/public/index.php?deleted=noid");
			exit();
		}
	}

	private function viewEquipo()
	{
		$equipo = null;
		$message = "";
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			try {
				$equipo = $this->equipoDAO->findById($_GET['id']);
				if (!$equipo) {
					$message = "No s'ha trobat cap equip amb aquest ID";
					header("HTTP/1.0 404 Not Found");
				}
			} catch (Exception $e) {
				$message = "Error cercant l'equip: " . $e->getMessage();
			}
		} else {
			$message = "ID no proporcionat";
			header("HTTP/1.0 400 Bad Request");
		}
		include __DIR__ . '/../vista/singleEquipo.php';
	}

	private function listEquipos()
	{
		require_once __DIR__ . '/../model/dao/UserDAO.php';
		try {
			$equiposRaw = $this->equipoDAO->findAll();
			$userDAO = new UserDAO($this->db);
			$equipos = [];
			foreach ($equiposRaw as $equipo) {
				$user = $userDAO->getById($equipo->getUserId());
				$nombreEntrenador = $user ? $user['username'] : 'Desconocido';
				$fechaCreacion = $user ? $user['created_at'] : '';
				$diferencia = $equipo->getObjetivo();
				$equipos[] = [
					'nombreEntrenador' => $nombreEntrenador,
					'equipo' => $equipo,
					'fechaCreacion' => $fechaCreacion,
					'diferencia' => $diferencia
				];
			}
		} catch (Exception $e) {
			$equipos = [];
			$message = "Error obtenint equips: " . $e->getMessage();
		}
		include __DIR__ . '/../vista/osm/tabla-clasificacion.php';
	}
}
?>