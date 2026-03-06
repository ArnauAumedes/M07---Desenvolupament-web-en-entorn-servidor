<?php
// Vista de detalle de un equipo
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';

if (!$isAjax):
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Equipo</title>
    <link href="public/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
<?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($equipo): ?>
        <div class="single-equipo-flex p-4">
            <div class="single-equipo-escudo d-flex align-items-center justify-content-center">
                <img src="<?php echo htmlspecialchars($equipo->getEscudo()); ?>" alt="Escudo" class="escudo-grande">
            </div>
            <div class="single-equipo-info">
                <h3 class="mb-3"><?php echo htmlspecialchars($equipo->getEquip()); ?></h3>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><strong>ID:</strong> <?php echo $equipo->getId(); ?></li>
                    <li class="list-group-item"><strong>Entrenador (user_id):</strong> <?php echo $equipo->getEntrenador() ?? 'No tiene entrenador'; ?></li>
                    <li class="list-group-item"><strong>Partidos Jugados:</strong> <?php echo $equipo->getJugados(); ?></li>
                    <li class="list-group-item"><strong>Ganados:</strong> <?php echo $equipo->getGanados(); ?></li>
                    <li class="list-group-item"><strong>Empatados:</strong> <?php echo $equipo->getEmpatados(); ?></li>
                    <li class="list-group-item"><strong>Perdidos:</strong> <?php echo $equipo->getPerdidos(); ?></li>
                    <li class="list-group-item"><strong>Objetivo:</strong> <?php echo $equipo->getObjetivo(); ?></li>
                </ul>
            </div>
        </div>
        <?php else: ?>
            <div class="alert alert-warning">No se ha encontrado el equipo solicitado.</div>
        <?php endif; ?>

<?php if (!$isAjax): ?>
    </div>
</body>
</html>
<?php endif; ?>