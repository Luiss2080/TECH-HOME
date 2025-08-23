<?php 
// Variables para mensajes flash
$error = flashGet('error');
$success = flashGet('success');
$errors = flashGet('errors') ?? [];
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta - Tech Home Bolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/register.css') ?>">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css">

    <!-- Headers anti-cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
</head>

<body>
    <!-- Fondo animado -->
    <div class="bg-animation">
        <div class="floating-shapes shape-1"></div>
        <div class="floating-shapes shape-2"></div>
        <div class="floating-shapes shape-3"></div>
        <div class="floating-shapes shape-4"></div>
        <div class="floating-shapes shape-5"></div>
        <div class="floating-shapes shape-6"></div>
    </div>

    <div class="register-container">
        <!-- Panel de bienvenida -->
        <div class="welcome-panel">
            <div class="robot-icons">
                <?php for ($i = 0; $i < 20; $i++): ?>
                    <i class="fas fa-robot robot-icon"></i>
                <?php endfor; ?>
            </div>

            <div class="logo-section">
                <div class="logo-container">
                    <img src="<?= asset('imagenes/logos/LogoTech-Home.png') ?>" alt="Tech Home Logo" class="logo-img">
                </div>
                <div class="logo-underline"></div>
            </div>

            <h1 class="welcome-title">¡Únete a Nosotros!</h1>
            <p class="welcome-text">
                Crea tu cuenta y forma parte de la comunidad más innovadora de Bolivia.
                Accede a cursos de robótica, programación, electrónica y mucho más.
            </p>

            <div class="features-list">
                <div class="feature-item">
                    <i class="fas fa-robot"></i>
                    <span>Cursos de Robótica</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-code"></i>
                    <span>Programación</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-microchip"></i>
                    <span>Electrónica</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-brain"></i>
                    <span>Inteligencia Artificial</span>
                </div>
            </div>

            <div class="copyright-text">
                © 2025 Tech Home Bolivia – Todos los derechos reservados
            </div>
        </div>

        <!-- Panel de registro -->
        <div class="register-panel">
            <div class="register-header">
                <h2 class="register-title">Crear Cuenta</h2>
                <p class="register-subtitle">Completa tus datos para empezar tu aventura tecnológica</p>
            </div>

            <!-- Formulario -->
            <form method="POST" action="<?= route('register.store') ?>" id="registerForm">
                <?= CSRF() ?>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <div class="input-wrapper">
                            <input type="text" class="form-input" id="nombre" name="nombre"
                                placeholder="Tu nombre..."
                                value="<?= old('nombre') ?>" required>
                            <i class="fas fa-user input-icon"></i>
                            <div class="tooltip">Ingresa tu nombre completo</div>
                        </div>
                        <?php if (isset($errors['nombre'])): ?>
                            <?php foreach ($errors['nombre'] as $error): ?>
                                <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Apellido</label>
                        <div class="input-wrapper">
                            <input type="text" class="form-input" id="apellido" name="apellido"
                                placeholder="Tu apellido..."
                                value="<?= old('apellido') ?>" required>
                            <i class="fas fa-user-tag input-icon"></i>
                            <div class="tooltip">Ingresa tu apellido completo</div>
                        </div>
                        <?php if (isset($errors['apellido'])): ?>
                            <?php foreach ($errors['apellido'] as $error): ?>
                                <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" class="form-input" id="email" name="email"
                            placeholder="ejemplo@correo.com"
                            value="<?= old('email') ?>" required>
                        <i class="fas fa-envelope input-icon"></i>
                        <div class="tooltip">Usaremos este email para enviarte información importante</div>
                    </div>
                    <?php if (isset($errors['email'])): ?>
                        <?php foreach ($errors['email'] as $error): ?>
                            <div class="invalid-feedback"><?= $error ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Teléfono (Opcional)</label>
                        <div class="input-wrapper">
                            <input type="tel" class="form-input" id="telefono" name="telefono"
                                placeholder="+591 12345678"
                                value="<?= old('telefono') ?>">
                            <i class="fas fa-phone input-icon"></i>
                            <div class="tooltip">Número de contacto (opcional)</div>
                        </div>
                        <?php if (isset($errors['telefono'])): ?>
                            <?php foreach ($errors['telefono'] as $error): ?>
                                <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Fecha de Nacimiento (Opcional)</label>
                        <div class="input-wrapper">
                            <input type="date" class="form-input" id="fecha_nacimiento" name="fecha_nacimiento"
                                value="<?= old('fecha_nacimiento') ?>">
                            <i class="fas fa-calendar input-icon"></i>
                            <div class="tooltip">Tu fecha de nacimiento (opcional)</div>
                        </div>
                        <?php if (isset($errors['fecha_nacimiento'])): ?>
                            <?php foreach ($errors['fecha_nacimiento'] as $error): ?>
                                <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" class="form-input" id="password" name="password"
                                placeholder="Mínimo 8 caracteres..." required>
                            <i class="fas fa-lock input-icon"></i>
                            <i class="fas fa-eye password-toggle" data-target="password"></i>
                            <div class="tooltip">Debe tener al menos 8 caracteres</div>
                        </div>
                        <?php if (isset($errors['password'])): ?>
                            <?php foreach ($errors['password'] as $error): ?>
                                <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmar Contraseña</label>
                        <div class="input-wrapper">
                            <input type="password" class="form-input" id="password_confirmation" name="password_confirmation"
                                placeholder="Repite tu contraseña..." required>
                            <i class="fas fa-lock input-icon"></i>
                            <i class="fas fa-eye password-toggle" data-target="password_confirmation"></i>
                            <div class="tooltip">Debe coincidir con la contraseña anterior</div>
                        </div>
                        <?php if (isset($errors['password_confirmation'])): ?>
                            <?php foreach ($errors['password_confirmation'] as $error): ?>
                                <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-info">
                    <div class="info-box">
                        <i class="fas fa-gift"></i>
                        <div>
                            <strong>¡Acceso Especial!</strong>
                            <p>Como nuevo usuario, tendrás acceso completo por 3 días para explorar todo nuestro contenido.</p>
                        </div>
                    </div>
                </div>

                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i>
                    Crear Mi Cuenta
                </button>
            </form>

            <!-- Redes sociales -->
            <div class="divider">
                <p class="register-subtitle">¿Tienes dudas? ¡Contáctanos!</p>
            </div>

            <div class="social-buttons">
                <a href="#" class="social-btn tiktok-btn">
                    <img src="<?= asset('imagenes/logos/tiktok.webp') ?>" alt="TikTok" class="social-logo">
                    TikTok
                </a>
                <a href="#" class="social-btn facebook-btn">
                    <img src="<?= asset('imagenes/logos/facebook.webp') ?>" alt="Facebook" class="social-logo">
                    Facebook
                </a>
                <a href="#" class="social-btn instagram-btn">
                    <img src="<?= asset('imagenes/logos/Instagram.webp') ?>" alt="Instagram" class="social-logo">
                    Instagram
                </a>
                <a href="#" class="social-btn whatsapp-btn">
                    <img src="<?= asset('imagenes/logos/wpps.webp') ?>" alt="WhatsApp" class="social-logo">
                    WhatsApp
                </a>
            </div>

            <div class="login-link">
                ¿Ya tienes cuenta? <a href="<?= route('login') ?>">Inicia sesión aquí</a>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });

        // Animaciones de inputs
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
                this.parentElement.style.zIndex = '10';
            });

            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
                this.parentElement.style.zIndex = '1';
            });
        });

        // Validación en tiempo real de contraseñas
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');

        function validatePasswords() {
            if (password.value && passwordConfirmation.value) {
                if (password.value === passwordConfirmation.value) {
                    passwordConfirmation.style.borderColor = '#28a745';
                    passwordConfirmation.parentElement.querySelector('.input-icon').style.color = '#28a745';
                } else {
                    passwordConfirmation.style.borderColor = '#dc3545';
                    passwordConfirmation.parentElement.querySelector('.input-icon').style.color = '#dc3545';
                }
            }
        }

        password.addEventListener('input', validatePasswords);
        passwordConfirmation.addEventListener('input', validatePasswords);

        // Auto-dismiss alerts
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-100%)';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        console.log('🚀 Register page loaded');
    </script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>

    <script>
        // Configuración personalizada de SweetAlert2
        const customSwal = Swal.mixin({
            customClass: {
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn',
                popup: 'swal-popup'
            },
            buttonsStyling: false
        });

        // Mostrar mensajes usando SweetAlert2
        <?php 
        $error = flashGet('error');
        $success = flashGet('success');
        $errors = flashGet('errors') ?? [];
        ?>
        
        <?php if ($error): ?>
            customSwal.fire({
                icon: 'error',
                title: '¡Error!',
                text: '<?= addslashes($error) ?>',
                confirmButtonText: 'Entendido',
                background: '#1f2937',
                color: '#fff',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                }
            });
        <?php endif; ?>

        <?php if ($success): ?>
            customSwal.fire({
                icon: 'success',
                title: '¡Cuenta creada!',
                html: '<?= addslashes($success) ?><br><br><strong>🔑 Importante:</strong> Revisa tu email para activar tu cuenta antes de iniciar sesión.',
                confirmButtonText: 'Ir al Login',
                background: '#1f2937',
                color: '#fff',
                timer: 8000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                }
            }).then((result) => {
                if (result.isConfirmed || result.dismiss === Swal.DismissReason.timer) {
                    window.location.href = '<?= route('login') ?>';
                }
            });
        <?php endif; ?>

        // Mostrar errores de validación
        <?php if (!empty($errors) && is_array($errors)): ?>
            <?php 
            $errorMessages = [];
            foreach ($errors as $field => $fieldErrors) {
                if (is_array($fieldErrors)) {
                    foreach ($fieldErrors as $fieldError) {
                        $errorMessages[] = ucfirst($field) . ': ' . $fieldError;
                    }
                }
            }
            if (!empty($errorMessages)): ?>
                let errorMessage = 'Por favor corrige los siguientes errores:\n\n';
                <?php foreach ($errorMessages as $errorMsg): ?>
                    errorMessage += '• <?= addslashes($errorMsg) ?>\n';
                <?php endforeach; ?>
                
                customSwal.fire({
                    icon: 'warning',
                    title: 'Errores de validación',
                    text: errorMessage,
                    confirmButtonText: 'Corregir',
                    background: '#1f2937',
                    color: '#fff',
                    showClass: {
                        popup: 'animate__animated animate__fadeInDown animate__faster'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOutUp animate__faster'
                    }
                });
            <?php endif; ?>
        <?php endif; ?>
    </script>

    <!-- Estilos personalizados para SweetAlert2 -->
    <style>
        .swal-popup {
            border-radius: 15px !important;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3) !important;
        }

        .swal-confirm-btn {
            background: linear-gradient(45deg, #dc2626, #b91c1c) !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 12px 24px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            margin: 0 5px !important;
            transition: all 0.3s ease !important;
        }

        .swal-confirm-btn:hover {
            background: linear-gradient(45deg, #b91c1c, #991b1b) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 5px 15px rgba(220, 38, 38, 0.4) !important;
        }

        .swal-cancel-btn {
            background: #6b7280 !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 12px 24px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            margin: 0 5px !important;
            transition: all 0.3s ease !important;
        }

        .swal-cancel-btn:hover {
            background: #4b5563 !important;
            transform: translateY(-2px) !important;
        }

        /* Animaciones personalizadas */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translate3d(0, -100%, 0);
            }

            to {
                opacity: 1;
                transform: none;
            }
        }

        @keyframes fadeOutUp {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
                transform: translate3d(0, -100%, 0);
            }
        }

        .animate__animated {
            animation-duration: 0.5s;
        }

        .animate__faster {
            animation-duration: 0.3s;
        }

        .animate__fadeInDown {
            animation-name: fadeInDown;
        }

        .animate__fadeOutUp {
            animation-name: fadeOutUp;
        }
    </style>
</body>

</html>