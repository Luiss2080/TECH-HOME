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
    <?php if (flashGet('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars(flashGet('success')) ?>
        </div>
    <?php endif; ?>

    <?php if (flashGet('error')): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars(flashGet('error')) ?>
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
                                        <span class="count">
                                            <?php
                                            try {
                                                $users = $role->users();
                                                echo is_array($users) ? count($users) : 0;
                                            } catch (Exception $e) {
                                                echo '0';
                                            }
                                            ?>
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    <span class="permissions-count" data-role-id="<?= $role->id ?>">
                                        <i class="fas fa-key"></i>
                                        <span class="count">
                                            <?php
                                            try {
                                                $permissions = $role->permissions();
                                                echo is_array($permissions) ? count($permissions) : 0;
                                            } catch (Exception $e) {
                                                echo '0';
                                            }
                                            ?>
                                        </span>
                                    </span>
                                </td>
                                <td class="text-muted">
                                    <?php 
                                    $fecha = $role->fecha_creacion ?? date('Y-m-d H:i:s');
                                    echo date('d/m/Y H:i', strtotime($fecha));
                                    ?>
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
<div class="modal fade" id="deleteRoleModal" tabindex="-1" role="dialog" aria-labelledby="deleteRoleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form id="deleteRoleForm" method="POST" action="">
                <?= CSRF() ?>
                <input type="hidden" name="_method" value="DELETE">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRoleModalLabel">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <div class="text-center py-3">
                        <div class="mb-4">
                            <div class="warning-icon">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                        </div>
                        <h4 class="mb-3">¿Estás seguro de eliminar este rol?</h4>
                        <p class="text-muted mb-2">Esta acción no se puede deshacer.</p>
                        <div class="role-to-delete-info">
                            <strong>Rol: <span id="roleNameToDelete" class="text-danger"></span></strong>
                        </div>
                    </div>
                    <div class="alert alert-warning mt-3" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>El rol será eliminado permanentemente del sistema junto con todas sus configuraciones.</small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Eliminar Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Overlay personalizado para mayor compatibilidad -->
<div class="custom-modal-overlay" id="customModalOverlay" style="display: none;"></div>

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

/* ============================================
   ESTILOS DEL MODAL
   ============================================ */
.modal {
    z-index: 1050;
}

.modal-dialog-centered {
    display: flex;
    align-items: center;
    min-height: calc(100% - 1rem);
}

.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    0% {
        opacity: 0;
        transform: translateY(-50px) scale(0.8);
    }
    100% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-header {
    border-bottom: 1px solid #e5e7eb;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-radius: 15px 15px 0 0;
}

.modal-title {
    color: #374151;
    font-weight: 600;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    margin: 0;
}

.modal-body {
    padding: 2rem 1.5rem;
    background: white;
}

.modal-footer {
    border-top: 1px solid #e5e7eb;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 0 0 15px 15px;
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}

.warning-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3);
    animation: warningPulse 2s ease-in-out infinite;
}

@keyframes warningPulse {
    0%, 100% { transform: scale(1); box-shadow: 0 10px 25px rgba(245, 158, 11, 0.3); }
    50% { transform: scale(1.05); box-shadow: 0 15px 35px rgba(245, 158, 11, 0.4); }
}

.warning-icon i {
    font-size: 2rem;
    color: white;
}

.role-to-delete-info {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
    padding: 1rem;
    margin-top: 1rem;
}

.role-to-delete-info strong {
    color: #991b1b;
}

.modal .btn {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.modal .btn-secondary {
    background: #6b7280;
    color: white;
}

.modal .btn-secondary:hover {
    background: #4b5563;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(107, 114, 128, 0.4);
}

.modal .btn-danger {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: white;
}

.modal .btn-danger:hover {
    background: linear-gradient(135deg, #b91c1c, #991b1b);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 38, 38, 0.4);
}

.btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.3s ease;
    color: #6b7280;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.btn-close:hover {
    opacity: 1;
    background: rgba(0, 0, 0, 0.1);
}

/* Overlay personalizado */
.custom-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

/* Asegurar que el modal esté por encima */
.modal.show {
    display: block !important;
}

.modal-backdrop {
    z-index: 1040;
}

/* Responsive del modal */
@media (max-width: 576px) {
    .modal-dialog {
        margin: 1rem;
        max-width: calc(100% - 2rem);
    }
    
    .modal-content {
        border-radius: 10px;
    }
    
    .modal-header,
    .modal-body,
    .modal-footer {
        padding: 1rem;
    }
    
    .warning-icon {
        width: 60px;
        height: 60px;
    }
    
    .warning-icon i {
        font-size: 1.5rem;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .modal .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Roles management page loaded');
    
    // Configurar modal de eliminación
    setupDeleteModal();
    
    // Auto-dismiss alerts
    setupAlertDismissal();
});

function setupDeleteModal() {
    const deleteButtons = document.querySelectorAll('.btn-delete-role');
    const modalElement = document.getElementById('deleteRoleModal');
    const customOverlay = document.getElementById('customModalOverlay');
    const roleNameSpan = document.getElementById('roleNameToDelete');
    const deleteForm = document.getElementById('deleteRoleForm');
    const closeButtons = modalElement.querySelectorAll('[data-bs-dismiss="modal"], .btn-close');
    
    console.log(`🔍 Found ${deleteButtons.length} delete buttons`);
    
    // Función para mostrar el modal
    function showModal() {
        console.log('📂 Showing modal');
        modalElement.style.display = 'block';
        customOverlay.style.display = 'block';
        
        // Añadir clases para animación
        setTimeout(() => {
            modalElement.classList.add('show');
            customOverlay.classList.add('show');
            console.log('✨ Modal animation triggered');
        }, 10);
        
        // Bloquear scroll del body
        document.body.style.overflow = 'hidden';
    }
    
    // Función para ocultar el modal
    function hideModal() {
        console.log('📁 Hiding modal');
        modalElement.classList.remove('show');
        customOverlay.classList.remove('show');
        
        setTimeout(() => {
            modalElement.style.display = 'none';
            customOverlay.style.display = 'none';
            document.body.style.overflow = 'auto';
            console.log('🚫 Modal hidden');
        }, 300);
        
        // Limpiar datos
        roleNameSpan.textContent = '';
        deleteForm.action = '';
    }
    
    // Event listeners para botones de eliminación
    deleteButtons.forEach((button, index) => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log(`🗑️ Delete button ${index} clicked`);
            
            const roleId = this.dataset.roleId;
            const roleName = this.dataset.roleName;
            
            console.log('📋 Role to delete:', { id: roleId, name: roleName });
            
            // Configurar el modal
            roleNameSpan.textContent = roleName;
            
            // Configurar la acción del formulario
            const currentUrl = window.location.pathname; // /admin/configuracion/roles
            deleteForm.action = `${currentUrl}/${roleId}`;
            
            console.log('🎯 Form action set to:', deleteForm.action);
            
            showModal();
        });
    });
    
    // Event listeners para cerrar modal
    closeButtons.forEach((button, index) => {
        button.addEventListener('click', function() {
            console.log(`❌ Close button ${index} clicked`);
            hideModal();
        });
    });
    
    // Cerrar con overlay
    customOverlay.addEventListener('click', function() {
        console.log('🖱️ Overlay clicked');
        hideModal();
    });
    
    // Cerrar con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalElement.classList.contains('show')) {
            console.log('⌨️ ESC key pressed');
            hideModal();
        }
    });
    
    // Debug del formulario antes del envío
    deleteForm.addEventListener('submit', function(e) {
        console.log('📝 Form submitting with:');
        console.log('- Action:', this.action);
        console.log('- Method:', this.method);
        console.log('- _method:', this.querySelector('input[name="_method"]').value);
        
        // Verificar que la acción esté configurada
        if (!this.action || this.action.endsWith('/')) {
            console.error('❌ Form action not properly set!');
            e.preventDefault();
            alert('Error: No se pudo configurar la URL de eliminación');
            return false;
        }
        
        // Confirmar una última vez
        const roleName = roleNameSpan.textContent;
        if (!confirm(`¿Realmente deseas eliminar el rol "${roleName}"?`)) {
            e.preventDefault();
            return false;
        }
        
        console.log('✅ Form submission allowed');
    });
}

function setupAlertDismissal() {
    // Auto-dismiss para alertas existentes
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        const closeBtn = alert.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', function() {
                alert.style.animation = 'slideUp 0.3s ease-out';
                setTimeout(() => alert.remove(), 300);
            });
        }
        
        // Auto-dismiss después de 8 segundos
        setTimeout(() => {
            if (alert.parentElement) {
                alert.style.animation = 'slideUp 0.3s ease-out';
                setTimeout(() => alert.remove(), 300);
            }
        }, 8000);
    });
}

// Estilos de animación para alertas
const alertStyles = document.createElement('style');
alertStyles.textContent = `
    @keyframes slideDown {
        0% { opacity: 0; transform: translateY(-100%); }
        100% { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideUp {
        0% { opacity: 1; transform: translateY(0); }
        100% { opacity: 0; transform: translateY(-100%); }
    }
`;
document.head.appendChild(alertStyles);
</script>
