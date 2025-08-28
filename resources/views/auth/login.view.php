<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tech Home Bolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/login.css') ?>">
    
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
    </div>

    <div class="login-container">
        <!-- Panel de bienvenida -->
        <div class="welcome-panel">
            <div class="robot-icons">
                <?php for ($i = 0; $i < 16; $i++): ?>
                    <i class="fas fa-robot robot-icon"></i>
                <?php endfor; ?>
            </div>

            <div class="logo-section">
                <div class="logo-container">
                    <img src="<?= asset('imagenes/logos/LogoTech-Home.png') ?>" alt="Tech Home Logo" class="logo-img">
                </div>
                <div class="logo-underline"></div>
            </div>

            <h1 class="welcome-title">¡Bienvenido!</h1>
            <p class="welcome-text">
                Inicia sesión con tu cuenta académica y da el primer paso hacia una experiencia única llena de innovación y creatividad
            </p>
            <div class="copyright-text">
                © 2025 Tech Home Bolivia – Todos los derechos reservados
            </div>
        </div>

        <!-- Panel de login -->
        <div class="login-panel">
            <div class="login-header">
                <h2 class="login-title">Iniciar Sesión</h2>
                <p class="login-subtitle">Ingresa tus credenciales para continuar</p>
            </div>

            <!-- Formulario -->
            <form method="POST" action="<?= route('login.loginForm') ?>">
                <?= CSRF() ?>
                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" class="form-input" id="email" name="email"
                            value="<?= old('email') ?>"
                            placeholder="Ingresa tu correo académico..." required>
                        <i class="fas fa-envelope input-icon"></i>
                        <div class="tooltip">Usa tu email registrado en la plataforma</div>
                        <?php if (isset($errors['email'])): ?>
                            <?php foreach ($errors['email'] as $error): ?>
                                <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" class="form-input" id="password" name="password"
                            placeholder="Ingresa tu contraseña..." required>
                        <i class="fas fa-lock input-icon"></i>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        <?php if (isset($errors['password'])): ?>
                            <?php foreach ($errors['password'] as $error): ?>
                                <div class="invalid-feedback"><?= $error ?></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="tooltip">Mínimo 8 caracteres</div>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" class="checkbox" id="remember">
                        <span>Recordarme</span>
                    </label>
                    <a href="<?= route('password.forgot') ?>" class="forgot-password">¿Olvidaste tu contraseña?</a>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Iniciar Sesión
                </button>
            </form>

            <!-- Redes sociales -->
            <div class="divider" style="text-align: center;">
                <p class="login-subtitle">¿Tienes dudas o quieres saber más?</p>
                <p class="login-invitation" style="font-weight: bold; margin-top: 2px;">¡Contáctate con nosotros!</p>
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

            <div class="register-link">
                ¿No tienes cuenta? <a href="<?= route('register') ?>">Regístrate aquí</a>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>

    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
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
        $errors = flashGet('errors') ?? [];
        $error = flashGet('error');
        $success = flashGet('success');
        $blocked = flashGet('blocked') ?? [];
        ?>
        
        <?php if (!empty($blocked)): ?>
            // Mensaje especial de bloqueo con countdown
            let timeRemaining = <?= $blocked['time_remaining'] ?? 0 ?>;
            
            customSwal.fire({
                icon: 'warning',
                title: '🔒 Cuenta Bloqueada',
                html: `
                    <div style="text-align: left; margin: 20px 0;">
                        <p><strong>⚠️ Tu cuenta está temporalmente bloqueada</strong></p>
                        <ul style="margin: 15px 0; padding-left: 20px;">
                            <li>Demasiados intentos fallidos (<?= $blocked['attempts_made'] ?? 3 ?>/3)</li>
                            <li>Tiempo restante: <span id="countdown">${timeRemaining}</span> minutos</li>
                            <li>Se desbloqueará automáticamente</li>
                        </ul>
                        <div style="background: #fee; padding: 15px; border-radius: 8px; margin-top: 15px;">
                            <strong>💡 Consejos de seguridad:</strong>
                            <ul style="margin: 10px 0; padding-left: 20px;">
                                <li>Verifica que escribes correctamente tu email y contraseña</li>
                                <li>Asegúrate de no tener CAPS LOCK activado</li>
                                <li>Si olvidaste tu contraseña, usa "¿Olvidaste tu contraseña?"</li>
                            </ul>
                        </div>
                    </div>
                `,
                confirmButtonText: 'Entendido',
                allowOutsideClick: false,
                background: '#1f2937',
                color: '#fff',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                },
                didOpen: () => {
                    // Countdown timer
                    const countdownElement = document.getElementById('countdown');
                    if (countdownElement && timeRemaining > 0) {
                        const interval = setInterval(() => {
                            timeRemaining--;
                            countdownElement.textContent = timeRemaining;
                            
                            if (timeRemaining <= 0) {
                                clearInterval(interval);
                                countdownElement.textContent = '0';
                                customSwal.update({
                                    html: `
                                        <div style="text-align: center; margin: 20px 0;">
                                            <p><strong>✅ Tu cuenta ha sido desbloqueada</strong></p>
                                            <p>Ya puedes intentar iniciar sesión nuevamente.</p>
                                        </div>
                                    `,
                                    icon: 'success',
                                    title: 'Cuenta Desbloqueada',
                                    confirmButtonText: 'Intentar de nuevo'
                                });
                            }
                        }, 60000); // Actualizar cada minuto
                    }
                }
            });
        <?php elseif (isset($errors['general'])): ?>
            <?php foreach ($errors['general'] as $errorMsg): ?>
                customSwal.fire({
                    icon: 'error',
                    title: '¡Error de acceso!',
                    text: '<?= addslashes($errorMsg) ?>',
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
            <?php endforeach; ?>
        <?php endif; ?>

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
                title: '¡Excelente!',
                text: '<?= addslashes($success) ?>',
                confirmButtonText: 'Continuar',
                background: '#1f2937',
                color: '#fff',
                timer: 5000,
                timerProgressBar: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                }
            });
        <?php endif; ?>

        // Mostrar errores de validación de campos específicos
        <?php if (!empty($errors)): ?>
            <?php 
            $fieldErrors = [];
            foreach ($errors as $field => $fieldErrorArray) {
                if ($field !== 'general' && is_array($fieldErrorArray)) {
                    $fieldErrors[$field] = $fieldErrorArray[0]; // Tomar el primer error de cada campo
                }
            }
            if (!empty($fieldErrors)): ?>
                let errorMessage = 'Por favor corrige los siguientes errores:\n\n';
                <?php foreach ($fieldErrors as $field => $fieldError): ?>
                    errorMessage += '• <?= ucfirst($field) ?>: <?= addslashes($fieldError) ?>\n';
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

        // Console debug
        console.log('🔐 Login page loaded with SweetAlert2');
        console.log('URL params:', window.location.search);

        // Verificar parámetros de logout
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('logout')) {
            console.log('✅ Logout exitoso detectado');
        }
        if (urlParams.get('error')) {
            console.log('❌ Error detectado:', urlParams.get('error'));
        }
        if (urlParams.get('timeout')) {
            console.log('⏰ Timeout detectado');
        }
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
            from { opacity: 0; transform: translate3d(0, -100%, 0); }
            to { opacity: 1; transform: none; }
        }
        
        @keyframes fadeOutUp {
            from { opacity: 1; }
            to { opacity: 0; transform: translate3d(0, -100%, 0); }
        }
        
        .animate__animated { animation-duration: 0.5s; }
        .animate__faster { animation-duration: 0.3s; }
        .animate__fadeInDown { animation-name: fadeInDown; }
        .animate__fadeOutUp { animation-name: fadeOutUp; }
    </style>
</body>

</html>