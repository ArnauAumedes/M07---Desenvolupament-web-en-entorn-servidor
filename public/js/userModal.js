/**
 * userModal.js
 * Script para manejar la apertura del modal de usuario al hacer clic en una fila de la tabla de clasificación, cargando el contenido mediante AJAX
 * Autor: Arnau Aumedes Jimenez
 */
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.tabla-clasificacion tr[data-user-id]').forEach(function(row) {
    row.addEventListener('click', function() {
      const userId = row.getAttribute('data-user-id');
      fetch('index.php?action=viewUser&id=' + encodeURIComponent(userId) + '&ajax=1')
        .then(res => res.text())
        .then(html => {
          document.getElementById('userModalBody').innerHTML = html;
          var modal = new bootstrap.Modal(document.getElementById('userModal'));
          modal.show();
        });
    });
  });
});