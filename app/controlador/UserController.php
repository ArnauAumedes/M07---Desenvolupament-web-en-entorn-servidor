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
}
