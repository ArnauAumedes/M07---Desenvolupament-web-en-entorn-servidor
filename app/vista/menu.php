<!--
    Menu view
    Autor: Arnau Aumedes Jimenez
    Fitxer: app/vista/menu.php
-->
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Gestió d'Articles - Sistema CRUD</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/practicas/Pràctica 03 - Paginació/public/css/style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
</head>

<body>
    <header>
        <?php
        require_once __DIR__ . '/./globals/header.php';
        ?>
    </header>
    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">

                <?php
                if (isset($_GET['deleted'])):
                    switch ($_GET['deleted']) {
                        // Mostrar el missatge després d'eliminar (exitós)
                        case 'success':
                            $deletedId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Èxit!</strong> Article amb ID ' . $deletedId . ' eliminat correctament.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
                            break;
                        // Mostrar el missatge després d'eliminar (error)
                        case 'error':
                            $errorMsg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'No s\'ha pogut eliminar l\'article';
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> ' . $errorMsg . '
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
                            break;
                    }
                endif;

                if (isset($_GET['created'])):
                    switch ($_GET['created']) {
                        // Mostrar missatge després de crear (existós)
                        case 'success':
                            $createdId = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : '';
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Èxit!</strong> Article creat correctament amb ID ' . $createdId . '.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
                            break;
                        // Mostrar missatge després de crear (error)
                        case 'error':
                            $errorMsg = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : 'No s\'ha pogut crear l\'article';
                            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Error!</strong> ' . $errorMsg . '
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
                            break;
                    }
                endif;

                if (isset($_GET['updated'])):
                    // Mostrar missatge després de actualitzar (exitós) 
                    switch ($_GET['updated']) {
                        case 'success':
                            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                                <strong>Èxit!</strong> Article actualitzat correctament.
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                              </div>';
                            break;
                    }
                endif;
                ?>

                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-6">
                            <h2>Gestionar <b>Articles</b></h2>
                            <form id="perPageForm" method="GET" class="form-inline" style="display:inline-block;">
                                <input type="hidden" name="action" value="menu">
                                <div class="hint-text">Mostrant <?php echo isset($perPage) ? $perPage : 5; ?> per pàgina
                                </div>
                                <select name="per_page" id="per_page"
                                    onchange="document.getElementById('perPageForm').submit()">
                                    <?php
                                    $options = [1, 6, 12, 22];
                                    $currentPerPage = $perPage ?? 6;
                                    foreach ($options as $option) {
                                        $selected = ($option == $currentPerPage) ? 'selected' : '';
                                        echo "<option value=\"$option\" $selected>$option</option>";
                                    }
                                    ?>
                                </select>
                                <input type="hidden" name="page" value="1">
                            </form>
                        </div>
                        <div class="col-sm-6 text-right">
                            <?php
                            require_once __DIR__ . '/../model/components/auth.php';
                            if (isLoggedIn()):
                                ?>
                                <a href="?action=create" data-toggle="tooltip" title="Crear" class="btn btn-success"><i
                                        class="material-icons">&#xE147;</i>
                                    <span>Crear</span></a>
                            <?php else: ?>
                                <span class="text-white">Inicia sessió per crear, actualitzar o eliminar articles</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            
                <!-- Div amb llistat d'articles (grid de targetes) -->
                <div id="articles-list" class="container-fluid">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                    <?php elseif (empty($articles)): ?>
                        <div class="alert alert-info">No hi ha articles per mostrar.</div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($articles as $article): ?>
                                <div class="col-12 col-md-6 col-lg-4 mb-4">
                                    <div class="article-card">
                                        <div class="article-card-header text-center">
                                            <h5 class="mb-1 article-title"><?php echo htmlspecialchars($article->getTitol()); ?>
                                            </h5>
                                            <div class="article-meta small text-muted">
                                                <span class="presentation">Article creat per: </span><span class="author-name"><?php echo htmlspecialchars($article->getAuthorName() ?? $article->getUserId()); ?>
                                            </div></span> 
                                        </div>
                                        <div class="article-body p-3">
                                            <p class="mb-2 text-truncate-3">
                                                <?php echo nl2br(htmlspecialchars($article->getCos())); ?>
                                            </p>
                                        </div>
                                        <div class="article-actions border-top p-2 d-flex justify-content-between">
                                            <div>
                                                <a href="?action=view&id=<?php echo urlencode($article->getId()); ?>"
                                                    class="btn btn-sm btn-outline-secondary">Veure</a>
                                            </div>
                                            <div>
                                                <?php if (isLoggedIn() && $_SESSION['user']['user_id'] == ($article->getUserId() ?? null)): ?>
                                                    <a href="?action=update&id=<?php echo urlencode($article->getId()); ?>"
                                                        class="btn btn-sm btn-warning">Actualitzar</a>
                                                    <?php
                                                    $aid = htmlspecialchars($article->getId(), ENT_QUOTES);
                                                    $atitle = htmlspecialchars($article->getTitol(), ENT_QUOTES);
                                                    ?>
                                                    <a href="?action=delete&id=<?php echo urlencode($article->getId()); ?>"
                                                        class="btn btn-sm btn-danger delete-link" data-id="<?php echo $aid; ?>"
                                                        data-title="<?php echo $atitle; ?>">
                                                        Eliminar
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        <!-- Div amb controls anterior/següent i selector per pàgines -->
        <div class="paginacion">
            <div id="pagination-controls" class="clearfix">
                <ul class="pagination">
                    <?php
                    $currentPage = $currentPage ?? 1;
                    $totalPages = $totalPages ?? 1;
                    $perPage = $perPage ?? 6;

                    // Enllaços prev/next simples
                    $prev = max(1, $currentPage - 1);
                    $next = min($totalPages, $currentPage + 1);

                    // Paràmetres per construir URL (mantenir action i per_page)
                    $baseParams = 'action=menu&per_page=' . $perPage;

                    // Botó "Primer"
                    $firstDisabled = ($currentPage <= 1) ? 'disabled' : '';
                    ?>
                    <li class="page-item <?php echo $firstDisabled; ?>">
                        <a class="page-link"
                            href="<?php echo ($currentPage > 1) ? '?' . $baseParams . '&page=1' : '#'; ?>">Inici</a>
                    </li>

                    <li class="page-item <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>">
                        <a class="page-link"
                            href="<?php echo ($currentPage > 1) ? '?' . $baseParams . '&page=' . $prev : '#'; ?>">Anterior</a>
                    </li>

                    <?php
                    // Generar enllaços numèrics amb màxim 5 visibles i "..." si cal
                    $maxLinks = 5;
                    if ($totalPages <= $maxLinks) {
                        $start = 1;
                        $end = $totalPages;
                    } else {
                        $half = floor($maxLinks / 2);
                        $start = max(1, $currentPage - $half);
                        $end = $start + $maxLinks - 1;
                        if ($end > $totalPages) {
                            $end = $totalPages;
                            $start = $totalPages - $maxLinks + 1;
                        }
                    }

                    // Si hi ha espai abans mostrar "1" i "..."
                    if ($start > 2) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }

                    for ($p = $start; $p <= $end; $p++) {
                        $active = ($p == $currentPage) ? 'active' : '';
                        echo '<li class="page-item ' . $active . '"><a class="page-link" href="' . (($p == $currentPage) ? '#' : '?' . $baseParams . '&page=' . $p) . '">' . $p . '</a></li>';
                    }

                    // Si hi ha espai després, mostrar últim
                    if ($end < $totalPages - 1) {
                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }
                    ?>

                    <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link"
                            href="<?php echo ($currentPage < $totalPages) ? '?' . $baseParams . '&page=' . $next : '#'; ?>">Següent</a>
                    </li>

                    <li class="page-item <?php echo ($currentPage >= $totalPages) ? 'disabled' : ''; ?>">
                        <a class="page-link"
                            href="<?php echo ($currentPage < $totalPages) ? '?' . $baseParams . '&page=' . $totalPages : '#'; ?>">Final</a>
                    </li>
                </ul>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                // Activar tooltip
                $('[data-toggle="tooltip"]').tooltip();

                // Confirmación antes de eliminar un artículo (registrado una sola vez)
                $(document).on('click', '.delete-link', function (e) {
                    e.preventDefault();
                    var $a = $(this);
                    var title = $a.data('title') || '';
                    var id = $a.data('id') || '';
                    var msg = 'Seguro que quieres eliminar el articulo ' + title + ' con id ' + id + '?';
                    if (confirm(msg)) {
                        window.location = $a.attr('href');
                    }
                });
            });
        </script>
    </div>
    </div>

</body>

<footer>
    <?php
    require_once __DIR__ . '/./globals/footer.php';
    ?>
</footer>

</html>