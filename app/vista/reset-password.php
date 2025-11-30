
<?php
require_once __DIR__ . '/../controlador/ResetPasswordController.php';
$controller = new ResetPasswordController();
$controller->handleRequest();
?>
<html>
    <head>
        <title>Reset Password</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    </head>
    <body>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <?php if ($controller->error): ?>
                        <div class="alert alert-danger"><?php echo $controller->error; ?></div>
                    <?php endif; ?>
                    <?php if ($controller->success): ?>
                        <div class="alert alert-success"><?php echo $controller->success; ?></div>
                    <?php endif; ?>
                    <?php if ($controller->showForm): ?>
                        <h2>Reset Password</h2>
                        <form method="post" action="" name="update">
                            <input type="hidden" name="action" value="update" class="form-control"/>
                            <div class="form-group">
                                <label><strong>Enter New Password:</strong></label>
                                <input type="password"  name="pass1" class="form-control"/>
                            </div>
                            <div class="form-group">
                                <label><strong>Re-Enter New Password:</strong></label>
                                <input type="password"  name="pass2" class="form-control"/>
                            </div>
                            <input type="hidden" name="email" value="<?php echo htmlspecialchars($controller->email); ?>"/>
                            <div class="form-group">
                                <input type="submit" id="reset" value="Reset Password"  class="btn btn-primary"/>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </body>
</html>