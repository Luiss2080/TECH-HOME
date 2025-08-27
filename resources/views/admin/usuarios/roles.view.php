<?php
$title = $title ?? 'Editar Roles de Usuario';
$errors = flashGet('errors') ?? [];
?>

<!-- Estilos específicos para el módulo  -->
<link rel="stylesheet" href="<?= asset('css/admin/admin.css'); ?>">

<div class="dashboard-content">
    <!-- Header -->
    <div class="section-header">
        <div class="section-header-content">
            <h2 class="section-title">
                <i class="fas fa-user-cog"></i>
                Editar Roles de Usuario
            </h2>
            <p class="section-subtitle">Gestiona los roles asignados a <?= htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido) ?></p>
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

    <div class="row">
        <!-- Información del Usuario -->
        <div class="col-lg-4">
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-user"></i>
                    Información del Usuario
                </h3>
                
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-details">
                        <div class="info-item">
                            <span class="info-label">Nombre Completo:</span>
                            <span class="info-value"><?= htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?= htmlspecialchars($usuario->email) ?></span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Estado:</span>
                            <span class="info-value">
                                <span class="badge bg-<?= $usuario->estado ? 'success' : 'danger' ?>">
                                    <?= $usuario->estado ? 'Activo' : 'Inactivo' ?>
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Fecha de Registro:</span>
                            <span class="info-value"><?= date('d/m/Y', strtotime($usuario->fecha_creacion)) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de Roles -->
        <div class="col-lg-8">
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-user-tag"></i>
                    Asignar Roles al Usuario
                </h3>

                <form method="POST" action="<?= route('usuarios.roles.update', ['id' => $usuario->id]); ?>" class="needs-validation" novalidate>
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-tags"></i>
                            Seleccionar Roles
                        </label>
                        
                        <?php if (empty($rolesDisponibles)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No hay roles disponibles para asignar.
                            </div>
                        <?php else: ?>
                            <div class="roles-grid">
                                <?php foreach ($rolesDisponibles as $rol): ?>
                                    <div class="role-option">
                                        <input 
                                            type="checkbox" 
                                            id="role_<?= $rol->id ?>" 
                                            name="roles[]" 
                                            value="<?= $rol->id ?>"
                                            <?= in_array($rol->id, $rolesUsuario) ? 'checked' : '' ?>
                                            class="role-checkbox"
                                        >
                                        <label for="role_<?= $rol->id ?>" class="role-label">
                                            <div class="role-content">
                                                <div class="role-name"><?= htmlspecialchars($rol->nombre) ?></div>
                                                <?php if (!empty($rol->descripcion)): ?>
                                                    <div class="role-description"><?= htmlspecialchars($rol->descripcion) ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="role-indicator">
                                                <i class="fas fa-check"></i>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="form-text">
                            Puedes seleccionar múltiples roles para este usuario. Los cambios se aplicarán inmediatamente.
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary" <?= empty($rolesDisponibles) ? 'disabled' : '' ?>>
                            <i class="fas fa-save"></i>
                            Guardar Cambios
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
</div>

<!-- CSS usando el estilo del proyecto -->
<style>
.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group {
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

.user-info {
    padding: 1rem;
}

.user-avatar {
    text-align: center;
    margin-bottom: 1rem;
}

.user-avatar i {
    font-size: 3rem;
    color: #6b7280;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
    margin-bottom: 1rem;
}

.info-label {
    font-weight: 600;
    color: #374151;
    font-size: 14px;
}

.info-value {
    color: #6b7280;
    font-size: 15px;
}

.roles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
}

.role-option {
    position: relative;
}

.role-checkbox {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.role-label {
    display: block;
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.role-label:hover {
    border-color: #dc2626;
    background: #fef2f2;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.1);
}

.role-checkbox:checked + .role-label {
    border-color: #dc2626;
    background: linear-gradient(135deg, #fef2f2 0%, #fecaca 100%);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
}

.role-content {
    flex: 1;
}

.role-name {
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 4px;
}

.role-description {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.4;
}

.role-indicator {
    width: 24px;
    height: 24px;
    border: 2px solid #e5e7eb;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    background: white;
}

.role-checkbox:checked + .role-label .role-indicator {
    border-color: #dc2626;
    background: #dc2626;
    color: white;
}

.role-indicator i {
    font-size: 12px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.role-checkbox:checked + .role-label .role-indicator i {
    opacity: 1;
}

.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 24px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.form-text {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.5rem;
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

.alert-warning {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #f59e0b;
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
