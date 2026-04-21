<!-- 
valor-equipo.php
Vista para mostrar el valor total de los equipos, con opciones de búsqueda y paginación
Autor: Arnau Aumedes Jimenez 
-->

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
    <link rel="stylesheet" href="public/css/style.css">
    <script src="resources/js/equipoSearch.js"></script>
    <script src="resources/js/equipoModal.js"></script>
</head>

<body>
    <?php

    // Incloure capçalera i fitxers necessaris
    require_once __DIR__ . '/../globals/header.php';
    require_once __DIR__ . '/../../model/components/auth.php';
    $isLoggedIn = isLoggedIn();
    ?>
    <div class="main">
        <div class="d-flex align-items-center justify-content-center mb-3"
            style="gap: 16px; max-width: 1100px; margin: auto;">
            <?php
            require_once __DIR__ . '/../globals/crudButtonsEquipo.php';
            ?>
            <?php
            require_once __DIR__ . '/../globals/searchBar.php';
            ?>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle mb-0 tabla-clasificacion">
                <thead class="thead-dark">
                    <tr>
                        <th class="align-middle" style="width:8%">POSICIÓN</th>
                        <th class="align-middle" style="width:8%">ID EQUIPO</th>
                        <th class="align-middle" style="width:22%">CLUB</th>
                        <th class="text-center align-middle" style="width:20%">VALOR TOTAL (€)</th>
                        <th class="text-center align-middle" style="width:20%">JUGADORES</th>
                        <th class="text-center align-middle" style="width:20%">VALOR PROMEDIO (€)</th>
                    </tr>
                </thead>
                <tbody id="tabla-equipos-body">
                        <?php
                        $user_id = $_SESSION['user']['user_id'] ?? null;
                        $isAdmin = $_SESSION['user']['isAdmin'] ?? 0;
                        foreach ($equipos as $index => $equipo):
                            $equipoId = $equipo->getId();
                            $valorTotal = $equipoDAO->getValorEquipo($equipoId);
                            $cantidadJugadores = $equipoDAO->getCantidadJugadores($equipoId);
                            $valorPromedio = $cantidadJugadores > 0 ? $equipoDAO->getMediaValorJugadores($equipoId) : 0;
                            ?>
                            <tr data-equipo-id="<?= urlencode($equipo->getId()) ?>" style="cursor:pointer;">
                                <td class="align-middle fs-4 fw-bold">
                                    <?= $index + 1 ?>
                                </td>
                                <td class="align-middle">
                                    <?= htmlspecialchars($equipo->getId()) ?>
                                </td>
                                <td class="align-middle d-flex align-items-center gap-2">
                                    <img src="<?= htmlspecialchars($equipo->getEscudo()) ?>"
                                        alt="<?= htmlspecialchars($equipo->getEquip()) ?>"
                                        style="height:32px; margin-right:8px;">
                                    <span class="fw-bold text-uppercase"><?= htmlspecialchars($equipo->getEquip()) ?></span>
                                    <?php
                                    if ($user_id !== null && ($equipo->getCreadorId() == $user_id || $isAdmin)):
                                    ?>
                                        <span class="crud-icons ms-2">
                                            <a href="index.php?action=update&id=<?= urlencode($equipo->getId()) ?>" title="Editar equipo">
                                                <i class="fa fa-pencil text-primary" aria-hidden="true"></i>
                                            </a>
                                            <a href="index.php?action=delete&id=<?= urlencode($equipo->getId()) ?>" title="Eliminar equipo" onclick="return confirm('¿Seguro que quieres eliminar este equipo?');">
                                                <i class="fa fa-trash text-danger" aria-hidden="true"></i>
                                            </a>
                                        </span>
                                    <?php endif; ?>
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
    <?php require_once __DIR__ . '/../globals/modalEquipo.php'; ?>
</body>
<div class="d-flex align-items-center justify-content-center mb-3" style="gap: 16px">
    <?php
    require_once __DIR__ . '/../globals/order.php';
    ?>
    <?php
    require_once __DIR__ . '/../globals/pagination.php';
    ?>
</div>
<?php require_once __DIR__ . '/../globals/footer.php'; ?>

</html>