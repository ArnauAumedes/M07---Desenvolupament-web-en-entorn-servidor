<?php
// model/dao/PasswordResetDAO.php
require_once __DIR__ . '/../database/database.php';

class PasswordResetDAO  {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getByKeyAndEmail($key, $email) {
        $stmt = $this->db->prepare('SELECT * FROM password_reset_temp WHERE `key` = :key AND `email` = :email LIMIT 1');
        $stmt->execute([':key' => $key, ':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteByEmail($email) {
        $stmt = $this->db->prepare('DELETE FROM password_reset_temp WHERE `email` = :email');
        $stmt->execute([':email' => $email]);
    }

    public function insert($email, $key, $expDate) {
        $stmt = $this->db->prepare('INSERT INTO password_reset_temp (`email`, `key`, `expDate`) VALUES (:email, :key, :expDate)');
        $stmt->execute([':email' => $email, ':key' => $key, ':expDate' => $expDate]);
    }
}
