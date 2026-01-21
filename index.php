<?php
/**
 * Front controller
 *
 * Ruteja les peticions per paràmetre ?action=
 * Autor: Arnau Aumedes Jimenez
 */
// Front controller: route by ?action=

$action = $_GET['action'] ?? 'list';


if ($action === 'login') {
	require_once __DIR__ . '/./app/controlador/loginController.php';
	exit;
}

if ($action === 'logout') {
	require_once __DIR__ . '/./app/controlador/logoutController.php';
	exit;
}

if ($action === 'register') {
	require_once __DIR__ . '/./app/controlador/registerController.php';
	exit;
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
$accionsUsuari = ['lista-entrenador', 'edit-profile', 'createUser', 'updateUser', 'deleteUser'];
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