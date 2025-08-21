<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tech Home Bolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/login.css') ?>">
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

                <?php if (isset($_SESSION['errors']['general'])): ?>
                    <?php foreach ($_SESSION['errors']['general'] as $error): ?>
                        <div class="invalid-feedback"><?= $error ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="invalid-feedback"><?= $_SESSION['error'] ?></div>
                <?php endif; ?>

                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
                <?php endif; ?>
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
                        <?php if (isset($_SESSION['errors']['email'])): ?>
                            <?php foreach ($_SESSION['errors']['email'] as $error): ?>
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
                        <?php if (isset($_SESSION['errors']['password'])): ?>
                            <?php foreach ($_SESSION['errors']['password'] as $error): ?>
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

        // Console debug
        console.log('🔐 Login page loaded');
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
</body>

</html>