<?php
/**
 * crudButtonsJugador.php
 * Vista para mostrar los botones de CRUD de jugadores, solo si el usuario está logueado
 * Autor: Arnau Aumedes Jimenez
 */

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
            <span>Bienvenido, puedes gestionar tus jugadores:</span>
            <div>
                <a href="index.php?action=createJugador"
                    class="btn btn-primary btn-sm mr-2">Crear
                    jugador</a>
                <a href="index.php?action=updateJugador"
                    class="btn btn-warning btn-sm mr-2">Actualizar
                    jugador</a>
                <a href="index.php?action=deleteJugador"
                    class="btn btn-danger btn-sm">Eliminar
                    jugador</a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info" role="alert">
            Debes iniciar sesión para crear, actualizar o eliminar jugadores.
        </div>
    <?php endif; ?>
</div>
