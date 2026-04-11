<!-- 
searchBar.php
Vista para mostrar la barra de búsqueda de equipos
Autor: Arnau Aumedes Jimenez 
-->
<?php
require_once __DIR__ . '/../../model/components/CookieHelper.php';

$currentSource = CookieHelper::getSourcePreference('source', 'data_source_preference', 'bdd');
?>
<div class="search-bar-component">
    <div style="display: flex; align-items: center; gap: 8px;">
        <select id="source-selector" aria-label="Fuente de datos" style="height: 38px; border-radius: 4px; border: 1px solid #ced4da; padding: 0 8px;">
            <option value="bdd" <?= $currentSource === 'bdd' ? 'selected' : '' ?>>Base de datos</option>
            <option value="api" <?= $currentSource === 'api' ? 'selected' : '' ?>>API</option>
        </select>
        <input type="text" id="search" placeholder="Cerca per nom" autocomplete="off">
        <!-- <button id="search-btn" type="button" style="background: none; border: none; padding: 0; cursor: pointer;">
            <span class="material-icons" style="font-size: 1.7em; color: #22223b;">search</span>
        </button> -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var sourceSelector = document.getElementById('source-selector');
    if (!sourceSelector) {
        return;
    }

    sourceSelector.addEventListener('change', function () {
        var source = sourceSelector.value;
        var url = new URL(window.location.href);
        url.searchParams.set('source', source);
        document.cookie = 'data_source_preference=' + source + ';path=/;max-age=2592000';
        window.location.href = url.toString();
    });
});
</script>