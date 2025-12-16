<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Tabla de clasificacion</title>
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
	<link rel="stylesheet" href="/practicas/public/css/style.css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="/practicas/public/css/style.css">
</head>


<?php
require_once __DIR__ . '/../globals/header.php';
require_once __DIR__ . '/../../model/database/database.php';
require_once __DIR__ . '/../../model/dao/EquipoDAO.php';

$db = new Database();
$equipoDAO = new EquipoDAO($db->getConnection());
$equipos = $equipoDAO->findAll();
// Ordenar equipos por puntos de mayor a menor
usort($equipos, function($a, $b) use ($equipoDAO) {
	$puntosA = $equipoDAO->getPuntos($a->getId());
	$puntosB = $equipoDAO->getPuntos($b->getId());
	return $puntosB <=> $puntosA;
});
?>
<div class="main">
	<div class="table-responsive">
		<table class="table table-bordered table-hover table-striped align-middle mb-0 tabla-clasificacion">
			<thead class="thead-dark">
				<tr>
					<th class="align-middle" style="width:8%">POSITION</th>
					<th class="align-middle" style="width:22%">CLUB</th>
					<th class="text-center align-middle" style="width:10%">JUGADOS</th>
					<th class="text-center align-middle" style="width:10%">GANADOS</th>
					<th class="text-center align-middle" style="width:10%">EMPATADOS</th>
					<th class="text-center align-middle" style="width:10%">PERDIDOS</th>
					<th class="text-center align-middle" style="width:10%">PUNTOS</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($equipos as $index => $equipo): ?>
					<tr>
						<td class="align-middle fs-4 fw-bold">
							<?= $index + 1 ?>
						</td>
						<td class="align-middle d-flex align-items-center gap-2">
							<img src="<?= htmlspecialchars($equipo->getEscudo()) ?>"
								alt="<?= htmlspecialchars($equipo->getEquip()) ?>" style="height:32px; margin-right:8px;">
							<div>
								<span class="fw-bold text-uppercase"><?= htmlspecialchars($equipo->getEquip()) ?></span><br>
								<span class="text-muted club-usuario" style="font-size:0.95em;">
									<?= htmlspecialchars($equipo->getUserId()) ?></span>
							</div>
						</td>
						<td class="text-center align-middle"><?= htmlspecialchars($equipo->getJugados()) ?></td>
						<td class="text-center align-middle"><?= htmlspecialchars($equipo->getGanados()) ?></td>
						<td class="text-center align-middle"><?= htmlspecialchars($equipo->getEmpatados()) ?></td>
						<td class="text-center align-middle"><?= htmlspecialchars($equipo->getPerdidos()) ?></td>
						<td class="text-center align-middle"><?= htmlspecialchars($equipoDAO->getPuntos($equipo->getId())) ?></td>
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