
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Valor de los Equipos</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/practicas/public/css/style.css">
</head>
<body>
<?php
require_once __DIR__ . '/../globals/header.php';
require_once __DIR__ . '/../../model/database/database.php';
require_once __DIR__ . '/../../model/dao/EquipoDAO.php';
require_once __DIR__ . '/../../model/dao/JugadorDAO.php';

$db = new Database();
$equipoDAO = new EquipoDAO($db->getConnection());
$jugadorDAO = new JugadorDAO($db->getConnection());
$equipos = $equipoDAO->findAll();
?>
<div class="main">
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped align-middle mb-0 tabla-clasificacion">
            <thead class="thead-dark">
                <tr>
                    <th class="align-middle" style="width:8%">POSICIÓN</th>
                    <th class="align-middle" style="width:22%">CLUB</th>
                    <th class="text-center align-middle" style="width:20%">VALOR TOTAL (€)</th>
                    <th class="text-center align-middle" style="width:20%">JUGADORES</th>
                    <th class="text-center align-middle" style="width:20%">VALOR PROMEDIO (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipos as $equipo): ?>
                    <?php
                        $equipoId = $equipo->getId();
                        $valorTotal = $equipoDAO->getValorEquipo($equipoId);
                        $jugadores = $jugadorDAO->findByEquipoId($equipoId);
                        $cantidadJugadores = count($jugadores);
                        $valorPromedio = $cantidadJugadores > 0 ? $equipoDAO->getMediaValorJugadores($equipoId) : 0;
                    ?>
                    <tr>
                        <td class="align-middle fs-4 fw-bold">
                            <?= htmlspecialchars($equipo->getPos()) ?>
                        </td>
                        <td class="align-middle d-flex align-items-center gap-2">
                            <img src="<?= htmlspecialchars($equipo->getEscudo()) ?>" alt="<?= htmlspecialchars($equipo->getEquip()) ?>"
                                style="height:32px; margin-right:8px;">
                            <span class="fw-bold text-uppercase"><?= htmlspecialchars($equipo->getEquip()) ?></span>
                        </td>
                        <td class="text-center align-middle" style="font-weight:bold; color:#2c3e50;">
                            <?= number_format($valorTotal, 2) ?> €
                        </td>
                        <td class="text-center align-middle">
                            <?= $cantidadJugadores ?>
                        </td>
                        <td class="text-center align-middle">
                            <?= number_format($valorPromedio, 2) ?> €
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/../globals/footer.php'; ?>
</body>
</html>