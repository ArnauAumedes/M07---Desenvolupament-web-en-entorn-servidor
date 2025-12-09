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
    ob_start();
    $userDAO->processRegister();
    $messages = ob_get_clean();
} else {
    $messages = '<div class="alert alert-danger">Error de connexió a la base de dades.</div>';
}

require_once __DIR__ . '/../vista/register.php';

?>
