<!-- 
change-password.php
Vista para mostrar el formulario de cambio de contraseña, con validaciones y mensajes de error/success
Autor: Arnau Aumedes Jimenez 
-->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña</title>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="col-md-6 mx-auto">
        <div class="login-card p-4">
            <h4 class="mb-3 text-center">Cambiar la contraseña</h4>
            <?php echo $messages ?? ''; ?>
            <form method="post" action="index.php?action=change-password">
                <div class="form-group">
                    <label for="old_password">Contraseña antigua</label>
                    <input id="old_password" name="old_password" type="password" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password">Nueva contraseña</label>
                    <input id="password" name="password" type="password" class="form-control">
                </div>
                <div class="form-group">
                    <label for="password2">Repetir nueva contraseña</label>
                    <input id="password2" name="password2" type="password" class="form-control">
                </div>
                <div class="form-group text-center mt-4">
                    <button name="btnChangePassword" type="submit" class="btn btn-success px-4">Cambiar contraseña</button>
                    <a href="index.php" class="btn btn-outline-secondary ml-2">Menú</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>