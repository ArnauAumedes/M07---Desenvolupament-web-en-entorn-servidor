<?php
require_once __DIR__ . '/../model/database/database.php';
require_once __DIR__ . '/../model/entities/Equipo.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';

class EquipoController
{
	private $equipoDAO;
	private $db;

	/**
	 * Constructor de EquipoController
	 * Inicializa la conexión a la base de datos y el DAO de equipos
	 */
	public function __construct()
	{
		$database = new Database();
		$this->db = $database->getConnection();
		$this->equipoDAO = new EquipoDAO($this->db);
	}

	/**
	 * Maneja la petición HTTP y redirige a la acción correspondiente
	 * según el parámetro 'action' recibido por GET.
	 *
	 * @return void
	 */
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
			case 'valor-equipo':
				$this->listEquipos('valor-equipo', function ($equipo) {
					return $this->equipoDAO->getValorEquipo($equipo->getId());
				});
				break;
			default:
				$this->listEquipos('tabla-clasificacion', function ($equipo) {
					return $this->equipoDAO->getPuntos($equipo->getId());
				});
				break;
		}
	}

	/**
	 * Crea un nuevo equipo a partir de los datos del formulario POST.
	 * Valida los datos recibidos y redirige según el resultado de la operación.
	 *
	 * @return void
	 */
	private function createEquipo()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			try {
				if (session_status() === PHP_SESSION_NONE)
					session_start();
				$user_id = $_SESSION['user']['user_id'] ?? null;
				if ($user_id === null) {
					throw new Exception('User not authenticated');
				}
				$equip = $_POST['equip'] ?? '';
				$objetivo = $_POST['objetivo'] ?? '';
				$escudo = $_POST['escudo'] ?? '';
				$jugados = (int) ($_POST['jugados'] ?? 0);
				$ganados = (int) ($_POST['ganados'] ?? 0);
				$empatados = (int) ($_POST['empatados'] ?? 0);
				$perdidos = (int) ($_POST['perdidos'] ?? 0);
				if (($ganados + $empatados + $perdidos) !== $jugados) {
					$error_partidos = 'La suma de partidos ganados, empatados y perdidos debe ser igual a los partidos jugados (' . $jugados . ').';
					include __DIR__ . '/../vista/crudEquipos/createEquipos.php';
					return;
				}
				// Validación de objetivo
				$totalEquipos = $this->equipoDAO->countAll();
				if (!is_numeric($objetivo) || $objetivo <= 1 || $objetivo >= $totalEquipos) {
					$error_partidos = 'El objetivo debe ser un número mayor que 1 y menor que el número total de equipos (' . $totalEquipos . ').';
					include __DIR__ . '/../vista/crudEquipos/createEquipos.php';
					return;
				}
				$equipo = new Equipo(null, $equip, $user_id, $escudo, $jugados, $ganados, $empatados, $perdidos, $objetivo);
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
			include __DIR__ . '/../vista/crudEquipos/createEquipos.php';
		}
	}

	/**
	 * Actualiza los datos de un equipo existente a partir del formulario POST.
	 * Valida los datos recibidos y redirige según el resultado de la operación.
	 *
	 * @return void
	 */
	private function updateEquipo()
	{
		$equipo = null;
		$message = "";
		$error_partidos = '';
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			try {
				if (session_status() === PHP_SESSION_NONE)
					session_start();
				$user_id = $_SESSION['user']['user_id'] ?? null;
				if ($user_id === null) {
					throw new Exception('User not authenticated');
				}
				$id = $_POST['id'] ?? '';
				$equip = $_POST['equip'] ?? '';
				$objetivo = $_POST['objetivo'] ?? '';
				$escudo = $_POST['escudo'] ?? '';
				$jugados = (int) ($_POST['jugados'] ?? 0);
				$ganados = (int) ($_POST['ganados'] ?? 0);
				$empatados = (int) ($_POST['empatados'] ?? 0);
				$perdidos = (int) ($_POST['perdidos'] ?? 0);
				if (($ganados + $empatados + $perdidos) !== $jugados) {
					$error_partidos = 'La suma de partidos ganados, empatados y perdidos debe ser igual a los partidos jugados (' . $jugados . ').';
				} else {
					// Validación de objetivo
					$totalEquipos = $this->equipoDAO->countAll();
					if (!is_numeric($objetivo) || $objetivo <= 1 || $objetivo >= $totalEquipos) {
						$error_partidos = 'El objetivo debe ser un número mayor que 1 y menor que el número total de equipos (' . $totalEquipos . ').';
					} else {
						$equipo = new Equipo($id, $equip, $user_id, $escudo, $jugados, $ganados, $empatados, $perdidos, $objetivo);
						$rowsAffected = $this->equipoDAO->update($equipo);
						if ($rowsAffected > 0) {
							header("Location: /practicas/public/index.php?updated=success");
							exit();
						} else {
							$message = "No s'ha pogut actualitzar l'equip";
						}
					}
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
		include __DIR__ . '/../vista/crudEquipos/updateEquipos.php';
	}

	/**
	 * Elimina un equipo por su ID recibido por GET.
	 * Redirige según el resultado de la operación.
	 *
	 * @return void
	 */
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
			include __DIR__ . '/../vista/crudEquipos/deleteEquipos.php';
		}
	}

	/**
	 * Muestra los datos de un equipo por su ID recibido por GET.
	 * Incluye la vista correspondiente para mostrar los detalles del equipo.
	 *
	 * @return void
	 */
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
		include __DIR__ . '/../vista/crudEquipos/singleEquipo.php';
	}
	/**
	 * Lista los equipos de forma paginada y opcionalmente ordenada.
	 * Incluye la vista especificada para mostrar los equipos.
	 *
	 * @param string $vista Nombre del archivo de vista a incluir (sin extensión ni ruta completa)
	 * @param callable|null $ordenCallback Función de callback para ordenar los equipos (opcional)
	 * @return void
	 */
	private function listEquipos($vista = 'tabla-clasificacion', $ordenCallback = null)
	{
		require_once __DIR__ . '/../model/dao/UserDAO.php';

		$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
		$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int) $_GET['limit'] : 10;
		$offset = ($page - 1) * $limit;

		try {
			$totalEquipos = $this->equipoDAO->countAll();
			$totalPages = max(1, ceil($totalEquipos / $limit));
			$equipos = $this->equipoDAO->findAll(); 

			if ($ordenCallback !== null) {
				$equipos = $this->equipoDAO->ordenarPorValor($equipos, $ordenCallback, 'desc'); 
			}

			$equipos = array_slice($equipos, $offset, $limit); 

		} catch (Exception $e) {
			$equipos = [];
			$message = "Error obteniendo equipos: " . $e->getMessage();
			$totalPages = 1;
			$page = 1;
			$limit = 10;
		}
		$equipoDAO = $this->equipoDAO;
		include __DIR__ . "/../vista/osm/{$vista}.php";
	}
}
?>