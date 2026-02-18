<?php
/**
 * Front controller
 *
 * Ruteja les peticions per paràmetre ?action=
 * Autor: Arnau Aumedes Jimenez
 */
// Front controller: route by ?action=

// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'login':
        require_once __DIR__ . '/./app/controlador/loginController.php';
        exit;
    case 'logout':
        require_once __DIR__ . '/./app/controlador/logoutController.php';
        exit;
    case 'register':
        require_once __DIR__ . '/./app/controlador/registerController.php';
        exit;
	case 'send-password':
		require_once __DIR__ . '/./app/controlador/ForgotPasswordController.php';
		exit;
	case 'change-password':
		require_once __DIR__ . '/./app/controlador/ChangePasswordController.php';
		exit;
}

// Mostrar mensaje de éxito al cambiar la contraseña
if (isset($_GET['success']) && $_GET['success'] === 'password') {
    echo '<div class="alert alert-success">Contraseña cambiada correctamente.</div>';
}

// Accions de jugadors
$accionsJugador = ['createJugador', 'updateJugador', 'deleteJugador', 'viewJugador', 'listJugador', 'mejores-valorados', 'pichichis', 'asistencias'];
if (in_array($action, $accionsJugador)) {
	require_once __DIR__ . '/./app/controlador/JugadorController.php';
	$jugadorController = new JugadorController();
	$jugadorController->handleRequest();
	exit;
}

//Accions d'usuari
$accionsUsuari = ['lista-entrenador', 'edit-profile', 'createUser', 'updateUser', 'deleteUser', 'viewUser'];
if (in_array($action, $accionsUsuari)) {
	require_once __DIR__ . '/./app/controlador/UserController.php';
	$userController = new UserController();
	$userController->handleRequest();
	exit;
}

// Per defecte es crida el controlador d'equips
require_once __DIR__ . '/./app/controlador/EquipoController.php';
$controller = new EquipoController();
$controller->handleRequest();
?>