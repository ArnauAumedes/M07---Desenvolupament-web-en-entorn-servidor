<!--
	Edit Profile view
	Autor: Arnau Aumedes Jimenez
	Fitxer: app/vista/edit-profile.php
-->
<!DOCTYPE html>
<html lang="ca">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Edita el teu perfil</title>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="public/css/style.css">
</head>

<body>
	<div class="row justify-content-center">
		<div class="col-md-6">
			<div class="login-card p-4">
				<h4 class="mb-3 text-center">Edita el teu perfil</h4>
				<?php
				// Missatges o errors generats pel controlador (si existeixen)
				echo $messages ?? '';
				// Suposem que $user conté les dades actuals de l'usuari (nickname, email)
				?>
				<form method="post" action="index.php?action=edit-profile">
					<div class="form-group">
						<label for="nickname">Nickname</label>
						<input id="nickname" name="nickname" type="text" class="form-control" autofocus value="<?php echo htmlspecialchars($user['nickname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
					</div>
					<div class="form-group">
						<label for="email">Email</label>
						<input id="email" name="email" type="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
					</div>
					<div class="form-group text-center mt-4">
						<button name="btnSubmit" type="submit" class="btn btn-success px-4">Desar canvis</button>
						<a href="index.php" class="btn btn-outline-secondary ml-2">Cancel·lar</a>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
	<?php if (!empty($messages)): ?>
	<script>
		document.getElementById('nickname').focus();
	</script>
	<?php endif; ?>
</body>

</html>
