<?php
if (!isset($page)) {
    $page = isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 0 ? (int) $_GET['page'] : 1;
}
if (!isset($limit)) {
    $validLimits = [1, 5, 10, 20];
    $limit = (isset($_GET['limit']) && in_array((int)$_GET['limit'], $validLimits)) ? (int)$_GET['limit'] : 10;
}
if (!isset($jugadorDAO) || !isset($equipoDAO)) {
    require_once __DIR__ . '/../../model/database/database.php';
    require_once __DIR__ . '/../../model/dao/JugadorDAO.php';
    require_once __DIR__ . '/../../model/dao/EquipoDAO.php';
    $db = new Database();
    if (!isset($jugadorDAO)) {
        $jugadorDAO = new JugadorDAO($db->getConnection());
    }
    if (!isset($equipoDAO)) {
        $equipoDAO = new EquipoDAO($db->getConnection());
    }
}
if (!isset($offset)) {
    $offset = ($page - 1) * $limit;
}
$totalJugadores = $jugadorDAO->countJugadores();
$totalPages = (int) ceil($totalJugadores / $limit);

// Obtener jugadores paginados y ordenarlos por suma de goles+asistencias
$jugadores = $jugadorDAO->getJugadoresPaginados($limit, $offset);
if (!function_exists('ordenarPorValorPagination')) {
    function ordenarPorValorPagination($jugadores, $jugadorDAO)
    {
        return $jugadorDAO->ordenarPorValor(
            $jugadores,
            function ($jugador) use ($jugadorDAO) {
                return $jugadorDAO->getSumaGolesAsistencias($jugador->getId());
            },
            'desc'
        );
    }
}
$jugadores = ordenarPorValorPagination($jugadores, $jugadorDAO);
?>