<?php
// Vista de detalle de un user
$isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';

if (!$isAjax):
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detalle del user</title>
        <link href="public/css/style.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body>
        <div class="container mt-5">
            <h2 class="mb-4">Detalle del user</h2>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($user): ?>
            <div class="single-equipo-flex p-4 d-flex gap-4">
                <div class="single-equipo-escudo d-flex align-items-center justify-content-center">
                    <img src="public/assets/default-user-profile.webp" alt="Imagen del usuario" class="escudo-grande">
                </div>
                <div class="single-equipo-info">
                    <h3 class="mb-3"><?php echo htmlspecialchars($user->getUsername()); ?></h3>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>ID:</strong> <?php echo $user->getId(); ?></li>
                        <li class="list-group-item"><strong>Email:</strong>
                            <?php echo htmlspecialchars($user->getEmail()); ?></li>
                        <li class="list-group-item"><strong>Administrador:</strong>
                            <?php echo ($user->isAdmin() ? 'Sí' : 'No'); ?></li>
                        <li class="list-group-item"><strong>Fecha de creación:</strong>
                            <?php echo method_exists($user, 'getCreatedAt') ? htmlspecialchars($user->getCreatedAt()) : '-'; ?>
                        </li>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">No se ha encontrado el usuario solicitado.</div>
        <?php endif; ?>

        <?php if (!$isAjax): ?>
        </div>
    </body>

    </html>
<?php endif; ?>