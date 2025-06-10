<?php
/**
 * ============================================================================
 * TECH HOME BOLIVIA - LOGIN MEJORADO PARA MANEJO DE LOGOUT
 * Instituto: Tech Home Bolivia – Escuela de Robótica y Tecnología Avanzada
 * ============================================================================
 */

// Iniciar sesión de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Variables de estado
$error = "";
$success = "";

// ============================================================================
// VERIFICACIÓN DE LOGOUT Y OTROS MENSAJES
// ============================================================================

// Verificar mensaje de logout
if (isset($_GET['logout'])) {
    $success = "Sesión cerrada correctamente";
    error_log("[LOGIN] Mensaje de logout mostrado");
    
    // Verificar que la sesión realmente esté cerrada
    if (isset($_SESSION['usuario_id'])) {
        error_log("[LOGIN WARNING] Sesión aún activa después de logout");
        // Forzar limpieza adicional
        $_SESSION = array();
        session_destroy();
        session_start(); // Reiniciar sesión limpia
    }
}

// Verificar mensaje de error en logout
if (isset($_GET['error']) && $_GET['error'] === 'logout') {
    $error = "Error al cerrar sesión. La sesión ha sido limpiada.";
    error_log("[LOGIN] Error en logout detectado");
}

// Verificar mensaje de timeout
if (isset($_GET['timeout'])) {
    $error = "Su sesión ha expirado. Por favor, inicie sesión nuevamente.";
    error_log("[LOGIN] Timeout de sesión");
}

// ============================================================================
// VERIFICACIÓN DE SESIÓN ACTIVA
// ============================================================================

if (isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id'])) {
    // Solo redirigir si NO es un logout o error
    if (!isset($_GET['logout']) && !isset($_GET['error']) && !isset($_GET['timeout'])) {
        $rol = strtolower($_SESSION['usuario_rol'] ?? 'estudiante');
        error_log("[LOGIN] Usuario ya autenticado, redirigiendo con rol: $rol");
        
        switch ($rol) {
            case 'administrador':
                header("Location: vistas/dashboard/admin.php");
                exit();
            case 'docente':
                header("Location: vistas/dashboard/docente.php");
                exit();
            case 'estudiante':
            default:
                header("Location: vistas/dashboard/estudiante.php");
                exit();
        }
    }
}

// ============================================================================
// PROCESAMIENTO DEL FORMULARIO DE LOGIN
// ============================================================================

if ($_POST) {
    // Sanitización de datos
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    error_log("[LOGIN] Procesando login para: $email");
    
    // Validación básica
    if (empty($email) || empty($password)) {
        $error = "Email y contraseña son obligatorios";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del email no es válido";
    } else {
        try {
            // Conexión a base de datos
            $conexion = new PDO("mysql:host=localhost;dbname=tech_home", "root", "");
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Consulta de usuario
            $stmt = $conexion->prepare("
                SELECT u.id, u.nombre, u.apellido, u.email, u.password, u.telefono, u.avatar, u.estado, r.nombre as rol_nombre 
                FROM usuarios u 
                LEFT JOIN roles r ON u.rol_id = r.id 
                WHERE u.email = ? AND u.estado = 1
            ");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            // Verificación de credenciales
            if ($user && password_verify($password, $user['password'])) {
                error_log("[LOGIN] Login exitoso para usuario: " . $user['id']);
                
                // Regenerar ID de sesión por seguridad
                session_regenerate_id(true);
                
                // Establecer variables de sesión
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['usuario_nombre'] = $user['nombre'];
                $_SESSION['usuario_apellido'] = $user['apellido'];
                $_SESSION['usuario_email'] = $user['email'];
                $_SESSION['usuario_telefono'] = $user['telefono'] ?? '';
                $_SESSION['usuario_avatar'] = $user['avatar'] ?? '';
                $_SESSION['usuario_rol'] = $user['rol_nombre'] ?? 'Estudiante';
                $_SESSION['login_time'] = time();
                $_SESSION['usuario_nombre_completo'] = $user['nombre'] . ' ' . $user['apellido'];
                
                // Redirección según rol
                switch (strtolower($user['rol_nombre'])) {
                    case 'administrador':
                        header("Location: vistas/dashboard/admin.php");
                        break;
                    case 'docente':
                        header("Location: vistas/dashboard/docente.php");
                        break;
                    case 'estudiante':
                    default:
                        header("Location: vistas/dashboard/estudiante.php");
                        break;
                }
                exit();
            } else {
                $error = "Email o contraseña incorrectos";
                error_log("[LOGIN] Credenciales incorrectas para: $email");
            }
        } catch (Exception $e) {
            $error = "Error del sistema: " . $e->getMessage();
            error_log("[LOGIN ERROR] " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tech Home Bolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="publico/css/login.css">
    
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
                <?php for($i = 0; $i < 16; $i++): ?>
                <i class="fas fa-robot robot-icon"></i>
                <?php endfor; ?>
            </div>

            <div class="logo-section">
                <div class="logo-container">
                    <img src="../TECH-HOME/publico/imagenes/logos/LogoTech-Home.png" alt="Tech Home Logo" class="logo-img">
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

            <!-- Alertas mejoradas -->
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Correo Electrónico</label>
                    <div class="input-wrapper">
                        <input type="email" class="form-input" id="email" name="email" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                               placeholder="Ingresa tu correo académico..." required>
                        <i class="fas fa-envelope input-icon"></i>
                        <div class="tooltip">Usa tu email registrado en la plataforma</div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="input-wrapper">
                        <input type="password" class="form-input" id="password" name="password" 
                               placeholder="Ingresa tu contraseña..." required>
                        <i class="fas fa-lock input-icon"></i>
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                        <div class="tooltip">Mínimo 8 caracteres con mayúsculas y números</div>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" class="checkbox" id="remember">
                        <span>Recordarme</span>
                    </label>
                    <a href="#" class="forgot-password">¿Olvidaste tu contraseña?</a>
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
                    <img src="publico/imagenes/logos/tiktok.webp" alt="TikTok" class="social-logo">
                    TikTok
                </a>
                <a href="#" class="social-btn facebook-btn">
                    <img src="publico/imagenes/logos/facebook.webp" alt="Facebook" class="social-logo">
                    Facebook
                </a>
                <a href="#" class="social-btn instagram-btn">
                    <img src="publico/imagenes/logos/Instagram.webp" alt="Instagram" class="social-logo">
                    Instagram
                </a>
                <a href="#" class="social-btn whatsapp-btn">
                    <img src="publico/imagenes/logos/wpps.webp" alt="WhatsApp" class="social-logo">
                    WhatsApp
                </a>
            </div>

            <div class="register-link">
                ¿No tienes cuenta? <a href="#">Regístrate aquí</a>
            </div>
        </div>
    </div>

    <!-- Debug info solo si hay parámetros GET -->
    <?php if (isset($_GET['debug'])): ?>
    <div style="position: fixed; bottom: 10px; right: 10px; background: rgba(0,0,0,0.8); color: white; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px; max-width: 300px;">
        <strong>Debug Info:</strong><br>
        GET params: <?php echo htmlspecialchars(http_build_query($_GET)); ?><br>
        Session status: <?php echo session_status(); ?><br>
        Session ID: <?php echo session_id(); ?><br>
        User in session: <?php echo isset($_SESSION['usuario_id']) ? 'Yes (' . $_SESSION['usuario_id'] . ')' : 'No'; ?>
    </div>
    <?php endif; ?>

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
        console.log('Login page loaded');
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