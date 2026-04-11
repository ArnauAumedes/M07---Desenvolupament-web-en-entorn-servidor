<!-- 
pagination.php
Vista para mostrar la paginación de la lista de equipos, con opciones para cambiar el número de elementos por página
Autor: Arnau Aumedes Jimenez 
-->

<div class="d-flex justify-content-center align-items-center flex-wrap gap-3 py-3" style="gap: 1.5rem;">
    <?php
    // Asegurarse de que las variables estén definidas
    if (!isset($page) || !is_numeric($page)) {
        $page = 1;
    }
    // Solo permitir valores válidos para 'limit'. Si no es válido, se asigna 10.
    $validLimits = [1, 5, 10, 20];
    if (!isset($limit) || !is_numeric($limit) || !in_array((int) $limit, $validLimits)) {
        $limit = 10;
    }
    if (!isset($totalPages) || !is_numeric($totalPages)) {
        $totalPages = 1;
    }

    // Mantener el parámetro action en la URL
    $action = isset($_GET['action']) ? urlencode($_GET['action']) : 'list';
    ?>
    <form method="get" class="mb-0 d-flex align-items-center bg-light rounded px-3 py-2 shadow-sm" style="gap:0.5rem;">
        <input type="hidden" name="action" value="<?= $action ?>">
        <label for="limit" class="mb-0 mr-2">Mostrar</label>
        <select name="limit" id="limit" class="form-control form-control-sm" onchange="this.form.submit()">
            <option value="1" <?= $limit == 1 ? 'selected' : '' ?>>1</option>
            <option value="5" <?= $limit == 5 ? 'selected' : '' ?>>5</option>
            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
            <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20</option>
        </select>
        <span class="ml-2">por página</span>
    </form>
    <nav>
        <ul class="pagination mb-0">
            <!-- Ir al inicio -->
            <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                <a class="page-link" href="<?= $page > 1 ? "?action=$action&page=1&limit=$limit" : '#' ?>">&laquo;</a>
            </li>
            <!-- Anterior -->
            <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
                <a class="page-link"
                    href="<?= $page > 1 ? "?action=$action&page=" . ($page - 1) . "&limit=$limit" : '#' ?>">&lt;</a>
            </li>
            <!-- Paginación numerada (puedes ajustar el rango visible aquí si lo deseas) -->
            <?php
            // Mostrar hasta 5 páginas centradas en la actual
            $visiblePages = 5;
            $start = max(1, $page - floor($visiblePages / 2));
            $end = min($totalPages, $start + $visiblePages - 1);
            if ($end - $start < $visiblePages - 1) {
                $start = max(1, $end - $visiblePages + 1);
            }
            // Mostrar ... antes si hay páginas ocultas al inicio
            if ($start > 1) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            for ($i = $start; $i <= $end; $i++): ?>
                <li class="page-item<?= $i == $page ? ' active' : '' ?>">
                    <?php if ($i == $page): ?>
                        <span class="page-link bg-primary text-white border-primary"><?= $i ?></span>
                    <?php else: ?>
                        <a class="page-link" href="?action=<?= $action ?>&page=<?= $i ?>&limit=<?= $limit ?>"><?= $i ?></a>
                    <?php endif; ?>
                </li>
            <?php endfor;
            // Mostrar ... después si hay páginas ocultas al final
            if ($end < $totalPages) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            ?>
            <!-- Siguiente -->
            <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                <a class="page-link"
                    href="<?= $page < $totalPages ? "?action=$action&page=" . ($page + 1) . "&limit=$limit" : '#' ?>">&gt;</a>
            </li>
            <!-- Ir al final -->
            <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
                <a class="page-link"
                    href="<?= $page < $totalPages ? "?action=$action&page=$totalPages&limit=$limit" : '#' ?>">&raquo;</a>
            </li>
        </ul>
    </nav>
</div>