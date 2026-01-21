<!--
    Update Jugador view
    Autor: Arnau Aumedes Jimenez
    Fitxer: app/vista/crudJugadores/updateJugadores.php
-->
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Jugador</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/practicas/public/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <!-- Formulario de búsqueda por ID (siempre visible) -->
        <form method="GET" action="/practicas/index.php" class="form-create mb-4">
            <h1>Actualizar Jugador</h1>
            <input type="hidden" name="action" value="updateJugador">
            <div class="form-group">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <label for="id">ID del jugador a actualizar:</label>
                <input type="number" name="id" id="id" class="form-control"
                    value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>" required>
            </div>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Buscar Jugador</button>
                <a href="/practicas/index.php" class="btn btn-secondary">Volver al menú</a>
            </div>
        </form>

        <!-- Formulario de actualización (visible solo si $jugador está definido) -->
        <?php if (isset($jugador)):
            require_once __DIR__ . '/../../../config/db-connection.php';
            require_once __DIR__ . '/../../model/dao/EquipoDAO.php';
            $db = new Database();
            $equipoDAO = new EquipoDAO($db->getConnection());
            $minJugados = $equipoDAO->getMinJugados();
            $maxJugados = $equipoDAO->getMaxJugados();
        ?>
            <form method="POST" action="/practicas/index.php?action=updateJugador&id=<?php echo urlencode($jugador->getId()); ?>" class="form-create">
                <h1>Editar Jugador</h1>
                <?php if (!empty($error_jugador)): ?>
                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_jugador); ?></div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="id_display">ID:</label>
                    <input type="text" id="id_display" class="form-control" value="<?php echo htmlspecialchars($jugador->getId()); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="nombre_completo">Nombre completo:</label>
                    <input type="text" name="nombre_completo" id="nombre_completo" class="form-control" required
                        value="<?php echo isset($_POST['nombre_completo']) ? htmlspecialchars($_POST['nombre_completo']) : htmlspecialchars($jugador->getNombreCompleto()); ?>">
                </div>
                <div class="form-group">
                    <label for="equipo_id">Equipo (ID):</label>
                    <input type="number" name="equipo_id" id="equipo_id" class="form-control" required
                        value="<?php echo isset($_POST['equipo_id']) ? htmlspecialchars($_POST['equipo_id']) : htmlspecialchars($jugador->getEquipoId()); ?>">
                </div>
                <div class="form-group">
                    <label for="valor">Valor:</label>
                    <input type="number" name="valor" id="valor" class="form-control" max="300" required
                        value="<?php echo isset($_POST['valor']) ? htmlspecialchars($_POST['valor']) : htmlspecialchars($jugador->getValor()); ?>">
                    <small class="form-text text-muted">Máximo permitido: 300</small>
                </div>
                <div class="form-group">
                    <label for="partidos">Partidos jugados:</label>
                    <input type="number" name="partidos" id="partidos" class="form-control" min="0" max="<?php echo $maxJugados; ?>" required
                        value="<?php echo isset($_POST['partidos']) ? htmlspecialchars($_POST['partidos']) : htmlspecialchars($jugador->getPartidos()); ?>">
                    <small class="form-text text-muted">Máximo permitido: <?php echo $maxJugados; ?></small>
                </div>
                <div class="form-group">
                    <label for="goles">Goles:</label>
                    <input type="number" name="goles" id="goles" class="form-control" min="0" max="100" required
                        value="<?php echo isset($_POST['goles']) ? htmlspecialchars($_POST['goles']) : htmlspecialchars($jugador->getGoles()); ?>">
                    <small class="form-text text-muted">Máximo permitido: 100</small>
                </div>
                <div class="form-group">
                    <label for="asistencias">Asistencias:</label>
                    <input type="number" name="asistencias" id="asistencias" class="form-control" min="0" max="100" required
                        value="<?php echo isset($_POST['asistencias']) ? htmlspecialchars($_POST['asistencias']) : htmlspecialchars($jugador->getAsistencias()); ?>">
                    <small class="form-text text-muted">Máximo permitido: 100</small>
                </div>
                <div class="d-flex justify-content-center mt-3" style="gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">Actualizar Jugador</button>
                    <a href="/practicas/index.php" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
