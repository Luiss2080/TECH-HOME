<?php
$title = $title ?? 'Gestión de Permisos - Configuración';
$permisos = $permisos ?? [];
?>

<div class="dashboard-content">
    
    <!-- Header de Permisos -->
    <div class="section-header">
        <div class="section-header-content">
            <h2 class="section-title">
                <i class="fas fa-key"></i>
                Gestión de Permisos
            </h2>
            <p class="section-subtitle">Administra los permisos del sistema que pueden ser asignados a los roles</p>
        </div>
        <div class="section-header-actions">
            <a href="<?= route('admin.permisos.crear'); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Crear Nuevo Permiso
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

    <!-- Filtros y Búsqueda -->
    <div class="section-card">
        <div class="filters-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" 
                       id="searchPermissions" 
                       class="form-control" 
                       placeholder="Buscar permisos por nombre o descripción...">
            </div>
            
            <div class="filter-dropdown">
                <select id="categoryFilter" class="form-select">
                    <option value="">Todas las categorías</option>
                    <option value="usuarios">Usuarios</option>
                    <option value="admin">Administración</option>
                    <option value="cursos">Cursos</option>
                    <option value="libros">Libros</option>
                    <option value="componentes">Componentes</option>
                    <option value="ventas">Ventas</option>
                    <option value="reportes">Reportes</option>
                    <option value="configuracion">Configuración</option>
                </select>
            </div>

            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                <i class="fas fa-times"></i>
                Limpiar
            </button>
        </div>
    </div>

    <!-- Tabla de Permisos -->
    <div class="section-card">
        <div class="table-container">
            <?php if (!empty($permisos)): ?>
                <table class="data-table" id="permissionsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre del Permiso</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th>Roles Asignados</th>
                            <th>Fecha de Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($permisos as $permiso): ?>
                            <?php
                            $categoria = explode('.', $permiso['name'])[0] ?? 'general';
                            ?>
                            <tr data-permission-id="<?= $permiso['id'] ?>" data-category="<?= $categoria ?>">
                                <td><?= $permiso['id'] ?></td>
                                <td>
                                    <div class="permission-info">
                                        <span class="permission-name"><?= htmlspecialchars($permiso['name']) ?></span>
                                        <span class="permission-badge <?= getCategoryClass($categoria) ?>">
                                            <i class="fas fa-<?= getCategoryIcon($categoria) ?>"></i>
                                            <?= ucfirst($categoria) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="text-muted">
                                    <?= htmlspecialchars($permiso['descripcion'] ?? 'Sin descripción') ?>
                                </td>
                                <td>
                                    <span class="category-tag <?= getCategoryClass($categoria) ?>">
                                        <?= ucfirst($categoria) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="roles-count" data-permission-id="<?= $permiso['id'] ?>">
                                        <i class="fas fa-user-shield"></i>
                                        <span class="count">-</span>
                                    </span>
                                </td>
                                <td class="text-muted">
                                    <?= date('d/m/Y H:i', strtotime($permiso['created_at'] ?? 'now')) ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-info" 
                                                title="Ver Roles"
                                                onclick="showPermissionRoles(<?= $permiso['id'] ?>, '<?= htmlspecialchars($permiso['name']) ?>')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary" 
                                                title="Editar"
                                                onclick="editPermission(<?= $permiso['id'] ?>, '<?= htmlspecialchars($permiso['name']) ?>', '<?= htmlspecialchars($permiso['descripcion'] ?? '') ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger btn-delete-permission" 
                                                data-permission-id="<?= $permiso['id'] ?>" 
                                                data-permission-name="<?= htmlspecialchars($permiso['name']) ?>"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <h3>No hay permisos registrados</h3>
                    <p>Comienza creando el primer permiso del sistema</p>
                    <a href="<?= route('admin.permisos.crear'); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Crear Primer Permiso
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Estadísticas de Permisos -->
    <div class="section-card">
        <h3 class="section-title">
            <i class="fas fa-chart-pie"></i>
            Estadísticas de Permisos
        </h3>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value"><?= count($permisos) ?></div>
                    <div class="stat-label">Total Permisos</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="categoriesCount">-</div>
                    <div class="stat-label">Categorías</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="assignedPermissions">-</div>
                    <div class="stat-label">Permisos Asignados</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-lock-open"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value" id="unassignedPermissions">-</div>
                    <div class="stat-label">Sin Asignar</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function getCategoryIcon($categoria) {
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

function getCategoryClass($categoria) {
    $classes = [
        'usuarios' => 'category-users',
        'admin' => 'category-admin',
        'cursos' => 'category-courses',
        'libros' => 'category-books',
        'componentes' => 'category-components',
        'ventas' => 'category-sales',
        'reportes' => 'category-reports',
        'configuracion' => 'category-config',
        'general' => 'category-general'
    ];
    
    return $classes[$categoria] ?? 'category-general';
}
?>

<style>
.filters-container {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
    padding: 1rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
}

.search-box {
    position: relative;
    flex: 1;
    min-width: 300px;
}

.search-box i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    z-index: 1;
}

.search-box .form-control {
    padding-left: 2.5rem;
}

.filter-dropdown {
    min-width: 200px;
}

.permission-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.permission-name {
    font-weight: 600;
    color: #374151;
}

.permission-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    padding: 0.125rem 0.375rem;
    border-radius: 4px;
    font-weight: 500;
}

.category-tag {
    display: inline-flex;
    align-items: center;
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 500;
}

/* Colores por categoría */
.category-users { background: #dbeafe; color: #1d4ed8; }
.category-admin { background: #fef3c7; color: #d97706; }
.category-courses { background: #d1fae5; color: #047857; }
.category-books { background: #e0e7ff; color: #5b21b6; }
.category-components { background: #fce7f3; color: #be185d; }
.category-sales { background: #ecfdf5; color: #065f46; }
.category-reports { background: #fff1f2; color: #dc2626; }
.category-config { background: #f0f9ff; color: #0284c7; }
.category-general { background: #f3f4f6; color: #374151; }

.roles-count {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
    color: #6b7280;
}

.roles-count i {
    color: #3b82f6;
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
}

.action-buttons .btn {
    padding: 0.375rem;
    min-width: 32px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.stat-card {
    background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 8px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #374151;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.25rem;
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
    // Cargar estadísticas
    loadPermissionStats();
    
    // Configurar filtros y búsqueda
    setupFilters();
    
    // Cargar contadores de roles
    loadRoleCounts();
});

function loadPermissionStats() {
    // Contar categorías únicas
    const categories = new Set();
    const rows = document.querySelectorAll('#permissionsTable tbody tr');
    
    rows.forEach(row => {
        const category = row.dataset.category;
        if (category) {
            categories.add(category);
        }
    });
    
    document.getElementById('categoriesCount').textContent = categories.size;
    
    // Simular datos de asignación (aquí harías llamadas AJAX reales)
    const totalPermissions = rows.length;
    const assignedCount = Math.floor(totalPermissions * 0.7);
    const unassignedCount = totalPermissions - assignedCount;
    
    document.getElementById('assignedPermissions').textContent = assignedCount;
    document.getElementById('unassignedPermissions').textContent = unassignedCount;
}

function setupFilters() {
    const searchInput = document.getElementById('searchPermissions');
    const categoryFilter = document.getElementById('categoryFilter');
    const table = document.getElementById('permissionsTable');
    
    if (!table) return;
    
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const permissionName = row.querySelector('.permission-name').textContent.toLowerCase();
            const description = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
            const category = row.dataset.category;
            
            const matchesSearch = permissionName.includes(searchTerm) || description.includes(searchTerm);
            const matchesCategory = !selectedCategory || category === selectedCategory;
            
            row.style.display = matchesSearch && matchesCategory ? '' : 'none';
        });
    }
    
    searchInput.addEventListener('input', filterTable);
    categoryFilter.addEventListener('change', filterTable);
}

function clearFilters() {
    document.getElementById('searchPermissions').value = '';
    document.getElementById('categoryFilter').value = '';
    
    // Mostrar todas las filas
    const rows = document.querySelectorAll('#permissionsTable tbody tr');
    rows.forEach(row => {
        row.style.display = '';
    });
}

function loadRoleCounts() {
    const rolesCounts = document.querySelectorAll('.roles-count');
    
    // Simular carga de datos (aquí harías llamadas AJAX reales)
    rolesCounts.forEach(element => {
        const permissionId = element.dataset.permissionId;
        const countElement = element.querySelector('.count');
        // Aquí harías una llamada AJAX para obtener el conteo real
        countElement.textContent = Math.floor(Math.random() * 5) + 1;
    });
}

function showPermissionRoles(permissionId, permissionName) {
    alert(`Mostrar roles asignados al permiso: ${permissionName}\nID: ${permissionId}\n\nFuncionalidad en desarrollo`);
}

function editPermission(permissionId, name, description) {
    // Aquí podrías abrir un modal de edición o redirigir a una página de edición
    alert(`Editar permiso:\nID: ${permissionId}\nNombre: ${name}\nDescripción: ${description}\n\nFuncionalidad en desarrollo`);
}

// Configurar botones de eliminación (similar al de roles)
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-delete-permission')) {
        const button = e.target.closest('.btn-delete-permission');
        const permissionId = button.dataset.permissionId;
        const permissionName = button.dataset.permissionName;
        
        if (confirm(`¿Estás seguro de eliminar el permiso "${permissionName}"?\n\nEsta acción no se puede deshacer.`)) {
            deletePermission(permissionId);
        }
    }
});

function deletePermission(permissionId) {
    // Aquí harías la llamada AJAX para eliminar el permiso
    console.log('Eliminando permiso:', permissionId);
    alert('Funcionalidad de eliminación en desarrollo');
}
</script>
