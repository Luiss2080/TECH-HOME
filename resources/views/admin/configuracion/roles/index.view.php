<?php
$title = $title ?? 'Gestión de Roles - Configuración';
$roles = $roles ?? [];
?>

<div class="dashboard-content">
    
    <!-- Header de Roles -->
    <div class="section-header">
        <div class="section-header-content">
            <h2 class="section-title">
                <i class="fas fa-user-shield"></i>
                Gestión de Roles
            </h2>
            <p class="section-subtitle">Administra los roles del sistema y sus permisos asociados</p>
        </div>
        <div class="section-header-actions">
            <a href="<?= route('admin.roles.crear'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Crear Nuevo Rol
            </a>
        </div>
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($_GET['success']) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <!-- Tabla de Roles -->
    <div class="section-card">
        <div class="table-container">
            <?php if (!empty($roles)): ?>
                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Rol</th>
                            <th>Descripción</th>
                            <th>Usuarios Asignados</th>
                            <th>Permisos</th>
                            <th>Fecha de Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                            <tr data-role-id="<?= $role->id ?>">
                                <td><?= $role->id ?></td>
                                <td>
                                    <div class="role-info">
                                        <span class="role-name"><?= htmlspecialchars($role->nombre) ?></span>
                                        <?php if (in_array($role->nombre, ['administrador', 'docente', 'estudiante'])): ?>
                                            <span class="badge badge-system">Sistema</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="text-muted"><?= htmlspecialchars($role->descripcion ?? 'Sin descripción') ?></td>
                                <td>
                                    <span class="users-count" data-role-id="<?= $role->id ?>">
                                        <i class="fas fa-users"></i>
                                        <span class="count"><?= count($role->users()) ?></span>
                                    </span>
                                </td>
                                <td>
                                    <span class="permissions-count" data-role-id="<?= $role->id ?>">
                                        <i class="fas fa-key"></i>
                                        <span class="count"><?php count($role->permissions()) ?></span>
                                    </span>
                                </td>
                                <td class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($role->created_at ?? $role->fecha_creacion ?? 'now')) ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="<?= route('admin.roles.permisos', ['id' => $role->id]) ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Asignar Permisos">
                                            <i class="fas fa-key"></i>
                                        </a>
                                        
                                        <?php if (!in_array($role->nombre, ['administrador', 'docente', 'estudiante'])): ?>
                                            <a href="<?= route('admin.roles.editar', ['id' => $role->id]) ?>" 
                                               class="btn btn-sm btn-outline-secondary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger btn-delete-role" 
                                                    data-role-id="<?= $role->id ?>" 
                                                    data-role-name="<?= htmlspecialchars($role->nombre) ?>"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php else: ?>
                                            <span class="btn btn-sm btn-outline-secondary disabled" title="Rol protegido">
                                                <i class="fas fa-shield-alt"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h3>No hay roles registrados</h3>
                    <p>Comienza creando el primer rol del sistema</p>
                    <a href="<?= route('admin.roles.crear'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Crear Primer Rol
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Información de Roles del Sistema -->
    <div class="section-card">
        <h3 class="section-title">
            <i class="fas fa-info-circle"></i>
            Información Importante
        </h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="info-content">
                    <h4>Roles del Sistema</h4>
                    <p>Los roles <strong>Administrador</strong>, <strong>Docente</strong> y <strong>Estudiante</strong> son roles protegidos del sistema y no pueden ser eliminados.</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div class="info-content">
                    <h4>Asignación de Usuarios</h4>
                    <p>Un rol con usuarios asignados no puede ser eliminado. Primero debes reasignar a los usuarios a otros roles.</p>
                </div>
            </div>
            <div class="info-item">
                <div class="info-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div class="info-content">
                    <h4>Permisos</h4>
                    <p>Los permisos definen qué acciones puede realizar un usuario con determinado rol en el sistema.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteRoleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h5>¿Estás seguro de eliminar este rol?</h5>
                    <p class="text-muted">Esta acción no se puede deshacer. El rol "<span id="roleNameToDelete"></span>" será eliminado permanentemente.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteRole">
                    <i class="fas fa-trash"></i>
                    Eliminar Rol
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.role-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.role-name {
    font-weight: 600;
    color: #374151;
}

.badge-system {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 500;
}

.users-count, .permissions-count {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.users-count i {
    color: #3b82f6;
}

.permissions-count i {
    color: #10b981;
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.action-buttons .btn {
    padding: 0.375rem;
    min-width: 32px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

.info-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}

.info-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.info-content h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 600;
    color: #374151;
}

.info-content p {
    margin: 0;
    font-size: 0.875rem;
    color: #6b7280;
    line-height: 1.4;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6b7280;
}

.empty-state-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-state h3 {
    color: #374151;
    margin-bottom: 0.5rem;
}

.empty-state p {
    margin-bottom: 1.5rem;
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
}

.alert-success {
    background: #d1fae5;
    border: 1px solid #a7f3d0;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    border: 1px solid #fecaca;
    color: #991b1b;
}

.btn-close {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    font-size: 1.25rem;
    cursor: pointer;
    opacity: 0.5;
}

.btn-close:hover {
    opacity: 1;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Configurar botones de eliminación
    setupDeleteButtons();
});


function setupDeleteButtons() {
    const deleteButtons = document.querySelectorAll('.btn-delete-role');
    const modal = new bootstrap.Modal(document.getElementById('deleteRoleModal'));
    const roleNameSpan = document.getElementById('roleNameToDelete');
    const confirmButton = document.getElementById('confirmDeleteRole');
    
    let roleToDelete = null;
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            roleToDelete = {
                id: this.dataset.roleId,
                name: this.dataset.roleName
            };
            
            roleNameSpan.textContent = roleToDelete.name;
            modal.show();
        });
    });
    
    confirmButton.addEventListener('click', function() {
        if (roleToDelete) {
            deleteRole(roleToDelete.id);
            modal.hide();
        }
    });
}

function deleteRole(roleId) {
    fetch(`/admin/configuracion/roles/${roleId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remover la fila de la tabla
            const row = document.querySelector(`tr[data-role-id="${roleId}"]`);
            if (row) {
                row.remove();
            }
            
            // Mostrar mensaje de éxito
            showAlert('success', data.message || 'Rol eliminado exitosamente');
        } else {
            showAlert('danger', data.message || 'Error al eliminar el rol');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('danger', 'Error de conexión al eliminar el rol');
    });
}

function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible">
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
            ${message}
        </div>
    `;
    
    const dashboardContent = document.querySelector('.dashboard-content');
    dashboardContent.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        const alert = dashboardContent.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>
