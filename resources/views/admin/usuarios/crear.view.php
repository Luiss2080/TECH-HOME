<div class="dashboard-content">
    <div class="section-card">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= route('admin.dashboard') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= route('usuarios') ?>">Usuarios</a></li>
                        <li class="breadcrumb-item active">Crear Usuario</li>
                    </ol>
                </nav>
                <h1 class="h3 mb-0">Crear Nuevo Usuario</h1>
            </div>
        </div>

        <!-- Alertas -->
        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= session('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Errores de validación -->
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6><i class="fas fa-exclamation-circle"></i> Por favor corrige los siguientes errores:</h6>
                <ul class="mb-0">
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
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user-plus"></i> Información del Usuario
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="<?= route('usuarios.store') ?>" id="form-usuario">
                            <?php CSRF() ?>
                            <div class="row">
                                <!-- Información Personal -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nombre" class="form-label">Nombre <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?= isset($errors['nombre']) ? 'is-invalid' : '' ?>"
                                            id="nombre" name="nombre" value="<?= $old['nombre'] ?? '' ?>" required>
                                        <?php if (isset($errors['nombre'])): ?>
                                            <div class="invalid-feedback">
                                                <?= is_array($errors['nombre']) ? $errors['nombre'][0] : $errors['nombre'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="apellido" class="form-label">Apellido <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control <?= isset($errors['apellido']) ? 'is-invalid' : '' ?>"
                                            id="apellido" name="apellido" value="<?= $old['apellido'] ?? '' ?>" required>
                                        <?php if (isset($errors['apellido'])): ?>
                                            <div class="invalid-feedback">
                                                <?= is_array($errors['apellido']) ? $errors['apellido'][0] : $errors['apellido'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                            id="email" name="email" value="<?= $old['email'] ?? '' ?>" required>
                                        <?php if (isset($errors['email'])): ?>
                                            <div class="invalid-feedback">
                                                <?= is_array($errors['email']) ? $errors['email'][0] : $errors['email'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="telefono" class="form-label">Teléfono</label>
                                        <input type="text" class="form-control <?= isset($errors['telefono']) ? 'is-invalid' : '' ?>"
                                            id="telefono" name="telefono" value="<?= $old['telefono'] ?? '' ?>">
                                        <?php if (isset($errors['telefono'])): ?>
                                            <div class="invalid-feedback">
                                                <?= is_array($errors['telefono']) ? $errors['telefono'][0] : $errors['telefono'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control <?= isset($errors['fecha_nacimiento']) ? 'is-invalid' : '' ?>"
                                            id="fecha_nacimiento" name="fecha_nacimiento"
                                            value="<?= $old['fecha_nacimiento'] ?? '' ?>">
                                        <?php if (isset($errors['fecha_nacimiento'])): ?>
                                            <div class="invalid-feedback">
                                                <?= is_array($errors['fecha_nacimiento']) ? $errors['fecha_nacimiento'][0] : $errors['fecha_nacimiento'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                            id="password" name="password" required>
                                        <div class="form-text">Mínimo 8 caracteres</div>
                                        <?php if (isset($errors['password'])): ?>
                                            <div class="invalid-feedback">
                                                <?= is_array($errors['password']) ? $errors['password'][0] : $errors['password'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control <?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>"
                                            id="password_confirmation" name="password_confirmation" required>
                                        <?php if (isset($errors['password_confirmation'])): ?>
                                            <div class="invalid-feedback">
                                                <?= is_array($errors['password_confirmation']) ? $errors['password_confirmation'][0] : $errors['password_confirmation'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Roles -->
                            <div class="mb-4">
                                <h6 class="mb-3"><i class="fas fa-user-tag"></i> Asignar Roles</h6>
                                <?php if (isset($roles) && !empty($roles)): ?>
                                    <div class="row">
                                        <?php foreach ($roles as $rol): ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input <?= isset($errors['roles']) ? 'is-invalid' : '' ?>" type="checkbox"
                                                        id="role_<?= $rol->id ?>" name="roles[]"
                                                        value="<?= $rol->id ?>"
                                                        <?= in_array($rol->id, $old['roles'] ?? []) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="role_<?= $rol->id ?>">
                                                        <strong><?= htmlspecialchars($rol->nombre) ?></strong>
                                                        <small class="text-muted d-block"><?= htmlspecialchars($rol->descripcion) ?></small>
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php if (isset($errors['roles'])): ?>
                                        <div class="invalid-feedback d-block">
                                            <?= is_array($errors['roles']) ? $errors['roles'][0] : $errors['roles'] ?>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        No hay roles disponibles.
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex justify-content-between">
                                <a href="<?= route('usuarios') ?>" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Crear Usuario
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Información de Ayuda -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle"></i> Información sobre Roles
                        </h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">
                            <strong>Tipos de Roles:</strong><br>
                            • <strong>Administrador:</strong> Acceso completo<br>
                            • <strong>Docente:</strong> Gestión de cursos y materiales<br>
                            • <strong>Estudiante:</strong> Acceso a contenido educativo<br>
                            • <strong>Vendedor:</strong> Gestión de ventas y componentes<br>
                            • <strong>Invitado:</strong> Acceso temporal limitado
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>