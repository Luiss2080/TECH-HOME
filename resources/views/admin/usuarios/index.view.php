<?php
$title = $title ?? 'Gestión de Usuarios';
$usuarios = $usuarios ?? [];
$roles = $roles ?? [];
?>

<!-- Estilos específicos para el módulo CRUD -->
<link rel="stylesheet" href="<?= asset('css/index.css'); ?>">

<!-- Contenedor principal del CRUD -->
<div class="crud-container">
    <div class="crud-content-wrapper">

        <!-- Header principal con icono y acciones -->
        <div class="crud-section-card">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="crud-section-title-group">
                        <h1 class="crud-section-title">Gestión de Usuarios</h1>
                        <p class="crud-section-subtitle">Administra los usuarios del sistema y sus roles asignados</p>
                    </div>
                </div>
                <div class="crud-section-header-actions">
                    <a href="<?= route('usuarios.crear'); ?>" class="crud-section-action-header">
                        <i class="fas fa-user-plus"></i>
                        Crear Nuevo Usuario
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas de usuarios -->
        <div class="crud-section-card">
            <div class="crud-stats-grid">
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Total Usuarios</h4>
                        <div class="crud-stat-number"><?= count($usuarios) ?></div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Usuarios Activos</h4>
                        <div class="crud-stat-number">
                            <?= count(array_filter($usuarios, fn($u) => $u['estado'] == 1)) ?>
                        </div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-yellow">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Registros Hoy</h4>
                        <div class="crud-stat-number">
                            <?= count(array_filter($usuarios, fn($u) => date('Y-m-d', strtotime($u->fecha_creacion ?? '1970-01-01')) === date('Y-m-d'))) ?>
                        </div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-red">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Usuarios Inactivos</h4>
                        <div class="crud-stat-number">
                            <?= count(array_filter($usuarios, fn($u) => $u['estado'] == 0)) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de búsqueda modernos -->
        <div class="crud-section-card">
            <div class="crud-filters-container">
                <h5 class="crud-filters-title">
                    <i class="fas fa-filter"></i>
                    Filtros de Búsqueda
                </h5>
                <div class="crud-filters-row">
                    <div class="crud-form-group">
                        <label for="crudFilterName">Buscar por nombre:</label>
                        <input type="text" class="crud-form-control" id="crudFilterName" placeholder="Filtrar por nombre o email...">
                    </div>
                    <div class="crud-form-group">
                        <label for="crudFilterRole">Filtrar por rol:</label>
                        <select class="crud-form-control" id="crudFilterRole">
                            <option value="">Todos los roles</option>
                            <option value="administrador">Administrador</option>
                            <option value="docente">Docente</option>
                            <option value="estudiante">Estudiante</option>
                            <option value="invitado">Invitado</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label for="crudFilterStatus">Filtrar por estado:</label>
                        <select class="crud-form-control" id="crudFilterStatus">
                            <option value="">Todos los estados</option>
                            <option value="1">Activos</option>
                            <option value="0">Inactivos</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <button type="button" class="crud-btn crud-btn-clear" onclick="crudClearFilters()">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- DEBUG: Verificar mensajes flash -->
        <?php 
        error_log("DEBUG usuarios.view - flashGet('error'): " . (flashGet('error') ?? 'NULL'));
        error_log("DEBUG usuarios.view - flashGet('success'): " . (flashGet('success') ?? 'NULL'));
        ?>

        <!-- Mensajes de éxito/error modernizados -->
        <?php if (flashGet('success')): ?>
            <div class="crud-alert crud-alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars(flashGet('success')) ?></span>
                <button type="button" class="crud-btn-close" data-bs-dismiss="alert">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (flashGet('error')): ?>
            <div class="crud-alert crud-alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <span><strong>ERROR:</strong> <?= htmlspecialchars(flashGet('error')) ?></span>
                <button type="button" class="crud-btn-close" data-bs-dismiss="alert">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Tabla de usuarios modernizada -->
        <div class="crud-section-card">
            <div class="crud-table-container">
                <?php if (!empty($usuarios)): ?>
                    <table class="crud-data-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-user"></i> USUARIO</th>
                                <th><i class="fas fa-envelope"></i> EMAIL</th>
                                <th><i class="fas fa-user-shield"></i> ROLES</th>
                                <th><i class="fas fa-toggle-on"></i> ESTADO</th>
                                <th><i class="fas fa-clock"></i> ÚLTIMO ACCESO</th>
                                <th><i class="fas fa-calendar"></i> REGISTRO</th>
                                <th><i class="fas fa-cogs"></i> ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario):
                                $usuario = new \App\Models\User($usuario);
                                ?>
                                <tr data-user-id="<?= $usuario->id ?>" class="crud-table-row">
                                    <td class="crud-text-muted">#<?= $usuario->id ?></td>
                                    <td>
                                        <div class="crud-user-info">
                                            <div class="crud-user-avatar">
                                                <?php if (!empty($usuario->avatar)): ?>
                                                    <img src="<?= asset('imagenes/avatars/' . $usuario->avatar) ?>" alt="Avatar">
                                                <?php else: ?>
                                                    <div class="crud-avatar-placeholder">
                                                        <?= strtoupper(substr($usuario->nombre, 0, 1) . substr($usuario->apellido ?? '', 0, 1)) ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="crud-user-details">
                                                <div class="crud-user-name"><?= htmlspecialchars($usuario->nombre . ' ' . ($usuario->apellido ?? '')) ?></div>
                                                <div class="crud-user-phone crud-text-muted"><?= htmlspecialchars($usuario->telefono ?? 'Sin teléfono') ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="mailto:<?= htmlspecialchars($usuario->email) ?>" class="crud-text-info">
                                            <?= htmlspecialchars($usuario->email) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <div class="crud-user-roles">
                                            <?php 
                                            try {
                                                $userRoles = $usuario->roles();
                                                if (!empty($userRoles)):
                                                    foreach ($userRoles as $role): ?>
                                                        <span class="crud-badge crud-badge-role"><?= htmlspecialchars($role['nombre']) ?></span>
                                                    <?php endforeach;
                                                else: ?>
                                                    <span class="crud-badge crud-badge-warning">Sin rol</span>
                                                <?php endif;
                                            } catch (Exception $e) { ?>
                                                <span class="crud-badge crud-badge-warning">Error</span>
                                            <?php } ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($usuario->estado == 1): ?>
                                            <span class="crud-badge crud-badge-success">
                                                <i class="fas fa-check-circle"></i>
                                                Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="crud-badge crud-badge-danger">
                                                <i class="fas fa-times-circle"></i>
                                                Inactivo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="crud-text-muted">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-clock"></i>
                                            <span>Sin datos</span>
                                        </div>
                                    </td>
                                    <td class="crud-text-muted">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-calendar-plus"></i>
                                            <span>
                                                <?php 
                                                $fecha = $usuario->fecha_creacion ?? date('Y-m-d H:i:s');
                                                echo date('d/m/Y H:i', strtotime($fecha));
                                                ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="crud-action-buttons">
                                            <a href="<?= route('usuarios.editar', ['id' => $usuario->id]) ?>" 
                                               class="crud-btn-sm crud-btn-outline-primary" 
                                               title="Editar Usuario">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= route('usuarios.roles', ['id' => $usuario->id]) ?>" 
                                               class="crud-btn-sm crud-btn-outline-info" 
                                               title="Asignar Roles">
                                                <i class="fas fa-user-shield"></i>
                                            </a>
                                            <a href="<?= route('usuarios.permisos', ['id' => $usuario->id]) ?>" 
                                               class="crud-btn-sm crud-btn-outline-warning" 
                                               title="Gestionar Permisos">
                                                <i class="fas fa-key"></i>
                                            </a>
                                            <button type="button" 
                                                    class="crud-btn-sm <?= $usuario->estado == 1 ? 'crud-btn-outline-warning' : 'crud-btn-outline-success' ?> crud-btn-toggle-status" 
                                                    data-user-id="<?= $usuario->id ?>" 
                                                    data-user-name="<?= htmlspecialchars($usuario->nombre . ' ' . ($usuario->apellido ?? '')) ?>"
                                                    data-current-status="<?= $usuario->estado ?>"
                                                    data-status-url="<?= route('usuarios.estado', ['id' => $usuario->id]) ?>"
                                                    title="<?= $usuario->estado == 1 ? 'Desactivar' : 'Activar' ?> Usuario">
                                                <i class="fas fa-<?= $usuario->estado == 1 ? 'ban' : 'check' ?>"></i>
                                            </button>
                                            <button type="button" 
                                                    class="crud-btn-sm crud-btn-outline-danger crud-btn-delete-user" 
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
                    <div class="crud-empty-state">
                        <div class="crud-empty-state-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>No hay usuarios registrados</h3>
                        <p>Comienza creando el primer usuario del sistema para empezar a gestionar el acceso y los permisos.</p>
                        <a href="<?= route('usuarios.crear'); ?>" class="crud-btn-primary">
                            <i class="fas fa-plus"></i>
                            Crear Primer Usuario
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Espacio de separacion -->
        <div style="height: 40px;"></div>  

    </div>
</div>

<!-- Modal de confirmación de eliminación modernizado -->
<div class="crud-modal" id="crudDeleteUserModal" tabindex="-1" role="dialog" aria-labelledby="crudDeleteUserModalLabel" aria-hidden="true">
    <div class="crud-modal-dialog" role="document">
        <div class="crud-modal-content">
            <form id="crudDeleteUserForm" method="POST" action="">
                <?= CSRF() ?>
                <input type="hidden" name="_method" value="DELETE">
                
                <div class="crud-modal-header">
                    <h5 class="crud-modal-title" id="crudDeleteUserModalLabel">
                        <i class="fas fa-exclamation-triangle"></i>
                        Confirmar Eliminación de Usuario
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="text-center">
                        <div class="crud-warning-icon">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <h4 class="crud-mb-3">¿Estás seguro de eliminar este usuario?</h4>
                        <p class="crud-text-muted crud-mb-2">Esta acción no se puede deshacer.</p>
                        <div class="crud-user-to-delete-info">
                            <strong>Usuario: <span id="crudUserNameToDelete" class="crud-text-danger"></span></strong>
                        </div>
                    </div>
                    <div class="crud-alert crud-alert-danger crud-mt-3" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>¡Atención!</strong> El usuario será eliminado permanentemente junto con:
                            <ul class="crud-mt-2 crud-mb-0">
                                <li>Todos sus datos personales</li>
                                <li>Sus asignaciones de roles</li>
                                <li>Su historial en el sistema</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="crud-modal-footer">
                    <button type="button" class="crud-btn crud-btn-clear" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="crud-btn crud-btn-outline-danger">
                        <i class="fas fa-user-times"></i>
                        Eliminar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de cambio de estado modernizado -->
<div class="crud-modal" id="crudToggleStatusModal" tabindex="-1" role="dialog" aria-labelledby="crudToggleStatusModalLabel" aria-hidden="true">
    <div class="crud-modal-dialog" role="document">
        <div class="crud-modal-content">
            <form id="crudToggleStatusForm" method="POST" action="">
                <?= CSRF() ?>
                
                <div class="crud-modal-header">
                    <h5 class="crud-modal-title" id="crudToggleStatusModalLabel">
                        <i class="fas fa-user-cog"></i>
                        Cambiar Estado del Usuario
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="text-center">
                        <div class="crud-status-icon">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <h4 class="crud-mb-3" id="crudStatusModalTitle">¿Cambiar estado del usuario?</h4>
                        <div class="crud-user-status-info">
                            <strong>Usuario: <span id="crudUserNameToToggle" class="crud-text-info"></span></strong>
                            <p class="crud-mt-2" id="crudStatusModalDescription">El usuario será activado/desactivado en el sistema.</p>
                        </div>
                    </div>
                </div>
                
                <div class="crud-modal-footer">
                    <button type="button" class="crud-btn crud-btn-clear" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="crud-btn crud-btn-outline-info" id="crudConfirmStatusToggle">
                        <i class="fas fa-check"></i>
                        Confirmar Cambio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Overlay personalizado -->
<div class="crud-custom-modal-overlay" id="crudCustomModalOverlay"></div>

<!-- JavaScript específico para usuarios CRUD directamente aquí (sin archivo externo) -->

<script>
// Funcionalidad de filtros modernizada
document.addEventListener('DOMContentLoaded', function() {
    const crudFilterName = document.getElementById('crudFilterName');
    const crudFilterRole = document.getElementById('crudFilterRole');
    const crudFilterStatus = document.getElementById('crudFilterStatus');
    const crudTableRows = document.querySelectorAll('.crud-data-table tbody tr');

    function crudApplyFilters() {
        const nameFilter = crudFilterName.value.toLowerCase();
        const roleFilter = crudFilterRole.value.toLowerCase();
        const statusFilter = crudFilterStatus.value;

        crudTableRows.forEach(row => {
            const nameCell = row.querySelector('.crud-user-name').textContent.toLowerCase();
            const emailCell = row.cells[2].textContent.toLowerCase();
            const roleCell = row.querySelector('.crud-user-roles').textContent.toLowerCase();
            const statusCell = row.querySelector('.crud-badge-success, .crud-badge-danger');
            const userStatus = statusCell && statusCell.classList.contains('crud-badge-success') ? '1' : '0';

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
    if (crudFilterName) crudFilterName.addEventListener('keyup', crudApplyFilters);
    if (crudFilterRole) crudFilterRole.addEventListener('change', crudApplyFilters);
    if (crudFilterStatus) crudFilterStatus.addEventListener('change', crudApplyFilters);

    // Función para limpiar filtros
    window.crudClearFilters = function() {
        if (crudFilterName) crudFilterName.value = '';
        if (crudFilterRole) crudFilterRole.value = '';
        if (crudFilterStatus) crudFilterStatus.value = '';
        
        // Mostrar todas las filas
        crudTableRows.forEach(row => {
            row.style.display = '';
        });
    };

    // Manejo de modales modernos
    const crudDeleteModal = document.getElementById('crudDeleteUserModal');
    const crudStatusModal = document.getElementById('crudToggleStatusModal');
    const crudOverlay = document.getElementById('crudCustomModalOverlay');

    // Botones de eliminar usuario
    document.querySelectorAll('.crud-btn-delete-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            const deleteUrl = this.dataset.deleteUrl;

            document.getElementById('crudUserNameToDelete').textContent = userName;
            document.getElementById('crudDeleteUserForm').action = deleteUrl;

            crudShowModal(crudDeleteModal);
        });
    });

    // Botones de cambio de estado
    document.querySelectorAll('.crud-btn-toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            const currentStatus = this.dataset.currentStatus;
            const statusUrl = this.dataset.statusUrl;

            document.getElementById('crudUserNameToToggle').textContent = userName;
            document.getElementById('crudToggleStatusForm').action = statusUrl;

            const modalTitle = document.getElementById('crudStatusModalTitle');
            const modalDescription = document.getElementById('crudStatusModalDescription');
            
            if (currentStatus === '1') {
                modalTitle.textContent = '¿Desactivar usuario?';
                modalDescription.textContent = 'El usuario será desactivado y no podrá acceder al sistema.';
            } else {
                modalTitle.textContent = '¿Activar usuario?';
                modalDescription.textContent = 'El usuario será activado y podrá acceder al sistema.';
            }

            crudShowModal(crudStatusModal);
        });
    });

    // Funciones para mostrar/ocultar modales
    function crudShowModal(modal) {
        if (modal && crudOverlay) {
            crudOverlay.classList.add('active');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function crudHideModal(modal) {
        if (modal && crudOverlay) {
            crudOverlay.classList.remove('active');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    // Event listeners para cerrar modales
    document.querySelectorAll('.crud-modal-close, [data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.crud-modal');
            crudHideModal(modal);
        });
    });

    // Cerrar modal al hacer clic en el overlay
    if (crudOverlay) {
        crudOverlay.addEventListener('click', function() {
            document.querySelectorAll('.crud-modal.show').forEach(modal => {
                crudHideModal(modal);
            });
        });
    }

    // Auto-dismiss de alertas después de 5 segundos
    document.querySelectorAll('.crud-alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Manejo manual de cierre de alertas
    document.querySelectorAll('.crud-btn-close').forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.crud-alert');
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        });
    });
});
</script>