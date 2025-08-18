<div class="dashboard-content">
    <div class="section-card">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="h3 mb-0">Gestión de Usuarios</h1>
                    <?php if (auth()->can('admin.usuarios.crear')): ?>
                        <a href="<?= route('usuarios.crear') ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nuevo Usuario
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Alertas -->
        <?php if (session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Filtros -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" id="filtro-nombre" placeholder="Buscar por nombre...">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filtro-rol">
                            <option value="">Todos los roles</option>
                            <option value="administrador">Administrador</option>
                            <option value="docente">Docente</option>
                            <option value="estudiante">Estudiante</option>
                            <option value="vendedor">Vendedor</option>
                            <option value="invitado">Invitado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" id="filtro-estado">
                            <option value="">Todos los estados</option>
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" id="limpiar-filtros">
                            <i class="fas fa-filter"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de Usuarios -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="tabla-usuarios">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Avatar</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Teléfono</th>
                                <th>Roles</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($usuarios) && !empty($usuarios)): ?>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr data-usuario-id="<?= $usuario['id'] ?>">
                                        <td><?= $usuario['id'] ?></td>
                                        <td>
                                            <img src="<?= $usuario['avatar'] ? asset('imagenes/avatars/' . $usuario['avatar']) : asset('imagenes/avatar-default.png') ?>"
                                                alt="Avatar" class="rounded-circle" width="40" height="40">
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?></strong>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($usuario['email']) ?></td>
                                        <td><?= htmlspecialchars($usuario['telefono'] ?? 'No especificado') ?></td>
                                        <td>
                                            <?php if ($usuario['roles_nombres']): ?>
                                                <?php $roles = explode(', ', $usuario['roles_nombres']); ?>
                                                <?php foreach ($roles as $rol): ?>
                                                    <span class="badge bg-primary me-1"><?= htmlspecialchars($rol) ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin roles</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $usuario['estado'] == 1 ? 'success' : 'danger' ?>">
                                                <?= $usuario['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <?php if (auth()->can('admin.usuarios.editar')): ?>
                                                    <a href="<?= route('usuarios.editar', ['id' => $usuario['id']]) ?>"
                                                        class="btn btn-sm btn-outline-primary" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (auth()->can('admin.usuarios.editar')): ?>
                                                    <button class="btn btn-sm btn-outline-<?= $usuario['estado'] == 1 ? 'warning' : 'success' ?>"
                                                        onclick="cambiarEstado(<?= $usuario['id'] ?>, '<?= $usuario['estado'] == 1 ? 'inactivo' : 'activo' ?>')"
                                                        title="<?= $usuario['estado'] == 1 ? 'Desactivar' : 'Activar' ?>">
                                                        <i class="fas fa-<?= $usuario['estado'] == 1 ? 'ban' : 'check' ?>"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if (auth()->can('admin.usuarios.eliminar') && $usuario['id'] != auth()->id): ?>
                                                    <button class="btn btn-sm btn-outline-danger"
                                                        onclick="eliminarUsuario(<?= $usuario['id'] ?>, '<?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>')"
                                                        title="Eliminar">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-users fa-3x mb-3"></i>
                                            <p>No hay usuarios registrados</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres eliminar al usuario <strong id="nombre-usuario-eliminar"></strong>?</p>
                <p class="text-danger"><small>Esta acción no se puede deshacer.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmar-eliminar">Eliminar Usuario</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables globales
    let usuarioIdEliminar = null;

    // Filtros
    document.getElementById('filtro-nombre').addEventListener('input', filtrarTabla);
    document.getElementById('filtro-rol').addEventListener('change', filtrarTabla);
    document.getElementById('filtro-estado').addEventListener('change', filtrarTabla);
    document.getElementById('limpiar-filtros').addEventListener('click', limpiarFiltros);

    function filtrarTabla() {
        const filtroNombre = document.getElementById('filtro-nombre').value.toLowerCase();
        const filtroRol = document.getElementById('filtro-rol').value.toLowerCase();
        const filtroEstado = document.getElementById('filtro-estado').value.toLowerCase();

        const filas = document.querySelectorAll('#tabla-usuarios tbody tr');

        filas.forEach(fila => {
            const nombre = fila.cells[2].textContent.toLowerCase();
            const roles = fila.cells[5].textContent.toLowerCase();
            const estado = fila.cells[6].textContent.toLowerCase();

            const coincideNombre = !filtroNombre || nombre.includes(filtroNombre);
            const coincideRol = !filtroRol || roles.includes(filtroRol);
            const coincideEstado = !filtroEstado || estado.includes(filtroEstado);

            fila.style.display = coincideNombre && coincideRol && coincideEstado ? '' : 'none';
        });
    }

    function limpiarFiltros() {
        document.getElementById('filtro-nombre').value = '';
        document.getElementById('filtro-rol').value = '';
        document.getElementById('filtro-estado').value = '';
        filtrarTabla();
    }

    function cambiarEstado(userId, nuevoEstado) {
        if (!confirm(`¿Confirmar cambio de estado a ${nuevoEstado}?`)) return;

        fetch(`<?= route('usuarios.estado', ['id' => ':id']) ?>`.replace(':id', userId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    estado: nuevoEstado
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cambiar el estado del usuario');
            });
    }

    function eliminarUsuario(userId, nombreUsuario) {
        usuarioIdEliminar = userId;
        document.getElementById('nombre-usuario-eliminar').textContent = nombreUsuario;

        const modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
        modal.show();
    }

    document.getElementById('confirmar-eliminar').addEventListener('click', function() {
        if (!usuarioIdEliminar) return;

        fetch(`<?= route('usuarios.delete', ['id' => ':id']) ?>`.replace(':id', usuarioIdEliminar), {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al eliminar el usuario');
            })
            .finally(() => {
                bootstrap.Modal.getInstance(document.getElementById('modalEliminar')).hide();
                usuarioIdEliminar = null;
            });
    });
</script>

</div>