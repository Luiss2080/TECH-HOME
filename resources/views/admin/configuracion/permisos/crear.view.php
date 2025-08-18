<?php
$title = $title ?? 'Crear Permiso - Configuración';
$error = $error ?? null;
$old_data = $old_data ?? [];
?>

<div class="dashboard-content">
    
    <!-- Header -->
    <div class="section-header">
        <div class="section-header-content">
            <h2 class="section-title">
                <i class="fas fa-key"></i>
                Crear Nuevo Permiso
            </h2>
            <p class="section-subtitle">Define un nuevo permiso para el sistema de control de acceso</p>
        </div>
        <div class="section-header-actions">
            <a href="<?= route('admin.permisos'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver a Permisos
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
                    Información del Permiso
                </h3>

                <form method="POST" action="<?= route('admin.permisos.store'); ?>" class="needs-validation" novalidate>
                    <div class="form-group">
                        <label for="nombre" class="form-label required">
                            <i class="fas fa-tag"></i>
                            Nombre del Permiso
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="nombre" 
                               name="nombre" 
                               value="<?= htmlspecialchars($old_data['nombre'] ?? '') ?>"
                               placeholder="Ej: usuarios.crear, libros.editar, reportes.ver..."
                               required>
                        <div class="invalid-feedback">
                            Por favor, ingresa el nombre del permiso.
                        </div>
                        <div class="form-text">
                            Usa la convención: <code>categoria.accion</code> (ej: usuarios.crear, cursos.editar, reportes.eliminar)
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="categoria" class="form-label">
                            <i class="fas fa-folder"></i>
                            Categoría
                        </label>
                        <select class="form-select" id="categoria" name="categoria">
                            <option value="">Seleccionar categoría</option>
                            <option value="usuarios" <?= ($old_data['categoria'] ?? '') === 'usuarios' ? 'selected' : '' ?>>
                                <i class="fas fa-users"></i> Usuarios
                            </option>
                            <option value="admin" <?= ($old_data['categoria'] ?? '') === 'admin' ? 'selected' : '' ?>>
                                <i class="fas fa-crown"></i> Administración
                            </option>
                            <option value="cursos" <?= ($old_data['categoria'] ?? '') === 'cursos' ? 'selected' : '' ?>>
                                <i class="fas fa-graduation-cap"></i> Cursos
                            </option>
                            <option value="libros" <?= ($old_data['categoria'] ?? '') === 'libros' ? 'selected' : '' ?>>
                                <i class="fas fa-book"></i> Libros
                            </option>
                            <option value="componentes" <?= ($old_data['categoria'] ?? '') === 'componentes' ? 'selected' : '' ?>>
                                <i class="fas fa-microchip"></i> Componentes
                            </option>
                            <option value="ventas" <?= ($old_data['categoria'] ?? '') === 'ventas' ? 'selected' : '' ?>>
                                <i class="fas fa-shopping-cart"></i> Ventas
                            </option>
                            <option value="reportes" <?= ($old_data['categoria'] ?? '') === 'reportes' ? 'selected' : '' ?>>
                                <i class="fas fa-chart-bar"></i> Reportes
                            </option>
                            <option value="configuracion" <?= ($old_data['categoria'] ?? '') === 'configuracion' ? 'selected' : '' ?>>
                                <i class="fas fa-cog"></i> Configuración
                            </option>
                        </select>
                        <div class="form-text">
                            La categoría se auto-completará basándose en el nombre del permiso.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="accion" class="form-label">
                            <i class="fas fa-play"></i>
                            Acción
                        </label>
                        <select class="form-select" id="accion" name="accion">
                            <option value="">Seleccionar acción</option>
                            <option value="ver" <?= ($old_data['accion'] ?? '') === 'ver' ? 'selected' : '' ?>>Ver / Listar</option>
                            <option value="crear" <?= ($old_data['accion'] ?? '') === 'crear' ? 'selected' : '' ?>>Crear</option>
                            <option value="editar" <?= ($old_data['accion'] ?? '') === 'editar' ? 'selected' : '' ?>>Editar</option>
                            <option value="eliminar" <?= ($old_data['accion'] ?? '') === 'eliminar' ? 'selected' : '' ?>>Eliminar</option>
                            <option value="gestionar" <?= ($old_data['accion'] ?? '') === 'gestionar' ? 'selected' : '' ?>>Gestionar</option>
                            <option value="aprobar" <?= ($old_data['accion'] ?? '') === 'aprobar' ? 'selected' : '' ?>>Aprobar</option>
                            <option value="exportar" <?= ($old_data['accion'] ?? '') === 'exportar' ? 'selected' : '' ?>>Exportar</option>
                        </select>
                        <div class="form-text">
                            La acción se auto-completará basándose en el nombre del permiso.
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
                                  placeholder="Describe qué permite hacer este permiso en el sistema..."><?= htmlspecialchars($old_data['descripcion'] ?? '') ?></textarea>
                        <div class="form-text">
                            Opcional. Proporciona una descripción clara de lo que permite este permiso.
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Crear Permiso
                        </button>
                        <a href="<?= route('admin.permisos'); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Panel de Información -->
        <div class="col-lg-4">
            <!-- Vista Previa del Permiso -->
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-eye"></i>
                    Vista Previa
                </h3>

                <div class="permission-preview">
                    <div class="preview-item">
                        <span class="preview-label">Nombre completo:</span>
                        <code id="previewName" class="preview-value">permiso.ejemplo</code>
                    </div>
                    
                    <div class="preview-item">
                        <span class="preview-label">Categoría:</span>
                        <span id="previewCategory" class="preview-badge category-general">General</span>
                    </div>
                    
                    <div class="preview-item">
                        <span class="preview-label">Acción:</span>
                        <span id="previewAction" class="preview-action">-</span>
                    </div>
                </div>
            </div>

            <!-- Convenciones de Nomenclatura -->
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-book-open"></i>
                    Convenciones de Nomenclatura
                </h3>

                <div class="conventions-list">
                    <div class="convention-item">
                        <div class="convention-format">
                            <code>usuarios.ver</code>
                        </div>
                        <div class="convention-description">
                            Ver lista de usuarios
                        </div>
                    </div>

                    <div class="convention-item">
                        <div class="convention-format">
                            <code>usuarios.crear</code>
                        </div>
                        <div class="convention-description">
                            Crear nuevos usuarios
                        </div>
                    </div>

                    <div class="convention-item">
                        <div class="convention-format">
                            <code>cursos.editar</code>
                        </div>
                        <div class="convention-description">
                            Editar información de cursos
                        </div>
                    </div>

                    <div class="convention-item">
                        <div class="convention-format">
                            <code>reportes.exportar</code>
                        </div>
                        <div class="convention-description">
                            Exportar reportes del sistema
                        </div>
                    </div>
                </div>

                <div class="form-text">
                    Usa siempre minúsculas y separa con punto la categoría de la acción.
                </div>
            </div>

            <!-- Permisos Sugeridos -->
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb"></i>
                    Permisos Comunes
                </h3>

                <div class="suggested-permissions">
                    <div class="permission-group">
                        <h4>Gestión de Usuarios</h4>
                        <div class="permission-suggestions">
                            <button type="button" class="suggestion-btn" onclick="fillPermission('usuarios.ver', 'Ver lista de usuarios')">
                                usuarios.ver
                            </button>
                            <button type="button" class="suggestion-btn" onclick="fillPermission('usuarios.crear', 'Crear nuevos usuarios')">
                                usuarios.crear
                            </button>
                            <button type="button" class="suggestion-btn" onclick="fillPermission('usuarios.editar', 'Editar información de usuarios')">
                                usuarios.editar
                            </button>
                            <button type="button" class="suggestion-btn" onclick="fillPermission('usuarios.eliminar', 'Eliminar usuarios del sistema')">
                                usuarios.eliminar
                            </button>
                        </div>
                    </div>

                    <div class="permission-group">
                        <h4>Gestión de Cursos</h4>
                        <div class="permission-suggestions">
                            <button type="button" class="suggestion-btn" onclick="fillPermission('cursos.ver', 'Ver lista de cursos')">
                                cursos.ver
                            </button>
                            <button type="button" class="suggestion-btn" onclick="fillPermission('cursos.crear', 'Crear nuevos cursos')">
                                cursos.crear
                            </button>
                            <button type="button" class="suggestion-btn" onclick="fillPermission('cursos.publicar', 'Publicar cursos en la plataforma')">
                                cursos.publicar
                            </button>
                        </div>
                    </div>

                    <div class="permission-group">
                        <h4>Administración</h4>
                        <div class="permission-suggestions">
                            <button type="button" class="suggestion-btn" onclick="fillPermission('admin.configuracion', 'Acceder a configuración del sistema')">
                                admin.configuracion
                            </button>
                            <button type="button" class="suggestion-btn" onclick="fillPermission('reportes.generar', 'Generar reportes del sistema')">
                                reportes.generar
                            </button>
                        </div>
                    </div>
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

.form-control, .form-select {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 0.75rem;
    font-size: 0.875rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-control:focus, .form-select:focus {
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

.permission-preview {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 1rem;
}

.preview-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.preview-item:last-child {
    margin-bottom: 0;
}

.preview-label {
    font-size: 0.875rem;
    color: #6b7280;
    font-weight: 500;
}

.preview-value {
    font-family: 'Courier New', monospace;
    background: #1f2937;
    color: #10b981;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
}

.preview-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 500;
}

.preview-action {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.conventions-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.convention-item {
    padding: 0.75rem;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
}

.convention-format {
    margin-bottom: 0.25rem;
}

.convention-format code {
    background: #1f2937;
    color: #10b981;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
}

.convention-description {
    font-size: 0.75rem;
    color: #6b7280;
}

.suggested-permissions {
    max-height: 400px;
    overflow-y: auto;
}

.permission-group {
    margin-bottom: 1.5rem;
}

.permission-group h4 {
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.75rem;
}

.permission-suggestions {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.suggestion-btn {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
    padding: 0.5rem;
    text-align: left;
    cursor: pointer;
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
    color: #374151;
    transition: all 0.2s ease;
}

.suggestion-btn:hover {
    background: #f3f4f6;
    border-color: #3b82f6;
    color: #3b82f6;
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

/* Colores por categoría para preview */
.category-usuarios { background: #dbeafe; color: #1d4ed8; }
.category-admin { background: #fef3c7; color: #d97706; }
.category-cursos { background: #d1fae5; color: #047857; }
.category-libros { background: #e0e7ff; color: #5b21b6; }
.category-componentes { background: #fce7f3; color: #be185d; }
.category-ventas { background: #ecfdf5; color: #065f46; }
.category-reportes { background: #fff1f2; color: #dc2626; }
.category-configuracion { background: #f0f9ff; color: #0284c7; }
.category-general { background: #f3f4f6; color: #374151; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación del formulario
    const form = document.querySelector('.needs-validation');
    const nombreInput = document.getElementById('nombre');
    const categoriaSelect = document.getElementById('categoria');
    const accionSelect = document.getElementById('accion');
    
    // Actualizar vista previa en tiempo real
    function updatePreview() {
        const nombre = nombreInput.value || 'permiso.ejemplo';
        const categoria = categoriaSelect.value || 'general';
        const accion = accionSelect.value || '-';
        
        document.getElementById('previewName').textContent = nombre;
        
        const categoryBadge = document.getElementById('previewCategory');
        categoryBadge.textContent = categoria.charAt(0).toUpperCase() + categoria.slice(1);
        categoryBadge.className = `preview-badge category-${categoria}`;
        
        document.getElementById('previewAction').textContent = accion;
    }
    
    // Auto-completar categoría y acción basándose en el nombre
    nombreInput.addEventListener('input', function() {
        const value = this.value.toLowerCase();
        const parts = value.split('.');
        
        if (parts.length >= 2) {
            const categoria = parts[0];
            const accion = parts[1];
            
            // Auto-seleccionar categoría si existe
            if (categoriaSelect.querySelector(`option[value="${categoria}"]`)) {
                categoriaSelect.value = categoria;
            }
            
            // Auto-seleccionar acción si existe
            if (accionSelect.querySelector(`option[value="${accion}"]`)) {
                accionSelect.value = accion;
            }
        }
        
        updatePreview();
    });
    
    categoriaSelect.addEventListener('change', updatePreview);
    accionSelect.addEventListener('change', updatePreview);
    
    // Validación del formulario
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        });
    }
    
    // Validar nombre del permiso en tiempo real
    if (nombreInput) {
        nombreInput.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            
            if (value.length < 3) {
                this.setCustomValidity('El nombre debe tener al menos 3 caracteres');
            } else if (!/^[a-z0-9._-]+$/.test(value)) {
                this.setCustomValidity('Solo se permiten letras minúsculas, números, puntos, guiones y guiones bajos');
            } else if (!value.includes('.')) {
                this.setCustomValidity('El nombre debe seguir el formato: categoria.accion');
            } else {
                this.setCustomValidity('');
            }
        });
    }
    
    // Inicializar vista previa
    updatePreview();
});

function fillPermission(nombre, descripcion) {
    document.getElementById('nombre').value = nombre;
    document.getElementById('descripcion').value = descripcion;
    
    // Auto-completar categoría y acción
    const parts = nombre.split('.');
    if (parts.length >= 2) {
        const categoria = parts[0];
        const accion = parts[1];
        
        const categoriaSelect = document.getElementById('categoria');
        const accionSelect = document.getElementById('accion');
        
        if (categoriaSelect.querySelector(`option[value="${categoria}"]`)) {
            categoriaSelect.value = categoria;
        }
        
        if (accionSelect.querySelector(`option[value="${accion}"]`)) {
            accionSelect.value = accion;
        }
    }
    
    // Actualizar vista previa
    document.dispatchEvent(new Event('DOMContentLoaded'));
}
</script>
