<?php
/**
 * singleJugador.php
 * Vista para mostrar el detalle de un jugador específico
 * Autor: Arnau Aumedes Jimenez
 */
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';

if (!$isAjax):
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detalle del Jugador</title>
        <link href="public/css/style.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body>
        <div class="container mt-5">
            <h2 class="mb-4">Detalle del Jugador</h2>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($jugador): ?>
            <div class="single-equipo-flex p-4 d-flex gap-4">
                <div class="single-equipo-escudo d-flex align-items-center justify-content-center">
                    <img src="public/assets/default-player-profile.png" alt="Imagen del jugador" class="escudo-grande">
                </div>
                <div class="single-equipo-info">
                    <h3 class="mb-3"><?php echo htmlspecialchars($jugador->getNombreCompleto()); ?></h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>ID:</strong> <?php echo $jugador->getId(); ?></li>
                        <li class="list-group-item"><strong>Equipo ID:</strong> <?php echo $jugador->getEquipoId(); ?></li>
                        <li class="list-group-item"><strong>Valor:</strong>
                            <?php echo number_format($jugador->getValor(), 2, ',', '.') . '$'; ?></li>
                        <li class="list-group-item"><strong>Partidos Jugados:</strong>
                            <?php echo $jugador->getPartidos(); ?></li>
                        <li class="list-group-item"><strong>Goles:</strong> <?php echo $jugador->getGoles(); ?></li>
                        <li class="list-group-item"><strong>Asistencias:</strong> <?php echo $jugador->getAsistencias(); ?>
                        </li>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">No se ha encontrado el jugador solicitado.</div>
        <?php endif; ?>

        <?php if (!$isAjax): ?>
        </div>
    </body>

    </html>
<?php endif; ?>