<?php
$title = $title ?? 'Editar Permisos de Usuario';
$errors = flashGet('errors') ?? [];
?>

<!-- Estilos específicos para el módulo  -->
<link rel="stylesheet" href="<?= asset('css/admin/admin.css'); ?>">

<div class="dashboard-content">
    <!-- Header -->
    <div class="section-header">
        <div class="section-header-content">
            <h2 class="section-title">
                <i class="fas fa-key"></i>
                Editar Permisos de Usuario
            </h2>
            <p class="section-subtitle">Gestiona los permisos directos asignados a <?= htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido) ?></p>
        </div>
        <div class="section-header-actions">
            <a href="<?= route('usuarios'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver a Usuarios
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

    <!-- Mostrar mensajes flash -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Información del usuario -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="card-title mb-1">
                        <?= htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido) ?>
                    </h5>
                    <p class="text-muted mb-0">
                        <i class="fas fa-envelope"></i> <?= htmlspecialchars($usuario->email) ?>
                        <span class="ms-3">
                            <i class="fas fa-circle <?= $usuario->estado == 1 ? 'text-success' : 'text-danger' ?>"></i>
                            <?= $usuario->estado == 1 ? 'Activo' : 'Inactivo' ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario para editar permisos -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">
                <i class="fas fa-key"></i>
                Permisos Directos del Usuario
            </h5>
            <p class="card-text text-muted">
                Selecciona los permisos directos que deseas asignar a este usuario. 
                <strong>Nota:</strong> Los permisos a través de roles se aplican automáticamente.
            </p>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= route('usuarios.permisos.update', ['id' => $usuario->id]); ?>">
                <?= CSRF() ?>
                <input type="hidden" name="_method" value="PUT">
                
                <!-- Grid de permisos -->
                <div class="permissions-grid">
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

                    // Array de permisos actuales del usuario para facilitar la verificación
                    $permisosActuales = [];
                    foreach ($permisosUsuario as $permiso) {
                        $permisosActuales[] = $permiso['id'];
                    }
                    ?>
                    
                    <?php foreach ($categorias as $categoria => $permisosCategoria): ?>
                        <div class="permission-category">
                            <h6 class="category-title">
                                <i class="fas fa-folder"></i>
                                <?= ucfirst($categoria) ?>
                            </h6>
                            <div class="permissions-list">
                                <?php foreach ($permisosCategoria as $permiso): ?>
                                    <div class="form-check permission-item">
                                        <input 
                                            class="form-check-input" 
                                            type="checkbox" 
                                            id="permission_<?= $permiso->id ?>" 
                                            name="permisos[]" 
                                            value="<?= $permiso->id ?>"
                                            <?= in_array($permiso->id, $permisosActuales) ? 'checked' : '' ?>
                                        >
                                        <label class="form-check-label" for="permission_<?= $permiso->id ?>">
                                            <span class="permission-name"><?= htmlspecialchars($permiso->name) ?></span>
                                            <?php if (!empty($permiso->description)): ?>
                                                <small class="text-muted d-block"><?= htmlspecialchars($permiso->description) ?></small>
                                            <?php endif; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Acciones -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar Permisos
                    </button>
                    <a href="<?= route('usuarios'); ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.permissions-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.permission-category {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 1rem;
    background-color: #f8f9fa;
}

.category-title {
    margin-bottom: 1rem;
    color: #495057;
    font-weight: 600;
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 0.5rem;
}

.category-title i {
    color: #007bff;
    margin-right: 0.5rem;
}

.permissions-list {
    max-height: 300px;
    overflow-y: auto;
}

.permission-item {
    margin-bottom: 0.75rem;
    padding: 0.5rem;
    background-color: white;
    border-radius: 4px;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}

.permission-item:hover {
    background-color: #f8f9fa;
    border-color: #007bff;
}

.permission-item .form-check-input:checked ~ .form-check-label {
    color: #007bff;
    font-weight: 500;
}

.permission-name {
    font-weight: 500;
    color: #495057;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-start;
    align-items: center;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #dee2e6;
}

.form-actions .btn {
    min-width: 120px;
}

.alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #dee2e6;
}

.section-title {
    margin-bottom: 0.5rem;
    color: #495057;
    font-weight: 600;
}

.section-title i {
    color: #007bff;
    margin-right: 0.75rem;
}

.section-subtitle {
    color: #6c757d;
    margin-bottom: 0;
    font-size: 0.95rem;
}

.section-header-actions .btn {
    min-width: auto;
}

@media (max-width: 768px) {
    .permissions-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .section-header-actions {
        text-align: left;
    }
}
</style>
