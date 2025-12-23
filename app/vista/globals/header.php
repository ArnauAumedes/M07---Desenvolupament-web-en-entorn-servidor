<?php
// Header fragment: shows different links when the user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../model/components/auth.php';

$isLoggedIn = isLoggedIn();
$user = getLoggedUser();
$username = $user['username'] ?? null;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/practicas/public/css/style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>

<nav id="header" class="navbar navbar-expand-sm navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="/practicas/public/index.php">Logo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <ul class="navbar-nav nav-left">
            <li class="nav-item">
                <a class="nav-link" href="/practicas/app/vista/osm/tabla-clasificacion.php">TABLA DE
                    CLASIFICACION</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/practicas/app/vista/osm/valor-equipo.php">VALOR DE EQUIPO</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/practicas/app/vista/osm/lista-entrenador.php">LISTA DE ENTRENADOR</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/practicas/app/vista/osm/pichichis.php">PICHICHIS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/practicas/app/vista/osm/asistencias.php">ASISTENCIAS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/practicas/app/vista/osm/mejores-valorados.php">MEJORES VALORADOS</a>
            </li>
        </ul>
        <ul class="navbar-nav nav-right">
            <?php if ($isLoggedIn): ?>
                <li class="nav-item nav-profile d-flex align-items-center">
                    <?php
                    $svg = rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" fill="#ffffff" opacity="0.15"/><path d="M12 14c-4 0-7 2-7 4v1h14v-1c0-2-3-4-7-4z" fill="#ffffff" opacity="0.15"/></svg>');
                    $imgSrc = "data:image/svg+xml;utf8,{$svg}";
                    ?>
                    <span class="d-flex align-items-center">
                        <img src="<?php echo $imgSrc; ?>" alt="profile" class="profile-img mr-2" />
                        <a class="nav-link header-btn" href="#" style="line-height:1;"><?php echo htmlspecialchars($username ?? 'Usuari'); ?></a>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link header-btn" href="/practicas/public/index.php?action=logout">Logout</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link header-btn" href="/practicas/public/index.php?action=login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link header-btn" href="/practicas/public/index.php?action=register">Register</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<?php
// Mostrar popup de benvinguda (flash) si existeix
if (!empty($_SESSION['flash_welcome'])) {
    $fw = $_SESSION['flash_welcome'];
    unset($_SESSION['flash_welcome']);
    $msg = 'Bienvenido ' . $fw;
    $json = json_encode($msg);
    echo "<script>window.addEventListener('load', function(){ alert($json); });</script>";
}
// Añadir confirmación al logout
$logoutName = json_encode($username ?? '');
echo '<script>' . "\n" .
    'document.addEventListener("DOMContentLoaded", function(){' . "\n" .
    '  var name = ' . $logoutName . ';' . "\n" .
    '  document.querySelectorAll(".logout-link").forEach(function(el){' . "\n" .
    '    el.addEventListener("click", function(e){' . "\n" .
    '      e.preventDefault();' . "\n" .
    '      if (confirm("Seguro que quieres salir " + name + "?")) {' . "\n" .
    '        window.location = this.href;' . "\n" .
    '      }' . "\n" .
    '    });' . "\n" .
    '  });' . "\n" .
    '});' . "\n" .
    '</script>';
?>