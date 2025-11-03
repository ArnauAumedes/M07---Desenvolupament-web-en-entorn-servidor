<?php
require_once __DIR__ . '/../entities/User.php';

class UserDAO extends User
{
    private $db;
    /**
     * Constructor per connectar a la DB per fer consultes
     * @param PDO $db Connexió a la base de dades
     * @return void Retorna l'objecte PDO
     */
    public function _construct(PDO $db) {
        $this->db = $db; 
    } 
    /**
     * Funcio per crear un nou usuari a la base de dades
     * @return int|false ID del nou usuari creat (lastInsertId) o false en cas d'error
     */
    public function create() {
        $userName = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password_hash'];
        $active = $_POST['active'];

        // Crear objecte User 
        $user = new User($userName, $email, $password, $active);

        // Utilitzar getters per obtenir dades de l'objecte
        $sql = "INSERT INTO users (username, email, password_hash, active) VALUES (:username, :email, :password_hash, :active)";
        $stmt = $this->db->prepare($sql); 
        $stmt->bindValue(':username', $user->getUsername(), PDO::PARAM_STR);
        $stmt->bindValue(':email', $user->getEmail(), PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $user->getPasswordHash(), PDO::PARAM_STR);
        $stmt->bindValue(':active', $user->isActive(), PDO::PARAM_INT);

        return $this->db->lastInsertId();
    }
    /**
     * Actualitza un usuari existent a la base de dades
     * 
     * Obté l'ID via $_GET i les noves dades via $_POST.
     * Actualitza tots els camps (Nom i contrasenya) de l'usuari identificat per email.
     * 
     * @return int Número de files afectades (1 si s'actualitza, 0 si no es troba)
     */
    public function update() {
        // Obtenir dades del formulari ($_POST)
        $email = $_POST['email'];
        $userName = $_POST['username'];
        $password_hash = $_POST['password_hash'];

        // Crear objecte User
        $user = new User($userName, $email, $password_hash);

        // Utilitzar getters per obtenir dades de l'objecte
        $sql = "UPDATE users SET username = :username, password_hash = :password_hash WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':username' => $user->getUsername(),
            ':password_hash' => $user->getPasswordHash()
        ]);

        return $stmt->rowCount();
    }
    /**
     * Elimina un usuari existent a la base de dades
     * @return int Número de files afectades (1 si s'elimina, 0 si no es troba)
     */
    public function delete() {
        $user_id = $_GET['user_id']; 

        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->execute([':id' => $user_id]); 
        
        return $stmt->rowCount(); 
    }
    /**
     * Busca tots els usuaris a la base de dades
     * @return User[] Llista d'objectes User amb tots els usuaris de la base de dades
     */
    public function findAll() {
        $stmt = $this->db->prepare("SELECT * FROM users ORDER BY id ASC"); 
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC); 

        $users = []; 
        foreach ($rows as $row) {
        $user = new User($row['username'], $row['email'], $row['password_hash'], $row['active']);
        $user->user_id = $row['user_id'];
        $users[] = $user;
        }
        return $users;
    }
    /**
     * Contar tots els usuaris a la base de dades
     * @return int Número total d'usuaris a la base de dades 
     */
    public function countAll() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    }
    /**
     * Buscar un usuari per email
     * @return User|null Retorna l'objecte User si es troba l'usuari, o null si no existeix
     */
    public function findByEmail() {
        $email = $_GET['email']; 

        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $user = new User($row['username'], $row['email'], $row['password_hash'], $row['active']); 
            $user->setEmail($row['email']);
            return $user; 
        }
        return null;
    }
}
?>