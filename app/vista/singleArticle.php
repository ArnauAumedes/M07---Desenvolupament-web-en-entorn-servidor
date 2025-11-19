<?php
/**
 * singleArticle.php
 * Simple view to display one article (title + body).
 * Espera un objecte $article o variables $title/$body predefinides.
 * Autor: Arnau Aumedes Jimenez
 */

if (isset($article)) {
    $title = $article->getTitol();
    $body = $article->getCos();
    $author = method_exists($article, 'getAuthorName') ? $article->getAuthorName() : null;
    $created = method_exists($article, 'getDataCreacio') ? $article->getDataCreacio() : null;
} else {
    $title = $title ?? 'Sense títol';
    $body = $body ?? '';
    $author = $author ?? '';
    $created = $created ?? null;
}
// Debug: print values to browser console so we can see what PHP retrieved
echo '<script>console.log(' . json_encode(["source" => "singleArticle.php", "title" => $title, "body" => $body, "author" => $author], JSON_UNESCAPED_UNICODE) . ');</script>';
?>
<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo htmlspecialchars($title); ?> - Article</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto|Varela+Round">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="/practicas/Pràctica 03 - Paginació/public/css/style.css">

</head>

<body>
    <header>
        <?php require_once __DIR__ . '/../globals/header.php'; ?>
    </header>

    <div class="container-xl">
        <div class="table-responsive">
            <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                        <div class="col-sm-12">
                            <h2>Article: <b><?php echo htmlspecialchars($title); ?></b></h2>
                        </div>
                    </div>
                </div>
                <header class="single-article-header d-flex justify-content-between align-items-start">
                    <h3 class="single-article-title mb-0"><?php echo htmlspecialchars($title); ?></h3>
                    <time class="single-article-time small text-muted">Data de publicacio: <?php echo htmlspecialchars($created ?? date('Y-m-d')); ?></time>
                </header>
                <?php if (!empty($author)): ?>
                    <div class="single-article-meta small text-muted mt-2">Article creat per: <strong class="single-author-name"><?php echo htmlspecialchars($author); ?></strong></div>
                <?php endif; ?>
                <div class="article-body p-4">
                    <div class="mb-3 text-body">
                        <?php echo nl2br(htmlspecialchars($body)); ?>
                    </div>
                    <div class="text-right">
                        <a href="?action=menu" class="btn btn-outline-secondary">Tornar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

    <footer>
        <?php require_once __DIR__ . '/../globals/footer.php'; ?>
    </footer>

</body>

</html>