<?php
$title = $title ?? 'Crear Rol - Configuración';
$error = $error ?? null;
$old_data = $old_data ?? [];
?>

<div class="dashboard-content">
    
    <!-- Header -->
    <div class="section-header">
        <div class="section-header-content">
            <h2 class="section-title">
                <i class="fas fa-user-plus"></i>
                Crear Nuevo Rol
            </h2>
            <p class="section-subtitle">Define un nuevo rol para el sistema con sus características y permisos</p>
        </div>
        <div class="section-header-actions">
            <a href="<?= route('admin.roles'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver a Roles
            </a>
        </div>
    </div>

    <!-- Mostrar error si existe -->
    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Formulario Principal -->
        <div class="col-lg-8">
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i>
                    Información del Rol
                </h3>

                <form method="POST" action="<?= route('admin.roles.store'); ?>" class="needs-validation" novalidate>
                    <div class="form-group">
                        <label for="nombre" class="form-label required">
                            <i class="fas fa-tag"></i>
                            Nombre del Rol
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="nombre" 
                               name="nombre" 
                               value="<?= htmlspecialchars($old_data['nombre'] ?? '') ?>"
                               placeholder="Ej: supervisor, vendedor, moderador..."
                               required>
                        <div class="invalid-feedback">
                            Por favor, ingresa el nombre del rol.
                        </div>
                        <div class="form-text">
                            El nombre debe ser único y descriptivo. Se recomienda usar minúsculas y sin espacios.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left"></i>
                            Descripción
                        </label>
                        <textarea class="form-control" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="4" 
                                  placeholder="Describe las funciones y responsabilidades de este rol..."><?= htmlspecialchars($old_data['descripcion'] ?? '') ?></textarea>
                        <div class="form-text">
                            Opcional. Proporciona una descripción clara de las funciones del rol.
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Crear Rol
                        </button>
                        <a href="<?= route('admin.roles'); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Panel de Información -->
        <div class="col-lg-4">
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb"></i>
                    Consejos para Crear Roles
                </h3>

                <div class="tips-list">
                    <div class="tip-item">
                        <div class="tip-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="tip-content">
                            <h4>Nombres Descriptivos</h4>
                            <p>Usa nombres claros como "supervisor", "vendedor", "moderador" en lugar de nombres genéricos.</p>
                        </div>
                    </div>

                    <div class="tip-item">
                        <div class="tip-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="tip-content">
                            <h4>Principio de Menor Privilegio</h4>
                            <p>Asigna solo los permisos necesarios para las funciones específicas del rol.</p>
                        </div>
                    </div>

                    <div class="tip-item">
                        <div class="tip-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="tip-content">
                            <h4>Planifica la Estructura</h4>
                            <p>Considera cómo se relacionará este rol con otros roles existentes en el sistema.</p>
                        </div>
                    </div>

                    <div class="tip-item">
                        <div class="tip-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="tip-content">
                            <h4>Permisos Posteriores</h4>
                            <p>Después de crear el rol, podrás asignar permisos específicos desde la lista de roles.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles Existentes -->
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Roles Existentes
                </h3>

                <div class="existing-roles">
                    <div class="role-item system-role">
                        <div class="role-badge">
                            <i class="fas fa-crown"></i>
                        </div>
                        <div class="role-info">
                            <span class="role-name">Administrador</span>
                            <span class="role-type">Sistema</span>
                        </div>
                    </div>

                    <div class="role-item system-role">
                        <div class="role-badge">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <div class="role-info">
                            <span class="role-name">Docente</span>
                            <span class="role-type">Sistema</span>
                        </div>
                    </div>

                    <div class="role-item system-role">
                        <div class="role-badge">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <div class="role-info">
                            <span class="role-name">Estudiante</span>
                            <span class="role-type">Sistema</span>
                        </div>
                    </div>
                </div>

                <div class="form-text">
                    Los roles del sistema no pueden ser modificados ni eliminados.
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.required::after {
    content: " *";
    color: #ef4444;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-control {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 0.75rem;
    font-size: 0.875rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.form-text {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.invalid-feedback {
    font-size: 0.75rem;
    color: #ef4444;
    margin-top: 0.25rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e5e7eb;
    margin-top: 2rem;
}

.card-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.tips-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.tip-item {
    display: flex;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
}

.tip-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981, #047857);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.875rem;
}

.tip-content h4 {
    margin: 0 0 0.25rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

.tip-content p {
    margin: 0;
    font-size: 0.75rem;
    color: #6b7280;
    line-height: 1.4;
}

.existing-roles {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.role-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
}

.system-role {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-color: #cbd5e1;
}

.role-badge {
    width: 32px;
    height: 32px;
    border-radius: 6px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.875rem;
}

.role-info {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.role-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
}

.role-type {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    font-weight: 500;
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
    // Validación del formulario
    const form = document.querySelector('.needs-validation');
    
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
    }
    
    // Validar nombre del rol en tiempo real
    const nombreInput = document.getElementById('nombre');
    if (nombreInput) {
        nombreInput.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            
            // Lista de nombres reservados
            const reserved = ['admin', 'root', 'system', 'guest', 'user'];
            
            if (reserved.includes(value)) {
                this.setCustomValidity('Este nombre está reservado para el sistema');
            } else if (value.length < 3) {
                this.setCustomValidity('El nombre debe tener al menos 3 caracteres');
            } else if (!/^[a-zA-Z0-9_-]+$/.test(value)) {
                this.setCustomValidity('Solo se permiten letras, números, guiones y guiones bajos');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});
</script>
