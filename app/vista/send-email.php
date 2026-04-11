<!-- 
send-email.php
Vista para mostrar el formulario de envío de correo electrónico para recuperar contraseña
Autor: Arnau Aumedes Jimenez 
-->
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Recuperar Contrasenya</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>

<?php
$error = $error ?? '';
$success = $success ?? '';
?>

<body>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="login-card p-4">
                <h4 class="mb-3 text-center">Recupera la teva contrasenya</h4>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <form method="post" action="" name="reset">
                    <div class="form-group">
                        <label for="email">Correu electrònic</label>
                        <input id="email" type="email" name="email" placeholder="username@email.com"
                            class="form-control" autofocus required />
                    </div>
                    <div class="form-group text-center mt-4">
                        <input type="submit" id="reset" value="Enviar" class="btn btn-success px-4" />
                        <a href="index.php" class="btn btn-outline-secondary ml-2">Menú</a>
                    </div>
                </form>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>

</html>