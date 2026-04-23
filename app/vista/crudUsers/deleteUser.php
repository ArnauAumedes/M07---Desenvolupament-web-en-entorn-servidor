<!--
	Delete User view
	Autor: Arnau Aumedes Jimenez
	Fitxer: app/vista/crudUsers/deleteUser.php
-->
<!DOCTYPE html>
<html lang="ca">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Eliminar Usuari</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="public/css/style.css">
</head>
<body>
	<div class="container mt-5">
		<form method="POST" action="index.php" class="form-create">
			<h1>Eliminar Usuari</h1>
			<input type="hidden" name="action" value="deleteUser">
			<input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken ?? '', ENT_QUOTES, 'UTF-8') ?>">
			<div class="form-group">
				<label for="id">ID del usuari a eliminar:</label>
				<input type="number" name="id" id="id" class="form-control" value="<?= htmlspecialchars((string) ($idPrefill ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
			</div>
			<div class="d-flex justify-content-center gap-2 mt-3">
				<button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro que quieres eliminar este usuario?')">
					Eliminar Usuari
				</button>
				<a href="index.php" class="btn btn-secondary">Volver al menú</a>
			</div>
		</form>
	</div>
</body>
</html>
