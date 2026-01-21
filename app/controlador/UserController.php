<?php
require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/entities/User.php';
require_once __DIR__ . '/../model/dao/UserDAO.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';

class UserController
{
	private $userDAO;
	private $equipoDAO;
	private $db;

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
	}

	/**
	 * Maneja la petición HTTP y redirige a la acción correspondiente
	 * según el parámetro 'action' recibido por GET.
	 *
	 * @return void
	 */
	public function handleRequest()
	{
		$action = $_GET['action'] ?? 'lista-entrenador';
		switch ($action) {
			case 'lista-entrenador':
				$this->listEntrenadores();
				break;
			case 'edit-profile':
				$this->editProfile();
				break;
			default:
				$this->listEntrenadores();
				break;
		}
	}

	/**
	 * Lista los entrenadores de forma paginada y opcionalmente ordenada.
	 * Incluye la vista correspondiente para mostrar los entrenadores.
	 * @param callable|null $ordenCallback Función de callback para ordenar los entrenadores (opcional)
	 * @return void
	 */
	private function listEntrenadores($ordenCallback = null)
	{
		$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
		$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int) $_GET['limit'] : 10;
		$offset = ($page - 1) * $limit;

		try {
			$totalEntrenadores = $this->userDAO->countAll();
			$totalPages = max(1, ceil($totalEntrenadores / $limit));
			$entrenadores = $this->userDAO->findAll();

			if ($ordenCallback !== null) {
				$entrenadores = $this->userDAO->ordenarPorValor($entrenadores, $ordenCallback, 'desc');
			}

			$entrenadores = array_slice($entrenadores, $offset, $limit);

			// Para cada entrenador, obtener todos sus equipos
			$entrenadoresConEquipos = [];
			foreach ($entrenadores as $entrenador) {
				$equipoList = $this->equipoDAO->findByEntrenadorId($entrenador->getId());
				$entrenadoresConEquipos[] = [
					'entrenador' => $entrenador,
					'equipos' => $equipoList
				];
			}
		} catch (Exception $e) {
			$entrenadoresConEquipos = [];
			$message = "Error obteniendo entrenadores: " . $e->getMessage();
			$totalPages = 1;
			$page = 1;
			$limit = 10;
		}
		$userDAO = $this->userDAO;
		$equipoDAO = $this->equipoDAO;
		include __DIR__ . '/../vista/osm/lista-entrenador.php';
	}

	private function editProfile()
	{
		session_start();
		$messages = '';
		// Comprobar si el usuario está logueado
		if (!isset($_SESSION['user']['user_id'])) {
			// Redirigir al login si no está logueado
			header('Location: /practicas/app/vista/login.php');
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
						   echo '<script>alert("Perfil actualizado correctamente."); window.location.href = "/practicas/index.php";</script>';
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
}