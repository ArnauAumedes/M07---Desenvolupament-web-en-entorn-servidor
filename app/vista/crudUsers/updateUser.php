<!--
	Update User view
	Autor: Arnau Aumedes Jimenez
	Fitxer: app/vista/crudUsers/updateUser.php
-->
<!DOCTYPE html>
<html lang="ca">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Actualitzar Usuari</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="/practicas/public/css/style.css">
</head>

<body>
	<div class="container mt-5">
		<!-- Formulario de búsqueda por ID (siempre visible) -->
		<form method="GET" action="/practicas/index.php" class="form-create mb-4">
			<h1>Actualizar Usuari</h1>
			<input type="hidden" name="action" value="updateUser">
			<div class="form-group">
				<?php if (!empty($messages)): ?>
					<div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($messages); ?></div>
				<?php endif; ?>
				<label for="id">ID del usuari a actualizar:</label>
				<input type="number" name="id" id="id" class="form-control"
					value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>" required>
			</div>
			<div class="d-flex justify-content-center gap-2 mt-3">
				<button type="submit" class="btn btn-primary">Buscar Usuari</button>
				<a href="/practicas/index.php" class="btn btn-secondary">Volver al menú</a>
			</div>
		</form>

		<!-- Formulario de actualización (visible solo si $user está definido) -->
		<?php if (isset($user)): ?>
			<form method="POST"
				action="/practicas/index.php?action=updateUser&id=<?php echo urlencode($user->getId()); ?>"
				class="form-create">
				<h1>Editar Usuari</h1>
				<?php if (!empty($messages)): ?>
					<div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($messages); ?></div>
				<?php endif; ?>
				<div class="form-group">
					<label for="id_display">ID:</label>
					<input type="text" id="id_display" class="form-control"
						value="<?php echo htmlspecialchars($user->getId()); ?>" readonly>
				</div>
				<div class="form-group">
					<label for="username">Nom d'usuari:</label>
					<input type="text" name="username" id="username" class="form-control" required
						value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : htmlspecialchars($user->getUsername()); ?>">
				</div>
				<div class="form-group">
					<label for="email">Email:</label>
					<input type="email" name="email" id="email" class="form-control" required
						value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($user->getEmail()); ?>">
				</div>
				<!-- Solo visible para admins: checkbox para isAdmin -->
				<?php if (!empty($_SESSION['user']['isAdmin']) && $_SESSION['user']['isAdmin'] == 1): ?>
					<div class="form-group form-check">
						<input type="checkbox" name="isAdmin" id="isAdmin" class="form-check-input" value="1" <?php echo ($user->isAdmin() == 1) ? 'checked' : ''; ?>>
						<label class="form-check-label" for="isAdmin">Administrador</label>
					</div>
				<?php endif; ?>
				<div class="d-flex justify-content-center mt-3" style="gap: 0.5rem;">
					<button type="submit" class="btn btn-primary">Actualizar Usuari</button>
					<a href="/practicas/index.php" class="btn btn-secondary">Cancelar</a>
				</div>
			</form>
		<?php endif; ?>
	</div>
</body>

</html>
