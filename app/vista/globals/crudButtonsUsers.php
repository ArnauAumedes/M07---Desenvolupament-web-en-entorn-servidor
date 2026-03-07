<?php
/**
 * crudButtonsUsers.php
 * Vista para mostrar los botones de CRUD de usuarios, solo si el usuario está logueado
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
			<span>Bienvenido, puedes gestionar los usuarios:</span>
			<div>
				<a href="index.php?action=createUser"
					class="btn btn-primary btn-sm mr-2">Crear usuario</a>
				<a href="index.php?action=updateUser"
					class="btn btn-warning btn-sm mr-2">Actualizar usuario</a>
				<a href="index.php?action=deleteUser"
					class="btn btn-danger btn-sm">Eliminar usuario</a>
			</div>
		</div>
	<?php else: ?>
		<div class="alert alert-info" role="alert">
			Debes iniciar sesión para crear, actualizar o eliminar usuarios.
		</div>
	<?php endif; ?>
</div>
