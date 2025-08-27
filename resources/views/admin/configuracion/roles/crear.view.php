<?php
$title = $title ?? 'Crear Rol - Configuración';
$errors = flashGet('errors') ?? [];
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

    <!-- Mostrar errores de validación -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Errores de validación:</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $field => $fieldErrors): ?>
                    <?php foreach ($fieldErrors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
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
                               class="form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>" 
                               id="nombre" 
                               name="nombre" 
                               value="<?= htmlspecialchars(old('nombre')) ?>"
                               placeholder="Ej: supervisor, vendedor, moderador..."
                               required>
                        <?php if (isset($errors['nombre'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= htmlspecialchars($errors['nombre'][0]) ?>
                            </div>
                        <?php else: ?>
                            <div class="invalid-feedback">
                                Por favor, ingresa el nombre del rol.
                            </div>
                        <?php endif; ?>
                        <div class="form-text">
                            El nombre debe ser único y descriptivo. Se recomienda usar minúsculas y sin espacios.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left"></i>
                            Descripción
                        </label>
                        <textarea class="form-control <?= isset($errors['descripcion']) ? 'is-invalid' : '' ?>" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="4" 
                                  placeholder="Describe las funciones y responsabilidades de este rol..."><?= htmlspecialchars(old('descripcion')) ?></textarea>
                        <?php if (isset($errors['descripcion'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= htmlspecialchars($errors['descripcion'][0]) ?>
                            </div>
                        <?php endif; ?>
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
