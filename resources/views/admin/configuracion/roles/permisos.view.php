<?php
$title = $title ?? 'Asignar Permisos - Configuración';
$role = $role ?? null;
if (!$role) {
    throw new Exception('Rol no encontrado');
}
$permisos = $permisos ?? [];
$permisosAsignados = $permisosAsignados ?? [];

// Crear array de IDs de permisos asignados para fácil verificación
$permisosAsignadosIds = array_column($permisosAsignados, 'id');
?>

<div class="dashboard-content">

    <!-- Header -->
    <div class="section-header">
        <div class="section-header-content">
            <h2 class="section-title">
                <i class="fas fa-key"></i>
                Asignar Permisos: <?= htmlspecialchars($role->nombre ?? 'Desconocido') ?>
            </h2>
            <p class="section-subtitle">Selecciona los permisos que tendrá este rol en el sistema</p>
        </div>
        <div class="section-header-actions">
            <a href="<?= route('admin.roles'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver a Roles
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Formulario de Permisos -->
        <div class="col-lg-8">
            <div class="section-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shield-alt"></i>
                        Permisos Disponibles
                    </h3>
                    <div class="permission-controls">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPermissions()">
                            <i class="fas fa-check-double"></i>
                            Seleccionar Todos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="unselectAllPermissions()">
                            <i class="fas fa-times"></i>
                            Deseleccionar Todos
                        </button>
                    </div>
                </div>

                <form method="POST" action="<?= route('admin.roles.permisos.store', ['id' => $role->id]); ?>" id="permissionsForm">
                    <?php if (!empty($permisos)): ?>
                        <!-- Agrupar permisos por categoría -->
                        <?php
                        $permisosPorCategoria = [];
                        foreach ($permisos as $permiso) {
                            $categoria = explode('.', $permiso->name)[0] ?? 'general';
                            $permisosPorCategoria[$categoria][] = $permiso;
                        }
                        ?>

                        <div class="permissions-grid">
                            <?php foreach ($permisosPorCategoria as $categoria => $permisosCategoria): ?>
                                <div class="permission-category">
                                    <div class="category-header">
                                        <div class="category-title">
                                            <i class="fas fa-<?= getCategoryIcon($categoria) ?>"></i>
                                            <span><?= ucfirst($categoria) ?></span>
                                            <span class="permission-count">(<?= count($permisosCategoria) ?>)</span>
                                        </div>
                                        <div class="category-actions">
                                            <button type="button"
                                                class="btn btn-sm btn-link"
                                                onclick="toggleCategory('<?= $categoria ?>')">
                                                <i class="fas fa-check"></i>
                                                Todos
                                            </button>
                                        </div>
                                    </div>

                                    <div class="permissions-list">
                                        <?php foreach ($permisosCategoria as $permiso): ?>
                                            <?php $isChecked = in_array($permiso->id, $permisosAsignadosIds); ?>
                                            <div class="permission-item">
                                                <div class="permission-checkbox">
                                                    <input type="checkbox"
                                                        id="permission_<?= $permiso->id ?>"
                                                        name="permisos[]"
                                                        value="<?= $permiso->id ?>"
                                                        class="form-check-input permission-check"
                                                        data-category="<?= $categoria ?>"
                                                        <?= $isChecked ? 'checked' : '' ?>>
                                                    <label for="permission_<?= $permiso->id ?>" class="form-check-label">
                                                        <div class="permission-info">
                                                            <span class="permission-name"><?= htmlspecialchars($permiso->name) ?></span>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Guardar Permisos
                            </button>
                            <a href="<?= route('admin.roles'); ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-key"></i>
                            </div>
                            <h3>No hay permisos disponibles</h3>
                            <p>Los permisos del sistema deben ser definidos mediante migraciones o seeders</p>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Panel de Información -->
        <div class="col-lg-4">
            <!-- Información del Rol -->
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Información del Rol
                </h3>

                <div class="role-info-card">
                    <div class="role-header">
                        <div class="role-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="role-details">
                            <h4><?= htmlspecialchars($role->nombre) ?></h4>
                            <p><?= htmlspecialchars($role->descripcion ?? 'Sin descripción') ?></p>
                        </div>
                    </div>

                    <div class="role-stats">
                        <div class="stat-item">
                            <span class="stat-value" id="selectedPermissionsCount"><?= count($permisosAsignados) ?></span>
                            <span class="stat-label">Permisos Seleccionados</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value"><?= count($permisos) ?></span>
                            <span class="stat-label">Permisos Disponibles</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permisos Actuales -->
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-list-check"></i>
                    Permisos Actuales
                </h3>

                <div class="current-permissions" id="currentPermissionsList">
                    <?php if (!empty($permisosAsignados)): ?>
                        <?php foreach ($permisosAsignados as $permiso): ?>
                            <div class="current-permission-item" data-permission-id="<?= $permiso['permission_id'] ?>">
                                <div class="permission-badge">
                                    <i class="fas fa-key"></i>
                                </div>
                                <span class="permission-text"><?= htmlspecialchars($permiso['name'] ?? 'Permiso ' . $permiso['permission_id']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-permissions">
                            <i class="fas fa-lock"></i>
                            <span>Sin permisos asignados</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Consejos -->
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb"></i>
                    Consejos de Seguridad
                </h3>

                <div class="tips-list">
                    <div class="tip-item">
                        <div class="tip-icon security">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="tip-content">
                            <h4>Principio de Menor Privilegio</h4>
                            <p>Asigna solo los permisos mínimos necesarios para las funciones del rol.</p>
                        </div>
                    </div>

                    <div class="tip-item">
                        <div class="tip-icon warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="tip-content">
                            <h4>Permisos Críticos</h4>
                            <p>Ten especial cuidado con permisos de administración y eliminación de datos.</p>
                        </div>
                    </div>

                    <div class="tip-item">
                        <div class="tip-icon info">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="tip-content">
                            <h4>Revisión Periódica</h4>
                            <p>Revisa regularmente los permisos asignados para mantener la seguridad.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function getCategoryIcon($categoria)
{
    $icons = [
        'usuarios' => 'users',
        'admin' => 'crown',
        'cursos' => 'graduation-cap',
        'libros' => 'book',
        'componentes' => 'microchip',
        'ventas' => 'shopping-cart',
        'reportes' => 'chart-bar',
        'configuracion' => 'cog',
        'general' => 'key'
    ];

    return $icons[$categoria] ?? 'key';
}
?>

<style>
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .permission-controls {
        display: flex;
        gap: 0.5rem;
    }

    .permissions-grid {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .permission-category {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        overflow: hidden;
    }

    .category-header {
        background: #f8fafc;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #e2e8f0;
    }

    .category-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: #374151;
    }

    .permission-count {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 400;
    }

    .permissions-list {
        padding: 1rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 0.75rem;
    }

    .permission-item {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .permission-item:hover {
        border-color: #3b82f6;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
    }

    .permission-checkbox {
        padding: 0.75rem;
    }

    .form-check-input {
        width: 18px;
        height: 18px;
        margin-right: 0.75rem;
        accent-color: #3b82f6;
    }

    .form-check-label {
        display: flex;
        align-items: flex-start;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .permission-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .permission-name {
        font-weight: 500;
        color: #374151;
    }

    .permission-description {
        font-size: 0.75rem;
        color: #6b7280;
        line-height: 1.4;
    }

    .role-info-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1.5rem;
    }

    .role-header {
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .role-icon {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.25rem;
    }

    .role-details h4 {
        margin: 0 0 0.25rem 0;
        font-size: 1.125rem;
        font-weight: 600;
        color: #374151;
    }

    .role-details p {
        margin: 0;
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.4;
    }

    .role-stats {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        display: block;
        font-size: 1.5rem;
        font-weight: 700;
        color: #3b82f6;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        font-weight: 500;
    }

    .current-permissions {
        max-height: 300px;
        overflow-y: auto;
    }

    .current-permission-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.5rem;
        margin-bottom: 0.5rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 4px;
        font-size: 0.875rem;
    }

    .permission-badge {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        background: linear-gradient(135deg, #10b981, #047857);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.75rem;
    }

    .permission-text {
        color: #374151;
    }

    .no-permissions {
        text-align: center;
        padding: 2rem;
        color: #6b7280;
        font-size: 0.875rem;
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
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.875rem;
        color: white;
    }

    .tip-icon.security {
        background: linear-gradient(135deg, #10b981, #047857);
    }

    .tip-icon.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .tip-icon.info {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
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

    .form-actions {
        display: flex;
        gap: 1rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
        margin-top: 2rem;
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

    .card-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        updatePermissionsCount();
        updateCurrentPermissionsList();
    });

    function selectAllPermissions() {
        const checkboxes = document.querySelectorAll('.permission-check');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        updatePermissionsCount();
        updateCurrentPermissionsList();
    }

    function unselectAllPermissions() {
        const checkboxes = document.querySelectorAll('.permission-check');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updatePermissionsCount();
        updateCurrentPermissionsList();
    }

    function toggleCategory(category) {
        const checkboxes = document.querySelectorAll(`[data-category="${category}"]`);
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);

        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });

        updatePermissionsCount();
        updateCurrentPermissionsList();
    }

    function updatePermissionsCount() {
        const checkedBoxes = document.querySelectorAll('.permission-check:checked');
        const countElement = document.getElementById('selectedPermissionsCount');
        if (countElement) {
            countElement.textContent = checkedBoxes.length;
        }
    }

    function updateCurrentPermissionsList() {
        const checkedBoxes = document.querySelectorAll('.permission-check:checked');
        const currentList = document.getElementById('currentPermissionsList');

        if (checkedBoxes.length === 0) {
            currentList.innerHTML = `
            <div class="no-permissions">
                <i class="fas fa-lock"></i>
                <span>Sin permisos asignados</span>
            </div>
        `;
            return;
        }

        let html = '';
        checkedBoxes.forEach(checkbox => {
            const label = checkbox.parentElement.querySelector('.permission-name');
            const permissionName = label ? label.textContent : 'Permiso ' + checkbox.value;

            html += `
            <div class="current-permission-item" data-permission-id="${checkbox.value}">
                <div class="permission-badge">
                    <i class="fas fa-key"></i>
                </div>
                <span class="permission-text">${permissionName}</span>
            </div>
        `;
        });

        currentList.innerHTML = html;
    }

    // Escuchar cambios en los checkboxes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('permission-check')) {
            updatePermissionsCount();
            updateCurrentPermissionsList();
        }
    });
</script>