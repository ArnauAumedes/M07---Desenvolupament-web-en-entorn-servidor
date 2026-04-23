<?php

/**
 * Classe Database per gestionar la connexió a la base de dades
 * 
 * Implementa el patró Singleton per garantir una única connexió PDO a la BD.
 * Centralitza la configuració de connexió i les opcions de PDO.
 * Utilitza prepared statements i mode d'errors per excepcions per a major seguretat.
 * 
 * @author Arnau Aumedes
 * @version 1.0
 */
require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/../.env');
class Database
{
    /**
     * @var Database|null Instancia unica de Database
     */
    private static $instance = null;

    /**
     * @var string Host del servidor de base de dades
     */
    private $host;

    /**
     * @var string Nom de la base de dades
     */
    private $dbname;

    /**
     * @var string Nom d'usuari per connectar a la BD
     */
    private $username;

    /**
     * @var string Contrasenya per connectar a la BD
     */
    private $password;

    /**
     * @var string Joc de caràcters de la connexió
     */
    private $charset;

    /**
     * @var PDO|null Objecte de connexió PDO
     */
    private $pdo;

    /**
     * Constructor de la classe Database
     * 
     * Inicialitza automàticament la connexió a la base de dades
     * en crear una instància de la classe.
     */
    private function __construct()
    {
        $this->host = getenv('DB_HOST');
        $this->dbname = getenv('DB_DATABASE');
        $this->username = getenv('DB_USERNAME');
        $this->password = getenv('DB_PASSWORD');
        $this->charset = getenv('DB_CHARSET');
        $this->connect();
    }

    /**
     * Retorna la instancia unica de la clase Database.
     *
     * @return Database
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Evita clonado de la instancia Singleton.
     */
    private function __clone()
    {
    }

    /**
     * Evita deserializacion de la instancia Singleton.
     */
    public function __wakeup()
    {
        throw new Exception('Cannot unserialize singleton Database');
    }

    /**
     * Estableix la connexió a la base de dades utilitzant PDO
     * 
     * Configura les opcions de PDO per:
     * - Mode d'errors amb excepcions (PDO::ERRMODE_EXCEPTION)
     * - Retorn de resultats com array associatiu (PDO::FETCH_ASSOC)
     * - Desactivació d'emulació de prepared statements per a major seguretat
     * 
     * @return void
     * @throws PDOException Si hi ha errors en la connexió
     */
    private function connect()
    {
        // DSN (Data Source Name)
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";

        // Opcions de PDO
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Mode d'errors amb excepcions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mode de recuperació per defecte (array associatiu)
            PDO::ATTR_EMULATE_PREPARES => false,                  // Desactivar emulació de prepares (més segur)
        ];

        try {
            // Crear la connexió PDO
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Gestio d'errors de connexio sense exposar detalls sensibles.
            error_log('Error de connexio a BD: ' . $e->getMessage());
            throw new Exception('Error de connexio a la base de dades');
        }
    }

    /**
     * Obté l'objecte de connexió PDO
     * 
     * Retorna la instància de PDO per poder executar consultes
     * des de les classes DAO.
     * 
     * @return PDO Objecte de connexió PDO a la base de dades
     */
    public function getConnection()
    {
        return $this->pdo;
    }
}
?>