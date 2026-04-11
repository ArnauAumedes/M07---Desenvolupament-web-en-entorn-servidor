<!-- 
tabla-clasificacion.php
Vista para mostrar la tabla de clasificación de equipos, con opciones de búsqueda, ordenamiento y paginación
Autor: Arnau Aumedes Jimenez 
-->
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
	<link rel="stylesheet" href="public/css/style.css">
	<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
	<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="public/css/style.css">
	<script src="resources/js/equipoSearch.js"></script>
	<script src="resources/js/equipoModal.js"></script>
</head>


<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Incloure capçalera i fitxers necessaris
require_once __DIR__ . '/../globals/header.php';
require_once __DIR__ . '/../../model/components/auth.php';
$isLoggedIn = isLoggedIn();

?>

<body>
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
	</div>
	<div class="table-responsive">
		<table class="table table-bordered table-hover table-striped align-middle mb-0 tabla-clasificacion">
			<thead class="thead-dark">
				<tr>
					<th class="align-middle" style="width:8%">POSICIÓN</th>
					<th class="align-middle" style="width:8%">ID EQUIPO</th>
					<th class="align-middle" style="width:22%">CLUB</th>
					<th class="text-center align-middle" style="width:10%">JUGADOS</th>
					<th class="text-center align-middle" style="width:10%">GANADOS</th>
					<th class="text-center align-middle" style="width:10%">EMPATADOS</th>
					<th class="text-center align-middle" style="width:10%">PERDIDOS</th>
					<th class="text-center align-middle" style="width:10%">PUNTOS</th>
				</tr>
			</thead>
			<tbody id="tabla-equipos-body">
				<?php
				// Obtener usuario actual y admin
				$user_id = $_SESSION['user']['user_id'] ?? null;
				$isAdmin = $_SESSION['user']['isAdmin'] ?? 0;
				foreach ($equipos as $index => $equipo): ?>
					<tr data-equipo-id="<?= urlencode($equipo->getId()) ?>" style="cursor:pointer;">
						<td class="align-middle fs-4 fw-bold">
							<?= $index + 1 ?>
						</td>
						<td class="align-middle">
							<?= htmlspecialchars($equipo->getId()) ?>
						</td>
						<td class="align-middle d-flex align-items-center gap-2">
							<img src="<?= htmlspecialchars($equipo->getEscudo()) ?>"
								alt="<?= htmlspecialchars($equipo->getEquip()) ?>" style="height:32px; margin-right:8px;">
							<div>
								<span class="fw-bold text-uppercase"><?= htmlspecialchars($equipo->getEquip()) ?></span><br>
								<span class="text-muted club-usuario" style="font-size:0.95em;">
									<?php if ($equipo->getEntrenador() === null || $equipo->getEntrenador() === ""): ?>
										no tiene entrenador
									<?php else: ?>
										<?= htmlspecialchars($equipo->getEntrenador()) ?>
									<?php endif; ?>
								</span>
								<?php
								// Mostrar iconos si el usuario puede hacer CRUD
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
							</div>
						</td>
						<td class="text-center align-middle"><?= htmlspecialchars($equipo->getJugados()) ?></td>
						<td class="text-center align-middle"><?= htmlspecialchars($equipo->getGanados()) ?></td>
						<td class="text-center align-middle"><?= htmlspecialchars($equipo->getEmpatados()) ?></td>
						<td class="text-center align-middle"><?= htmlspecialchars($equipo->getPerdidos()) ?></td>
						<td class="text-center align-middle">
							<?= htmlspecialchars($equipoDAO->getPuntos($equipo->getId())) ?>
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

<?php
require_once __DIR__ . '/../globals/footer.php';
?>

</html>