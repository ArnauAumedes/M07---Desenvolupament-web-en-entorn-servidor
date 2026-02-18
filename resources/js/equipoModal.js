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