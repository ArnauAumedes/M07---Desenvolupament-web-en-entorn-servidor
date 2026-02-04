<?php
// Vista de detalle de un equipo
// Espera la variable $equipo (instancia de Equipo) y $message (string de error)
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Detalle del Equipo</title>
	<link href="public/css/style.css" rel="stylesheet">
	<!-- Bootstrap CDN -->
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
	<div class="container mt-5">
		<h2 class="mb-4">Detalle del Equipo</h2>
		<?php if (!empty($message)): ?>
			<div class="alert alert-danger"><?php echo htmlspecialchars($message); ?></div>
		<?php endif; ?>
		<?php if ($equipo): ?>
			<div class="card">
				<div class="card-header">
					<h4><?php echo htmlspecialchars($equipo->getEquip()); ?></h4>
				</div>
				<div class="card-body">
					<ul class="list-group list-group-flush">
						<li class="list-group-item"><strong>ID:</strong> <?php echo $equipo->getId(); ?></li>
						<li class="list-group-item"><strong>Entrenador (user_id):</strong> <?php echo $equipo->getUserId(); ?></li>
						<li class="list-group-item"><strong>Escudo:</strong> <img src="<?php echo htmlspecialchars($equipo->getEscudo()); ?>" alt="Escudo" style="height:40px;"></li>
						<li class="list-group-item"><strong>Partidos Jugados:</strong> <?php echo $equipo->getJugados(); ?></li>
						<li class="list-group-item"><strong>Ganados:</strong> <?php echo $equipo->getGanados(); ?></li>
						<li class="list-group-item"><strong>Empatados:</strong> <?php echo $equipo->getEmpatados(); ?></li>
						<li class="list-group-item"><strong>Perdidos:</strong> <?php echo $equipo->getPerdidos(); ?></li>
						<li class="list-group-item"><strong>Objetivo:</strong> <?php echo $equipo->getObjetivo(); ?></li>
					</ul>
				</div>
			</div>
			<a href="index.php" class="btn btn-secondary mt-3">Volver al listado</a>
		<?php else: ?>
			<div class="alert alert-warning">No se ha encontrado el equipo solicitado.</div>
		<?php endif; ?>
	</div>
</body>
</html>
