<?php
// public/index.php - MEJOR FORMA
require_once __DIR__ . '/../app/controlador/ArticleController.php';

$controller = new ArticleController();
$controller->handleRequest();
?>