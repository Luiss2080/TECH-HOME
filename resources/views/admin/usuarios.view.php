<?php
$title = $title ?? 'Gestión de Usuarios';
$usuarios = $usuarios ?? [];
$roles = $roles ?? [];
?>

<!-- Estilos específicos para usuarios -->
<link rel="stylesheet" href="<?= asset('css/CRUD/index.css'); ?>">

<!-- Gestión de Usuarios -->
<div class="section-card">
    <div class="section-header">
        <div class="section-header-content">
            <h2 class="section-title">
                <i class="fas fa-users"></i>
                Gestión de Usuarios
            </h2>
            <p class="section-subtitle">Administra los usuarios del sistema y sus roles asignados</p>
        </div>
        <div class="section-header-actions">
            <a href="<?= route('usuarios.crear'); ?>" class="section-action-header">
                <i class="fas fa-user-plus"></i>
                Crear Nuevo Usuario
            </a>
        </div>
    </div>
</div>

<!-- Filtros de Búsqueda -->
<div class="section-card">
    <h5 class="mb-3">
        <i class="fas fa-filter"></i>
        Filtros de Búsqueda
    </h5>
    <div class="filters-container">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="filterName">Buscar por nombre:</label>
                    <input type="text" class="form-control" id="filterName" placeholder="Filtrar por nombre o email...">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="filterRole">Filtrar por rol:</label>
                    <select class="form-control" id="filterRole">
                        <option value="">Todos los roles</option>
                        <option value="administrador">Administrador</option>
                        <option value="docente">Docente</option>
                        <option value="estudiante">Estudiante</option>
                        <option value="invitado">Invitado</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="filterStatus">Filtrar por estado:</label>
                    <select class="form-control" id="filterStatus">
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="clearFilters()">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DEBUG: Verificar mensajes flash -->
<?php 
error_log("DEBUG usuarios.view - flashGet('error'): " . (flashGet('error') ?? 'NULL'));
error_log("DEBUG usuarios.view - flashGet('success'): " . (flashGet('success') ?? 'NULL'));
?>

<!-- Mensajes de éxito/error -->
<?php if (flashGet('success')): ?>
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <i class="fas fa-check-circle"></i>
        <?= htmlspecialchars(flashGet('success')) ?>
    </div>
<?php endif; ?>

<?php if (flashGet('error')): ?>
    <div class="alert alert-danger alert-dismissible" style="background-color: #f8d7da !important; border-color: #f5c6cb !important; color: #721c24 !important; padding: 15px !important; margin: 20px 0 !important;">
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <i class="fas fa-exclamation-triangle"></i>
        <strong>ERROR:</strong> <?= htmlspecialchars(flashGet('error')) ?>
    </div>
<?php endif; ?>

<!-- Tabla de Usuarios -->
<div class="section-card">
    <div class="table-container">
        <?php if (!empty($usuarios)): ?>
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Estado</th>
                        <th>Último Acceso</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario):
                        $usuario = new \App\Models\User($usuario);
                        ?>
                        <tr data-user-id="<?= $usuario->id ?>">
                            <td><?= $usuario->id ?></td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?php if (!empty($usuario->avatar)): ?>
                                            <img src="<?= asset('imagenes/avatars/' . $usuario->avatar) ?>" alt="Avatar">
                                        <?php else: ?>
                                            <div class="avatar-placeholder">
                                                <?= strtoupper(substr($usuario->nombre, 0, 1) . substr($usuario->apellido ?? '', 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="user-details">
                                        <span class="user-name"><?= htmlspecialchars($usuario->nombre . ' ' . ($usuario->apellido ?? '')) ?></span>
                                        <span class="user-phone"><?= htmlspecialchars($usuario->telefono ?? 'Sin teléfono') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($usuario->email) ?></td>
                            <td>
                                <div class="user-roles">
                                    <?php 
                                    try {
                                        $userRoles = $usuario->roles();
                                        if (!empty($userRoles)):
                                            foreach ($userRoles as $role): ?>
                                                <span class="badge badge-role"><?= htmlspecialchars($role['nombre']) ?></span>
                                            <?php endforeach;
                                        else: ?>
                                            <span class="badge badge-warning">Sin rol</span>
                                        <?php endif;
                                    } catch (Exception $e) { ?>
                                        <span class="badge badge-warning">Error</span>
                                    <?php } ?>
                                </div>
                            </td>
                            <td>
                                <?php if ($usuario->estado == 1): ?>
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i>
                                        Activo
                                    </span>
                                <?php else: ?>
                                    <span class="badge badge-danger">
                                        <i class="fas fa-times-circle"></i>
                                        Inactivo
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-muted">
                                <?php 
                                // Aquí puedes agregar lógica para mostrar último acceso si tienes esa información
                                echo 'Sin datos';
                                ?>
                            </td>
                            <td class="text-muted">
                                <?php 
                                $fecha = $usuario->fecha_creacion ?? date('Y-m-d H:i:s');
                                echo date('d/m/Y H:i', strtotime($fecha));
                                ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= route('usuarios.editar', ['id' => $usuario->id]) ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Editar Usuario">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= route('usuarios.roles', ['id' => $usuario->id]) ?>" 
                                       class="btn btn-sm btn-outline-info" title="Asignar Roles">
                                        <i class="fas fa-user-shield"></i>
                                    </a>
                                    <a href="<?= route('usuarios.permisos', ['id' => $usuario->id]) ?>" 
                                       class="btn btn-sm btn-outline-warning" title="Gestionar Permisos">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm <?= $usuario->estado == 1 ? 'btn-outline-warning' : 'btn-outline-success' ?> btn-toggle-status" 
                                            data-user-id="<?= $usuario->id ?>" 
                                            data-user-name="<?= htmlspecialchars($usuario->nombre . ' ' . ($usuario->apellido ?? '')) ?>"
                                            data-current-status="<?= $usuario->estado ?>"
                                            data-status-url="<?= route('usuarios.estado', ['id' => $usuario->id]) ?>"
                                            title="<?= $usuario->estado == 1 ? 'Desactivar' : 'Activar' ?> Usuario">
                                        <i class="fas fa-<?= $usuario->estado == 1 ? 'ban' : 'check' ?>"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger btn-delete-user" 
                                            data-user-id="<?= $usuario->id ?>" 
                                            data-user-name="<?= htmlspecialchars($usuario->nombre . ' ' . ($usuario->apellido ?? '')) ?>"
                                            data-delete-url="<?= route('usuarios.delete', ['id' => $usuario->id]) ?>"
                                            title="Eliminar Usuario">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>No hay usuarios registrados</h3>
                <p>Comienza creando el primer usuario del sistema</p>
                <a href="<?= route('usuarios.crear'); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Crear Primer Usuario
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Estadísticas de Usuarios -->
<div class="section-card">
    <h3 class="section-title">
        <i class="fas fa-chart-bar"></i>
        Estadísticas de Usuarios
    </h3>
    <div class="stats-grid">
        <div class="stat-item">
            <div class="stat-icon bg-blue">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h4>Total Usuarios</h4>
                <span class="stat-number"><?= count($usuarios) ?></span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon bg-green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h4>Usuarios Activos</h4>
                <span class="stat-number">
                    <?= count(array_filter($usuarios, fn($u) => $u['estado'] == 1)) ?>
                </span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon bg-yellow">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="stat-content">
                <h4>Registros Hoy</h4>
                <span class="stat-number">
                    <?= count(array_filter($usuarios, fn($u) => date('Y-m-d', strtotime($u->fecha_creacion ?? '1970-01-01')) === date('Y-m-d'))) ?>
                </span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon bg-red">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <h4>Usuarios Inactivos</h4>
                <span class="stat-number">
                    <?= count(array_filter($usuarios, fn($u) => $u['estado'] == 0)) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="deleteUserForm" method="POST" action="">
                <?= CSRF() ?>
                <input type="hidden" name="_method" value="DELETE">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteUserModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Confirmar Eliminación de Usuario
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="text-center py-3">
                        <div class="mb-4">
                            <div class="warning-icon">
                                <i class="fas fa-user-times"></i>
                            </div>
                        </div>
                        <h4 class="mb-3">¿Estás seguro de eliminar este usuario?</h4>
                        <p class="text-muted mb-2">Esta acción no se puede deshacer.</p>
                        <div class="user-to-delete-info">
                            <strong>Usuario: <span id="userNameToDelete" class="text-danger"></span></strong>
                        </div>
                    </div>
                    <div class="alert alert-danger mt-3" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <small>
                            <strong>¡Atención!</strong> El usuario será eliminado permanentemente junto con:
                            <ul class="mt-2 mb-0">
                                <li>Todos sus datos personales</li>
                                <li>Sus asignaciones de roles</li>
                                <li>Su historial en el sistema</li>
                            </ul>
                        </small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-user-times me-1"></i>
                        Eliminar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Cambio de Estado -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1" role="dialog" aria-labelledby="toggleStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="toggleStatusForm" method="POST" action="">
                <?= CSRF() ?>
                
                <div class="modal-header">
                    <h5 class="modal-title" id="toggleStatusModalLabel">
                        <i class="fas fa-user-cog text-info me-2"></i>
                        Cambiar Estado del Usuario
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="text-center py-3">
                        <div class="mb-4">
                            <div class="status-icon">
                                <i class="fas fa-user-cog"></i>
                            </div>
                        </div>
                        <h4 class="mb-3" id="statusModalTitle">¿Cambiar estado del usuario?</h4>
                        <div class="user-status-info">
                            <strong>Usuario: <span id="userNameToToggle" class="text-primary"></span></strong>
                            <p class="mt-2" id="statusModalDescription">El usuario será activado/desactivado en el sistema.</p>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="confirmStatusToggle">
                        <i class="fas fa-check me-1"></i>
                        Confirmar Cambio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Overlay personalizado para mayor compatibilidad -->
<div class="custom-modal-overlay" id="customModalOverlay" style="display: none;"></div>

<!-- JavaScript específico para usuarios -->
<script src="<?= asset('js/admin/usuarios.js'); ?>"></script>

<script>
// Funcionalidad de filtros
document.addEventListener('DOMContentLoaded', function() {
    const filterName = document.getElementById('filterName');
    const filterRole = document.getElementById('filterRole');
    const filterStatus = document.getElementById('filterStatus');
    const tableRows = document.querySelectorAll('.data-table tbody tr');

    function applyFilters() {
        const nameFilter = filterName.value.toLowerCase();
        const roleFilter = filterRole.value.toLowerCase();
        const statusFilter = filterStatus.value;

        tableRows.forEach(row => {
            const nameCell = row.querySelector('.user-name').textContent.toLowerCase();
            const emailCell = row.cells[2].textContent.toLowerCase();
            const roleCell = row.querySelector('.user-roles').textContent.toLowerCase();
            const statusCell = row.querySelector('.badge-success, .badge-danger');
            const userStatus = statusCell && statusCell.classList.contains('badge-success') ? '1' : '0';

            let showRow = true;

            // Filtro por nombre/email
            if (nameFilter && !nameCell.includes(nameFilter) && !emailCell.includes(nameFilter)) {
                showRow = false;
            }

            // Filtro por rol
            if (roleFilter && !roleCell.includes(roleFilter)) {
                showRow = false;
            }

            // Filtro por estado
            if (statusFilter && userStatus !== statusFilter) {
                showRow = false;
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    // Event listeners para los filtros
    filterName.addEventListener('keyup', applyFilters);
    filterRole.addEventListener('change', applyFilters);
    filterStatus.addEventListener('change', applyFilters);
});

function clearFilters() {
    document.getElementById('filterName').value = '';
    document.getElementById('filterRole').value = '';
    document.getElementById('filterStatus').value = '';
    
    // Mostrar todas las filas
    document.querySelectorAll('.data-table tbody tr').forEach(row => {
        row.style.display = '';
    });
}
</script>