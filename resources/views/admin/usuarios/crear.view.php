<?php
$title = $title ?? 'Crear Nuevo Usuario';
$errors = $errors ?? [];
$old = $old ?? [];
$roles = $roles ?? [];
?>

<!-- Estilos específicos para el módulo CRUD - Crear -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">

<!-- Contenedor principal del CRUD de creación -->
<div class="crud-edit-container">
    <div class="crud-edit-wrapper">

        <!-- Header principal con información del nuevo usuario -->
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
                        <p class="crud-section-subtitle">
                            Registra un nuevo usuario en el sistema y asigna sus roles correspondientes
                        </p>
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

        <!-- Alertas de sesión -->
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

        <!-- Errores de validación -->
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

        <!-- Sección: Información Personal -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-user"></i>
                    Información Personal
                </h2>
                <p class="crud-section-subtitle">Datos básicos del nuevo usuario en el sistema</p>
            </div>
            
            <div class="crud-form-body">
                <form method="POST" action="<?= route('usuarios.store') ?>" id="crudFormCrearUsuario" class="crud-form">
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
                                   placeholder="+591 7xxxxxxx">
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

                        <div class="crud-form-group">
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

                        <div class="crud-form-group">
                            <label for="crudEstado" class="crud-form-label">
                                <i class="fas fa-toggle-on"></i>
                                Estado del Usuario
                                <span class="crud-required">*</span>
                            </label>
                            <select class="crud-form-control <?= isset($errors['estado']) ? 'is-invalid' : '' ?>"
                                    id="crudEstado" 
                                    name="estado" 
                                    required>
                                <option value="activo" <?= old('estado', 'activo') === 'activo' ? 'selected' : '' ?>>
                                    Activo
                                </option>
                                <option value="inactivo" <?= old('estado') === 'inactivo' ? 'selected' : '' ?>>
                                    Inactivo
                                </option>
                            </select>
                            <div class="crud-form-text">
                                <i class="fas fa-info-circle"></i>
                                Los usuarios inactivos no pueden acceder al sistema
                            </div>
                            <?php if (isset($errors['estado'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array($errors['estado']) ? $errors['estado'][0] : $errors['estado'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sección: Credenciales de Seguridad -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-key"></i>
                    Credenciales de Seguridad
                </h2>
                <p class="crud-section-subtitle">Configuración de contraseña para el acceso al sistema</p>
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
                               form="crudFormCrearUsuario"
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
                               form="crudFormCrearUsuario"
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

        <!-- Sección: Asignación de Roles -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-user-tag"></i>
                    Asignación de Roles
                </h2>
                <p class="crud-section-subtitle">Selecciona los roles y permisos para este nuevo usuario</p>
            </div>
            
            <div class="crud-form-body">
                <?php if (isset($roles) && !empty($roles)): ?>
                    <div class="crud-roles-grid">
                        <?php foreach ($roles as $rol): ?>
                            <?php if (strtolower($rol->nombre) !== 'mirones'): ?>
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
                                           form="crudFormCrearUsuario"
                                           <?= in_array($rol->id, $old['roles'] ?? []) ? 'checked' : '' ?>>
                                    <div class="crud-role-content">
                                        <div class="crud-role-header">
                                            <div class="crud-role-icon">
                                                <i class="fas fa-<?= $rol->nombre === 'Administrador' ? 'crown' : ($rol->nombre === 'Docente' ? 'chalkboard-teacher' : ($rol->nombre === 'Estudiante' ? 'graduation-cap' : ($rol->nombre === 'Vendedor' ? 'store' : 'user-shield'))) ?>"></i>
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
                    <div class="crud-empty-state">
                        <div class="crud-empty-state-icon">
                            <i class="fas fa-user-tag"></i>
                        </div>
                        <h4>No hay roles disponibles</h4>
                        <p>No se encontraron roles en el sistema. Contacta al administrador para configurar los roles necesarios.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Sección: Información de Ayuda -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-info-circle"></i>
                    Información de Ayuda
                </h2>
                <p class="crud-section-subtitle">Guía detallada para crear usuarios correctamente</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-info-panel">
                    <div class="crud-info-tabs">
                        <button class="crud-info-tab active" data-tab="roles">
                            <i class="fas fa-user-tag"></i>
                            Roles Disponibles
                        </button>
                        <button class="crud-info-tab" data-tab="seguridad">
                            <i class="fas fa-shield-alt"></i>
                            Seguridad
                        </button>
                        <button class="crud-info-tab" data-tab="ayuda">
                            <i class="fas fa-question-circle"></i>
                            Ayuda General
                        </button>
                    </div>
                    
                    <div class="crud-info-pane active" id="roles">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-crown"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Administrador:</strong> Acceso completo al sistema, gestión de usuarios, configuración y supervisión general de todas las funcionalidades.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Docente:</strong> Gestión de cursos, materiales educativos, calificaciones y seguimiento académico de estudiantes.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Estudiante:</strong> Acceso a contenido educativo, actividades, tareas y recursos de aprendizaje asignados.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Vendedor:</strong> Gestión de ventas, inventario de componentes electrónicos y atención a clientes del área comercial.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="seguridad">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Contraseñas seguras:</strong> Utiliza al menos 8 caracteres con combinación de mayúsculas, minúsculas, números y símbolos especiales.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Principio de menor privilegio:</strong> Asigna solo los roles y permisos mínimos necesarios para las funciones del usuario.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-envelope-open-text"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Verificación de email:</strong> Confirma que el correo electrónico sea válido y accesible para el nuevo usuario.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="ayuda">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-asterisk"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Campos obligatorios:</strong> Los campos marcados con <span style="color: var(--danger-color);">*</span> son requeridos para crear el usuario correctamente.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-bell"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Notificación automática:</strong> El usuario recibirá un correo de bienvenida con sus credenciales de acceso al sistema.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-edit"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Edición posterior:</strong> Toda la información del usuario puede modificarse posteriormente desde la gestión de usuarios.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Acciones Rápidas Previas -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-bolt"></i>
                    Acciones Rápidas
                </h2>
                <p class="crud-section-subtitle">Herramientas útiles antes de crear el usuario</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Verificar Email</h4>
                            <p>Comprobar si el email ya está registrado</p>
                            <button type="button" class="crud-btn-action" onclick="verificarEmail()">
                                <i class="fas fa-check"></i>
                                Verificar
                            </button>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-random"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Generar Contraseña</h4>
                            <p>Crear una contraseña segura automáticamente</p>
                            <button type="button" class="crud-btn-action" onclick="generarPassword()">
                                <i class="fas fa-magic"></i>
                                Generar
                            </button>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-list"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Ver Usuarios Existentes</h4>
                            <p>Consultar la lista actual de usuarios</p>
                            <a href="<?= route('usuarios') ?>" class="crud-btn-action">
                                <i class="fas fa-external-link-alt"></i>
                                Ver Lista
                            </a>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-file-import"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Importar Usuarios</h4>
                            <p>Crear múltiples usuarios desde archivo</p>
                            <button type="button" class="crud-btn-action" onclick="importarUsuarios()">
                                <i class="fas fa-upload"></i>
                                Importar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción principal -->
        <div class="crud-section-card">
            <div class="crud-form-actions">
                <a href="<?= route('usuarios') ?>" class="crud-btn crud-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Cancelar y Volver
                </a>
                <button type="submit" form="crudFormCrearUsuario" class="crud-btn crud-btn-primary" id="crudBtnSubmit">
                    <i class="fas fa-save"></i>
                    Crear Usuario
                </button>
            </div>
        </div>

        <!-- Espacio de separación -->
        <div style="height: 20px;"></div> 

    </div>
</div>

<!-- JavaScript específico para crear usuario -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('crudFormCrearUsuario');
    const submitBtn = document.getElementById('crudBtnSubmit');

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

    // Función para cambiar tabs de información
    document.querySelectorAll('.crud-info-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Remover clase active de todos los tabs
            document.querySelectorAll('.crud-info-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.crud-info-pane').forEach(p => p.classList.remove('active'));
            
            // Activar tab seleccionado
            this.classList.add('active');
            const targetPane = document.getElementById(this.dataset.tab);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

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
        } else if (password.length > 0) {
            passwordField.classList.remove('crud-form-success');
            passwordField.classList.add('crud-form-error');
        } else {
            passwordField.classList.remove('crud-form-success', 'crud-form-error');
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
        } else {
            confirmPasswordField.classList.remove('crud-form-success', 'crud-form-error');
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

    // Formateo automático de teléfono
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

    // Manejo del envío del formulario
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            const password = passwordField.value;
            const confirmPassword = confirmPasswordField.value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                confirmPasswordField.focus();
                return false;
            }

            if (password && password.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres');
                passwordField.focus();
                return false;
            }

            const rolesSeleccionados = document.querySelectorAll('input[name="roles[]"]:checked');
            if (rolesSeleccionados.length === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos un rol para el usuario');
                document.querySelector('.crud-roles-grid').scrollIntoView({ behavior: 'smooth' });
                return false;
            }

            submitBtn.classList.add('crud-form-loading');
            submitBtn.disabled = true;
            
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando Usuario...';
            
            setTimeout(() => {
                if (document.querySelector('.crud-alert-danger')) {
                    submitBtn.classList.remove('crud-form-loading');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                }
            }, 1000);
        });
    }

    // Funciones para acciones rápidas
    window.verificarEmail = function() {
        const emailField = document.getElementById('crudEmail');
        const email = emailField.value.trim();
        
        if (!email) {
            alert('Por favor ingresa un email para verificar');
            emailField.focus();
            return;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            alert('Por favor ingresa un email válido');
            emailField.focus();
            return;
        }
        
        // Aquí iría la lógica para verificar el email en el servidor
        alert('Verificando email: ' + email + '\n\nEsta funcionalidad se implementará próximamente.');
    };

    window.generarPassword = function() {
        const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*';
        let password = '';
        
        // Asegurar al menos un carácter de cada tipo
        password += 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'[Math.floor(Math.random() * 26)]; // Mayúscula
        password += 'abcdefghijklmnopqrstuvwxyz'[Math.floor(Math.random() * 26)]; // Minúscula
        password += '0123456789'[Math.floor(Math.random() * 10)]; // Número
        password += '!@#$%^&*'[Math.floor(Math.random() * 8)]; // Símbolo
        
        // Completar hasta 12 caracteres
        for (let i = 4; i < 12; i++) {
            password += caracteres[Math.floor(Math.random() * caracteres.length)];
        }
        
        // Mezclar los caracteres
        password = password.split('').sort(() => 0.5 - Math.random()).join('');
        
        document.getElementById('crudPassword').value = password;
        document.getElementById('crudPasswordConfirmation').value = password;
        
        validatePasswords();
        
        alert('Contraseña generada automáticamente.\nAsegúrate de que el usuario la cambie en su primer acceso.');
    };

    window.importarUsuarios = function() {
        alert('Funcionalidad de importación masiva en desarrollo.\n\nPróximamente podrás crear múltiples usuarios desde un archivo CSV o Excel.');
    };

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

    // Validación visual al escribir en campos requeridos
    const requiredFields = document.querySelectorAll('input[required]');
    requiredFields.forEach(field => {
        field.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('crud-form-error');
                this.classList.add('crud-form-success');
            } else {
                this.classList.remove('crud-form-success');
                if (this.classList.contains('is-invalid')) {
                    this.classList.add('crud-form-error');
                }
            }
        });

        field.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('crud-form-error');
                this.classList.remove('crud-form-success');
            }
        });
    });

    // Mostrar/ocultar contraseña
    function togglePasswordVisibility(fieldId, iconElement) {
        const field = document.getElementById(fieldId);
        const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
        field.setAttribute('type', type);
        
        iconElement.classList.toggle('fa-eye');
        iconElement.classList.toggle('fa-eye-slash');
    }

    // Validación final antes del envío
    function validateForm() {
        let isValid = true;
        const errors = [];

        // Validar campos requeridos
        const nombre = document.getElementById('crudNombre').value.trim();
        const apellido = document.getElementById('crudApellido').value.trim();
        const email = document.getElementById('crudEmail').value.trim();
        const password = document.getElementById('crudPassword').value;
        const confirmPassword = document.getElementById('crudPasswordConfirmation').value;

        if (!nombre) {
            errors.push('El nombre es obligatorio');
            document.getElementById('crudNombre').classList.add('crud-form-error');
            isValid = false;
        }

        if (!apellido) {
            errors.push('El apellido es obligatorio');
            document.getElementById('crudApellido').classList.add('crud-form-error');
            isValid = false;
        }

        if (!email) {
            errors.push('El email es obligatorio');
            document.getElementById('crudEmail').classList.add('crud-form-error');
            isValid = false;
        }

        if (!password || password.length < 8) {
            errors.push('La contraseña debe tener al menos 8 caracteres');
            document.getElementById('crudPassword').classList.add('crud-form-error');
            isValid = false;
        }

        if (password !== confirmPassword) {
            errors.push('Las contraseñas no coinciden');
            document.getElementById('crudPasswordConfirmation').classList.add('crud-form-error');
            isValid = false;
        }

        const rolesSeleccionados = document.querySelectorAll('input[name="roles[]"]:checked');
        if (rolesSeleccionados.length === 0) {
            errors.push('Debe seleccionar al menos un rol');
            isValid = false;
        }

        if (!isValid && errors.length > 0) {
            alert('Errores encontrados:\n• ' + errors.join('\n• '));
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
            }
        });
    }

    // Sugerencias automáticas para email corporativo
    const nombreField = document.getElementById('crudNombre');
    const apellidoField = document.getElementById('crudApellido');
    
    function sugerirEmail() {
        const nombre = nombreField.value.toLowerCase().replace(/\s+/g, '');
        const apellido = apellidoField.value.toLowerCase().replace(/\s+/g, '');
        
        if (nombre && apellido && !emailField.value) {
            const sugerencia = `${nombre}.${apellido}@techhome.bo`;
            emailField.placeholder = `Sugerencia: ${sugerencia}`;
        }
    }

    if (nombreField && apellidoField) {
        nombreField.addEventListener('blur', sugerirEmail);
        apellidoField.addEventListener('blur', sugerirEmail);
    }
});
</script>