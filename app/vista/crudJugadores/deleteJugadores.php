<!--
    Delete Jugador view
    Autor: Arnau Aumedes Jimenez
    Fitxer: app/vista/crudJugadores/deleteJugadores.php
-->
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Jugador</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="/practicas/public/css/style.css">
</head>
<body>
    <div class="container mt-5">
        <form method="GET" action="/practicas/public/index.php" class="form-create">
            <h1>Eliminar Jugador</h1>
            <input type="hidden" name="action" value="deleteJugador">
            <div class="form-group">
                <label for="id">ID del jugador a eliminar:</label>
                <input type="number" name="id" id="id" class="form-control" required>
            </div>
            <div class="d-flex justify-content-center gap-2 mt-3">
                <button type="submit" class="btn btn-danger" onclick="return confirm('¿Estás seguro que quieres eliminar este jugador?')">
                    Eliminar Jugador
                </button>
                <a href="/practicas/public/index.php" class="btn btn-secondary">Volver al menú</a>
            </div>
        </form>
    </div>
</body>
</html>
