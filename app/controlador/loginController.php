<?php
/**
 * loginController.php
 * Controlador per al login: crea la connexió, crida al model i mostra la vista
 * Autor: Arnau Aumedes Jimenez
 */

require_once __DIR__ . '/../model/database/database.php';
require_once __DIR__ . '/../model/dao/UserDAO.php';

// Iniciar variables
$messages = '';

try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch (Exception $e) {
    error_log('DB init error (controller): ' . $e->getMessage());
    $pdo = null;
}

if ($pdo instanceof PDO) {
    $userDAO = new UserDAO($pdo);

    // Capturem qualsevol output generat pel model per mostrar-lo dins la vista
    ob_start();
    $userDAO->processLogin();
    $messages = ob_get_clean();
} else {
    $messages = '<div class="alert alert-danger">Error de connexió a la base de dades.</div>';
}

// Finalment incloem la vista (la vista mostrarà $messages si cal)
require_once __DIR__ . '/../vista/login.php';


?>