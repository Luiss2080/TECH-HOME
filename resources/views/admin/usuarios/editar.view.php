<?php
$title = $title ?? 'Editar Usuario';
$usuario = $usuario ?? null;
$roles = $roles ?? [];
$usuarioRoles = $usuarioRoles ?? [];
$errors = $errors ?? [];

// Verificar que el usuario existe
if (!$usuario) {
    header('Location: ' . route('usuarios'));
    exit();
}
?>

<!-- Estilos específicos para el módulo CRUD - Editar -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">

<!-- Contenedor principal del CRUD de edición -->
<div class="crud-edit-container">
    <div class="crud-edit-wrapper">

        <!-- Header principal con información del usuario -->
        <div class="crud-section-card">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon">
                        <i class="fas fa-user-edit"></i>
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
                                    <i class="fas fa-user-edit"></i>
                                    Editar Usuario
                                </li>
                            </ol>
                        </nav>
                        <h1 class="crud-section-title">Editar Usuario</h1>
                        <p class="crud-section-subtitle">
                            Actualiza la información de <?= $usuario ? htmlspecialchars($usuario->nombre . ' ' . ($usuario->apellido ?? '')) : 'Usuario' ?>
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
                <p class="crud-section-subtitle">Datos básicos del usuario en el sistema</p>
            </div>
            
            <div class="crud-form-body">
                <form method="POST" action="<?= $usuario ? route('usuarios.update', ['id' => $usuario->id]) : '#' ?>" id="crudFormEditarUsuario" class="crud-form">
                    <?php CSRF() ?>
                    <input type="hidden" name="_method" value="PUT">
                    
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
                                   value="<?= $usuario ? htmlspecialchars(old('nombre', $usuario->nombre)) : '' ?>" 
                                   required 
                                   data-original="<?= $usuario ? htmlspecialchars($usuario->nombre) : '' ?>"
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
                                   value="<?= $usuario ? htmlspecialchars(old('apellido', $usuario->apellido)) : '' ?>" 
                                   required 
                                   data-original="<?= $usuario ? htmlspecialchars($usuario->apellido) : '' ?>"
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
                                   value="<?= $usuario ? htmlspecialchars(old('email', $usuario->email)) : '' ?>" 
                                   required 
                                   data-original="<?= $usuario ? htmlspecialchars($usuario->email) : '' ?>"
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
                                   value="<?= $usuario ? htmlspecialchars(old('telefono', $usuario->telefono)) : '' ?>" 
                                   data-original="<?= $usuario ? htmlspecialchars($usuario->telefono ?? '') : '' ?>"
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
                                   value="<?= $usuario ? htmlspecialchars(old('fecha_nacimiento', $usuario->fecha_nacimiento)) : '' ?>"
                                   data-original="<?= $usuario ? htmlspecialchars($usuario->fecha_nacimiento ?? '') : '' ?>">
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
                                    required
                                    data-original="<?= $usuario ? ($usuario->estado == 1 ? 'activo' : 'inactivo') : 'inactivo' ?>">
                                <option value="activo" <?= old('estado', $usuario ? ($usuario->estado == 1 ? 'activo' : 'inactivo') : 'inactivo') === 'activo' ? 'selected' : '' ?>>
                                    Activo
                                </option>
                                <option value="inactivo" <?= old('estado', $usuario ? ($usuario->estado == 1 ? 'activo' : 'inactivo') : 'inactivo') === 'inactivo' ? 'selected' : '' ?>>
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
                <p class="crud-section-subtitle">Cambiar contraseña del usuario (Opcional)</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-password-note">
                    <i class="fas fa-info-circle"></i>
                    <span>Deja estos campos vacíos para mantener la contraseña actual del usuario</span>
                </div>
                
                <div class="crud-form-grid">
                    <div class="crud-form-group">
                        <label for="crudPassword" class="crud-form-label">
                            <i class="fas fa-lock"></i>
                            Nueva Contraseña
                        </label>
                        <input type="password" 
                               class="crud-form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                               id="crudPassword" 
                               name="password" 
                               form="crudFormEditarUsuario"
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
                            Confirmar Nueva Contraseña
                        </label>
                        <input type="password" 
                               class="crud-form-control <?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>"
                               id="crudPasswordConfirmation" 
                               name="password_confirmation"
                               form="crudFormEditarUsuario"
                               placeholder="Repite la nueva contraseña">
                        <div class="crud-form-text">
                            <i class="fas fa-check-double"></i>
                            Debe coincidir con la nueva contraseña
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
                <p class="crud-section-subtitle">Selecciona los roles y permisos para este usuario</p>
            </div>
            
            <div class="crud-form-body">
                <?php if (isset($roles) && !empty($roles)): ?>
                    <div class="crud-roles-grid">
                        <?php foreach ($roles as $rol): ?>
                            <div class="crud-role-item <?= in_array($rol->id, old('roles', $usuarioRoles)) ? 'selected' : '' ?>" 
                                 onclick="crudToggleRole(this, <?= $rol->id ?>)"
                                 tabindex="0"
                                 role="checkbox"
                                 aria-checked="<?= in_array($rol->id, old('roles', $usuarioRoles)) ? 'true' : 'false' ?>">
                                <input class="crud-role-checkbox" 
                                       type="checkbox"
                                       id="crudRole<?= $rol->id ?>" 
                                       name="roles[]"
                                       value="<?= $rol->id ?>"
                                       form="crudFormEditarUsuario"
                                       <?= in_array($rol->id, old('roles', $usuarioRoles)) ? 'checked' : '' ?>>
                                <div class="crud-role-content">
                                    <div class="crud-role-header">
                                        <div class="crud-role-icon">
                                            <i class="fas fa-user-shield"></i>
                                        </div>
                                        <div class="crud-role-name"><?= htmlspecialchars($rol->nombre) ?></div>
                                    </div>
                                    <div class="crud-role-description"><?= htmlspecialchars($rol->descripcion ?? 'Sin descripción disponible') ?></div>
                                </div>
                            </div>
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

        <!-- Sección: Información del Usuario -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-info-circle"></i>
                    Información del Sistema
                </h2>
                <p class="crud-section-subtitle">Detalles técnicos y estadísticas del usuario</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-info-panel">
                    <div class="crud-info-tabs">
                        <button class="crud-info-tab active" data-tab="detalles">
                            <i class="fas fa-list"></i>
                            Detalles
                        </button>
                        <button class="crud-info-tab" data-tab="actividad">
                            <i class="fas fa-chart-line"></i>
                            Actividad
                        </button>
                        <button class="crud-info-tab" data-tab="permisos">
                            <i class="fas fa-shield-alt"></i>
                            Permisos
                        </button>
                    </div>
                    
                    <div class="crud-info-pane active" id="detalles">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-hashtag"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>ID de Usuario:</strong> #<?= $usuario->id ?><br>
                                    Identificador único en la base de datos
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Fecha de Creación:</strong> <?= date('d/m/Y H:i', strtotime($usuario->fecha_creacion)) ?><br>
                                    Momento en que se registró por primera vez
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Última Actualización:</strong> <?= $usuario->fecha_actualizacion ? date('d/m/Y H:i', strtotime($usuario->fecha_actualizacion)) : 'Nunca' ?><br>
                                    Última modificación de datos
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-toggle-on"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Estado Actual:</strong> <span class="<?= $usuario->estado == 1 ? 'crud-text-success' : 'crud-text-danger' ?>"><?= $usuario->estado == 1 ? 'Activo' : 'Inactivo' ?></span><br>
                                    Estado de la cuenta en el sistema
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="actividad">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-sign-in-alt"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Último Acceso:</strong> Información no disponible<br>
                                    Última vez que inició sesión
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Sesiones Activas:</strong> 0 sesiones<br>
                                    Dispositivos conectados actualmente
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Historial de Cambios:</strong> Ver registro completo<br>
                                    Todas las modificaciones realizadas
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="permisos">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-user-shield"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Roles Asignados:</strong> <?= count($usuarioRoles) ?> rol(es)<br>
                                    Roles activos para este usuario
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Permisos Especiales:</strong> Ninguno<br>
                                    Permisos adicionales fuera de roles
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Nivel de Acceso:</strong> Estándar<br>
                                    Clasificación general de permisos
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 

        <!-- Sección: Acciones Rápidas -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-bolt"></i>
                    Acciones Rápidas
                </h2>
                <p class="crud-section-subtitle">Herramientas adicionales para gestionar este usuario</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Enviar Notificación</h4>
                            <p>Envía un correo personalizado al usuario</p>
                            <button type="button" class="crud-btn-action" onclick="openNotificationModal()">
                                <i class="fas fa-paper-plane"></i>
                                Enviar
                            </button>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Restablecer Contraseña</h4>
                            <p>Genera una nueva contraseña temporal</p>
                            <button type="button" class="crud-btn-action" onclick="resetPassword(<?= $usuario->id ?>)">
                                <i class="fas fa-sync"></i>
                                Restablecer
                            </button>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Suspender Cuenta</h4>
                            <p>Deshabilita temporalmente el acceso</p>
                            <button type="button" class="crud-btn-action danger" onclick="suspendUser(<?= $usuario->id ?>)">
                                <i class="fas fa-pause"></i>
                                Suspender
                            </button>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-file-download"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Exportar Datos</h4>
                            <p>Descarga la información del usuario</p>
                            <button type="button" class="crud-btn-action" onclick="exportUserData(<?= $usuario->id ?>)">
                                <i class="fas fa-download"></i>
                                Exportar
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
                <button type="submit" form="crudFormEditarUsuario" class="crud-btn crud-btn-primary" id="crudBtnSubmit">
                    <i class="fas fa-save"></i>
                    Actualizar Usuario
                </button>
            </div>
        </div>

          <!-- Espacio de separacion -->
        <div style="height: 20px;"></div> 

    </div>
</div>

<!-- Indicador de cambios flotante -->
<div class="crud-changes-indicator" id="crudChangesIndicator">
    <i class="fas fa-exclamation-circle"></i>
    <span>Tienes <span class="crud-changes-count" id="crudChangesCount">0</span> cambios sin guardar</span>
</div>

<!-- JavaScript específico para editar usuario -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let changesCount = 0;
    const changesIndicator = document.getElementById('crudChangesIndicator');
    const changesCountEl = document.getElementById('crudChangesCount');
    const form = document.getElementById('crudFormEditarUsuario');
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
        
        updateChangesCount();
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

    // Detectar cambios en los campos del formulario
    function detectChanges() {
        const formControls = document.querySelectorAll('.crud-form-control');
        let changes = 0;

        formControls.forEach(control => {
            const original = control.dataset.original || '';
            const current = control.value || '';
            const hasChanged = original !== current;

            if (hasChanged) {
                control.classList.add('modified');
                control.parentElement.classList.add('has-changes');
                changes++;
            } else {
                control.classList.remove('modified');
                control.parentElement.classList.remove('has-changes');
            }
        });

        // Detectar cambios en roles
        const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
        const originalRoles = <?= json_encode($usuarioRoles) ?>;
        const currentRoles = Array.from(roleCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => parseInt(cb.value));

        const rolesChanged = JSON.stringify(originalRoles.sort()) !== JSON.stringify(currentRoles.sort());
        if (rolesChanged) {
            changes++;
        }

        changesCount = changes;
        updateChangesIndicator();
    }

    function updateChangesIndicator() {
        changesCountEl.textContent = changesCount;
        if (changesCount > 0) {
            changesIndicator.classList.add('show');
        } else {
            changesIndicator.classList.remove('show');
        }
    }

    function updateChangesCount() {
        detectChanges();
    }

    // Event listeners para detectar cambios
    document.querySelectorAll('.crud-form-control').forEach(control => {
        control.addEventListener('input', updateChangesCount);
        control.addEventListener('change', updateChangesCount);
    });

    // Validación de contraseñas en tiempo real
    const passwordField = document.getElementById('crudPassword');
    const confirmPasswordField = document.getElementById('crudPasswordConfirmation');

    function validatePasswords() {
        const password = passwordField.value;
        const confirmPassword = confirmPasswordField.value;

        if (password || confirmPassword) {
            if (password.length >= 8) {
                passwordField.classList.remove('crud-form-error');
                passwordField.classList.add('crud-form-success');
            } else if (password.length > 0) {
                passwordField.classList.remove('crud-form-success');
                passwordField.classList.add('crud-form-error');
            }

            if (confirmPassword.length > 0) {
                if (password === confirmPassword && password.length >= 8) {
                    confirmPasswordField.classList.remove('crud-form-error');
                    confirmPasswordField.classList.add('crud-form-success');
                } else {
                    confirmPasswordField.classList.remove('crud-form-success');
                    confirmPasswordField.classList.add('crud-form-error');
                }
            }
        } else {
            passwordField.classList.remove('crud-form-success', 'crud-form-error');
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
            }
        });
    }

    // Manejo del envío del formulario
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            const password = passwordField.value;
            const confirmPassword = confirmPasswordField.value;

            if (password || confirmPassword) {
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
            }

            const rolesSeleccionados = document.querySelectorAll('input[name="roles[]"]:checked');
            if (rolesSeleccionados.length === 0) {
                e.preventDefault();
                alert('Debe seleccionar al menos un rol para el usuario');
                return false;
            }

            submitBtn.classList.add('crud-form-loading');
            submitBtn.disabled = true;
            
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando Usuario...';
            
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
    window.openNotificationModal = function() {
        alert('Funcionalidad de notificación en desarrollo');
    };

    window.resetPassword = function(userId) {
        if (confirm('¿Estás seguro de que deseas restablecer la contraseña de este usuario?')) {
            alert('Funcionalidad de restablecimiento en desarrollo');
        }
    };

    window.suspendUser = function(userId) {
        if (confirm('¿Estás seguro de que deseas suspender esta cuenta de usuario?')) {
            alert('Funcionalidad de suspensión en desarrollo');
        }
    };

    window.exportUserData = function(userId) {
        alert('Iniciando exportación de datos del usuario...');
    };

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

    // Advertir sobre cambios no guardados al salir
    window.addEventListener('beforeunload', function(e) {
        if (changesCount > 0) {
            const message = 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
            e.returnValue = message;
            return message;
        }
    });

    // Inicializar detección de cambios
    updateChangesCount();
});
</script>