<?php
$title = $title ?? 'Editar Roles de Usuario';
$usuario = $usuario ?? null;
$rolesDisponibles = $rolesDisponibles ?? [];
$rolesUsuario = $rolesUsuario ?? [];
$errors = $errors ?? [];

// Verificar que el usuario existe
if (!$usuario) {
    header('Location: ' . route('usuarios'));
    exit();
}
?>

<!-- Estilos específicos para el módulo CRUD - Editar Roles -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">

<!-- Contenedor principal del CRUD de edición de roles -->
<div class="crud-edit-container">
    <div class="crud-edit-wrapper">

        <!-- Header principal con información del usuario -->
        <div class="crud-section-card">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon">
                        <i class="fas fa-user-cog"></i>
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
                                    <i class="fas fa-user-cog"></i>
                                    Editar Roles
                                </li>
                            </ol>
                        </nav>
                        <h1 class="crud-section-title">Editar Roles de Usuario</h1>
                        <p class="crud-section-subtitle">
                            Gestiona los roles asignados a <?= $usuario ? htmlspecialchars($usuario->nombre . ' ' . ($usuario->apellido ?? '')) : 'Usuario' ?>
                        </p>
                    </div>
                </div>
                <div class="crud-section-header-actions">
                    <a href="<?= route('usuarios') ?>" class="crud-section-action-header crud-btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Volver a Usuarios
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

        <!-- Sección: Información del Usuario -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-user"></i>
                    Información del Usuario
                </h2>
                <p class="crud-section-subtitle">Datos del usuario al que se asignarán los roles</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-info-panel">
                    <div class="crud-info-tabs">
                        <button class="crud-info-tab active" data-tab="datos">
                            <i class="fas fa-id-card"></i>
                            Datos Básicos
                        </button>
                        <button class="crud-info-tab" data-tab="estadisticas">
                            <i class="fas fa-chart-pie"></i>
                            Estadísticas
                        </button>
                        <button class="crud-info-tab" data-tab="historial">
                            <i class="fas fa-history"></i>
                            Historial
                        </button>
                    </div>
                    
                    <div class="crud-info-pane active" id="datos">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Nombre Completo:</strong> <?= htmlspecialchars($usuario->nombre . ' ' . ($usuario->apellido ?? '')) ?><br>
                                    Usuario registrado en el sistema
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Correo Electrónico:</strong> <?= htmlspecialchars($usuario->email) ?><br>
                                    Email principal de contacto
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
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-calendar-plus"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Fecha de Registro:</strong> <?= date('d/m/Y H:i', strtotime($usuario->fecha_creacion)) ?><br>
                                    Momento en que se registró por primera vez
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="estadisticas">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Roles Actuales:</strong> <?= count($rolesUsuario) ?> rol(es)<br>
                                    Cantidad de roles asignados actualmente
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Nivel de Acceso:</strong> <?= empty($rolesUsuario) ? 'Sin acceso' : 'Estándar' ?><br>
                                    Clasificación general de permisos
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Última Actualización:</strong> <?= $usuario->fecha_actualizacion ? date('d/m/Y H:i', strtotime($usuario->fecha_actualizacion)) : 'Nunca' ?><br>
                                    Última modificación de roles
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="historial">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Cambios de Roles:</strong> Información no disponible<br>
                                    Historial de modificaciones de roles
                                </div>
                            </div>
                            
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
                                    <i class="fas fa-user-edit"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Editado Por:</strong> <?= session('user_nombre') ?? 'Sistema' ?><br>
                                    Usuario que realiza esta modificación
                                </div>
                            </div>
                        </div>
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
                <p class="crud-section-subtitle">Selecciona y modifica los roles asignados a este usuario</p>
            </div>
            
            <div class="crud-form-body">
                <form method="POST" action="<?= route('usuarios.roles.update', ['id' => $usuario->id]) ?>" id="crudFormEditarRoles" class="crud-form">
                    <?php CSRF() ?>
                    <input type="hidden" name="_method" value="PUT">
                    
                    <?php if (isset($rolesDisponibles) && !empty($rolesDisponibles)): ?>
                        <div class="crud-roles-grid">
                            <?php foreach ($rolesDisponibles as $rol): ?>
                                <div class="crud-role-item <?= in_array($rol->id, $rolesUsuario) ? 'selected' : '' ?>" 
                                     onclick="crudToggleRole(this, <?= $rol->id ?>)"
                                     tabindex="0"
                                     role="checkbox"
                                     aria-checked="<?= in_array($rol->id, $rolesUsuario) ? 'true' : 'false' ?>"
                                     data-original="<?= in_array($rol->id, $rolesUsuario) ? 'true' : 'false' ?>">
                                    <input class="crud-role-checkbox" 
                                           type="checkbox"
                                           id="crudRole<?= $rol->id ?>" 
                                           name="roles[]"
                                           value="<?= $rol->id ?>"
                                           <?= in_array($rol->id, $rolesUsuario) ? 'checked' : '' ?>>
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
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="crud-form-text crud-mt-2">
                            <i class="fas fa-info-circle"></i>
                            Puedes seleccionar múltiples roles para este usuario. Los cambios se aplicarán inmediatamente al guardar.
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
                </form>
            </div>
        </div>

        <!-- Sección: Comparación de Cambios -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-exchange-alt"></i>
                    Comparación de Cambios
                </h2>
                <p class="crud-section-subtitle">Visualiza los cambios que se realizarán en los roles del usuario</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-info-panel">
                    <div class="crud-info-tabs">
                        <button class="crud-info-tab active" data-tab="actuales">
                            <i class="fas fa-user-check"></i>
                            Roles Actuales
                        </button>
                        <button class="crud-info-tab" data-tab="nuevos">
                            <i class="fas fa-user-plus"></i>
                            Roles Nuevos
                        </button>
                        <button class="crud-info-tab" data-tab="comparacion">
                            <i class="fas fa-balance-scale"></i>
                            Comparación
                        </button>
                    </div>
                    
                    <div class="crud-info-pane active" id="actuales">
                        <div class="crud-info-list" id="rolesActualesList">
                            <?php if (!empty($rolesUsuario)): ?>
                                <?php foreach ($rolesDisponibles as $rol): ?>
                                    <?php if (in_array($rol->id, $rolesUsuario)): ?>
                                        <div class="crud-info-item">
                                            <div class="crud-info-item-icon">
                                                <i class="fas fa-<?= $rol->nombre === 'Administrador' ? 'crown' : ($rol->nombre === 'Docente' ? 'chalkboard-teacher' : ($rol->nombre === 'Estudiante' ? 'graduation-cap' : ($rol->nombre === 'Vendedor' ? 'store' : 'user-shield'))) ?>"></i>
                                            </div>
                                            <div class="crud-info-item-content">
                                                <strong><?= htmlspecialchars($rol->nombre) ?>:</strong> <?= htmlspecialchars($rol->descripcion ?? 'Sin descripción') ?><br>
                                                Rol asignado actualmente
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="crud-empty-state">
                                    <div class="crud-empty-state-icon">
                                        <i class="fas fa-user-minus"></i>
                                    </div>
                                    <h4>Sin roles asignados</h4>
                                    <p>Este usuario no tiene roles asignados actualmente.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="nuevos">
                        <div class="crud-info-list" id="rolesNuevosList">
                            <div class="crud-empty-state">
                                <div class="crud-empty-state-icon">
                                    <i class="fas fa-user-clock"></i>
                                </div>
                                <h4>Selecciona roles</h4>
                                <p>Los roles seleccionados aparecerán aquí para previsualizar los cambios.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="comparacion">
                        <div class="crud-info-list" id="comparacionList">
                            <div class="crud-empty-state">
                                <div class="crud-empty-state-icon">
                                    <i class="fas fa-equals"></i>
                                </div>
                                <h4>Sin cambios</h4>
                                <p>Modifica los roles para ver una comparación detallada de los cambios.</p>
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
                <p class="crud-section-subtitle">Herramientas para gestión rápida de roles</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Seleccionar Todos</h4>
                            <p>Asignar todos los roles disponibles</p>
                            <button type="button" class="crud-btn-action" onclick="seleccionarTodosRoles()">
                                <i class="fas fa-check-circle"></i>
                                Seleccionar
                            </button>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Deseleccionar Todos</h4>
                            <p>Remover todos los roles del usuario</p>
                            <button type="button" class="crud-btn-action danger" onclick="deseleccionarTodosRoles()">
                                <i class="fas fa-minus-circle"></i>
                                Deseleccionar
                            </button>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-undo"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Restaurar Original</h4>
                            <p>Volver a la configuración inicial</p>
                            <button type="button" class="crud-btn-action" onclick="restaurarRolesOriginales()">
                                <i class="fas fa-history"></i>
                                Restaurar
                            </button>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-copy"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Copiar de Usuario</h4>
                            <p>Copiar roles de otro usuario</p>
                            <button type="button" class="crud-btn-action" onclick="copiarRolesUsuario()">
                                <i class="fas fa-user-friends"></i>
                                Copiar
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
                <button type="submit" form="crudFormEditarRoles" class="crud-btn crud-btn-primary" id="crudBtnSubmit" <?= empty($rolesDisponibles) ? 'disabled' : '' ?>>
                    <i class="fas fa-save"></i>
                    Guardar Cambios en Roles
                </button>
            </div>
        </div>

        <!-- Espacio de separación -->
        <div style="height: 20px;"></div> 

    </div>
</div>

<!-- Indicador de cambios flotante -->
<div class="crud-changes-indicator" id="crudChangesIndicator">
    <i class="fas fa-exclamation-circle"></i>
    <span>Tienes <span class="crud-changes-count" id="crudChangesCount">0</span> cambios sin guardar</span>
</div>

<!-- JavaScript específico para editar roles de usuario -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let changesCount = 0;
    const changesIndicator = document.getElementById('crudChangesIndicator');
    const changesCountEl = document.getElementById('crudChangesCount');
    const form = document.getElementById('crudFormEditarRoles');
    const submitBtn = document.getElementById('crudBtnSubmit');
    const rolesOriginales = <?= json_encode($rolesUsuario) ?>;

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
        updateRolesPreviews();
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

    // Detectar cambios en los roles
    function detectChanges() {
        const roleCheckboxes = document.querySelectorAll('input[name="roles[]"]');
        const currentRoles = Array.from(roleCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => parseInt(cb.value));

        const rolesChanged = JSON.stringify(rolesOriginales.sort()) !== JSON.stringify(currentRoles.sort());
        
        changesCount = rolesChanged ? 1 : 0;
        updateChangesIndicator();

        return {
            changed: rolesChanged,
            current: currentRoles,
            original: rolesOriginales
        };
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

    // Actualizar previsualizaciones de roles
    function updateRolesPreviews() {
        const checkboxes = document.querySelectorAll('input[name="roles[]"]');
        const rolesNuevosList = document.getElementById('rolesNuevosList');
        const comparacionList = document.getElementById('comparacionList');
        
        // Obtener roles seleccionados
        const rolesSeleccionados = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => {
                const roleItem = cb.closest('.crud-role-item');
                const roleName = roleItem.querySelector('.crud-role-name').textContent;
                const roleDesc = roleItem.querySelector('.crud-role-description').textContent;
                const roleIcon = roleItem.querySelector('.crud-role-icon i').className;
                return { id: cb.value, name: roleName, description: roleDesc, icon: roleIcon };
            });

        // Actualizar lista de roles nuevos
        if (rolesSeleccionados.length > 0) {
            let nuevosHTML = '';
            rolesSeleccionados.forEach(rol => {
                nuevosHTML += `
                    <div class="crud-info-item">
                        <div class="crud-info-item-icon">
                            <i class="${rol.icon}"></i>
                        </div>
                        <div class="crud-info-item-content">
                            <strong>${rol.name}:</strong> ${rol.description}<br>
                            Rol que será asignado
                        </div>
                    </div>
                `;
            });
            rolesNuevosList.innerHTML = nuevosHTML;
        } else {
            rolesNuevosList.innerHTML = `
                <div class="crud-empty-state">
                    <div class="crud-empty-state-icon">
                        <i class="fas fa-user-minus"></i>
                    </div>
                    <h4>Sin roles seleccionados</h4>
                    <p>No hay roles seleccionados para este usuario.</p>
                </div>
            `;
        }

        // Actualizar comparación
        const changes = detectChanges();
        if (changes.changed) {
            const added = changes.current.filter(id => !rolesOriginales.includes(parseInt(id)));
            const removed = rolesOriginales.filter(id => !changes.current.includes(id));
            
            let comparacionHTML = '';
            
            if (added.length > 0) {
                added.forEach(roleId => {
                    const checkbox = document.querySelector(`input[value="${roleId}"]`);
                    const roleItem = checkbox.closest('.crud-role-item');
                    const roleName = roleItem.querySelector('.crud-role-name').textContent;
                    const roleIcon = roleItem.querySelector('.crud-role-icon i').className;
                    
                    comparacionHTML += `
                        <div class="crud-info-item">
                            <div class="crud-info-item-icon" style="background: var(--success-color);">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="crud-info-item-content">
                                <strong>Agregar rol:</strong> ${roleName}<br>
                                <span class="crud-text-success">Se asignará este rol al usuario</span>
                            </div>
                        </div>
                    `;
                });
            }
            
            if (removed.length > 0) {
                removed.forEach(roleId => {
                    const checkbox = document.querySelector(`input[value="${roleId}"]`);
                    if (checkbox) {
                        const roleItem = checkbox.closest('.crud-role-item');
                        const roleName = roleItem.querySelector('.crud-role-name').textContent;
                        
                        comparacionHTML += `
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon" style="background: var(--danger-color);">
                                    <i class="fas fa-minus"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Remover rol:</strong> ${roleName}<br>
                                    <span class="crud-text-danger">Se quitará este rol del usuario</span>
                                </div>
                            </div>
                        `;
                    }
                });
            }
            
            if (comparacionHTML) {
                comparacionList.innerHTML = comparacionHTML;
            } else {
                comparacionList.innerHTML = `
                    <div class="crud-empty-state">
                        <div class="crud-empty-state-icon">
                            <i class="fas fa-equals"></i>
                        </div>
                        <h4>Sin cambios</h4>
                        <p>No hay modificaciones en los roles del usuario.</p>
                    </div>
                `;
            }
        } else {
            comparacionList.innerHTML = `
                <div class="crud-empty-state">
                    <div class="crud-empty-state-icon">
                        <i class="fas fa-equals"></i>
                    </div>
                    <h4>Sin cambios</h4>
                    <p>Los roles seleccionados coinciden con los roles actuales.</p>
                </div>
            `;
        }
    }

    // Event listeners para detectar cambios
    document.querySelectorAll('.crud-role-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateChangesCount);
    });

    // Funciones para acciones rápidas
    window.seleccionarTodosRoles = function() {
        const roleItems = document.querySelectorAll('.crud-role-item');
        roleItems.forEach(item => {
            if (!item.classList.contains('selected')) {
                const checkbox = item.querySelector('input[type="checkbox"]');
                item.classList.add('selected');
                checkbox.checked = true;
                item.setAttribute('aria-checked', 'true');
            }
        });
        updateChangesCount();
        updateRolesPreviews();
    };

    window.deseleccionarTodosRoles = function() {
        if (confirm('¿Estás seguro de que deseas quitar todos los roles a este usuario?')) {
            const roleItems = document.querySelectorAll('.crud-role-item');
            roleItems.forEach(item => {
                if (item.classList.contains('selected')) {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    item.classList.remove('selected');
                    checkbox.checked = false;
                    item.setAttribute('aria-checked', 'false');
                }
            });
            updateChangesCount();
            updateRolesPreviews();
        }
    };

    window.restaurarRolesOriginales = function() {
        if (confirm('¿Deseas restaurar la configuración original de roles?')) {
            const roleItems = document.querySelectorAll('.crud-role-item');
            roleItems.forEach(item => {
                const checkbox = item.querySelector('input[type="checkbox"]');
                const roleId = parseInt(checkbox.value);
                const shouldBeSelected = rolesOriginales.includes(roleId);
                
                if (shouldBeSelected) {
                    item.classList.add('selected');
                    checkbox.checked = true;
                    item.setAttribute('aria-checked', 'true');
                } else {
                    item.classList.remove('selected');
                    checkbox.checked = false;
                    item.setAttribute('aria-checked', 'false');
                }
            });
            updateChangesCount();
            updateRolesPreviews();
        }
    };

    window.copiarRolesUsuario = function() {
        alert('Funcionalidad en desarrollo.\n\nPróximamente podrás copiar roles de otros usuarios existentes en el sistema.');
    };

    // Manejo del envío del formulario
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            const rolesSeleccionados = document.querySelectorAll('input[name="roles[]"]:checked');
            
            if (rolesSeleccionados.length === 0) {
                e.preventDefault();
                if (confirm('¿Estás seguro de que deseas quitar todos los roles a este usuario?\n\nEl usuario no tendrá acceso al sistema sin roles asignados.')) {
                    // Permitir el envío
                    this.submit();
                }
                return false;
            }

            submitBtn.classList.add('crud-form-loading');
            submitBtn.disabled = true;
            
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando Cambios...';
            
            setTimeout(() => {
                if (document.querySelector('.crud-alert-danger')) {
                    submitBtn.classList.remove('crud-form-loading');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                }
            }, 1000);
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

    // Advertir sobre cambios no guardados al salir
    window.addEventListener('beforeunload', function(e) {
        if (changesCount > 0) {
            const message = 'Tienes cambios sin guardar en los roles. ¿Estás seguro de que quieres salir?';
            e.returnValue = message;
            return message;
        }
    });

    // Resaltar roles con cambios
    function highlightChangedRoles() {
        const roleItems = document.querySelectorAll('.crud-role-item');
        roleItems.forEach(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            const roleId = parseInt(checkbox.value);
            const originallySelected = rolesOriginales.includes(roleId);
            const currentlySelected = checkbox.checked;
            
            if (originallySelected !== currentlySelected) {
                item.classList.add('has-changes');
                if (currentlySelected && !originallySelected) {
                    item.classList.add('role-added');
                } else if (!currentlySelected && originallySelected) {
                    item.classList.add('role-removed');
                }
            } else {
                item.classList.remove('has-changes', 'role-added', 'role-removed');
            }
        });
    }

    // Actualizar resaltado cuando cambian los roles
    document.querySelectorAll('.crud-role-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', highlightChangedRoles);
    });

    // Mostrar contador de roles seleccionados
    function updateRoleCounter() {
        const selectedRoles = document.querySelectorAll('input[name="roles[]"]:checked').length;
        const totalRoles = document.querySelectorAll('input[name="roles[]"]').length;
        
        // Actualizar texto del botón si existe
        const submitText = submitBtn.querySelector('span') || submitBtn;
        if (selectedRoles === 0) {
            submitBtn.innerHTML = '<i class="fas fa-user-minus"></i> Quitar Todos los Roles';
            submitBtn.classList.add('danger');
        } else {
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios en Roles';
            submitBtn.classList.remove('danger');
        }
    }

    // Event listener para actualizar contador
    document.querySelectorAll('.crud-role-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateRoleCounter);
    });

    // Inicializar
    updateRolesPreviews();
    updateChangesCount();
    highlightChangedRoles();
    updateRoleCounter();

    // Animación de entrada para roles
    const roleItems = document.querySelectorAll('.crud-role-item');
    roleItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        setTimeout(() => {
            item.style.transition = 'all 0.3s ease-out';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>

<style>
/* Estilos específicos para cambios en roles */
.crud-role-item.has-changes {
    border-width: 3px;
    position: relative;
}

.crud-role-item.role-added {
    border-color: var(--success-color);
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(16, 185, 129, 0.02));
}

.crud-role-item.role-removed {
    border-color: var(--danger-color);
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.05), rgba(239, 68, 68, 0.02));
}

.crud-role-item.role-added::before {
    content: 'NUEVO';
    position: absolute;
    top: -8px;
    right: 8px;
    background: var(--success-color);
    color: white;
    font-size: 0.6rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 10px;
    letter-spacing: 0.5px;
}

.crud-role-item.role-removed::before {
    content: 'QUITAR';
    position: absolute;
    top: -8px;
    right: 8px;
    background: var(--danger-color);
    color: white;
    font-size: 0.6rem;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 10px;
    letter-spacing: 0.5px;
}

.crud-btn.danger {
    background: linear-gradient(135deg, var(--danger-color), #f87171);
    border-color: var(--danger-color);
}

.crud-btn.danger:hover {
    background: linear-gradient(135deg, #dc2626, var(--danger-color));
    border-color: #dc2626;
}
</style>