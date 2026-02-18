<?php
// 1. Enviar el código de estado HTTP 404
header("HTTP/1.0 404 Not Found");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página No Encontrada - 404</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <h1>404 - Página no encontrada</h1>
    <p>Lo sentimos, la página que buscas no existe o ha sido movida.</p>
    <p><a href="index.php">Volver a la página de inicio</a></p>
</body>
</html>
