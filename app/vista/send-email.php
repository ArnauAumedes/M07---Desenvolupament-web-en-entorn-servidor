<?php
require_once __DIR__ . '/../controlador/ForgotPasswordController.php';
$controller = new ForgotPasswordController();
$controller->handleRequest();
?>
<html>
<head>
    <title>Password Recovery using PHP and MySQL</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <h2>Forgot Password</h2>
                <?php if ($controller->error): ?>
                    <div class="alert alert-danger"><?php echo $controller->error; ?></div>
                <?php endif; ?>
                <?php if ($controller->success): ?>
                    <div class="alert alert-success"><?php echo $controller->success; ?></div>
                <?php endif; ?>
                <form method="post" action="" name="reset">
                    <div class="form-group">
                        <label><strong>Enter Your Email Address:</strong></label>
                        <input type="email" name="email" placeholder="username@email.com" class="form-control" />
                    </div>
                    <div class="form-group">
                        <input type="submit" id="reset" value="Reset Password" class="btn btn-primary" />
                    </div>
                </form>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>
</body>
</html>