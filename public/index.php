<?php
/**
 * Front controller
 *
 * Ruteja les peticions per paràmetre ?action=
 * Autor: Arnau Aumedes Jimenez
 */
// Front controller: route by ?action=
$action = $_GET['action'] ?? 'menu';

if ($action === 'login') {
	// Delegate login handling to the dedicated controller
	require_once __DIR__ . '/../app/controlador/loginController.php';
	exit;
}

if ($action === 'logout') {
	// Simple logout endpoint handled by public script or controller
	// If you want to centralize, you can create app/controlador/logoutController.php
	require_once __DIR__ . '/../app/controlador/logoutController.php';
	exit;
}

if ($action === 'register') {
	require_once __DIR__ . '/../app/controlador/registerController.php';
	exit;	
}

require_once __DIR__ . '/../app/controlador/EquipoController.php';

$controller = new EquipoController();
$controller->handleRequest();
?>