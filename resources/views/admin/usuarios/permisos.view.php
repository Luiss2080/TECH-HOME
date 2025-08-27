<?php
$title = $title ?? 'Editar Permisos de Usuario';
$usuario = $usuario ?? null;
$permisos = $permisos ?? [];
$permisosUsuario = $permisosUsuario ?? [];
$errors = $errors ?? [];

// Verificar que el usuario existe
if (!$usuario) {
    header('Location: ' . route('usuarios'));
    exit();
}

// Array de permisos actuales del usuario para facilitar la verificación
$permisosActuales = [];
foreach ($permisosUsuario as $permiso) {
    $permisosActuales[] = is_array($permiso) ? $permiso['id'] : $permiso->id;
}
?>

<!-- Estilos específicos para el módulo CRUD - Editar Permisos -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">

<!-- Contenedor principal del CRUD de edición de permisos -->
<div class="crud-edit-container">
    <div class="crud-edit-wrapper">

        <!-- Header principal con información del usuario -->
        <div class="crud-section-card">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon">
                        <i class="fas fa-key"></i>
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
                                    <i class="fas fa-key"></i>
                                    Editar Permisos
                                </li>
                            </ol>
                        </nav>
                        <h1 class="crud-section-title">Editar Permisos de Usuario</h1>
                        <p class="crud-section-subtitle">
                            Gestiona los permisos directos asignados a <?= $usuario ? htmlspecialchars($usuario->nombre . ' ' . ($usuario->apellido ?? '')) : 'Usuario' ?>
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
                <p class="crud-section-subtitle">Usuario al que se asignarán los permisos directos</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-info-panel">
                    <div class="crud-info-tabs">
                        <button class="crud-info-tab active" data-tab="datos">
                            <i class="fas fa-id-card"></i>
                            Datos Básicos
                        </button>
                        <button class="crud-info-tab" data-tab="resumen">
                            <i class="fas fa-chart-bar"></i>
                            Resumen Permisos
                        </button>
                        <button class="crud-info-tab" data-tab="advertencias">
                            <i class="fas fa-exclamation-triangle"></i>
                            Advertencias
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
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="resumen">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Permisos Totales:</strong> <span id="total-permissions"><?= count($permisos) ?></span><br>
                                    Cantidad total de permisos en el sistema
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Permisos Asignados:</strong> <span id="assigned-permissions"><?= count($permisosActuales) ?></span><br>
                                    Permisos directos actualmente asignados
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Disponibles:</strong> <span id="available-permissions"><?= count($permisos) - count($permisosActuales) ?></span><br>
                                    Permisos que se pueden asignar
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="advertencias">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Permisos Directos:</strong> Solo asigna permisos que no sean cubiertos por roles<br>
                                    Los permisos a través de roles se aplican automáticamente
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Permisos Críticos:</strong> Ten cuidado con permisos de eliminación y administración<br>
                                    Pueden afectar la seguridad del sistema
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-undo"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Reversibilidad:</strong> Los cambios se pueden modificar en cualquier momento<br>
                                    El historial de cambios se registra automáticamente
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Asignación de Permisos -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-shield-alt"></i>
                    Permisos Directos del Usuario
                </h2>
                <p class="crud-section-subtitle">Selecciona los permisos específicos para este usuario</p>
            </div>
            
            <div class="crud-form-body">
                <form method="POST" action="<?= route('usuarios.permisos.update', ['id' => $usuario->id]) ?>" id="crudFormEditarPermisos" class="crud-form">
                    <?php CSRF() ?>
                    <input type="hidden" name="_method" value="PUT">
                    
                    <?php if (isset($permisos) && !empty($permisos)): ?>
                        <?php
                        // Organizar permisos por categorías
                        $categorias = [];
                        foreach ($permisos as $permiso) {
                            $parts = explode('.', $permiso->name);
                            $categoria = $parts[0] ?? 'otros';
                            if (!isset($categorias[$categoria])) {
                                $categorias[$categoria] = [];
                            }
                            $categorias[$categoria][] = $permiso;
                        }
                        
                        // Categorías con iconos personalizados
                        $iconosCategoria = [
                            'usuarios' => 'fas fa-users',
                            'permisos' => 'fas fa-shield-alt',
                            'roles' => 'fas fa-user-tag',
                            'reportes' => 'fas fa-chart-line',
                            'configuracion' => 'fas fa-cog',
                            'sistema' => 'fas fa-server',
                            'dashboard' => 'fas fa-tachometer-alt',
                            'inventario' => 'fas fa-boxes',
                            'ventas' => 'fas fa-shopping-cart',
                            'otros' => 'fas fa-ellipsis-h'
                        ];
                        ?>
                        
                        <div class="crud-permissions-categories">
                            <?php foreach ($categorias as $categoria => $permisosCategoria): ?>
                                <div class="crud-permission-category">
                                    <div class="crud-category-header" onclick="toggleCategory('<?= $categoria ?>')">
                                        <div class="crud-category-info">
                                            <div class="crud-category-icon">
                                                <i class="<?= $iconosCategoria[$categoria] ?? 'fas fa-folder' ?>"></i>
                                            </div>
                                            <div class="crud-category-details">
                                                <h4 class="crud-category-title"><?= ucfirst(str_replace('_', ' ', $categoria)) ?></h4>
                                                <p class="crud-category-description"><?= count($permisosCategoria) ?> permisos disponibles</p>
                                            </div>
                                        </div>
                                        <div class="crud-category-actions">
                                            <button type="button" class="crud-btn-category-toggle" onclick="event.stopPropagation(); selectAllInCategory('<?= $categoria ?>')">
                                                <i class="fas fa-check-double"></i>
                                                Todos
                                            </button>
                                            <button type="button" class="crud-btn-category-toggle" onclick="event.stopPropagation(); deselectAllInCategory('<?= $categoria ?>')">
                                                <i class="fas fa-times"></i>
                                                Ninguno
                                            </button>
                                            <i class="fas fa-chevron-down crud-category-arrow"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="crud-category-content" id="category-<?= $categoria ?>">
                                        <div class="crud-permissions-grid">
                                            <?php foreach ($permisosCategoria as $permiso): ?>
                                                <div class="crud-permission-item <?= in_array($permiso->id, $permisosActuales) ? 'selected' : '' ?> <?= strpos($permiso->name, 'delete') !== false || strpos($permiso->name, 'destroy') !== false ? 'critical' : '' ?>" 
                                                     onclick="crudTogglePermission(this, <?= $permiso->id ?>)"
                                                     tabindex="0"
                                                     role="checkbox"
                                                     aria-checked="<?= in_array($permiso->id, $permisosActuales) ? 'true' : 'false' ?>"
                                                     data-category="<?= $categoria ?>"
                                                     data-critical="<?= strpos($permiso->name, 'delete') !== false || strpos($permiso->name, 'destroy') !== false ? 'true' : 'false' ?>">
                                                    <input class="crud-permission-checkbox" 
                                                           type="checkbox"
                                                           id="crudPermission<?= $permiso->id ?>" 
                                                           name="permisos[]"
                                                           value="<?= $permiso->id ?>"
                                                           <?= in_array($permiso->id, $permisosActuales) ? 'checked' : '' ?>>
                                                    <div class="crud-permission-content">
                                                        <div class="crud-permission-header">
                                                            <div class="crud-permission-icon">
                                                                <i class="fas fa-<?= strpos($permiso->name, 'create') !== false ? 'plus' : (strpos($permiso->name, 'read') !== false || strpos($permiso->name, 'view') !== false ? 'eye' : (strpos($permiso->name, 'update') !== false || strpos($permiso->name, 'edit') !== false ? 'edit' : (strpos($permiso->name, 'delete') !== false || strpos($permiso->name, 'destroy') !== false ? 'trash' : 'key'))) ?>"></i>
                                                            </div>
                                                            <div class="crud-permission-name"><?= htmlspecialchars($permiso->name) ?></div>
                                                        </div>
                                                        <?php if (!empty($permiso->description)): ?>
                                                            <div class="crud-permission-description"><?= htmlspecialchars($permiso->description) ?></div>
                                                        <?php endif; ?>
                                                        <?php if (strpos($permiso->name, 'delete') !== false || strpos($permiso->name, 'destroy') !== false): ?>
                                                            <div class="crud-permission-warning">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                Permiso crítico
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <?php if (isset($errors['permisos'])): ?>
                            <div class="crud-invalid-feedback crud-roles-error">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?= is_array($errors['permisos']) ? $errors['permisos'][0] : $errors['permisos'] ?>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="crud-empty-state">
                            <div class="crud-empty-state-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h4>No hay permisos disponibles</h4>
                            <p>No se encontraron permisos en el sistema. Contacta al administrador para configurar los permisos necesarios.</p>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Sección: Acciones Rápidas -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-bolt"></i>
                    Acciones Rápidas
                </h2>
                <p class="crud-section-subtitle">Herramientas para gestión rápida de permisos</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Seleccionar Todos</h4>
                            <p>Asignar todos los permisos disponibles</p>
                            <button type="button" class="crud-btn-action" onclick="seleccionarTodosPermisos()">
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
                            <p>Remover todos los permisos del usuario</p>
                            <button type="button" class="crud-btn-action danger" onclick="deseleccionarTodosPermisos()">
                                <i class="fas fa-minus-circle"></i>
                                Deseleccionar
                            </button>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Solo Lectura</h4>
                            <p>Asignar solo permisos de visualización</p>
                            <button type="button" class="crud-btn-action" onclick="seleccionarSoloLectura()">
                                <i class="fas fa-eye"></i>
                                Solo Ver
                            </button>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-copy"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Copiar Permisos</h4>
                            <p>Copiar permisos de otro usuario</p>
                            <button type="button" class="crud-btn-action" onclick="copiarPermisosUsuario()">
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
                <button type="submit" form="crudFormEditarPermisos" class="crud-btn crud-btn-primary" id="crudBtnSubmit">
                    <i class="fas fa-save"></i>
                    Guardar Permisos
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

<!-- JavaScript específico para editar permisos de usuario -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let changesCount = 0;
    const changesIndicator = document.getElementById('crudChangesIndicator');
    const changesCountEl = document.getElementById('crudChangesCount');
    const form = document.getElementById('crudFormEditarPermisos');
    const submitBtn = document.getElementById('crudBtnSubmit');
    const permisosOriginales = <?= json_encode($permisosActuales) ?>;

    // Función para alternar selección de permisos
    window.crudTogglePermission = function(element, permisoId) {
        const checkbox = element.querySelector('input[type="checkbox"]');
        const isSelected = element.classList.contains('selected');
        const isCritical = element.dataset.critical === 'true';
        
        if (!isSelected && isCritical) {
            if (!confirm('Este permiso es crítico y puede afectar la seguridad del sistema. ¿Está seguro de que desea asignarlo?')) {
                return;
            }
        }
        
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
        updatePermissionsCounter();
    };

    // Función para alternar categoría
    window.toggleCategory = function(categoria) {
        const categoryContent = document.getElementById('category-' + categoria);
        const arrow = document.querySelector(`[onclick*="${categoria}"] .crud-category-arrow`);
        
        if (categoryContent.style.display === 'none') {
            categoryContent.style.display = 'block';
            arrow.style.transform = 'rotate(180deg)';
        } else {
            categoryContent.style.display = 'none';
            arrow.style.transform = 'rotate(0deg)';
        }
    };

    // Función para seleccionar todos en categoría
    window.selectAllInCategory = function(categoria) {
        const categoryItems = document.querySelectorAll(`[data-category="${categoria}"]`);
        categoryItems.forEach(item => {
            if (!item.classList.contains('selected')) {
                const checkbox = item.querySelector('input[type="checkbox"]');
                item.classList.add('selected');
                checkbox.checked = true;
                item.setAttribute('aria-checked', 'true');
            }
        });
        updateChangesCount();
        updatePermissionsCounter();
    };

    // Función para deseleccionar todos en categoría
    window.deselectAllInCategory = function(categoria) {
        const categoryItems = document.querySelectorAll(`[data-category="${categoria}"]`);
        categoryItems.forEach(item => {
            if (item.classList.contains('selected')) {
                const checkbox = item.querySelector('input[type="checkbox"]');
                item.classList.remove('selected');
                checkbox.checked = false;
                item.setAttribute('aria-checked', 'false');
            }
        });
        updateChangesCount();
        updatePermissionsCounter();
    };

    // Función para cambiar tabs de información
    document.querySelectorAll('.crud-info-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.crud-info-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.crud-info-pane').forEach(p => p.classList.remove('active'));
            
            this.classList.add('active');
            const targetPane = document.getElementById(this.dataset.tab);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    // Detectar cambios en los permisos
    function detectChanges() {
        const permissionCheckboxes = document.querySelectorAll('input[name="permisos[]"]');
        const currentPermisos = Array.from(permissionCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => parseInt(cb.value));

        const permisosChanged = JSON.stringify(permisosOriginales.sort()) !== JSON.stringify(currentPermisos.sort());
        
        changesCount = permisosChanged ? 1 : 0;
        updateChangesIndicator();

        return permisosChanged;
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

    // Actualizar contadores de permisos
    function updatePermissionsCounter() {
        const totalPermisos = document.querySelectorAll('input[name="permisos[]"]').length;
        const asignados = document.querySelectorAll('input[name="permisos[]"]:checked').length;
        const disponibles = totalPermisos - asignados;

        animateCounter('assigned-permissions', asignados);
        animateCounter('available-permissions', disponibles);
    }

    // Función para animar contadores
    function animateCounter(elementId, targetValue) {
        const element = document.getElementById(elementId);
        const currentValue = parseInt(element.textContent);
        
        if (currentValue !== targetValue) {
            element.style.transform = 'scale(1.15)';
            element.style.color = 'var(--primary-red)';
            
            setTimeout(() => {
                element.textContent = targetValue;
                element.style.transform = 'scale(1)';
                element.style.color = 'var(--accent-teal)';
            }, 150);
        }
    }

    // Funciones para acciones rápidas
    window.seleccionarTodosPermisos = function() {
        if (confirm('¿Estás seguro de que deseas asignar TODOS los permisos a este usuario?\n\nEsto incluye permisos críticos que pueden afectar la seguridad del sistema.')) {
            const permissionItems = document.querySelectorAll('.crud-permission-item');
            permissionItems.forEach(item => {
                if (!item.classList.contains('selected')) {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    item.classList.add('selected');
                    checkbox.checked = true;
                    item.setAttribute('aria-checked', 'true');
                }
            });
            updateChangesCount();
            updatePermissionsCounter();
        }
    };

    window.deseleccionarTodosPermisos = function() {
        if (confirm('¿Estás seguro de que deseas quitar todos los permisos directos a este usuario?')) {
            const permissionItems = document.querySelectorAll('.crud-permission-item');
            permissionItems.forEach(item => {
                if (item.classList.contains('selected')) {
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    item.classList.remove('selected');
                    checkbox.checked = false;
                    item.setAttribute('aria-checked', 'false');
                }
            });
            updateChangesCount();
            updatePermissionsCounter();
        }
    };

    window.seleccionarSoloLectura = function() {
        // Deseleccionar todos primero
        const permissionItems = document.querySelectorAll('.crud-permission-item');
        permissionItems.forEach(item => {
            const checkbox = item.querySelector('input[type="checkbox"]');
            const permissionName = checkbox.value;
            const permissionNameText = item.querySelector('.crud-permission-name').textContent.toLowerCase();
            
            // Deseleccionar todo
            item.classList.remove('selected');
            checkbox.checked = false;
            item.setAttribute('aria-checked', 'false');
            
            // Seleccionar solo permisos de lectura/visualización
            if (permissionNameText.includes('read') || permissionNameText.includes('view') || permissionNameText.includes('index') || permissionNameText.includes('show')) {
                item.classList.add('selected');
                checkbox.checked = true;
                item.setAttribute('aria-checked', 'true');
            }
        });
        updateChangesCount();
        updatePermissionsCounter();
        alert('Se han seleccionado solo los permisos de visualización/lectura.');
    };

    window.copiarPermisosUsuario = function() {
        alert('Funcionalidad en desarrollo.\n\nPróximamente podrás copiar permisos de otros usuarios existentes en el sistema.');
    };

    // Event listeners para detectar cambios
    document.querySelectorAll('.crud-permission-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateChangesCount);
    });

    // Manejo del envío del formulario
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            const permisosSeleccionados = document.querySelectorAll('input[name="permisos[]"]:checked');
            const permisosCriticos = document.querySelectorAll('input[name="permisos[]"]:checked[data-critical="true"]');
            
            if (permisosSeleccionados.length === 0) {
                if (!confirm('¿Estás seguro de que deseas quitar todos los permisos directos a este usuario?\n\nEl usuario solo tendrá los permisos asignados a través de sus roles.')) {
                    e.preventDefault();
                    return false;
                }
            }
            
            if (permisosCriticos.length > 0) {
                const criticosNames = Array.from(permisosCriticos).map(cb => {
                    return cb.closest('.crud-permission-item').querySelector('.crud-permission-name').textContent;
                });
                
                if (!confirm(`Se han seleccionado ${permisosCriticos.length} permiso(s) crítico(s):\n\n${criticosNames.join('\n')}\n\n¿Confirmas la asignación?`)) {
                    e.preventDefault();
                    return false;
                }
            }

            submitBtn.classList.add('crud-form-loading');
            submitBtn.disabled = true;
            
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando Permisos...';
            
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

    // Navegación con teclado para permisos
    document.addEventListener('keydown', function(e) {
        if (e.target.classList.contains('crud-permission-item')) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                const permisoId = e.target.querySelector('input[type="checkbox"]').value;
                crudTogglePermission(e.target, permisoId);
            }
        }
    });

    // Advertir sobre cambios no guardados al salir
    window.addEventListener('beforeunload', function(e) {
        if (changesCount > 0) {
            const message = 'Tienes cambios sin guardar en los permisos. ¿Estás seguro de que quieres salir?';
            e.returnValue = message;
            return message;
        }
    });

    // Inicializar categorías colapsadas por defecto
    document.querySelectorAll('.crud-category-content').forEach((content, index) => {
        if (index > 0) { // Mantener la primera categoría abierta
            content.style.display = 'none';
        }
    });

    // Función de búsqueda de permisos
    function createSearchFunctionality() {
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Buscar permisos...';
        searchInput.className = 'crud-form-control crud-mb-2';
        searchInput.style.marginBottom = '1rem';

        const firstCategory = document.querySelector('.crud-permission-category');
        if (firstCategory) {
            firstCategory.parentNode.insertBefore(searchInput, firstCategory);
        }

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const permissionItems = document.querySelectorAll('.crud-permission-item');
            
            permissionItems.forEach(item => {
                const permissionName = item.querySelector('.crud-permission-name').textContent.toLowerCase();
                const permissionDesc = item.querySelector('.crud-permission-description')?.textContent.toLowerCase() || '';
                
                if (permissionName.includes(searchTerm) || permissionDesc.includes(searchTerm)) {
                    item.style.display = 'block';
                    item.parentElement.style.display = 'grid';
                    item.closest('.crud-permission-category').style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            // Ocultar categorías vacías
            document.querySelectorAll('.crud-permission-category').forEach(category => {
                const visibleItems = category.querySelectorAll('.crud-permission-item[style*="block"], .crud-permission-item:not([style*="none"])');
                if (visibleItems.length === 0 && searchTerm) {
                    category.style.display = 'none';
                } else {
                    category.style.display = 'block';
                }
            });
        });
    }

    // Inicializar funcionalidades
    createSearchFunctionality();
    updatePermissionsCounter();
    updateChangesCount();

    // Mostrar contador de permisos críticos
    function updateCriticalCounter() {
        const criticalSelected = document.querySelectorAll('.crud-permission-item.selected.critical').length;
        if (criticalSelected > 0) {
            submitBtn.innerHTML = `<i class="fas fa-exclamation-triangle"></i> Guardar (${criticalSelected} críticos)`;
            submitBtn.classList.add('has-critical');
        } else {
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Permisos';
            submitBtn.classList.remove('has-critical');
        }
    }

    // Event listener para actualizar contador crítico
    document.querySelectorAll('.crud-permission-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateCriticalCounter);
    });

    // Animación de entrada para permisos
    const permissionItems = document.querySelectorAll('.crud-permission-item');
    permissionItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        setTimeout(() => {
            item.style.transition = 'all 0.3s ease-out';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 50);
    });

    // Inicializar
    updateCriticalCounter();
});
</script>

<style>
/* Estilos específicos para permisos */
.crud-permissions-categories {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.crud-permission-category {
    background: rgba(255, 255, 255, 0.8);
    border-radius: var(--border-radius-md);
    border: 2px solid rgba(0, 0, 0, 0.08);
    overflow: hidden;
    transition: var(--transition-base);
}

.crud-permission-category:hover {
    border-color: rgba(220, 38, 38, 0.2);
    box-shadow: var(--shadow-light);
}

.crud-category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.05), rgba(220, 38, 38, 0.02));
    cursor: pointer;
    transition: var(--transition-base);
}

.crud-category-header:hover {
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.08), rgba(220, 38, 38, 0.04));
}

.crud-category-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.crud-category-icon {
    width: 50px;
    height: 50px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.crud-category-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.crud-category-description {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin: 0;
}

.crud-category-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.crud-btn-category-toggle {
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: var(--border-radius-sm);
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-secondary);
    cursor: pointer;
    transition: var(--transition-base);
}

.crud-btn-category-toggle:hover {
    background: var(--primary-red);
    color: white;
    border-color: var(--primary-red);
}

.crud-category-arrow {
    font-size: 1rem;
    color: var(--text-secondary);
    transition: var(--transition-base);
}

.crud-category-content {
    padding: 1.5rem;
    border-top: 1px solid rgba(0, 0, 0, 0.08);
}

.crud-permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
}

.crud-permission-item {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
    border: 2px solid rgba(0, 0, 0, 0.08);
    border-radius: var(--border-radius-sm);
    padding: 1rem;
    cursor: pointer;
    transition: var(--transition-bounce);
    position: relative;
    overflow: hidden;
}

.crud-permission-item:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: var(--shadow-medium);
    border-color: var(--primary-red);
}

.crud-permission-item.selected {
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.1), rgba(220, 38, 38, 0.05));
    border-color: var(--primary-red);
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
}

.crud-permission-item.critical {
    border-left: 4px solid var(--danger-color);
}

.crud-permission-item.critical::before {
    content: 'CRÍTICO';
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

.crud-permission-checkbox {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.crud-permission-content {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.crud-permission-header {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.crud-permission-icon {
    width: 35px;
    height: 35px;
    background: linear-gradient(135deg, var(--accent-teal), #2dd4bf);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.crud-permission-item.selected .crud-permission-icon {
    background: var(--gradient-primary);
}

.crud-permission-name {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--text-primary);
}

.crud-permission-description {
    font-size: 0.8rem;
    color: var(--text-secondary);
    line-height: 1.4;
}

.crud-permission-warning {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.75rem;
    color: var(--danger-color);
    font-weight: 600;
    margin-top: 0.3rem;
}

.crud-btn.has-critical {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24);
    border-color: var(--warning-color);
}

.crud-btn.has-critical:hover {
    background: linear-gradient(135deg, #d97706, var(--warning-color));
}

/* Responsive */
@media (max-width: 768px) {
    .crud-permissions-grid {
        grid-template-columns: 1fr;
    }
    
    .crud-category-header {
        padding: 1rem;
    }
    
    .crud-category-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .crud-category-actions {
        flex-direction: column;
    }
}
</style>