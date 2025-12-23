<!--
    Create view
    Autor: Arnau Aumedes Jimenez
    Fitxer: app/vista/create.php
-->
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Article</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/practicas/public/css/style.css">
</head>

<body>
    <div class="container mt-5">
        <?php
        require_once __DIR__ . '/../model/database/database.php';
        require_once __DIR__ . '/../model/dao/EquipoDAO.php';
        $db = new Database();
        $equipoDAO = new EquipoDAO($db->getConnection());
        $minJugados = $equipoDAO->getMinJugados();
        $maxJugados = $equipoDAO->getMaxJugados();
        ?>
        <form method="POST" action="/practicas/public/index.php?action=create" class="form-create">
            <h1>Crear Nuevo Equipo</h1>
            <?php if (!empty($error_partidos)): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_partidos); ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="equip">Nombre del equipo:</label>
                <input type="text" name="equip" id="equip" class="form-control" required
                    value="<?php echo isset($_POST['equip']) ? htmlspecialchars($_POST['equip']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="objetivo">Objetivo:</label>
                <input type="text" name="objetivo" id="objetivo" class="form-control" required
                    value="<?php echo isset($_POST['objetivo']) ? htmlspecialchars($_POST['objetivo']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="escudo">Escudo (URL de imagen):</label>
                <input type="text" name="escudo" id="escudo" class="form-control" required
                    value="<?php echo isset($_POST['escudo']) ? htmlspecialchars($_POST['escudo']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="jugados">Partidos jugados:</label>
                <input type="number" name="jugados" id="jugados" class="form-control" min="0"
                    max="<?php echo $maxJugados; ?>"
                    value="<?php echo isset($_POST['jugados']) ? htmlspecialchars($_POST['jugados']) : $minJugados; ?>"
                    required>
                <small class="form-text text-muted">Máximo permitido: <?php echo $maxJugados; ?></small>
            </div>
            <div class="form-group">
                <label for="ganados">Partidos ganados:</label>
                <input type="number" name="ganados" id="ganados" class="form-control" min="0"
                    value="<?php echo isset($_POST['ganados']) ? htmlspecialchars($_POST['ganados']) : 0; ?>" required>
            </div>
            <div class="form-group">
                <label for="empatados">Partidos empatados:</label>
                <input type="number" name="empatados" id="empatados" class="form-control" min="0"
                    value="<?php echo isset($_POST['empatados']) ? htmlspecialchars($_POST['empatados']) : 0; ?>"
                    required>
            </div>
            <div class="form-group">
                <label for="perdidos">Partidos perdidos:</label>
                <input type="number" name="perdidos" id="perdidos" class="form-control" min="0"
                    value="<?php echo isset($_POST['perdidos']) ? htmlspecialchars($_POST['perdidos']) : 0; ?>"
                    required>
            </div>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Crear Equipo</button>
                <a href="/practicas/public/index.php" class="btn btn-secondary">Volver al menú</a>
            </div>
        </form>
    </div>
</body>

</html>