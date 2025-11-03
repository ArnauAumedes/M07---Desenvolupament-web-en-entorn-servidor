<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Article</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/practicas/Pràctica 03 - Paginació/public/css/style.css">
</head>

<body>
    <div class="container mt-5">
        <h1>Crear Nou Article</h1>
        <form method="POST" action="/practicas/Pràctica 03 - Paginació/public/index.php?action=create">
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" name="dni" id="dni" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="titol">Títol:</label>
                <input type="text" name="titol" id="titol" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="cos">Cos:</label>
                <textarea name="cos" id="cos" class="form-control" rows="5" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Crear Article</button>
            <a href="/practicas/Pràctica 03 - Paginació/public/index.php" class="btn btn-secondary">Tornar al
                menú</a>
        </form>
    </div>
</body>

</html>