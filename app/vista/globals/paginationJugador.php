<div class="d-flex justify-content-center align-items-center flex-wrap gap-3 py-3" style="gap: 1.5rem;">
    <?php
    if (!isset($page) || !is_numeric($page)) {
        $page = 1;
    }
    if (!isset($limit) || !is_numeric($limit)) {
        $limit = 10;
    }
    if (!isset($totalPages) || !is_numeric($totalPages)) {
        $totalPages = 1;
    }
    ?>
    <form method="get" class="mb-0 d-flex align-items-center bg-light rounded px-3 py-2 shadow-sm" style="gap:0.5rem;">
        <label for="limit" class="mb-0 font-weight-bold text-secondary" style="font-size:1.05em;">Mostrar</label>
        <select name="limit" id="limit" class="form-control form-control-sm d-inline-block w-auto mx-2 border-primary"
            style="min-width:60px;" onchange="this.form.submit()">
            <?php foreach ([1, 5, 10, 20] as $opt): ?>
                <option value="<?= $opt ?>" <?= $limit == $opt ? ' selected' : '' ?>><?= $opt ?></option>
            <?php endforeach; ?>
        </select>
        <span class="mb-0 text-secondary" style="font-size:1.05em;">por página</span>
        <!-- Mantener otros parámetros de la URL (como page) -->
        <?php foreach ($_GET as $key => $value): ?>
            <?php if ($key !== 'limit'): ?>
                <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endif; ?>
        <?php endforeach; ?>
    </form>
    <nav aria-label="Paginación de mejores valorados" class="mb-0">
        <ul class="pagination mb-0 shadow-sm">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link bg-primary text-white" href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>"
                        aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item<?= $i === $page ? ' active' : '' ?>">
                    <a class="page-link<?= $i === $page ? ' bg-primary text-white border-primary' : '' ?>"
                        href="?page=<?= $i ?>&limit=<?= $limit ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link bg-primary text-white" href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>"
                        aria-label="Siguiente">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>