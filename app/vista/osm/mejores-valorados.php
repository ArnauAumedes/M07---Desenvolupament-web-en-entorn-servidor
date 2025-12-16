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
    require_once __DIR__ . '/../globals/header.php';
    require_once __DIR__ . '/../../model/database/database.php';
    require_once __DIR__ . '/../../model/dao/JugadorDAO.php';
    require_once __DIR__ . '/../../model/dao/EquipoDAO.php';

    /**
     * Definir variables i ordenar la taula de maxim contribuidors per majors contribuidors
     * @var Database $db Instancia de la base de dades
     * @var JugadorDAO $jugadorDAO Instancia del DAO de jugadors
     * @var EquipoDAO $equipoDAO Instancia del DAO d'equips
     * @var array $jugadores Llista de jugadors
     */
    $db = new Database();
    $jugadorDAO = new JugadorDAO($db->getConnection());
    $equipoDAO = new EquipoDAO($db->getConnection());
    $jugadores = $jugadorDAO->findAll();
    $jugadores = $jugadorDAO->ordenarPorValor(
        $jugadores,
        function ($jugador) use ($jugadorDAO) {
            return $jugadorDAO->getSumaGolesAsistencias($jugador->getId());
        },
        'desc'
    );
    ?>

    <div class="main">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-striped align-middle mb-0 tabla-clasificacion">
                <thead class="thead-dark">
                    <tr>
                        <th class="align-middle" style="width:20%">NOMBRE</th>
                        <th class="align-middle" style="width:22%">CLUB</th>
                        <th class="text-center align-middle" style="width:14%">PARTIDOS</th>
                        <th class="text-center align-middle" style="width:14%">GOLES</th>
                        <th class="text-center align-middle" style="width:14%">ASISTENCIAS</th>
                        <th class="text-center align-middle" style="width:16%">GOLES + ASISTENCIAS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jugadores as $jugador): ?>
                        <?php
                        $equipo = $equipoDAO->findById($jugador->getEquipoId());
                        $sumaGA = $jugadorDAO->getSumaGolesAsistencias($jugador->getId());
                        ?>
                        <tr>
                            <td class="align-middle fw-bold text-uppercase">
                                <?= htmlspecialchars($jugador->getNombreCompleto()) ?>
                            </td>
                            <td class="align-middle d-flex align-items-center gap-2">
                                <?php if ($equipo): ?>
                                    <img src="<?= htmlspecialchars($equipo->getEscudo()) ?>"
                                        alt="<?= htmlspecialchars($equipo->getEquip()) ?>"
                                        style="height:32px; margin-right:8px;">
                                    <span class="fw-bold text-uppercase"><?= htmlspecialchars($equipo->getEquip()) ?></span><br>
                                    <span class="text-muted club-usuario" style="font-size:0.95em;">
                                        <?= htmlspecialchars($equipo->getUserId()) ?>
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
                                <?= $sumaGA ?>
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