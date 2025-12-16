<?php
/**
 * UserDAO
 *
 * Gestiona l'accés a la taula `users` per registre i autenticació.
 * Autor: Arnau Aumedes Jimenez
 */
require_once __DIR__ . '/../entities/User.php';

class UserDAO extends User
{
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    // Comprueba si existe un usuario por email
    public function existsByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT user_id FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() !== false;
    }

    // Comprueba si existe un usuario por username
    public function existsByUsername($username)
    {
        $stmt = $this->db->prepare('SELECT user_id FROM users WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        return $stmt->fetch() !== false;
    }

    // Inserta un nuevo usuario
    public function createUser($username, $email, $hashedPassword)
    {
        $stmt = $this->db->prepare('INSERT INTO users (username, email, password, active) VALUES (:username, :email, :password, 1)');
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword
        ]);
        return $this->db->lastInsertId();
    }

    // Obtiene usuario por email
    public function getByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtiene usuario por username
    public function getByUsername($username)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Actualiza la contraseña por email
    public function updatePasswordByEmail($email, $hashedPassword, $trnDate)
    {
        $stmt = $this->db->prepare('UPDATE users SET password = :password, trn_date = :trn_date WHERE email = :email');
        $stmt->execute([
            ':password' => $hashedPassword,
            ':trn_date' => $trnDate,
            ':email' => $email
        ]);
    }

    // Obtiene usuario por id
    public function getById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE user_id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>