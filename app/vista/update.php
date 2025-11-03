<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualitzar Article</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/practicas/Pràctica 03 - Paginació/public/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Actualitzar Article</h1>

        <!-- Formulario de búsqueda por ID (siempre visible) -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Buscar Article per ID</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="/practicas/Pràctica 03 - Paginació/public/index.php">
                    <input type="hidden" name="action" value="update">
                    <div class="form-group">
                        <label for="id">ID de l'article a actualitzar:</label>
                        <input type="number" name="id" id="id" class="form-control" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Cercar Article</button>
                    <a href="/practicas/Pràctica 03 - Paginació/public/index.php" class="btn btn-secondary">Tornar al menú</a>
                </form>
            </div>
        </div>

        <!-- Missatge  -->
        <?php if (isset($message) && !empty($message)): ?>
            <div class="alert alert-warning"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    
        <!-- Formulari d'actualització (només es mostra si s'ha trobat l'article) -->
        <?php if (isset($article) && $article): ?>
            <div class="card">
                <div class="card-header">
                    <h3>Editar Article</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/practicas/Pràctica 03 - Paginació/public/index.php?action=update&id=<?php echo urlencode($article->getId()); ?>">
                        <div class="form-group">
                            <label for="id_display">ID:</label>
                            <input type="text" id="id_display" class="form-control" value="<?php echo htmlspecialchars($article->getId()); ?>" readonly>
                        </div>

                        <div class="form-group">
                            <label for="dni_update">DNI:</label>
                            <input type="text" name="dni" id="dni_update" class="form-control" value="<?php echo htmlspecialchars($article->getDni()); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="titol">Títol:</label>
                            <input type="text" name="titol" id="titol" class="form-control" value="<?php echo htmlspecialchars($article->getTitol()); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="cos">Cos:</label>
                            <textarea name="cos" id="cos" class="form-control" rows="5" required><?php echo htmlspecialchars($article->getCos()); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Actualitzar Article</button>
                        <a href="/practicas/Pràctica 03 - Paginació/public/index.php" class="btn btn-secondary">Cancel·lar</a>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>