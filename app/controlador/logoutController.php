<?php
/**
 * logoutController.php
 * Controlador per al logout: destrueix la sessió i redirigeix a l'index públic
 * Autor: Arnau Aumedes Jimenez
 */

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

// Destruir la sessió
session_destroy();

// Redirigir a la pàgina pública principal
header('Location: /practicas/M07---Desenvolupament-web-en-entorn-servidor/public/index.php');
exit;

?>
