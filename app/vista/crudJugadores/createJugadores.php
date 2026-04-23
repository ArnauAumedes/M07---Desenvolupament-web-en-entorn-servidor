<!--
    Create Jugador view
    Autor: Arnau Aumedes Jimenez
    Fitxer: app/vista/crudJugadores/createJugadores.php
-->
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Jugador</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>

<body>
    <div class="container mt-5">
        <?php
        require_once __DIR__ . '/../../../config/db-connection.php';
        require_once __DIR__ . '/../../model/dao/EquipoDAO.php';
        $db = Database::getInstance();
        $equipoDAO = new EquipoDAO($db->getConnection());
        $minJugados = $equipoDAO->getMinJugados();
        $maxJugados = $equipoDAO->getMaxJugados();
        ?>
        <form method="POST" action="index.php?action=createJugador" class="form-create">
            <h1>Crear Nuevo Jugador</h1>
            <?php if (!empty($error_jugador)): ?>
                <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_jugador); ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label for="nombre_completo">Nombre completo:</label>
                <input type="text" name="nombre_completo" id="nombre_completo" class="form-control" required
                    value="<?php echo isset($_POST['nombre_completo']) ? htmlspecialchars($_POST['nombre_completo']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="equipo_id">Equipo (ID):</label>
                <input type="number" name="equipo_id" id="equipo_id" class="form-control" required
                    value="<?php echo isset($_POST['equipo_id']) ? htmlspecialchars($_POST['equipo_id']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="valor">Valor:</label>
                <input type="number" name="valor" id="valor" class="form-control" max="300" required
                    value="<?php echo isset($_POST['valor']) ? htmlspecialchars($_POST['valor']) : ''; ?>">
                <small class="form-text text-muted">Máximo permitido: 300</small>
            </div>
            <div class="form-group">
                <label for="partidos">Partidos jugados:</label>
                <input type="number" name="partidos" id="partidos" class="form-control" min="0"
                    max="<?php echo $maxJugados; ?>"
                    value="<?php echo isset($_POST['partidos']) ? htmlspecialchars($_POST['partidos']) : $minJugados; ?>"
                    required>
                <small class="form-text text-muted">Máximo permitido: <?php echo $maxJugados; ?></small>
            </div>
            <div class="form-group">
                <label for="goles">Goles:</label>
                <input type="number" name="goles" id="goles" class="form-control" min="0" max="100" required
                    value="<?php echo isset($_POST['goles']) ? htmlspecialchars($_POST['goles']) : 0; ?>">
                <small class="form-text text-muted">Máximo permitido: 100</small>
            </div>
            <div class="form-group">
                <label for="asistencias">Asistencias:</label>
                <input type="number" name="asistencias" id="asistencias" class="form-control" min="0" max="100" required
                    value="<?php echo isset($_POST['asistencias']) ? htmlspecialchars($_POST['asistencias']) : 0; ?>">
                <small class="form-text text-muted">Máximo permitido: 100</small>
            </div>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Crear Jugador</button>
                <a href="index.php" class="btn btn-secondary">Volver al menú</a>
            </div>
        </form>
    </div>
</body>

</html>