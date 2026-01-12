<div class="d-flex justify-content-center align-items-center flex-wrap gap-3 py-3" style="gap: 1.5rem;">
    <?php
    // Asegurarse de que las variables estén definidas
    if (!isset($page) || !is_numeric($page)) { $page = 1; }
    // Solo permitir valores válidos para 'limit'. Si no es válido, se asigna 10.
    $validLimits = [1, 5, 10, 20];
    if (!isset($limit) || !is_numeric($limit) || !in_array((int)$limit, $validLimits)) {
        $limit = 10;
    }
    if (!isset($totalPages) || !is_numeric($totalPages)) { $totalPages = 1; }

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
            <!--  Si no es la primera página, mostrar enlace a la anterior -->
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link bg-primary text-white" href="?action=<?= $action ?>&page=<?= $page - 1 ?>&limit=<?= $limit ?>">Anterior</a>
                </li>
            <?php endif; ?>
            <!--  Enlaces a las páginas -->
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                    <a class="page-link<?= $i === $page ? ' bg-primary text-white border-primary' : '' ?>"
                       href="?action=<?= $action ?>&page=<?= $i ?>&limit=<?= $limit ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <!--  Si no es la última página, mostrar enlace a la siguiente -->
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link bg-primary text-white" href="?action=<?= $action ?>&page=<?= $page + 1 ?>&limit=<?= $limit ?>">Siguiente</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
