<?php
$title = $title ?? 'Editar Rol - Configuración';
$errors = flashGet('errors') ?? [];
$role = $role ?? null;
if (!$role) {
    throw new Exception("Rol no encontrado");
}
?>

<div class="dashboard-content">

    <!-- Header -->
    <div class="section-header">
        <div class="section-header-content">
            <h2 class="section-title">
                <i class="fas fa-edit"></i>
                Editar Rol: <?= htmlspecialchars($role->nombre ?? 'Desconocido') ?>
            </h2>
            <p class="section-subtitle">Modifica la información y características de este rol</p>
        </div>
        <div class="section-header-actions">
            <a href="<?= route('admin.roles'); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i>
                Volver a Roles
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

    <div class="row">
        <!-- Formulario Principal -->
        <div class="col-lg-8">
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-edit"></i>
                    Información del Rol
                </h3>

                <form method="POST" action="<?= route('admin.roles.update', ['id' => $role->id]); ?>" class="needs-validation" novalidate>
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        <label for="nombre" class="form-label required">
                            <i class="fas fa-tag"></i>
                            Nombre del Rol
                        </label>
                        <input type="text"
                            class="form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>"
                            id="nombre"
                            name="nombre"
                            value="<?= htmlspecialchars(old('nombre', $role->nombre ?? '')) ?>"
                            placeholder="Ej: supervisor, vendedor, moderador..."
                            required>
                        <?php if (isset($errors['nombre'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= htmlspecialchars($errors['nombre'][0]) ?>
                            </div>
                        <?php else: ?>
                            <div class="invalid-feedback">
                                Por favor, ingresa el nombre del rol.
                            </div>
                        <?php endif; ?>
                        <div class="form-text">
                            El nombre debe ser único y descriptivo. Se recomienda usar minúsculas y sin espacios.
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-align-left"></i>
                            Descripción
                        </label>
                        <textarea class="form-control <?= isset($errors['descripcion']) ? 'is-invalid' : '' ?>"
                            id="descripcion"
                            name="descripcion"
                            rows="4"
                            placeholder="Describe las funciones y responsabilidades de este rol..."><?= htmlspecialchars(old('descripcion', $role->descripcion ?? '')) ?></textarea>
                        <?php if (isset($errors['descripcion'])): ?>
                            <div class="invalid-feedback d-block">
                                <?= htmlspecialchars($errors['descripcion'][0]) ?>
                            </div>
                        <?php endif; ?>
                        <div class="form-text">
                            Opcional. Proporciona una descripción clara de las funciones del rol.
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Actualizar Rol
                        </button>
                        <a href="<?= route('admin.roles'); ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Panel de Información -->
        <div class="col-lg-4">
            <!-- Información del Rol -->
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Detalles del Rol
                </h3>

                <div class="role-details">
                    <div class="detail-item">
                        <span class="detail-label">ID del Rol:</span>
                        <span class="detail-value"><?= $role->id ?? 'N/A' ?></span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Fecha de Creación:</span>
                        <span class="detail-value">
                            <?= date('d/m/Y H:i', strtotime($role->created_at ?? $role->fecha_creacion ?? 'now')) ?>
                        </span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Última Modificación:</span>
                        <span class="detail-value">
                            <?= date('d/m/Y H:i', strtotime($role->updated_at ?? $role->fecha_modificacion ?? 'now')) ?>
                        </span>
                    </div>

                    <div class="detail-item">
                        <span class="detail-label">Estado:</span>
                        <span class="detail-value">
                            <span class="badge badge-success">Activo</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Acciones Adicionales -->
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-tools"></i>
                    Acciones Adicionales
                </h3>

                <div class="action-buttons-vertical">
                    <a href="<?= route('admin.roles.permisos', ['id' => $role->id]); ?>" class="btn btn-outline-primary btn-block">
                        <i class="fas fa-key"></i>
                        Gestionar Permisos
                    </a>

                    <button type="button" class="btn btn-outline-info btn-block" onclick="showUsersList()">
                        <i class="fas fa-users"></i>
                        Ver Usuarios Asignados
                    </button>

                    <button type="button" class="btn btn-outline-secondary btn-block" onclick="duplicateRole()">
                        <i class="fas fa-copy"></i>
                        Duplicar Rol
                    </button>
                </div>
            </div>

            <!-- Consejos -->
            <div class="section-card">
                <h3 class="card-title">
                    <i class="fas fa-lightbulb"></i>
                    Consejos de Edición
                </h3>

                <div class="tips-list">
                    <div class="tip-item">
                        <div class="tip-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="tip-content">
                            <h4>Cambios de Nombre</h4>
                            <p>Cambiar el nombre del rol puede afectar referencias en el código. Verifica que no cause conflictos.</p>
                        </div>
                    </div>

                    <div class="tip-item">
                        <div class="tip-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="tip-content">
                            <h4>Usuarios Existentes</h4>
                            <p>Los usuarios que ya tienen asignado este rol mantendrán los cambios automáticamente.</p>
                        </div>
                    </div>

                    <div class="tip-item">
                        <div class="tip-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="tip-content">
                            <h4>Permisos</h4>
                            <p>Recuerda revisar y actualizar los permisos después de modificar el rol si es necesario.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .role-details {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #6b7280;
    }

    .detail-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
    }

    .badge-success {
        background: linear-gradient(135deg, #10b981, #047857);
        color: white;
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-weight: 500;
    }

    .action-buttons-vertical {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .btn-block {
        width: 100%;
        justify-content: flex-start;
        gap: 0.5rem;
    }

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

    .form-control {
        border: 1px solid #d1d5db;
        border-radius: 6px;
        padding: 0.75rem;
        font-size: 0.875rem;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
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
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.875rem;
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
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validación del formulario
        const form = document.querySelector('.needs-validation');

        if (form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            });
        }

        // Validar nombre del rol en tiempo real
        const nombreInput = document.getElementById('nombre');
        if (nombreInput) {
            nombreInput.addEventListener('input', function() {
                const value = this.value.toLowerCase();

                // Lista de nombres reservados
                const reserved = ['admin', 'root', 'system', 'guest', 'user'];

                if (reserved.includes(value)) {
                    this.setCustomValidity('Este nombre está reservado para el sistema');
                } else if (value.length < 3) {
                    this.setCustomValidity('El nombre debe tener al menos 3 caracteres');
                } else if (!/^[a-zA-Z0-9_-]+$/.test(value)) {
                    this.setCustomValidity('Solo se permiten letras, números, guiones y guiones bajos');
                } else {
                    this.setCustomValidity('');
                }
            });
        }
    });

    function showUsersList() {
        alert('Funcionalidad de ver usuarios asignados en desarrollo');
    }

    function duplicateRole() {
        if (confirm('¿Quieres crear una copia de este rol?')) {
            alert('Funcionalidad de duplicar rol en desarrollo');
        }
    }
</script>