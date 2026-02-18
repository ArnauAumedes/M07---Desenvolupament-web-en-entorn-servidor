<?php
require_once __DIR__ . '/../controlador/ResetPasswordController.php';
$controller = new ResetPasswordController();
$controller->handleRequest();
?>
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Cambiar la contraseña</title>
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
                <h4 class="mb-3 text-center">Introdueix la nova contrasenya</h4>
                <?php if ($controller->error): ?>
                    <div class="alert alert-danger"><?php echo $controller->error; ?></div>
                <?php endif; ?>
                <?php if ($controller->success): ?>
                    <div class="alert alert-success"><?php echo $controller->success; ?></div>
                <?php endif; ?>
                <?php if ($controller->showForm): ?>
                    <form method="post" action="" name="update">
                        <input type="hidden" name="action" value="update" class="form-control" />
                        <div class="form-group">
                            <label for="pass1">Nova contrasenya</label>
                            <input id="pass1" type="password" name="pass1" class="form-control" required />
                        </div>
                        <div class="form-group">
                            <label for="pass2">Repeteix la nova contrasenya</label>
                            <input id="pass2" type="password" name="pass2" class="form-control" required />
                        </div>
                        <input type="hidden" name="email" value="<?php echo htmlspecialchars($controller->email); ?>" />
                        <div class="form-group text-center mt-4">
                            <input type="submit" id="reset" value="Restablir contrasenya" class="btn btn-primary px-4" />
                            <a href="index.php" class="btn btn-outline-secondary ml-2">Tornar al menú</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</body>

</html>