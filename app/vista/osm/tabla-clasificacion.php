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
$equipos = [
	[
		'pos' => 1,
		'club' => 'LIVERPOOL',
		'usuario' => 'USUARIO1',
		'escudo' => 'https://upload.wikimedia.org/wikipedia/en/0/0c/Liverpool_FC.svg',
		'jugados' => 10,
		'ganados' => 10,
		'empatados' => 0,
		'perdidos' => 0,
		'puntos' => 30,
		'gf_gc' => '20-2',
		'rendimiento' => ['V', 'V', 'V', 'V', 'V'],
		'bg' => '',
		'trofeo' => true
	],
	[
		'pos' => 2,
		'club' => 'ARSENAL',
		'usuario' => 'USUARIO2',
		'escudo' => 'https://upload.wikimedia.org/wikipedia/en/5/53/Arsenal_FC.svg',
		'jugados' => 10,
		'ganados' => 9,
		'empatados' => 1,
		'perdidos' => 0,
		'puntos' => 28,
		'gf_gc' => '18-1',
		'rendimiento' => ['V', 'V', 'V', 'V', 'V'],
		'bg' => '',
		'trofeo' => true
	],
	[
		'pos' => 3,
		'club' => 'TOTTENHAM',
		'usuario' => 'USUARIO3',
		'escudo' => 'https://upload.wikimedia.org/wikipedia/en/b/b4/Tottenham_Hotspur_F.C._logo.svg',
		'jugados' => 10,
		'ganados' => 9,
		'empatados' => 0,
		'perdidos' => 1,
		'puntos' => 27,
		'gf_gc' => '22-12',
		'rendimiento' => ['V', 'V', 'V', 'V', 'V'],
		'bg' => '',
		'trofeo' => true
	],
	[
		'pos' => 4,
		'club' => 'MANCHESTER CITY',
		'usuario' => 'USUARIO4',
		'escudo' => 'https://upload.wikimedia.org/wikipedia/en/e/eb/Manchester_City_FC_badge.svg',
		'jugados' => 10,
		'ganados' => 8,
		'empatados' => 1,
		'perdidos' => 1,
		'puntos' => 25,
		'gf_gc' => '21-4',
		'rendimiento' => ['V', 'V', 'V', 'V', 'V'],
		'bg' => '',
		'trofeo' => true
	],
	[
		'pos' => 5,
		'club' => 'CHEALSE',
		'usuario' => 'USUARIO5',
		'escudo' => 'https://upload.wikimedia.org/wikipedia/en/c/cc/Chelsea_FC.svg',
		'jugados' => 10,
		'ganados' => 8,
		'empatados' => 0,
		'perdidos' => 2,
		'puntos' => 24,
		'gf_gc' => '16-4',
		'rendimiento' => ['V', 'V', 'V', 'V', 'V'],
		'bg' => '',
		'trofeo' => true
	],
	[
		'pos' => 6,
		'club' => 'MANCHESTER UNITED',
		'usuario' => 'USUARIO6',
		'escudo' => 'https://upload.wikimedia.org/wikipedia/en/7/7a/Manchester_United_FC_crest.svg',
		'jugados' => 10,
		'ganados' => 7,
		'empatados' => 1,
		'perdidos' => 2,
		'puntos' => 22,
		'gf_gc' => '10-2',
		'rendimiento' => ['V', 'V', 'V', 'D', 'V'],
		'bg' => '',
		'trofeo' => false
	],
	[
		'pos' => 7,
		'club' => 'LEICHESTER CITY',
		'usuario' => 'USUARIO7',
		'escudo' => 'https://upload.wikimedia.org/wikipedia/en/6/63/Leicester_City_crest.svg',
		'jugados' => 10,
		'ganados' => 7,
		'empatados' => 1,
		'perdidos' => 2,
		'puntos' => 22,
		'gf_gc' => '15-6',
		'rendimiento' => ['E', 'V', 'D', 'D', 'V'],
		'bg' => '',
		'trofeo' => false
	],
];
?>
<div class="main">
	<div class="table-responsive">
		<table class="table table-bordered table-hover table-striped align-middle mb-0 tabla-clasificacion">
			<thead class="thead-dark">
				<tr>
					<th class="align-middle">POSITION</th>
					<th class="align-middle">CLUB</th>
					<th class="text-center align-middle">JUGADOS</th>
					<th class="text-center align-middle">GANADOS</th>
					<th class="text-center align-middle">EMPATADOS</th>
					<th class="text-center align-middle">PERDIDOS</th>
					<th class="text-center align-middle">PUNTOS</th>
					<th class="text-center align-middle">GF - GC</th>
					<th class="text-center align-middle">RENDIMIENTO</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($equipos as $equipo): ?>
					<tr class="<?= $equipo['bg'] ?>">
						<td class="align-middle fs-4 fw-bold">
							<?= $equipo['pos'] ?>
							<?php if ($equipo['trofeo']): ?><span class="ms-1">🏆</span><?php endif; ?>
						</td>
						<td class="align-middle d-flex align-items-center gap-2">
							<img src="<?= $equipo['escudo'] ?>" alt="<?= $equipo['club'] ?>"
								style="height:32px; margin-right:8px;">
							<div>
								<span class="fw-bold text-uppercase"><?= $equipo['club'] ?></span><br>
								<span class="text-muted club-usuario"
									style="font-size:0.95em;"><?= $equipo['usuario'] ?></span>
							</div>
						</td>
						<td class="text-center align-middle"><?= $equipo['jugados'] ?></td>
						<td class="text-center align-middle"><?= $equipo['ganados'] ?></td>
						<td class="text-center align-middle"><?= $equipo['empatados'] ?></td>
						<td class="text-center align-middle"><?= $equipo['perdidos'] ?></td>
						<td class="text-center align-middle"><?= $equipo['puntos'] ?></td>
						<td class="text-center align-middle"><?= $equipo['gf_gc'] ?></td>
						<td class="text-center align-middle">
							<?php foreach ($equipo['rendimiento'] as $r): ?>
								<?php
								if ($r === 'V') {
									echo '<span class="badge bg-success mx-1">V</span>';
								} elseif ($r === 'E') {
									echo '<span class="badge bg-warning text-dark mx-1">E</span>';
								} elseif ($r === 'D') {
									echo '<span class="badge bg-danger mx-1">D</span>';
								}
								?>
							<?php endforeach; ?>
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