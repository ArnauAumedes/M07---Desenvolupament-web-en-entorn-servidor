<?php
/**
 * registerController.php
 * Controlador per al registre: crea la connexió, crida al model i mostra la vista
 * Autor: Arnau Aumedes Jimenez
 */
require_once __DIR__ . '/../model/database/database.php';
require_once __DIR__ . '/../model/dao/UserDAO.php';

$messages = '';
try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch (Exception $e) {
    error_log('DB init error (register controller): ' . $e->getMessage());
    $pdo = null;
}


if ($pdo instanceof PDO) {
    $userDAO = new UserDAO($pdo);
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_lifetime', 2400);
        ini_set('session.gc_maxlifetime', 2400);
        session_set_cookie_params(2400);
        session_start();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnRegister'])) {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password2 = $_POST['password2'] ?? '';

        // Validaciones
        if ($username === '' || $email === '' || $password === '' || $password2 === '') {
            $messages = '<div class="alert alert-danger">Tots els camps són obligatoris</div>';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $messages = '<div class="alert alert-danger">Email invàlid</div>';
        } elseif ($password !== $password2) {
            $messages = '<div class="alert alert-danger">Les contrasenyes no coincideixen</div>';
        } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/i', $password) || !preg_match('/[0-9]/', $password)) {
            $messages = '<div class="alert alert-danger">La contrasenya ha de tenir almenys 8 caràcters i incloure lletres i números</div>';
        } elseif ($userDAO->existsByEmail($email)) {
            $messages = '<div class="alert alert-danger">Ja existeix un usuari amb aquest email</div>';
        } elseif ($userDAO->existsByUsername($username)) {
            $messages = '<div class="alert alert-danger">Ja existeix un usuari amb aquest nom d\'usuari</div>';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            try {
                $userId = $userDAO->createUser($username, $email, $hash);
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'user_id' => $userId,
                    'username' => $username,
                    'email' => $email
                ];
                $_SESSION['flash_welcome'] = $username;
                header('Location: /practicas/public/index.php?action=menu');
                exit;
            } catch (Exception $e) {
                $messages = '<div class="alert alert-danger">Error del servidor. Torna-ho a intentar més tard.</div>';
                $messages .= '<div class="alert alert-warning"><small>Debug: ' . htmlspecialchars($e->getMessage()) . '</small></div>';
                error_log('Register error: ' . $e->getMessage());
            }
        }
    }
} else {
    $messages = '<div class="alert alert-danger">Error de connexió a la base de dades.</div>';
}

require_once __DIR__ . '/../vista/register.php';

?>
