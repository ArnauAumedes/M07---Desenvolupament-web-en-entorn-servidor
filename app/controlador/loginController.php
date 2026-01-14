<?php
/**
 * loginController.php
 * Controlador per al login: crea la connexió, crida al model i mostra la vista
 * Autor: Arnau Aumedes Jimenez
 */

require_once __DIR__ . '/../model/database/database.php';
require_once __DIR__ . '/../model/dao/UserDAO.php';
require_once __DIR__ . '/../model/dao/UserTokenDAO.php';

// Iniciar variables
$messages = '';

try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch (Exception $e) {
    error_log('DB init error (controller): ' . $e->getMessage());
    $pdo = null;
}

// Comprovar connexió a la base de dades
if ($pdo instanceof PDO) {
    $userDAO = new UserDAO($pdo);
    $usuarioTokenDAO = new UserTokenDAO($pdo);
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_lifetime', 2400);
        ini_set('session.gc_maxlifetime', 2400);
        session_set_cookie_params(2400);
        session_start();
    }
    // Comprovar si s'ha enviat el formulari de login
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSubmit'])) {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $messages = '<div class="alert alert-danger">ELS CAMPS NO PODEN ESTAR BUITS</div>';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $messages = '<div class="alert alert-danger">Email invàlid</div>';
        } else {
            // Comprovar credencials
            $user = $userDAO->getByEmail($email);
            if (!$user) {
                $messages = '<div class="alert alert-danger">Credencials incorrectes.</div>';
            } elseif (!isset($user['password']) || !password_verify($password, $user['password'])) {
                $messages = '<div class="alert alert-danger">Credencials incorrectes.</div>';
            } elseif (isset($user['active']) && !$user['active']) {
                $messages = '<div class="alert alert-danger">El compte no està actiu.</div>';
            } else {
                // Login correcte: guardar dades en sessió
                session_regenerate_id(true);
                $_SESSION['user'] = [
                    'user_id' => $user['user_id'],
                    'username' => $user['username'],
                    'email' => $user['email'],
                    'dni' => $user['dni'] ?? null
                ];
                // Gestionar el "remember me"
                if (isset($_POST['rememberMe'])) {
                    $token = bin2hex(random_bytes(32));
                    $userId = $user['user_id'];
                    $expires = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30);
                    $userId = $user['user_id'];
                    $stmt = $usuarioTokenDAO->create(new UserToken($userId, $token, $expires));
                    setcookie('rememberme', $token, time() + (86400 * 30), "/", "", true, true);
                }
                // Redirigir a la pàgina de menú
                $_SESSION['flash_welcome'] = $user['username'] ?? ($user['email'] ?? 'Usuari');
                header('Location: /practicas/public/index.php?action=menu');
                exit;
            }
        }
    }
} else {
    $messages = '<div class="alert alert-danger">Error de connexió a la base de dades.</div>';
}

require_once __DIR__ . '/../vista/login.php';


?>