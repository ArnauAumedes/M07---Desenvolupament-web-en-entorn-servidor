<?php
/**
 * ResetPasswordController.php
 * Controlador para el reseteo de contraseña mediante enlace enviado por email
 * Autor: Arnau Aumedes Jimenez
 */
require_once __DIR__ . '/../model/dao/UserDAO.php';
require_once __DIR__ . '/../model/dao/PasswordResetDAO.php';

class ResetPasswordController
{
    public $error = '';
    public $success = '';
    public $showForm = false;
    public $email = '';
    public $key = '';

    private $db;
    private $userDao;private $passwordResetDao;    

    public function __construct($db = null)
    {
        if ($db === null) {
            require_once __DIR__ . '/../../config/db-connection.php';
            $dbInstance = new Database();
            $this->db = $dbInstance->getConnection();
        } else {
            $this->db = $db;
        }
        $this->userDao = new UserDAO($this->db);
        $this->passwordResetDao = new PasswordResetDAO($this->db);
    }

    public function handleRequest()
    {
        if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) && ($_GET["action"] == "reset-password") && !isset($_POST["action"])) {
            $this->key = $_GET["key"];
            $this->email = $_GET["email"];
            $curDate = date("Y-m-d H:i:s");
            $row = $this->passwordResetDao->getByKeyAndEmail($this->key, $this->email);
            if (!$row) {
                $this->error = 'Enlace inválido.';
            } else {
                $expDate = $row['expDate'];
                if ($expDate >= $curDate) {
                    $this->showForm = true;
                } else {
                    $this->error = 'El enlace ha expirado.';
                }
            }
        }
        if (isset($_POST["email"]) && isset($_POST["action"]) && ($_POST["action"] == "update")) {
            $this->email = $_POST["email"];
            $pass1 = $_POST["pass1"];
            $pass2 = $_POST["pass2"];
            $curDate = date("Y-m-d H:i:s");
            // Validar fuerza de la contraseña
            if ($pass1 !== $pass2) {
                $this->error = 'Las contraseñas no coinciden.';
                $this->showForm = true;
            } elseif (strlen($pass1) < 8 || !preg_match('/[A-Z]/i', $pass1) || !preg_match('/[0-9]/', $pass1)) {
                $this->error = 'La contrasenya ha de tenir almenys 8 caràcters i incloure lletres i números';
                $this->showForm = true;
            } else {
                $hashed = password_hash($pass1, PASSWORD_DEFAULT);
                $this->userDao->updatePasswordByEmail($this->email, $hashed, $curDate);
                $this->passwordResetDao->deleteByEmail($this->email);
                $this->success = '¡Contraseña actualizada correctamente!';
            }
        }
        $error = $this->error;
        $success = $this->success;
        $showForm = $this->showForm;
        $email = $this->email;
        require_once __DIR__ . '/../vista/reset-password.php';
    }
}
