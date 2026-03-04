<!--
    Register view
    Autor: Arnau Aumedes Jimenez
    Fitxer: app/vista/register.php
-->
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Register - Gestió d'Articles</title>
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
                <h4 class="mb-3 text-center">Registre d'usuari</h4>
                <?php echo $messages ?? ''; ?>
                <form method="post" action="index.php?action=register">
                    <div class="form-group">
                        <label for="username">Nom d'usuari</label>
                        <input id="username" name="username" type="text" class="form-control"
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="text" class="form-control"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Contrasenya</label>
                        <input id="password" name="password" type="password" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="password2">Repetir Contrasenya</label>
                        <input id="password2" name="password2" type="password" class="form-control">
                    </div>
                    <div class="form-group text-center mt-2 d-flex justify-content-center">
                        <a href="index.php?action=login"><b><u>Ya tienes cuenta? Inicia sesión</u></b></a>
                    </div>
                    <div class="form-group text-center mt-4">
                        <button name="btnRegister" type="submit" class="btn btn-success px-4">Registrar</button>
                        <a href="index.php" class="btn btn-outline-secondary ml-2">Menú</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>

</html>