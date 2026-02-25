<!--
	Login view
	Autor: Arnau Aumedes Jimenez
	Fitxer: app/vista/login.php
-->
<!DOCTYPE html>
<html lang="ca">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>Login - Gestió d'Articles</title>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="public/css/style.css">
</head>
<?php
require_once __DIR__ . '/../controlador/oauth/google.php';
?>

<body>
	<div class="row justify-content-center">
		<div class="col-md-6">
			<div class="login-card p-4">
				<h4 class="mb-3 text-center">Iniciar Sesión</h4>
				<?php
				// Mensajes o errores generados por el controlador (si existen)
				echo $messages ?? '';
				// Aseguramos que la variable de sesión existe
				if (!isset($_SESSION['login_attempts'])) {
					$_SESSION['login_attempts'] = 0;
				}
				?>
				<form method="post" action="index.php?action=login">
					<div class="form-group">
						<label for="email">Email</label>
						<input id="email" name="email" type="text" class="form-control" autofocus
							value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
					</div>
					<div class="form-group">
						<label for="password">Contrasenya</label>
						<input id="password" name="password" type="password" class="form-control">
					</div>
					<?php if ($_SESSION['login_attempts'] >= 3): ?>
						<div class="g-recaptcha" data-sitekey="<?php echo getenv('RECAPTCHA_SITE_KEY'); ?>"></div>
						<script src="https://www.google.com/recaptcha/api.js" async defer></script>
					<?php endif; ?>
					<div class="form-group text-center mt-2 d-flex justify-content-center" style="gap: 16px">
						<div class="form-group form-check text-left">
							<input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
							<label class="form-check-label" for="rememberMe">Recordarme</label>
						</div>
						<a href="index.php?action=send-email"><b><u>Olvidé mi contraseña</u></b>
						</a>
						<a href="index.php?action=register"><b><u>Crear cuenta</u></b>
						</a>
					</div>
					<a class="btn btn-outline-primary" href="<?= $client->createAuthUrl() ?>">
						<img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png"
							alt="Iniciar sesión con Google" style="height:40px;">
					</a>
					<div class="form-group text-center mt-4">
						<button name="btnSubmit" type="submit" class="btn btn-success px-4">Entrar</button>
						<a href="index.php" class="btn btn-outline-secondary ml-2">Menú</a>
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
			document.getElementById('password').focus();
		</script>
	<?php endif; ?>
</body>

</html>