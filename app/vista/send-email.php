<?php
require_once __DIR__ . '/../controlador/ForgotPasswordController.php';
$controller = new ForgotPasswordController();
$controller->handleRequest();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Recuperar Contrasenya - Gestió d'Articles</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/practicas/M07---Desenvolupament-web-en-entorn-servidor/public/css/style.css">
</head>
<body>
    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-8">
                            <h2>Recuperar <b>Contrasenya</b></h2>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="login-card p-4">
                            <h4 class="mb-3 text-center">Recupera la teva contrasenya</h4>
                            <?php if ($controller->error): ?>
                                <div class="alert alert-danger"><?php echo $controller->error; ?></div>
                            <?php endif; ?>
                            <?php if ($controller->success): ?>
                                <div class="alert alert-success"><?php echo $controller->success; ?></div>
                            <?php endif; ?>
                            <form method="post" action="" name="reset">
                                <div class="form-group">
                                    <label for="email">Correu electrònic</label>
                                    <input id="email" type="email" name="email" placeholder="username@email.com" class="form-control" autofocus required />
                                </div>
                                <div class="form-group text-center mt-4">
                                    <input type="submit" id="reset" value="Enviar enllaç de recuperació" class="btn btn-primary px-4" />
                                    <a href="/practicas/M07---Desenvolupament-web-en-entorn-servidor/public/index.php" class="btn btn-outline-secondary ml-2">Tornar al menú</a>
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