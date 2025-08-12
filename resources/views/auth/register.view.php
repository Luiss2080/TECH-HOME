<div class="container mt-5">
    <style>
        .form-label {
            font-weight: 500;
            color: #003366;
        }

        .card-header {
            background: linear-gradient(135deg, #003366, #004080);
        }

        .select2-container--default .select2-selection--single {
            border-color: #dee2e6;
            height: 38px;
            padding: 5px;
        }
    </style>
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">
                        <i class="bi bi-person-plus me-2"></i>
                        <?= traducir('Registrarse') ?>
                    </h3>
                </div>

                <div class="card-body">
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success"><?= traducir('Registro Exitoso') ?></div>
                    <?php endif; ?>

                    <form action="<?= route('auth.register') ?>" method="POST">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">

                        <!-- Nombre Completo -->
                        <div class="mb-3">
                            <label class="form-label">
                                <?= traducir('Nombre Completo') ?>
                            </label>
                            <input type="text"
                                class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                                name="name"
                                value="<?= $old['name'] ?? '' ?>">
                            <?php if (isset($errors['name'])):
                                foreach ($errors['name'] as $error): ?>
                                    <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach;
                            endif; ?>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label"><?= traducir('Correo Electrónico') ?></label>
                            <input type="email"
                                class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                name="email"
                                value="<?= $old['email'] ?? '' ?>">
                            <?php if (isset($errors['email'])):
                                foreach ($errors['email'] as $error): ?>
                                    <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach;
                            endif; ?>
                        </div>

                        <!-- Teléfono -->
                        <div class="mb-3">
                            <label class="form-label"> <?= traducir('Teléfono') ?></label>
                            <input type="tel"
                                class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                                name="phone"
                                value="<?= $old['phone'] ?? '' ?>">
                            <?php if (isset($errors['phone'])):
                                foreach ($errors['phone'] as $error): ?>
                                    <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach;
                            endif; ?>
                        </div>

                        <!-- Usuario -->
                        <div class="mb-3">
                            <label class="form-label"><?= traducir('Usuario') ?></label>
                            <input type="text"
                                class="form-control <?= isset($errors['user']) ? 'is-invalid' : '' ?>"
                                name="user"
                                value="<?= $old['user'] ?? '' ?>">
                            <?php if (isset($errors['user'])):
                                foreach ($errors['user'] as $error): ?>
                                    <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach;
                            endif; ?>
                        </div>

                        <!-- Contraseña -->
                        <div class="mb-3">
                            <label class="form-label"><?= traducir('Contraseña') ?></label>
                            <input type="password"
                                class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                name="password">
                            <?php if (isset($errors['password'])):
                                foreach ($errors['password'] as $error): ?>
                                    <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach;
                            endif; ?>
                        </div>

                        <!-- Confirmar Contraseña -->
                        <div class="mb-3">
                            <label class="form-label"> <?= traducir('Confirmar Contraseña') ?></label>
                            <input type="password"
                                class="form-control"
                                name="password_confirmation">
                        </div>
                        <!-- Términos y Condiciones -->
                        <div class="mb-3 form-check">
                            <input type="checkbox"
                                class="form-check-input <?= isset($errors['terms']) ? 'is-invalid' : '' ?>"
                                name="terms"
                                id="terms"
                                <?= isset($old['terms']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="terms">
                                <?= traducir('Acepto los términos y condiciones') ?>
                            </label>
                            <?php if (isset($errors['terms'])):
                                foreach ($errors['terms'] as $error): ?>
                                    <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach;
                            endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-person-check me-2"></i> <?= traducir('Registrarse') ?>
                        </button>
                    </form>
                </div>

                <div class="card-footer text-center">
                    <?= traducir('Ya tienes una cuenta?') ?>
                    <a href="<?= route('login') ?>" class="text-decoration-none">
                        <?= traducir('Inicia sesión') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>