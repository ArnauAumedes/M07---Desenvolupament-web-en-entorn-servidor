<!-- Script para Bootstrap (JavaScript Bundle con Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Modal Bootstrap para el detalle del User -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="userModalLabel">Detalle del User</h2>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
            <div class="modal-body" id="userModalBody">
                <!-- Aquí se cargará el contenido AJAX -->
            </div>
        </div>
    </div>
</div>