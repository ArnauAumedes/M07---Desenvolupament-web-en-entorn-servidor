<?php
/**
 * logoutController.php
 * Controlador per al logout: destrueix la sessió i redirigeix a l'index públic
 * Autor: Arnau Aumedes Jimenez
 */
require_once __DIR__ . '/../../config/db-connection.php';
require_once __DIR__ . '/../model/dao/UserTokenDAO.php';

try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch (Exception $e) {
    error_log('DB init error (controller): ' . $e->getMessage());
    $pdo = null;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Netejar dades de sessió
$_SESSION = [];

// Eliminar cookie de sessió si s'utilitza cookies
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'], $params['secure'], $params['httponly']
    );
}

// Eliminar token "remember me" de la base de dades i la cookie
if (isset($_COOKIE['rememberme'])) {
    if ($pdo instanceof PDO) {
        $tokenDAO = new UserTokenDAO($pdo);
        $tokenDAO->deleteByPlainToken($_COOKIE['rememberme']);
    }
    setcookie('rememberme', '', [
        'expires' => time() - 3600,
        'path' => '/',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}
session_destroy();

// Redirigir a la pàgina d'inici 
header('Location: index.php');
exit;

?>
