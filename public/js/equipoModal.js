/**
 * equipoModal.js
 * Script para manejar la apertura del modal de equipo al hacer clic en una fila de la tabla de clasificación, cargando el contenido mediante AJAX
 * Autor: Arnau Aumedes Jimenez
 */
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.tabla-clasificacion tr[data-equipo-id]').forEach(function(row) {
    row.addEventListener('click', function() {
      const equipoId = row.getAttribute('data-equipo-id');
      fetch('index.php?action=view&id=' + encodeURIComponent(equipoId) + '&ajax=1')
        .then(res => res.text())
        .then(html => {
          document.getElementById('equipoModalBody').innerHTML = html;
          var modal = new bootstrap.Modal(document.getElementById('equipoModal'));
          modal.show();
        });
    });
  });
});