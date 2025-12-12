
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista Entrenadores</title>
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
require_once __DIR__ . '/../../model/dao/UserDAO.php';
$db = (new Database())->getConnection();
$equipoDAO = new EquipoDAO($db);
$userDAO = new UserDAO($db);
$equipos = $equipoDAO->findAll();

?>
<div class="main">
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped align-middle mb-0 tabla-clasificacion">
            <thead class="thead-dark">
                <tr>
                    <th class="align-middle">ENTRENADOR</th>
                    <th class="align-middle">EQUIPO</th>
                    <th class="text-center align-middle">OBJETIVO</th>
                    <th class="text-center align-middle">POSICIÓN</th>
                    <th class="text-center align-middle">DIFERENCIA</th>
                    <th class="text-center align-middle">FECHA CREACIÓN</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipos as $equipo): ?>
                    <tr>
                        <td class="align-middle fw-bold">
                            <?php
                            $user = $userDAO->getById($equipo->getUserId());
                            echo htmlspecialchars($user['username'] ?? '');
                            ?>
                        </td>
                        <td class="align-middle d-flex align-items-center gap-2">
                            <img src="<?= htmlspecialchars($equipo->getEscudo()) ?>" alt="<?= htmlspecialchars($equipo->getEquip()) ?>"
                                style="height:32px; margin-right:8px;">
                            <span class="fw-bold text-uppercase"><?= htmlspecialchars($equipo->getEquip()) ?></span>
                        </td>
                        <td class="text-center align-middle"><?= htmlspecialchars($equipo->getObjetivo()) ?></td>
                        <td class="text-center align-middle"><?= htmlspecialchars($equipo->getPos()) ?></td>
                        <td class="text-center align-middle">
                            <?php
                            $diferencia = $equipo->getObjetivo() - $equipo->getPos();
                            if ($diferencia > 0) {
                                echo '<span style="color:green">+' . $diferencia . '</span>';
                            } elseif ($diferencia < 0) {
                                echo '<span style="color:red">' . $diferencia . '</span>';
                            } else {
                                echo $diferencia;
                            }
                            ?>
                        </td>
                        <td class="text-center align-middle">
                            <?= htmlspecialchars($user['created_at'] ?? '-') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
<?php
require_once __DIR__ . '/../globals/footer.php';
?>
</html>