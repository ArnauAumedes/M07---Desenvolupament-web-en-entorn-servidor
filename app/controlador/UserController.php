<?php
/**
 * UserController.php
 * Controlador para la gestión de usuarios
 * Autor: Arnau Aumedes Jimenez
 */
require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/entities/User.php';
require_once __DIR__ . '/../model/dao/UserDAO.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';
require_once __DIR__ . '/../model/components/CookieHelper.php';
require_once __DIR__ . '/../services/DataSourceResolver.php';
require_once __DIR__ . '/../services/UserDataService.php';
require_once __DIR__ . '/../services/EquipoDataService.php';

class UserController
{
	private $userDAO;
	private $equipoDAO;
	private $db;
	private $userDataService;
	private $equipoDataService;
	private $currentSource = 'bdd';

	/**
	 * Constructor de UserController
	 * Inicializa la conexión a la base de datos y el DAO de usuarios
	 */
	public function __construct()
	{
		$database = new Database();
		$this->db = $database->getConnection();
		$this->userDAO = new UserDAO($this->db);
		$this->equipoDAO = new EquipoDAO($this->db);
		$this->userDataService = new UserDataService($this->db);
		$this->equipoDataService = new EquipoDataService($this->db);
	}

	/**
	 * Maneja la petición HTTP y redirige a la acción correspondiente
	 * según el parámetro 'action' recibido por GET.	
	 *
	 * @return void
	 */
	public function handleRequest()
	{
		$this->currentSource = DataSourceResolver::resolve();
		$action = $_POST['action'] ?? ($_GET['action'] ?? 'lista-entrenador');
		switch ($action) {
			case 'createUser':
				$this->createUser();
				break;
			case 'updateUser':
				$this->updateUser();
				break;
			case 'deleteUser':
				$this->deleteUser();
				break;
			case 'edit-profile':
				$this->editProfile();
				break;
			case 'viewUser':
				$this->viewUser();
				break;
			default:
				$this->listEntrenadores();
				break;
		}
	}

	private function createUser()
	{
		$messages = '';
		// Solo mostrar equipos sin user_id o el equipo actual del usuario
		$equipos = [];
		$allEquipos = $this->equipoDAO->findAll();
		$userIdForEdit = null;
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$userIdForEdit = $_GET['id'];
		} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
			$userIdForEdit = $_POST['id'];
		}
		foreach ($allEquipos as $equipo) {
			if ($equipo->getCreadorId() === null || ($userIdForEdit && $equipo->getCreadorId() == $userIdForEdit)) {
				$equipos[] = $equipo;
			}
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$username = trim($_POST['username'] ?? '');
			$email = trim($_POST['email'] ?? '');
			$password = $_POST['password'] ?? '';
			$password2 = $_POST['password2'] ?? '';
			$isAdmin = (!empty($_SESSION['user']['isAdmin']) && $_SESSION['user']['isAdmin'] == 1 && isset($_POST['isAdmin'])) ? 1 : 0;

			// Validaciones
			if ($username === '' || $email === '' || $password === '' || $password2 === '' || empty($_POST['equipo_id'])) {
				$messages = 'Tots els camps són obligatoris';
			} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$messages = 'Email invàlid';
			} elseif ($password !== $password2) {
				$messages = 'Les contrasenyes no coincideixen';
			} elseif (strlen($password) < 8 || !preg_match('/[A-Z]/i', $password) || !preg_match('/[0-9]/', $password)) {
				$messages = 'La contrasenya ha de tenir almenys 8 caràcters i incloure lletres i números';
			} elseif ($this->userDAO->existsByEmail($email)) {
				$messages = 'Ja existeix un usuari amb aquest email';
			} elseif ($this->userDAO->existsByUsername($username)) {
				$messages = 'Ja existeix un usuari amb aquest nom d\'usuari';
			} else {
				$hash = password_hash($password, PASSWORD_DEFAULT);
				$user = new User(null, $username, $email, $hash, 1, $isAdmin);
				$userId = $this->userDAO->create($user);
				if ($userId) {
					if ($userId && isset($_POST['equipo_id']) && $_POST['equipo_id'] !== '') {
						$equipoId = $_POST['equipo_id'];
						$equipo = $this->equipoDAO->findById($equipoId);
						if ($equipo) {
							$equipo->setEntrenador($userId);
							$this->equipoDAO->update($equipo);
						}
					}
					header("Location: index.php?createdUser=success&id=" . $userId);
					exit();
				} else {
					$messages = 'Error al crear l\'usuari.';
				}
			}
		}
		include __DIR__ . '/../vista/crudUsers/createUser.php';
	}


	private function updateUser()
	{
		$user = null;
		$messages = '';
		// Solo mostrar equipos sin user_id o el equipo actual del usuario
		$equipos = [];
		$allEquipos = $this->equipoDAO->findAll();
		$userIdForEdit = null;
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$userIdForEdit = $_GET['id'];
		} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
			$userIdForEdit = $_POST['id'];
		}
		foreach ($allEquipos as $equipo) {
			if ($equipo->getCreadorId() === null || ($userIdForEdit && $equipo->getCreadorId() == $userIdForEdit)) {
				$equipos[] = $equipo;
			}
		}

		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$id = $_POST['id'] ?? '';
			$username = trim($_POST['username'] ?? '');
			$email = trim($_POST['email'] ?? '');
			$isAdmin = (!empty($_SESSION['user']['isAdmin']) && $_SESSION['user']['isAdmin'] == 1 && isset($_POST['isAdmin'])) ? 1 : 0;
			$equipoId = $_POST['equipo_id'] ?? '';

			if ($username === '' || $email === '' || empty($_POST['equipo_id'])) {
				$messages = 'Tots els camps són obligatoris';
			} else {
				$user = $this->userDAO->findById($id);
				if ($user) {
					$user->setUserName($username);
					$user->setEmail($email);
					if (!empty($_SESSION['user']['isAdmin']) && $_SESSION['user']['isAdmin'] == 1) {
						$user->setIsAdmin($isAdmin);
					}
					$rowsAffected = $this->userDAO->updateProfile($user);

					// Actualizar equipo si se selecciona
					if (!empty($equipoId)) {
						$equipo = $this->equipoDAO->findById($equipoId);
						if ($equipo) {
							$equipo->setEntrenador($user->getId());
							$this->equipoDAO->update($equipo);
						}
					}

					if ($rowsAffected > 0) {
						header("Location: index.php?updatedUser=success");
						exit();
					} else {
						$messages = "No s'ha pogut actualitzar l'usuari.";
					}
				} else {
					$messages = "No s'ha trobat l'usuari.";
				}
			}
		}
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			$user = $this->userDAO->findById($_GET['id']);
			if (!$user) {
				$messages = "No s'ha trobat cap usuari amb aquest ID.";
			}
		}
		include __DIR__ . '/../vista/crudUsers/updateUser.php';
	}

	private function deleteUser()
	{
		$messages = '';
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !empty($_POST['id'])) {
			if (!$this->isValidCsrfToken($_POST['csrf_token'] ?? null)) {
				error_log('Error eliminando usuario: invalid csrf token');
				header("Location: index.php?deletedUser=error");
				exit();
			}

			$id = $_POST['id'];
			$rowsAffected = $this->userDAO->delete($id);
			if ($rowsAffected > 0) {
				header("Location: index.php?deletedUser=success&id=" . $id);
				exit();
			} else {
				header("Location: index.php?deletedUser=error");
				exit();
			}
		}
		$csrfToken = $this->getCsrfToken();
		$idPrefill = $_GET['id'] ?? '';
		include __DIR__ . '/../vista/crudUsers/deleteUser.php';
	}
	/**
	 * Lista los entrenadores de forma paginada y opcionalmente ordenada.
	 * Incluye la vista correspondiente para mostrar los entrenadores.
	 * @param callable|null $ordenCallback Función de callback para ordenar los entrenadores (opcional)
	 * @return void
	 */
	private function listEntrenadores($ordenCallback = null)
	{
		$source = $this->currentSource;
		// Usar CookieHelper para obtener la página actual (GET o cookie)
		$page = CookieHelper::getPagePreference('page', 'page_preference', 1);
		$limit = CookieHelper::getLimitPreference('limit', 'limit_preference', 10);

		try {
			// Calcular total de entrenadores y páginas
			$totalEntrenadores = count($this->userDataService->getAll($source));
			$totalPages = max(1, ceil($totalEntrenadores / $limit));

			// Ajustar la página si excede el total de páginas
			if ($page > $totalPages) {
				$page = 1;
				CookieHelper::set('page_preference', $page);
			}

			$offset = ($page - 1) * $limit;
			$entrenadores = $this->userDataService->getAll($source);

			// Usar CookieHelper para obtener el orden (asc/desc), por defecto 'desc'
			$order = CookieHelper::getOrderPreference('order', 'order_preference', 'desc');
			if ($ordenCallback !== null) {
				$entrenadores = $this->userDataService->sortByValue($entrenadores, $ordenCallback, $order);
			}

			// Paginación
			$entrenadores = array_slice($entrenadores, $offset, $limit);

			// Para cada entrenador, obtener todos sus equipos
			$entrenadoresConEquipos = [];
			foreach ($entrenadores as $entrenador) {
				$equipoList = $this->equipoDataService->findByEntrenador((int) $entrenador->getId(), $source);
				$entrenadoresConEquipos[] = [
					'entrenador' => $entrenador,
					'equipos' => $equipoList
				];
			}
		} catch (Exception $e) {
			$entrenadoresConEquipos = [];
			error_log('Error listando entrenadores: ' . $e->getMessage());
			$message = "Error interno del servidor.";
			$totalPages = 1;
			$page = 1;
			$limit = 5;
		}
		$userDAO = $this->userDataService;
		$equipoDAO = $this->equipoDataService;
		include __DIR__ . '/../vista/osm/lista-entrenador.php';
	}

	private function viewUser()
	{
		$user = null;
		$message = '';
		if (isset($_GET['id']) && !empty($_GET['id'])) {
			try {
				$user = $this->userDAO->findById($_GET['id']);
				if (!$user) {
					$message = "No se ha encontrado ningún user con ese ID.";
					header("HTTP/1.0 404 Not Found");
				}
			} catch (Exception $e) {
				error_log('Error visualizando usuario: ' . $e->getMessage());
				$message = "Error interno del servidor.";
			}
		} else {
			header("HTTP/1.0 400 Bad Request");
			$message = "ID de user no proporcionado.";
		}
		if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
			include __DIR__ . '/../vista/crudUsers/singleUser.php';
		}
	}
	private function editProfile()
	{
		session_start();
		$messages = '';
		// Comprobar si el usuario está logueado
		if (!isset($_SESSION['user']['user_id'])) {
			// Redirigir al login si no está logueado
			header('Location: app/vista/login.php');
			exit();
		}

		// Si el formulario ha sido enviado
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSubmit'])) {
			$nickname = trim($_POST['nickname'] ?? '');
			$email = trim($_POST['email'] ?? '');

			// Validación básica
			if ($nickname === '' || $email === '') {
				$messages = '<div class="alert alert-danger">Tots els camps són obligatoris.</div>';
			} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$messages = '<div class="alert alert-danger">El correu no és vàlid.</div>';
			} else {
				// Actualizar en la base de datos
				$userObj = $this->userDAO->findById($_SESSION['user']['user_id']);
				if ($userObj) {
					$userObj->setUsername($nickname);
					$userObj->setEmail($email);
					$rowsAffected = $this->userDAO->updateProfile($userObj);
					if ($rowsAffected > 0) {
						// Actualizar el nombre en la sesión para reflejar el cambio en el header
						$_SESSION['user']['username'] = $nickname;
						echo '<script>alert("Perfil actualizado correctamente."); window.location.href = "index.php";</script>';
						exit();
					} else {
						$messages = '<div class="alert alert-warning">No s\'ha actualitzat cap dada (potser no has canviat res).</div>';
					}
				} else {
					$messages = '<div class="alert alert-danger">Usuari no trobat.</div>';
				}
			}
		}

		// Obtener los datos del usuario desde la base de datos
		$userObj = $this->userDAO->findById($_SESSION['user']['user_id']);
		if ($userObj) {
			$user = [
				'nickname' => $userObj->getUsername(),
				'email' => $userObj->getEmail()
			];
		} else {
			$user = ['nickname' => '', 'email' => ''];
			$messages = '<div class="alert alert-danger">No s\'han pogut carregar les dades de l\'usuari.</div>';
		}

		include __DIR__ . '/../vista/edit-profile.php';
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