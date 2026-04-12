<?php
/**
 * PasswordResetDAO.php
 * Data Access Object para la gestión de reseteo de contraseñas
 * Autor: Arnau Aumedes Jimenez
 */
require_once __DIR__ . '/../../../config/db-connection.php';

class PasswordResetDAO  {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getByKeyAndEmail($key, $email) {
        $stmt = $this->db->prepare('SELECT * FROM password_reset_temp WHERE `email` = :email ORDER BY `expDate` DESC LIMIT 1');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return false;
        }

        return password_verify($key, $row['key']) ? $row : false;
    }

    public function deleteByEmail($email) {
        $stmt = $this->db->prepare('DELETE FROM password_reset_temp WHERE `email` = :email');
        $stmt->execute([':email' => $email]);
    }

    public function insert($email, $key, $expDate) {
        $this->deleteByEmail($email);
        $hashedKey = password_hash($key, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare('INSERT INTO password_reset_temp (`email`, `key`, `expDate`) VALUES (:email, :key, :expDate)');
        $stmt->execute([':email' => $email, ':key' => $hashedKey, ':expDate' => $expDate]);
    }
}
