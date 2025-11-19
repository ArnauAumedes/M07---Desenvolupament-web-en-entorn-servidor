<?php
// Header fragment: shows different links when the user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../model/components/auth.php';

$isLoggedIn = isLoggedIn();
$user = getLoggedUser();
$username = $user['username'] ?? null;

?>
<style>
    #header {
        background-color: #435d7d;
    }
    .profile-img {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 8px;
        vertical-align: middle;
    }
    .nav-profile {
        display: flex;
        align-items: center;
    }
</style>

<!-- Header fragment: include this inside the <body> of your pages -->
<nav id="header" class="navbar navbar-expand-sm navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand text-white" href="/practicas/Pràctica 03 - Paginació/public/index.php">Logo</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mynavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="mynavbar">
            <ul class="navbar-nav ms-auto">
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item nav-profile">
                        <?php
                        // small inline SVG placeholder as profile image (generic)
                        $svg = rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4" fill="#ffffff" opacity="0.15"/><path d="M12 14c-4 0-7 2-7 4v1h14v-1c0-2-3-4-7-4z" fill="#ffffff" opacity="0.15"/></svg>');
                        $imgSrc = "data:image/svg+xml;utf8,{$svg}";
                        ?>
                        <img src="<?php echo $imgSrc; ?>" alt="profile" class="profile-img" />
                        <a class="nav-link text-white" href="#"><?php echo htmlspecialchars($username ?? 'Usuari'); ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white logout-link" href="/practicas/Pràctica 03 - Paginació/public/index.php?action=logout">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/practicas/Pràctica 03 - Paginació/public/index.php?action=login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/practicas/Pràctica 03 - Paginació/public/index.php?action=register">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
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
// end header
?>

