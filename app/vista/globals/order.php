<form method="get" class="mb-0 d-flex align-items-center bg-light rounded px-3 py-2 shadow-sm" style="gap:0.5rem;">
    <input type="hidden" name="action" value="<?= htmlspecialchars($_GET['action'] ?? 'list') ?>">
    <label for="orderSelect" class="mb-0 mr-2 font-weight-bold text-secondary" style="font-size: 1rem;">
        Ordenar equipos:
    </label>
    <select id="orderSelect" name="order" class="form-control form-control-sm" style="width: 160px;" onchange="this.form.submit()">
        <option value="desc" <?= ($order === 'desc') ? 'selected' : '' ?>>Descendente</option>
        <option value="asc" <?= ($order === 'asc') ? 'selected' : '' ?>>Ascendente</option>
    </select>
</form>