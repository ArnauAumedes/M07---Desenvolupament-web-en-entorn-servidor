<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mejores Valorados</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/practicas/public/css/style.css">
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
    ?>

    <div class="main">
        <div class="d-flex align-items-center justify-content-center mb-3"
            style="gap: 16px; max-width: 1100px; margin: auto;">
            <?php
            require_once __DIR__ . '/../globals/crudButtonsJugador.php';
            ?>
            <?php
            require_once __DIR__ . '/../globals/searchBar.php';
            ?>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle mb-0 tabla-clasificacion">
                <thead class="thead-dark">
                    <tr>
                        <th class="align-middle" style="width:10%">ID JUGADOR</th>
                        <th class="align-middle" style="width:20%">NOMBRE</th>
                        <th class="align-middle" style="width:22%">CLUB</th>
                        <th class="text-center align-middle" style="width:14%">PARTIDOS</th>
                        <th class="text-center align-middle" style="width:14%">GOLES</th>
                        <th class="text-center align-middle" style="width:14%">ASISTENCIAS</th>
                        <th class="text-center align-middle" style="width:16%">GOLES + ASISTENCIAS</th>
                    </tr>
                </thead>
                <tbody id="tabla-jugadores-body">
                    <?php foreach ($jugadores as $jugador):
                        $equipo = $equipoDAO->findById($jugador->getEquipoId()); ?>
                        <tr onclick="window.location='/practicas/index.php?action=viewJugador&id=<?= urlencode($jugador->getId()) ?>'"
                            style="cursor:pointer;">
                            <td class="align-middle">
                                <?= htmlspecialchars($jugador->getId()) ?>
                            </td>
                            <td class="align-middle fw-bold text-uppercase">
                                <?= htmlspecialchars($jugador->getNombreCompleto()) ?>
                            </td>
                            <td class="align-middle d-flex align-items-center gap-2">
                                <?php if ($jugador): ?>
                                    <img src="<?= htmlspecialchars($equipo->getEscudo()) ?>"
                                        alt="<?= htmlspecialchars($equipo->getEquip()) ?>"
                                        style="height:32px; margin-right:8px;">
                                    <span class="fw-bold text-uppercase"><?= htmlspecialchars($equipo->getEquip()) ?></span><br>
                                    <span class="text-muted club-usuario" style="font-size:0.95em;">
                                        <?php if ($equipo->getUserId() === null || $equipo->getUserId() === ""): ?>
                                            no tiene entrenador
                                        <?php else: ?>
                                            <?= htmlspecialchars($equipo->getUserId()) ?>
                                        <?php endif; ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center align-middle">
                                <?= htmlspecialchars($jugador->getPartidos()) ?>
                            </td>
                            <td class="text-center align-middle">
                                <?= htmlspecialchars($jugador->getGoles()) ?>
                            </td>
                            <td class="text-center align-middle">
                                <?= htmlspecialchars($jugador->getAsistencias()) ?>
                            </td>
                            <td class="text-center align-middle" style="font-weight:bold;">
                                <?= htmlspecialchars($jugadorDAO->getSumaGolesAsistencias($jugador->getId())) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="/practicas/resources/js/jugadorSearch.js"></script>
</body>
<!-- Paginacion -->
<div class="d-flex align-items-center justify-content-center mb-3" style="gap: 16px">
    <?php
    require_once __DIR__ . '/../globals/order.php';
    ?>
    <?php
    require_once __DIR__ . '/../globals/pagination.php';
    ?>
</div> <?php require_once __DIR__ . '/../globals/footer.php'; ?>

</html>