<!--
    Update view
    Autor: Arnau Aumedes Jimenez
    Fitxer: app/vista/crudEquipos/updateEquipos.php
-->
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualitzar Article</title>
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
        <!-- Formulario de búsqueda por ID (siempre visible) -->
        <form method="GET" action="index.php" class="form-create mb-4">
            <h1>Actualizar Equipo</h1>
            <input type="hidden" name="action" value="update">
            <div class="form-group">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
                <label for="id">ID del equipo a actualizar:</label>
                <input type="number" name="id" id="id" class="form-control"
                    value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>" required>
            </div>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Buscar Equipo</button>
                <a href="index.php" class="btn btn-secondary">Volver al menú</a>
            </div>
        </form>

        <!-- Formulario de actualización (visible solo si $equipo está definido) -->

        <?php if (isset($equipo)): ?>
            <form method="POST"
                action="index.php?action=update&id=<?php echo urlencode($equipo->getId()); ?>"
                class="form-create">
                <h1>Editar Equipo</h1>
                <?php if (!empty($error_partidos)): ?>
                    <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error_partidos); ?></div>
                <?php endif; ?>
                <div class="form-group">
                    <label for="id_display">ID:</label>
                    <input type="text" id="id_display" class="form-control"
                        value="<?php echo htmlspecialchars($equipo->getId()); ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="equip">Nombre del equipo:</label>
                    <input type="text" name="equip" id="equip" class="form-control" required
                        value="<?php echo isset($_POST['equip']) ? htmlspecialchars($_POST['equip']) : htmlspecialchars($equipo->getEquip()); ?>">
                </div>
                <div class="form-group">
                    <label for="objetivo">Objetivo:</label>
                    <input type="text" name="objetivo" id="objetivo" class="form-control" required
                        value="<?php echo isset($_POST['objetivo']) ? htmlspecialchars($_POST['objetivo']) : htmlspecialchars($equipo->getObjetivo()); ?>">
                </div>
                <div class="form-group">
                    <label for="escudo">Escudo (URL de imagen):</label>
                    <input type="text" name="escudo" id="escudo" class="form-control" required
                        value="<?php echo isset($_POST['escudo']) ? htmlspecialchars($_POST['escudo']) : htmlspecialchars($equipo->getEscudo()); ?>">
                </div>
                <div class="form-group">
                    <label for="jugados">Partidos jugados:</label>
                    <input type="number" name="jugados" id="jugados" class="form-control" min="0"
                        max="<?php echo $maxJugados; ?>"
                        value="<?php echo isset($_POST['jugados']) ? htmlspecialchars($_POST['jugados']) : htmlspecialchars($equipo->getJugados()); ?>"
                        required>
                    <small class="form-text text-muted">Máximo permitido: <?php echo $maxJugados; ?></small>
                </div>
                <div class="form-group">
                    <label for="ganados">Partidos ganados:</label>
                    <input type="number" name="ganados" id="ganados" class="form-control" min="0"
                        value="<?php echo isset($_POST['ganados']) ? htmlspecialchars($_POST['ganados']) : htmlspecialchars($equipo->getGanados()); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="empatados">Partidos empatados:</label>
                    <input type="number" name="empatados" id="empatados" class="form-control" min="0"
                        value="<?php echo isset($_POST['empatados']) ? htmlspecialchars($_POST['empatados']) : htmlspecialchars($equipo->getEmpatados()); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label for="perdidos">Partidos perdidos:</label>
                    <input type="number" name="perdidos" id="perdidos" class="form-control" min="0"
                        value="<?php echo isset($_POST['perdidos']) ? htmlspecialchars($_POST['perdidos']) : htmlspecialchars($equipo->getPerdidos()); ?>"
                        required>
                </div>
                <div class="d-flex justify-content-center mt-3" style="gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary">Actualizar Equipo</button>
                    <a href="index.php" class="btn btn-secondary">Menú</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>