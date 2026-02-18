<?php
// 1. Enviar el código de estado HTTP 401
header("HTTP/1.0 401 Unauthorized");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Página No Autorizada - 401</title>
    <link rel="stylesheet" href="public/css/styles.css">
</head>
<body>
    <h1>401 - Acceso no autorizado</h1>
    <p>Lo sentimos, no tienes permiso para acceder a esta página.</p>
    <p><a href="index.php">Volver a la página de inicio</a></p>
</body>
</html>
