<?php
// filepath: c:\xampp\htdocs\practicas\Pràctica 03 - Paginació\app\controller\ArticleController.php
require_once __DIR__ . '/../model/entities/Article.php';
require_once __DIR__ . '/../model/dao/ArticleDAO.php';
require_once __DIR__ . '/../model/database/database.php';

/**
 * Controlador principal per gestionar les operacions CRUD d'articles
 * 
 * Aquesta classe segueix el patró MVC (Model-Vista-Controlador) i actua com a
 * Front Controller, gestionant totes les peticions HTTP relacionades amb articles.
 * 
 * @author Arnau Aumedes
 * @version 1.0
 */
class ArticleController
{
    /**
     * @var ArticleDAO Objecte d'accés a dades per a articles
     */
    private $articleDAO;
    
    /**
     * @var PDO Connexió a la base de dades
     */
    private $db;

    /**
     * Constructor de la classe ArticleController
     * 
     * Inicialitza la connexió a la base de dades i crea una instància
     * del Data Access Object (DAO) per gestionar les operacions amb articles.
     */
    public function __construct()
    {
        // Utilitzar la classe Database centralitzada
        $database = new Database();
        $this->db = $database->getConnection();
        $this->articleDAO = new ArticleDAO($this->db);
    }

    /**
     * Maneja totes les peticions HTTP i les redirigeix a l'acció corresponent
     * 
     * Aquest mètode actua com a enrutador (router), llegint el paràmetre 'action'
     * de la URL i cridant al mètode privat corresponent.
     * 
     * @return void
     */
    public function handleRequest()
    {
        $action = $_GET['action'] ?? 'menu';

        switch ($action) {
            case 'create':
                $this->createArticle();
                break;
            case 'update':
                $this->updateArticle();
                break;
            case 'delete':
                $this->deleteArticle();
                break;
            case 'menu':
            default:
                $this->showMenu();
                break;
        }
    }

    /**
     * Gestiona la creació d'un nou article
     * 
     * - Si és una petició GET: Mostra el formulari de creació
     * - Si és una petició POST: Processa les dades i crea l'article a la BD
     * 
     * En cas d'èxit, redirigeix al menú amb missatge de confirmació.
     * En cas d'error, redirigeix al menú amb missatge d'error.
     * 
     * @return void
     * @throws Exception Si hi ha errors en la creació de l'article
     */
    private function createArticle()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // El DAO s'encarrega d'obtenir les dades de $_POST
                $result = $this->articleDAO->create();

                if ($result) {
                    // Redirigir al menú con mensaje de éxito
                    header("Location: /practicas/Pràctica 03 - Paginació/public/index.php?created=success&id=" . $result);
                    exit();
                } else {
                    // Redirigir al menú con mensaje de error
                    header("Location: /practicas/Pràctica 03 - Paginació/public/index.php?created=error");
                    exit();
                }
            } catch (Exception $e) {
                // Redirigir al menú con mensaje de error
                header("Location: /practicas/Pràctica 03 - Paginació/public/index.php?created=error&msg=" . urlencode($e->getMessage()));
                exit();
            }
        } else {
            // Mostrar formulari de creació
            include __DIR__ . '/../vista/create.php';
        }
    }

    /**
     * Gestiona l'actualització d'un article existent
     * 
     * Flux d'operació:
     * 1. Si és POST: Actualitza l'article amb les dades del formulari
     * 2. Si es proporciona un ID via GET: Busca l'article per mostrar-lo
     * 3. Carrega la vista update.php amb l'article trobat (si existeix)
     * 
     * @return void
     * @throws Exception Si hi ha errors en l'actualització o cerca de l'article
     */
    private function updateArticle()
    {
        $article = null;
        $message = "";

        // Si és POST, actualitzar
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // El DAO s'encarrega d'obtenir les dades de $_POST i $_GET
                $rowsAffected = $this->articleDAO->update();

                if ($rowsAffected > 0) {
                    // Redirigir al menú con mensaje de éxito
                    header("Location: /practicas/Pràctica 03 - Paginació/public/index.php?updated=success");
                    exit();
                } else {    
                    $message = "No s'ha pogut actualitzar l'article";
                }
            } catch (Exception $e) {
                $message = "Error: " . $e->getMessage();
            }
        }

        // Si hi ha ID, buscar l'article per mostrar-lo
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            try {
                $article = $this->articleDAO->findById();
                if (!$article) {
                    $message = "No s'ha trobat cap article amb aquest ID";
                }
            } catch (Exception $e) {
                $message = "Error cercant l'article: " . $e->getMessage();
            }
        }

        // Carregar vista
        include __DIR__ . '/../vista/update.php';
    }

    /**
     * Gestiona l'eliminació d'un article
     * 
     * Elimina l'article de la base de dades utilitzant l'ID proporcionat
     * via paràmetre GET. Redirigeix al menú amb missatge de confirmació o error.
     * 
     * @return void
     * @throws Exception Si hi ha errors en l'eliminació de l'article
     */
    private function deleteArticle()
    {
        if (isset($_GET['id']) && !empty($_GET['id'])) {
            try {
                $id = $_GET['id'];
                // El DAO s'encarrega d'obtenir l'ID de $_GET
                $rowsAffected = $this->articleDAO->delete();

                if ($rowsAffected > 0) {
                    // Redirigir al menú con mensaje de éxito
                    header("Location: /practicas/Pràctica 03 - Paginació/public/index.php?deleted=success&id=" . $id);
                    exit();
                } else {
                    // Redirigir al menú con mensaje de error
                    header("Location: /practicas/Pràctica 03 - Paginació/public/index.php?deleted=error");
                    exit();
                }
            } catch (Exception $e) {
                // Redirigir al menú con mensaje de error
                header("Location: /practicas/Pràctica 03 - Paginació/public/index.php?deleted=error&msg=" . urlencode($e->getMessage()));
                exit();
            }
        } else {
            // Redirigir al menú si no hay ID
            header("Location: /practicas/Pràctica 03 - Paginació/public/index.php?deleted=noid");
            exit();
        }
    }

    /**
     * Mostra el menú principal amb la llista de tots els articles
     * 
     * Obté tots els articles de la base de dades i carrega la vista menu.php.
     * En cas d'error, passa un array buit i un missatge d'error a la vista.
     * 
     * @return void
     */
    private function showMenu()
    {
        // Obtenir els parametres de paginació
        $allowed = [1,5,10,20];
        // Default a 5 si no és vàlid
        $perPage = isset($_GET['per_page']) ? (int) $_GET['per_page'] : 5;
        if (!in_array($perPage, $allowed)) {
            $perPage = 5;
            if (isset($_GET['per_page'])) {
                header("Location: /practicas/Pràctica 03 - Paginació/public/index.php?per_page=5");
            }
        }

        // Pàgina actual
        $currentPage = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
  
        
        try {
            $totalItems = $this->articleDAO->countAll(); 
            $totalPages = max(1, (int)ceil($totalItems / $perPage));
            // Ajustar la pàgina actual si excedeix el total de pàgines
            if ($currentPage > $totalPages || $currentPage < 1) {
                if ($currentPage !== 1) {
                    header("Location: /practicas/Pràctica 03 - Paginació/public/index.php?per_page=" . $perPage . "&page=1");
                }
                $currentPage = 1; 
            }
            // Calcular offset
            $offset = ($currentPage - 1) * $perPage;
            $articles = $this->articleDAO->findPage($perPage, $offset);
        } catch (Exception $e) {
            $articles = [];
            $totalItems = 0;
            $totalPages = 1;
            $message = "Error obtenint articles: " . $e->getMessage();        
        }

        include __DIR__ . '/../vista/menu.php';  // Cambiar a la vista del menú
    }
}
?>