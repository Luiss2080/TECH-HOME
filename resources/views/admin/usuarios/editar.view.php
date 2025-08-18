<div class="dashboard-content">
    <div class="section-card">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= route('admin.dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= route('usuarios') ?>">Usuarios</a></li>
                        <li class="breadcrumb-item active">Editar Usuario</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0">Editar Usuario: <?= htmlspecialchars($usuario->nombre . ' ' . $usuario->apellido) ?></h1>
            </div>
        </div>

        <!-- Alertas -->
        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-edit"></i> Información del Usuario
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= route('usuarios.update', ['id' => $usuario->id]) ?>" id="form-usuario">
                            <?php CSRF() ?>
                            <input type="hidden" name="_method" value="PUT">

                            <div class="row">
                                <!-- Información Personal -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nombre" name="nombre"
                                            value="<?= old('nombre', $usuario->nombre) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="apellido" name="apellido"
                                            value="<?= old('apellido', $usuario->apellido) ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="<?= old('email', $usuario->email) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control" id="telefono" name="telefono"
                                            value="<?= old('telefono', $usuario->telefono) ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                                            value="<?= old('fecha_nacimiento', $usuario->fecha_nacimiento) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                        <select class="form-select" id="estado" name="estado" required>
                                            <option value="activo" <?= old('estado', $usuario->estado == 1 ? 'activo' : 'inactivo') === 'activo' ? 'selected' : '' ?>>Activo</option>
                                            <option value="inactivo" <?= old('estado', $usuario->estado == 1 ? 'activo' : 'inactivo') === 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Cambiar Contraseña -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">
                                        <i class="fas fa-key"></i> Cambiar Contraseña (Opcional)
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password" class="form-label">Nueva Contraseña</label>
                                                <input type="password" class="form-control" id="password" name="password">
                                                <div class="form-text">Dejar vacío para mantener la contraseña actual</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
                                                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex justify-content-between">
                                <a href="<?= route('usuarios') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Actualizar Usuario
                                </button>
                            </div>
                    </div>
                </div>
            </div>

            <!-- Roles -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-tag"></i> Roles Asignados
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($roles) && !empty($roles)): ?>
                            <div class="mb-3">
                                <label class="form-label">Seleccionar Roles <span class="text-danger">*</span></label>
                                <?php foreach ($roles as $rol): ?>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            id="role_<?= $rol['id'] ?>" name="roles[]"
                                            value="<?= $rol['id'] ?>"
                                            <?= in_array($rol['id'], old('roles', $usuarioRoles)) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="role_<?= $rol['id'] ?>">
                                            <?= htmlspecialchars($rol['nombre']) ?>
                                            <small class="text-muted d-block"><?= htmlspecialchars($rol['descripcion']) ?></small>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                No hay roles disponibles.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Información del Usuario
                        </h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <strong>ID:</strong> <?= $usuario->id ?><br>
                            <strong>Fecha de Creación:</strong> <?= date('d/m/Y H:i', strtotime($usuario->fecha_creacion)) ?><br>
                            <strong>Última Actualización:</strong> <?= $usuario->fecha_actualizacion ? date('d/m/Y H:i', strtotime($usuario->fecha_actualizacion)) : 'Nunca' ?>
                        </small>
                    </div>
                </div>
            </div>
        </form>
        </div>

<script>
    // Validación del formulario
    document.getElementById('form-usuario').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;

        // Solo validar contraseñas si se proporciona una nueva
        if (password || confirmPassword) {
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Las contraseñas no coinciden');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('La contraseña debe tener al menos 8 caracteres');
                return false;
            }
        }

        // Verificar que al menos un rol esté seleccionado
        const rolesSeleccionados = document.querySelectorAll('input[name="roles[]"]:checked');
        if (rolesSeleccionados.length === 0) {
            e.preventDefault();
            alert('Debe seleccionar al menos un rol');
            return false;
        }
    });
</script>

</div>