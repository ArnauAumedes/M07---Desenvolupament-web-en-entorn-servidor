<?php
require_once __DIR__ . '/../model/database/database.php';
require_once __DIR__ . '/../model/entities/User.php';
require_once __DIR__ . '/../model/dao/UserDAO.php';
require_once __DIR__ . '/../model/dao/EquipoDAO.php';

class UserController
{
	private $userDAO;
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
			case 'lista-entrenador':
				$this->listEntrenadores();
				break;
			default:
				$this->listEntrenadores();
				break;
		}
	}

	/**
	 * Lista los entrenadores de forma paginada y opcionalmente ordenada.
	 * Incluye la vista correspondiente para mostrar los entrenadores.
	 *
	 * @param callable|null $ordenCallback Función de callback para ordenar los entrenadores (opcional)
	 * @return void
	 */
	private function listEntrenadores($ordenCallback = null)
	{
		$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
		$limit = isset($_GET['limit']) && is_numeric($_GET['limit']) ? (int) $_GET['limit'] : 10;
		$offset = ($page - 1) * $limit;

		try {
			// Suponiendo que hay un método countEntrenadores en UserDAO
			$totalEntrenadores = $this->userDAO->countAll();
			$totalPages = max(1, ceil($totalEntrenadores / $limit));
			// Suponiendo que hay un método getEntrenadoresPaginados en UserDAO
			$entrenadores = $this->userDAO->getUsersPaginados($limit, $offset);

			if ($ordenCallback !== null) {
				// Suponiendo que hay un método ordenarPorValor en UserDAO
				$entrenadores = $this->userDAO->ordenarPorValor($entrenadores, $ordenCallback, 'desc');
			}
		} catch (Exception $e) {
			$entrenadores = [];
			$message = "Error obteniendo entrenadores: " . $e->getMessage();
			$totalPages = 1;
			$page = 1;
			$limit = 10;
		}
		$userDAO = $this->userDAO;
		include __DIR__ . '/../vista/osm/lista-entrenador.php';
	}
}
