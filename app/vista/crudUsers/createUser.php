<!--
    Create User view
    Autor: Arnau Aumedes Jimenez
    Fitxer: app/vista/crudUsers/createUser.php
-->
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuari</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/practicas/public/css/style.css">
</head>

<body>
    <div class="container mt-5">
        <?php
        // Aquí podrías requerir el DAO si necesitas datos auxiliares
        ?>
        <form method="POST" action="/practicas/index.php?action=createUser" class="form-create">
            <h1>Crear Nou Usuari</h1>
            <!--  Mensajes de error o confirmación -->
            <?php if (!empty($messages)): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($messages); ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="username">Nom d'usuari:</label>
                <input type="text" name="username" id="username" class="form-control" required
                    value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" required
                    value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="password">Contrasenya:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password2">Repetir Contrasenya:</label>
                <input type="password" name="password2" id="password2" class="form-control" required>
            </div>
            <div>
                <label for="equipo_id">Asignar equipo:</label>
                <select name="equipo_id" id="equipo_id" class="form-control">
                    <option value="">Selecciona un equipo</option>
                    <?php
                    foreach ($equipos as $equipo) {
                        echo '<option value="' . $equipo->getId() . '">' . htmlspecialchars($equipo->getEquip()) . '</option>';
                    }
                    ?>
                </select>
            </div>
            <!-- Solo visible para admins: checkbox para isAdmin (añadir después en el backend) -->
            <?php if (!empty($_SESSION['user']['isAdmin']) && $_SESSION['user']['isAdmin'] == 1): ?>
                <div class="form-group form-check">
                    <input type="checkbox" class="form-check-input" id="isAdmin" name="isAdmin" value="1" <?php echo isset($_POST['isAdmin']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="isAdmin">Administrador</label>
                </div>
            <?php endif; ?>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Crear Usuari</button>
                <a href="/practicas/index.php" class="btn btn-secondary">Volver al menú</a>
            </div>
        </form>
    </div>
</body>

</html>