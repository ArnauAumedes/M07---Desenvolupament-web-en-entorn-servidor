<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Article</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/practicas/Pràctica 03 - Paginació/public/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Eliminar Article</h1>
    
        <form method="GET" action="/practicas/Pràctica 03 - Paginació/public/index.php">
            <input type="hidden" name="action" value="delete">
            <div class="form-group">
                <label for="id">ID de l'article a eliminar:</label>
                <input type="number" name="id" id="id" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-danger" onclick="return confirm('Estàs segur que vols eliminar aquest article?')">
                Eliminar Article
            </button>
            <a href="/practicas/Pràctica 03 - Paginació/public/index.php" class="btn btn-secondary">Tornar al menú</a>
        </form>
    </div>
</body>
</html>