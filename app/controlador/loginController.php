<?php
/**
 * loginController.php
 * Controlador per al login: crea la connexió, crida al model i mostra la vista
 * Autor: Arnau Aumedes Jimenez
 */

require_once __DIR__ . '/../../config/db-connection.php';
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
    // Inicialitzar intents de login
    if (!isset($_SESSION['login_attempts']))
        $_SESSION['login_attempts'] = 0;
    
    // Comprovar si s'ha enviat el formulari de login
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnSubmit'])) {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $login_fallido = false;

        if ($email === '' || $password === '') {
            $messages = '<div class="alert alert-danger">ELS CAMPS NO PODEN ESTAR BUITS</div>';
            $login_fallido = true;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $messages = '<div class="alert alert-danger">Email invàlid</div>';
            $login_fallido = true;
        } else {
            // Validar reCAPTCHA si hay 3 o más intentos fallidos
            if ($_SESSION['login_attempts'] >= 3) {
                if (empty($_POST['g-recaptcha-response'])) {
                    $messages = '<div class="alert alert-danger">Por favor, confirma el reCAPTCHA.</div>';
                    $login_fallido = true;
                } else {
                    $recaptcha = $_POST['g-recaptcha-response'];
                    $secret = getenv('RECAPTCHA_SECRET_KEY');
                    $response = file_get_contents(
                        "https://www.google.com/recaptcha/api/siteverify?secret=$secret&response=$recaptcha"
                    );
                    $result = json_decode($response, true);
                    if (!$result['success']) {
                        $messages = '<div class="alert alert-danger">Por favor, confirma el reCAPTCHA.</div>';
                        $login_fallido = true;
                    }
                }
            }

            // Comprobar credenciales solo si no ha fallado antes
            if (!$login_fallido) {
                $user = $userDAO->getByEmail($email);
                if (!$user) {
                    $messages = '<div class="alert alert-danger">Credencials incorrectes.</div>';
                    $login_fallido = true;
                } elseif (!isset($user['password']) || !password_verify($password, $user['password'])) {
                    $messages = '<div class="alert alert-danger">Credencials incorrectes.</div>';
                    $login_fallido = true;
                } elseif (isset($user['active']) && !$user['active']) {
                    $messages = '<div class="alert alert-danger">El compte no està actiu.</div>';
                    $login_fallido = true;
                }
            }

            // Actualizar contador de intentos
            if ($login_fallido) {
                $_SESSION['login_attempts']++;
            } else {
                $_SESSION['login_attempts'] = 0; // reset on success
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
                    $stmt = $usuarioTokenDAO->create(new UserToken($userId, $token, $expires));
                    setcookie('rememberme', $token, time() + (86400 * 30), "/", "", true, true);
                }
                // Redirigir a la pàgina de menú
                $_SESSION['flash_welcome'] = $user['username'] ?? ($user['email'] ?? 'Usuari');
                header('Location: /practicas/index.php?action=menu');
                exit;
            }
        }
    }
} else {
    $messages = '<div class="alert alert-danger">Error de connexió a la base de dades.</div>';
}

require_once __DIR__ . '/../vista/login.php';


?>