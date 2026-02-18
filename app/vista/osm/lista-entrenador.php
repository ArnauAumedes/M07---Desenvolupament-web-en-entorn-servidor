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
    <link rel="stylesheet" href="public/css/style.css">
    <script src="resources/js/userSearch.js"></script>
    <script src="resources/js/userModal.js"></script>
</head>

<body>
    <?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Incloure capçalera i fitxers necessaris
    require_once __DIR__ . '/../globals/header.php';
    require_once __DIR__ . '/../../model/components/auth.php';
    $isLoggedIn = isLoggedIn();
    $user = getLoggedUser();
    $isAdmin = !empty($user['isAdmin']) && $user['isAdmin'] == 1;
    ?>
    <div class="main">
        <div class="d-flex align-items-center justify-content-center mb-3"
            style="gap: 16px; max-width: 1100px; margin: auto;">
            <?php if ($isLoggedIn && $isAdmin):
                require_once __DIR__ . '/../globals/crudButtonsUsers.php';
            endif; ?>
            <?php
            require_once __DIR__ . '/../globals/searchBar.php';
            ?>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle mb-0 tabla-clasificacion">
                <thead class="thead-dark">
                    <tr>
                        <th class="align-middle" style="width:20%">ENTRENADOR</th>
                        <th class="align-middle" style="width:22%">EQUIPO</th>
                        <th class="text-center align-middle" style="width:14%">OBJETIVO</th>
                        <th class="text-center align-middle" style="width:14%">POSICIÓN</th>
                        <th class="text-center align-middle" style="width:14%">DIFERENCIA</th>
                        <th class="text-center align-middle" style="width:16%">FECHA CREACIÓN</th>
                    </tr>
                </thead>
                <tbody id="tabla-users-body">
                    <?php $posicion = 1; ?>
                    <?php foreach ($entrenadoresConEquipos as $entrenadorConEquipos): ?>
                        <?php $entrenador = $entrenadorConEquipos['entrenador']; ?>
                        <?php $user = $userDAO->findById($entrenador->getId()); ?>
                        <?php if (!empty($entrenadorConEquipos['equipos'])): ?>
                            <?php foreach ($entrenadorConEquipos['equipos'] as $equipo): ?>
                                <tr data-user-id="<?= urlencode($user->getId()) ?>" style="cursor:pointer;">
                                    <td class="align-middle fw-bold">
                                        <?= htmlspecialchars($user ? $user->getUsername() : '') ?>
                                    </td>
                                    <td class="align-middle d-flex align-items-center gap-2">
                                        <img src="<?= htmlspecialchars($equipo->getEscudo()) ?>"
                                            alt="<?= htmlspecialchars($equipo->getEquip()) ?>"
                                            style="height:32px; margin-right:8px;">
                                        <span class="fw-bold text-uppercase"><?= htmlspecialchars($equipo->getEquip()) ?></span>
                                    </td>
                                    <td class="text-center align-middle"><?= htmlspecialchars($equipo->getObjetivo()) ?></td>
                                    <td class="text-center align-middle"><?= $posicion ?></td>
                                    <?php $dif = $equipoDAO->getDiferenciaObjetivoPosicion($equipo->getObjetivo(), $posicion); ?>
                                    <td class="text-center align-middle">
                                        <span class="fw-bold" style="color: <?= htmlspecialchars($dif['color'] ?? '#000') ?>;">
                                            <?= ($dif['simbolo'] ?? '') . ($dif['valor'] ?? '') ?>
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        <?= htmlspecialchars($user ? $user->getCreatedAt() : '-') ?>
                                    </td>
                                </tr>
                                <?php $posicion++; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td class="align-middle fw-bold">
                                    <?= htmlspecialchars($user ? $user->getUsername() : '') ?>
                                </td>
                                <td class="align-middle text-muted" colspan="4">Sin equipo</td>
                                <td class="text-center align-middle">
                                    <?= htmlspecialchars($user ? $user->getCreatedAt() : '-') ?>
                                </td>
                            </tr>
                            <?php $posicion++; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php require_once __DIR__ . '/../globals/modalUser.php'; ?>
</body>
<div class="d-flex align-items-center justify-content-center mb-3" style="gap: 16px">
    <?php
    require_once __DIR__ . '/../globals/order.php';
    ?>
    <?php
    require_once __DIR__ . '/../globals/pagination.php';
    ?>
</div>
<?php
require_once __DIR__ . '/../globals/footer.php';
?>

</html>