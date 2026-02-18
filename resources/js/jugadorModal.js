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