/**
 * jugadorModal.js
 * Script para manejar la apertura del modal de jugador al hacer clic en una fila de la tabla de clasificación, cargando el contenido mediante AJAX
 * Autor: Arnau Aumedes Jimenez
 */
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.tabla-clasificacion tr[data-jugador-id]').forEach(function(row) {
    row.addEventListener('click', function() {
      const jugadorId = row.getAttribute('data-jugador-id');
      fetch('index.php?action=viewJugador&id=' + encodeURIComponent(jugadorId) + '&ajax=1')
        .then(res => res.text())
        .then(html => {
          document.getElementById('jugadorModalBody').innerHTML = html;
          var modal = new bootstrap.Modal(document.getElementById('jugadorModal'));
          modal.show();
        });
    });
  });
});