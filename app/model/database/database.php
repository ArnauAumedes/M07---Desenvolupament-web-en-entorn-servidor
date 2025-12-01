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
class Database {
    /**
     * @var string Host del servidor de base de dades
     */
    private $host = '127.0.0.1';
    
    /**
     * @var string Nom de la base de dades
     */
    private $dbname = 'pt04_arnau_aumedes';
    
    /**
     * @var string Nom d'usuari per connectar a la BD
     */
    private $username = 'root';
    
    /**
     * @var string Contrasenya per connectar a la BD
     */
    private $password = '';
    
    /**
     * @var string Joc de caràcters de la connexió
     */
    private $charset = 'utf8mb4';
    
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
    public function __construct() {
        $this->connect();
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
    private function connect() {
        // DSN (Data Source Name)
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";

        // Opcions de PDO
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Mode d'errors amb excepcions
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Mode de recuperació per defecte (array associatiu)
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Desactivar emulació de prepares (més segur)
        ];

        try {
            // Crear la connexió PDO
            $this->pdo = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Gestió d'errors de connexió
            die('Error de connexió: ' . $e->getMessage());
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
    public function getConnection() {
        return $this->pdo;
    }
}
?>