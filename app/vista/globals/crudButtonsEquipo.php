<?php
// CRUD buttons fragment: shows different links when the user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../model/components/auth.php';

$isLoggedIn = isLoggedIn();
$user = getLoggedUser();
$username = $user['username'] ?? null;

?>

<div class="container my-3 crud-buttons-container">
    <?php if ($isLoggedIn): ?>
        <div class="alert alert-info d-flex justify-content-between align-items-center" role="alert">
            <span>Bienvenido, puedes gestionar tus equipos:</span>
            <div>
                <a href="/practicas/index.php?action=create"
                    class="btn btn-primary btn-sm mr-2">Crear
                    equipo</a>
                <a href="/practicas/index.php?action=update"
                    class="btn btn-warning btn-sm mr-2">Actualizar
                    equipo</a>
                <a href="/practicas/index.php?action=delete"
                    class="btn btn-danger btn-sm">Eliminar
                    equipo</a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            Debes iniciar sesión para crear, actualizar o eliminar equipos.
        </div>
    <?php endif; ?>
</div>