<?php
$title = $title ?? 'Crear Nuevo Usuario';
$errors = $errors ?? [];
$old = $old ?? [];
$roles = $roles ?? [];
?>

<!-- Estilos específicos para el módulo CRUD - Crear -->
<link rel="stylesheet" href="<?= asset('css/CRUD/crear.css'); ?>">

<!-- Contenedor principal del CRUD -->
<div class="crud-create-container">
    <div class="crud-create-wrapper">

        <!-- Header principal con breadcrumb y título -->
        <div class="crud-section-card">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="crud-section-title-group">
                        <nav aria-label="breadcrumb" class="crud-breadcrumb-nav">
                            <ol class="crud-breadcrumb">
                                <li class="crud-breadcrumb-item">
                                    <a href="<?= route('admin.dashboard') ?>">
                                        <i class="fas fa-tachometer-alt"></i>
                                        Dashboard
                                    </a>
                                </li>
                                <li class="crud-breadcrumb-item">
                                    <a href="<?= route('usuarios') ?>">
                                        <i class="fas fa-users"></i>
                                        Usuarios
                                    </a>
                                </li>
                                <li class="crud-breadcrumb-item active">
                                    <i class="fas fa-user-plus"></i>
                                    Crear Usuario
                                </li>
                            </ol>
                        </nav>
                        <h1 class="crud-section-title">Crear Nuevo Usuario</h1>
                        <p class="crud-section-subtitle">Registra un nuevo usuario en el sistema y asigna sus roles correspondientes</p>
                    </div>
                </div>
                <div class="crud-section-header-actions">
                    <a href="<?= route('usuarios') ?>" class="crud-section-action-header crud-btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Volver a Lista
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertas de sesión modernizadas -->
        <?php if (session('error')): ?>
            <div class="crud-alert crud-alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><strong>ERROR:</strong> <?= htmlspecialchars(session('error')) ?></span>
                <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (session('success')): ?>
            <div class="crud-alert crud-alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars(session('success')) ?></span>
                <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Errores de validación modernizados -->
        <?php if (!empty($errors)): ?>
            <div class="crud-alert crud-alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="crud-alert-content">
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul class="crud-error-list">
                        <?php foreach ($errors as $field => $fieldErrors): ?>
                            <?php if (is_array($fieldErrors)): ?>
                                <?php foreach ($fieldErrors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><?= htmlspecialchars($fieldErrors) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Formulario principal - Información Personal -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-user"></i>
                    Información Personal
                </h2>
                <p class="crud-section-subtitle">Datos básicos del usuario en el sistema</p>
            </div>
            
            <div class="crud-form-body">
                <form method="POST" action="<?= route('usuarios.store') ?>" id="crudFormUsuario" class="crud-form">
                    <?php CSRF() ?>
                    
                    <div class="crud-form-grid">
                        <div class="crud-form-group">
                            <label for="crudNombre" class="crud-form-label">
                                <i class="fas fa-user"></i>
                                Nombre
                                <span class="crud-required">*</span>
                            </label>
                            <input type="text" 
                                   class="crud-form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>"
                                   id="crudNombre" 
                                   name="nombre" 
                                   value="<?= htmlspecialchars($old['nombre'] ?? '') ?>" 
                                   required 
                                   placeholder="Ingresa el nombre completo">
                            <?php if (isset($errors['nombre'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array($errors['nombre']) ? $errors['nombre'][0] : $errors['nombre'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="crud-form-group">
                            <label for="crudApellido" class="crud-form-label">
                                <i class="fas fa-user"></i>
                                Apellido
                                <span class="crud-required">*</span>
                            </label>
                            <input type="text" 
                                   class="crud-form-control <?= isset($errors['apellido']) ? 'is-invalid' : '' ?>"
                                   id="crudApellido" 
                                   name="apellido" 
                                   value="<?= htmlspecialchars($old['apellido'] ?? '') ?>" 
                                   required 
                                   placeholder="Ingresa el apellido completo">
                            <?php if (isset($errors['apellido'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array($errors['apellido']) ? $errors['apellido'][0] : $errors['apellido'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="crud-form-group">
                            <label for="crudEmail" class="crud-form-label">
                                <i class="fas fa-envelope"></i>
                                Correo Electrónico
                                <span class="crud-required">*</span>
                            </label>
                            <input type="email" 
                                   class="crud-form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                   id="crudEmail" 
                                   name="email" 
                                   value="<?= htmlspecialchars($old['email'] ?? '') ?>" 
                                   required 
                                   placeholder="usuario@ejemplo.com">
                            <div class="crud-form-text">
                                <i class="fas fa-info-circle"></i>
                                Se usará para notificaciones y recuperación de cuenta
                            </div>
                            <?php if (isset($errors['email'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array($errors['email']) ? $errors['email'][0] : $errors['email'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="crud-form-group">
                            <label for="crudTelefono" class="crud-form-label">
                                <i class="fas fa-phone"></i>
                                Teléfono
                            </label>
                            <input type="text" 
                                   class="crud-form-control <?= isset($errors['telefono']) ? 'is-invalid' : '' ?>"
                                   id="crudTelefono" 
                                   name="telefono" 
                                   value="<?= htmlspecialchars($old['telefono'] ?? '') ?>" 
                                   placeholder="+591 7XXXXXXX">
                            <div class="crud-form-text">
                                <i class="fas fa-info-circle"></i>
                                Incluye código de país (opcional)
                            </div>
                            <?php if (isset($errors['telefono'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array($errors['telefono']) ? $errors['telefono'][0] : $errors['telefono'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="crud-form-group crud-form-group-full">
                            <label for="crudFechaNacimiento" class="crud-form-label">
                                <i class="fas fa-calendar-alt"></i>
                                Fecha de Nacimiento
                            </label>
                            <input type="date" 
                                   class="crud-form-control <?= isset($errors['fecha_nacimiento']) ? 'is-invalid' : '' ?>"
                                   id="crudFechaNacimiento" 
                                   name="fecha_nacimiento"
                                   value="<?= htmlspecialchars($old['fecha_nacimiento'] ?? '') ?>">
                            <div class="crud-form-text">
                                <i class="fas fa-info-circle"></i>
                                Campo opcional para estadísticas demográficas
                            </div>
                            <?php if (isset($errors['fecha_nacimiento'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array($errors['fecha_nacimiento']) ? $errors['fecha_nacimiento'][0] : $errors['fecha_nacimiento'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
            </div>
        </div>

        <!-- Formulario - Credenciales de Acceso -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-shield-alt"></i>
                    Credenciales de Acceso
                </h2>
                <p class="crud-section-subtitle">Configuración de seguridad para el acceso al sistema</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-form-grid">
                    <div class="crud-form-group">
                        <label for="crudPassword" class="crud-form-label">
                            <i class="fas fa-lock"></i>
                            Contraseña
                            <span class="crud-required">*</span>
                        </label>
                        <input type="password" 
                               class="crud-form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                               id="crudPassword" 
                               name="password" 
                               required 
                               placeholder="Mínimo 8 caracteres">
                        <div class="crud-form-text">
                            <i class="fas fa-shield-alt"></i>
                            Mínimo 8 caracteres, incluye mayúsculas, minúsculas y números
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <div class="crud-invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?= is_array($errors['password']) ? $errors['password'][0] : $errors['password'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="crud-form-group">
                        <label for="crudPasswordConfirmation" class="crud-form-label">
                            <i class="fas fa-lock"></i>
                            Confirmar Contraseña
                            <span class="crud-required">*</span>
                        </label>
                        <input type="password" 
                               class="crud-form-control <?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>"
                               id="crudPasswordConfirmation" 
                               name="password_confirmation" 
                               required 
                               placeholder="Repite la contraseña">
                        <div class="crud-form-text">
                            <i class="fas fa-check-double"></i>
                            Debe coincidir exactamente con la contraseña anterior
                        </div>
                        <?php if (isset($errors['password_confirmation'])): ?>
                            <div class="crud-invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?= is_array($errors['password_confirmation']) ? $errors['password_confirmation'][0] : $errors['password_confirmation'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario - Roles y Permisos -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-user-tag"></i>
                    Roles y Permisos
                </h2>
                <p class="crud-section-subtitle">Asigna los roles apropiados para definir los permisos del usuario</p>
            </div>
            
            <div class="crud-form-body">
                <?php if (isset($roles) && !empty($roles)): ?>
                    <div class="crud-roles-grid">
                        <?php foreach ($roles as $rol): ?>
                            <?php if (strtolower($rol->nombre) !== 'mirones'): // Excluir el rol Mirones ?>
                                <div class="crud-role-item <?= in_array($rol->id, $old['roles'] ?? []) ? 'selected' : '' ?>" 
                                     onclick="crudToggleRole(this, <?= $rol->id ?>)"
                                     tabindex="0"
                                     role="checkbox"
                                     aria-checked="<?= in_array($rol->id, $old['roles'] ?? []) ? 'true' : 'false' ?>">
                                    <input class="crud-role-checkbox" 
                                           type="checkbox"
                                           id="crudRole<?= $rol->id ?>" 
                                           name="roles[]"
                                           value="<?= $rol->id ?>"
                                           <?= in_array($rol->id, $old['roles'] ?? []) ? 'checked' : '' ?>>
                                    <div class="crud-role-content">
                                        <div class="crud-role-header">
                                            <div class="crud-role-icon">
                                                <i class="fas fa-<?= $rol->nombre === 'Administrador' ? 'crown' : ($rol->nombre === 'Docente' ? 'chalkboard-teacher' : ($rol->nombre === 'Estudiante' ? 'graduation-cap' : ($rol->nombre === 'Vendedor' ? 'store' : 'user'))) ?>"></i>
                                            </div>
                                            <div class="crud-role-name"><?= htmlspecialchars($rol->nombre) ?></div>
                                        </div>
                                        <div class="crud-role-description"><?= htmlspecialchars($rol->descripcion ?? 'Sin descripción disponible') ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php if (isset($errors['roles'])): ?>
                        <div class="crud-invalid-feedback crud-roles-error">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?= is_array($errors['roles']) ? $errors['roles'][0] : $errors['roles'] ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="crud-empty-state crud-roles-empty">
                        <div class="crud-empty-state-icon">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <h4>No hay roles disponibles</h4>
                        <p>No se encontraron roles en el sistema. Contacta al administrador para configurar los roles necesarios.</p>
                    </div>
                <?php endif; ?>
                
                <!-- Botones de acción -->
                <div class="crud-form-actions">
                    <a href="<?= route('usuarios') ?>" class="crud-btn crud-btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Cancelar y Volver
                    </a>
                    <button type="submit" class="crud-btn crud-btn-primary" id="crudBtnSubmit">
                        <i class="fas fa-save"></i>
                        Crear Usuario
                    </button>
                </div>
                </form>
            </div>
        </div>

        <!-- Panel de información y ayuda -->
        <div class="crud-section-card">
            <div class="crud-info-panel">
                <div class="crud-info-tabs">
                    <button class="crud-info-tab active" data-tab="roles">
                        <i class="fas fa-info-circle"></i>
                        Información de Roles
                    </button>
                    <button class="crud-info-tab" data-tab="security">
                        <i class="fas fa-shield-alt"></i>
                        Consejos de Seguridad
                    </button>
                    <button class="crud-info-tab" data-tab="help">
                        <i class="fas fa-question-circle"></i>
                        Ayuda General
                    </button>
                </div>

                <div class="crud-info-content">
                    <div class="crud-info-pane active" id="crudTabRoles">
                        <h4 class="crud-info-title">
                            <i class="fas fa-user-tag"></i>
                            Tipos de Roles Disponibles
                        </h4>
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Administrador:</strong> Acceso completo al sistema, gestión de usuarios, configuración y supervisión general.
                                </div>
                            </div>
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Docente:</strong> Gestión de cursos, materiales educativos, calificaciones y seguimiento de estudiantes.
                                </div>
                            </div>
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Estudiante:</strong> Acceso a contenido educativo, actividades, tareas y recursos de aprendizaje.
                                </div>
                            </div>
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Vendedor:</strong> Gestión de ventas, inventario de componentes electrónicos y atención a clientes.
                                </div>
                            </div>
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Invitado:</strong> Acceso temporal limitado para visitantes o usuarios en evaluación.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="crud-info-pane" id="crudTabSecurity">
                        <h4 class="crud-info-title">
                            <i class="fas fa-shield-alt"></i>
                            Recomendaciones de Seguridad
                        </h4>
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Contraseñas seguras:</strong> Utiliza al menos 8 caracteres con mayúsculas, minúsculas, números y símbolos.
                                </div>
                            </div>
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Principio de menor privilegio:</strong> Asigna solo los roles y permisos mínimos necesarios.
                                </div>
                            </div>
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-envelope-open-text"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Verificación de email:</strong> Confirma que el correo electrónico sea válido y accesible.
                                </div>
                            </div>
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Revisión periódica:</strong> Los roles y permisos pueden modificarse posteriormente según necesidad.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="crud-info-pane" id="crudTabHelp">
                        <h4 class="crud-info-title">
                            <i class="fas fa-lightbulb"></i>
                            Información Importante
                        </h4>
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-asterisk"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Campos obligatorios:</strong> Los campos marcados con <span style="color: var(--danger-color);">*</span> son requeridos para crear el usuario.
                                </div>
                            </div>
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Notificaciones:</strong> El usuario recibirá un email de bienvenida con sus credenciales de acceso.
                                </div>
                            </div>
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Modificaciones:</strong> Toda la información del usuario puede editarse posteriormente desde la lista de usuarios.
                                </div>
                            </div>
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Historial:</strong> El sistema registra automáticamente la fecha y hora de creación del usuario.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="height: 40px;"></div>  <!-- Mediano -->


    </div>
</div>

<!-- JavaScript específico para crear usuario -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sistema de tabs para información
    const tabs = document.querySelectorAll('.crud-info-tab');
    const panes = document.querySelectorAll('.crud-info-pane');

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Remover clase active de todos los tabs y panes
            tabs.forEach(t => t.classList.remove('active'));
            panes.forEach(p => p.classList.remove('active'));
            
            // Activar tab y pane correspondiente
            this.classList.add('active');
            document.getElementById('crudTab' + targetTab.charAt(0).toUpperCase() + targetTab.slice(1)).classList.add('active');
        });
    });

    // Función para alternar selección de roles
    window.crudToggleRole = function(element, roleId) {
        const checkbox = element.querySelector('input[type="checkbox"]');
        const isSelected = element.classList.contains('selected');
        
        if (isSelected) {
            element.classList.remove('selected');
            checkbox.checked = false;
            element.setAttribute('aria-checked', 'false');
        } else {
            element.classList.add('selected');
            checkbox.checked = true;
            element.setAttribute('aria-checked', 'true');
        }
    };

    // Validación de contraseñas en tiempo real
    const passwordField = document.getElementById('crudPassword');
    const confirmPasswordField = document.getElementById('crudPasswordConfirmation');

    function validatePasswords() {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;

        // Validar longitud de contraseña
        if (password.length >= 8) {
            passwordField.classList.remove('crud-form-error');
            passwordField.classList.add('crud-form-success');
        } else {
            passwordField.classList.remove('crud-form-success');
            if (password.length > 0) {
                passwordField.classList.add('crud-form-error');
            }
        }

        // Validar coincidencia de contraseñas
        if (confirmPassword.length > 0) {
            if (password === confirmPassword && password.length >= 8) {
                confirmPasswordField.classList.remove('crud-form-error');
                confirmPasswordField.classList.add('crud-form-success');
            } else {
                confirmPasswordField.classList.remove('crud-form-success');
                confirmPasswordField.classList.add('crud-form-error');
            }
        }
    }

    if (passwordField && confirmPasswordField) {
        passwordField.addEventListener('input', validatePasswords);
        confirmPasswordField.addEventListener('input', validatePasswords);
    }

    // Validación de email en tiempo real
    const emailField = document.getElementById('crudEmail');
    if (emailField) {
        emailField.addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email.length > 0) {
                if (emailRegex.test(email)) {
                    this.classList.remove('crud-form-error');
                    this.classList.add('crud-form-success');
                } else {
                    this.classList.remove('crud-form-success');
                    this.classList.add('crud-form-error');
                }
            } else {
                this.classList.remove('crud-form-success', 'crud-form-error');
            }
        });
    }

    // Auto-dismiss de alertas después de 6 segundos
    const alerts = document.querySelectorAll('.crud-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px) scale(0.95)';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 300);
        }, 6000);
    });

    // Manejo del envío del formulario
    const form = document.getElementById('crudFormUsuario');
    const submitBtn = document.getElementById('crudBtnSubmit');

    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Agregar clase de loading al botón
            submitBtn.classList.add('crud-form-loading');
            submitBtn.disabled = true;
            
            // Cambiar texto del botón
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando Usuario...';
            
            // Si hay errores, restaurar el botón después de un momento
            setTimeout(() => {
                if (document.querySelector('.crud-alert-danger')) {
                    submitBtn.classList.remove('crud-form-loading');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                }
            }, 1000);
        });
    }

    // Navegación con teclado para roles
    document.addEventListener('keydown', function(e) {
        if (e.target.classList.contains('crud-role-item')) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const roleId = e.target.querySelector('input[type="checkbox"]').value;
                crudToggleRole(e.target, roleId);
            }
        }
    });

    // Hacer los elementos de rol enfocables
    const roleItems = document.querySelectorAll('.crud-role-item');
    roleItems.forEach((item, index) => {
        item.setAttribute('tabindex', '0');
        
        // Agregar indicadores visuales de enfoque
        item.addEventListener('focus', function() {
            this.style.outline = '2px solid var(--primary-red)';
            this.style.outlineOffset = '2px';
        });
        
        item.addEventListener('blur', function() {
            this.style.outline = 'none';
        });
    });

    // Limpiar clases de error cuando el usuario empieza a escribir
    const formControls = document.querySelectorAll('.crud-form-control');
    formControls.forEach(control => {
        control.addEventListener('input', function() {
            this.classList.remove('crud-form-error');
        });
    });

    // Formateo automático de teléfono para números bolivianos
    const phoneField = document.getElementById('crudTelefono');
    if (phoneField) {
        phoneField.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            
            if (value.startsWith('591')) {
                if (value.length <= 11) {
                    this.value = '+' + value.replace(/(\d{3})(\d{1})(\d{7})/, '$1 $2 $3');
                }
            } else if (value.length <= 8) {
                this.value = value.replace(/(\d{1})(\d{7})/, '$1 $2');
            }
        });
    }

    // Validación final antes del envío
    function validateForm() {
        const nombre = document.getElementById('crudNombre').value.trim();
        const apellido = document.getElementById('crudApellido').value.trim();
        const email = document.getElementById('crudEmail').value.trim();
        const password = document.getElementById('crudPassword').value;
        const confirmPassword = document.getElementById('crudPasswordConfirmation').value;
        
        let isValid = true;

        if (!nombre) {
            document.getElementById('crudNombre').classList.add('crud-form-error');
            isValid = false;
        }

        if (!apellido) {
            document.getElementById('crudApellido').classList.add('crud-form-error');
            isValid = false;
        }

        if (!email) {
            document.getElementById('crudEmail').classList.add('crud-form-error');
            isValid = false;
        }

        if (!password || password.length < 8) {
            document.getElementById('crudPassword').classList.add('crud-form-error');
            isValid = false;
        }

        if (password !== confirmPassword) {
            document.getElementById('crudPasswordConfirmation').classList.add('crud-form-error');
            isValid = false;
        }

        return isValid;
    }

    // Evento de validación al enviar
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                
                // Scroll al primer error
                const firstError = document.querySelector('.crud-form-error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
                
                // Mostrar notificación de error
                if (!document.querySelector('.crud-alert-danger')) {
                    const alertHTML = `
                        <div class="crud-alert crud-alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><strong>ERROR:</strong> Por favor corrige los errores en el formulario antes de continuar.</span>
                            <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    `;
                    
                    document.querySelector('.crud-create-wrapper').insertAdjacentHTML('afterbegin', alertHTML);
                }
            }
        });
    }
});
</script>

