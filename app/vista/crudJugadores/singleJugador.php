<?php
// Vista de detalle de un jugador
// Espera la variable $jugador (instancia de Jugador) y $message (string de error)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Jugador</title>
    <link href="public/css/style.css" rel="stylesheet">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Detalle del Jugador</h2>
        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($jugador): ?>
            <div class="card">
                <div class="card-header">
                    <h4><?php echo htmlspecialchars($jugador->getNombreCompleto()); ?></h4>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>ID:</strong> <?php echo $jugador->getId(); ?></li>
                        <li class="list-group-item"><strong>Equipo ID:</strong> <?php echo $jugador->getEquipoId(); ?></li>
                        <li class="list-group-item"><strong>Valor:</strong> <?php echo $jugador->getValor(); ?></li>
                        <li class="list-group-item"><strong>Partidos Jugados:</strong> <?php echo $jugador->getPartidos(); ?></li>
                        <li class="list-group-item"><strong>Goles:</strong> <?php echo $jugador->getGoles(); ?></li>
                        <li class="list-group-item"><strong>Asistencias:</strong> <?php echo $jugador->getAsistencias(); ?></li>
                    </ul>
                </div>
            </div>
            <a href="index.php" class="btn btn-secondary mt-3">Volver al listado</a>
        <?php else: ?>
            <div class="alert alert-warning">No se ha encontrado el jugador solicitado.</div>
        <?php endif; ?>
    </div>
</body>
</html>