<?php
require_once __DIR__ . '/../entities/Article.php';

/**
 * Data Access Object (DAO) per gestionar les operacions de base de dades dels articles
 * 
 * Aquesta classe implementa el patró DAO (Data Access Object) per encapsular
 * tota la lògica d'accés a dades relacionada amb la taula 'articles'.
 * Utilitza PDO amb prepared statements per prevenir SQL Injection.
 * 
 * @author Arnau Aumedes
 * @version 1.0
 */
class ArticleDAO extends Article 
{
    /**
     * @var PDO Connexió a la base de dades PDO
     */
    private $db;

    /**
     * Constructor del ArticleDAO
     * 
     * @param PDO $db Objecte PDO amb la connexió a la base de dades
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Crea un nou article a la base de dades
     * 
     * Obté les dades del formulari via $_POST, crea un objecte Article
     * i l'insereix a la taula 'articles' utilitzant prepared statements.
     * 
     * @return int|false ID del nou article creat (lastInsertId) o false en cas d'error
     * @throws PDOException Si hi ha errors en la inserció a la base de dades
     */
    public function create()
    {
        // Obtenir dades del formulari ($_POST)
        $titol = $_POST['titol'] ?? '';
        $cos = $_POST['cos'] ?? '';
        $dni = $_POST['dni'] ?? null;

        // Crear objecte Article
        $article = new Article($dni, $titol, $cos);
        
        // Utilitzar getters per obtenir dades de l'objecte
        $sql = "INSERT INTO articles (titol, cos, dni) VALUES (:titol, :cos, :dni)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':titol', $article->getTitol(), PDO::PARAM_STR);
        $stmt->bindValue(':cos', $article->getCos(), PDO::PARAM_STR);
        $stmt->bindValue(':dni', $article->getDni(), PDO::PARAM_STR);
        $stmt->execute();
        
        return $this->db->lastInsertId();
    }

    /**
     * Cerca un article per ID
     * 
     * Obté l'ID del paràmetre $_GET i retorna un objecte Article
     * amb totes les seves dades si existeix.
     * 
     * @return Article|null Objecte Article si es troba, null si no existeix
     * @throws PDOException Si hi ha errors en la consulta a la base de dades
     */
    public function findById()
    {
        // Obtenir ID del paràmetre URL ($_GET)
        $id = $_GET['id'] ?? '';
        
        $stmt = $this->db->prepare("SELECT * FROM articles WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            // Crear objecte Article
            $article = new Article($row['dni'], $row['titol'], $row['cos']);
            $article->setId($row['id']);
            return $article;
        }
        return null;
    }

    /**
     * Actualitza un article existent a la base de dades
     * 
     * Obté l'ID via $_GET i les noves dades via $_POST.
     * Actualitza tots els camps (titol, cos, dni) de l'article identificat per ID.
     * 
     * @return int Número de files afectades (1 si s'actualitza, 0 si no es troba)
     * @throws PDOException Si hi ha errors en l'actualització a la base de dades
     */
    public function update()
    {
        // Obtenir ID de la URL ($_GET) i dades del formulari ($_POST)
        $id = $_GET['id'] ?? '';
        $dni = $_POST['dni'] ?? '';
        $titol = $_POST['titol'] ?? '';
        $cos = $_POST['cos'] ?? '';

        // Crear objecte Article
        $article = new Article($dni, $titol, $cos);
        
        // Utilitzar getters per accedir a les dades de l'objecte
        $sql = "UPDATE articles SET titol = :titol, cos = :cos, dni = :dni WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':titol' => $article->getTitol(),
            ':cos' => $article->getCos(),
            ':dni' => $article->getDni(),
            ':id' => $id
        ]);
        
        return $stmt->rowCount();
    }

    /**
     * Elimina un article de la base de dades
     * 
     * Obté l'ID via $_GET i elimina l'article corresponent de la taula.
     * 
     * @return int Número de files eliminades (1 si s'elimina, 0 si no es troba)
     * @throws PDOException Si hi ha errors en l'eliminació de la base de dades
     */
    public function delete()
    {
        // Obtenir ID del paràmetre URL ($_GET)
        $id = $_GET['id'] ?? '';
        
        $stmt = $this->db->prepare("DELETE FROM articles WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }

    /**
     * Obté tots els articles de la base de dades
     * 
     * Retorna un array amb tots els articles ordenats per ID ascendent.
     * Cada element de l'array és un objecte Article.
     * 
     * @return Article[] Array d'objectes Article (pot estar buit si no hi ha articles)
     * @throws PDOException Si hi ha errors en la consulta a la base de dades
     */
    public function findAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM articles ORDER BY id ASC");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $articles = [];
        foreach ($rows as $row) {
            $article = new Article($row['dni'], $row['titol'], $row['cos']);
            // Agregar el ID manualmente ya que no está en el constructor
            $article->id = $row['id'];
            $articles[] = $article;
        }
        
        return $articles;
    }

    /**
     * Funcio per retornar el nombre total d'articules
     * 
     * @return int Nombre total d'articules
     */
    public function countAll() 
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM articles");
        return $stmt->fetchColumn();
    }

    public function findPage($limit, $offset)
    {
        $stmt = $this->db->prepare("SELECT * FROM articles ORDER BY id ASC LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $articles = [];
        foreach ($rows as $row) {
            $article = new Article($row['dni'], $row['titol'], $row['cos']);
            $article->setId($row['id']);
            $articles[] = $article;
        }
        return $articles;
    }
}   
?>