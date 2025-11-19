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
    <link rel="stylesheet" href="/practicas/Pràctica 03 - Paginació/public/css/style.css">
</head>

<body>
    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-8">
                            <h2>Registre <b>Usuari</b></h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="login-card p-4">
                            <h4 class="mb-3 text-center">Crear compte</h4>
                            <?php echo $messages ?? ''; ?>
                            <form method="post" action="/practicas/Pràctica 03 - Paginació/public/index.php?action=register">
                                <div class="form-group">
                                    <label for="username">Nom d'usuari</label>
                                    <input id="username" name="username" type="text" class="form-control" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input id="email" name="email" type="text" class="form-control" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label for="password">Contrasenya</label>
                                    <input id="password" name="password" type="password" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label for="password2">Repetir Contrasenya</label>
                                    <input id="password2" name="password2" type="password" class="form-control">
                                </div>
                                <div class="form-group text-center mt-4">
                                    <button name="btnRegister" type="submit" class="btn btn-success px-4">Registrar</button>
                                    <a href="/practicas/Pràctica 03 - Paginació/public/index.php" class="btn btn-outline-secondary ml-2">Cancel·lar</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>

</html>
