<?php
$user = auth();
$roles = $user ? $user->roles : [];
$isAuth = $user ? true : false;
$isAdmin = in_array('administrador', array_column($roles, 'nombre'));
$isDocente = in_array('docente', array_column($roles, 'nombre'));
$isEstudiante = in_array('estudiante', array_column($roles, 'nombre'));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - Tech Home Bolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Evitar cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Estilos base -->
    <meta name="author" content="Desarrollado por estudiantes de la UPDS">
    <link rel="icon" href="<?= asset('faviconTH.png') ?>" type="image/png">

    <!-- Etiquetas meta para descripción y compatibilidad con WhatsApp -->
    <meta name="description" content="Tech Home Bolivia: Una plataforma educativa que simula un entorno de estudio, venta de libros y herramientas, además de ofrecer cursos especializados.">
    <meta property="og:title" content="Tech Home Bolivia">
    <meta property="og:description" content="Explora nuestra plataforma educativa para estudiar, comprar libros y herramientas, y realizar cursos especializados.">
    <meta property="og:image" content="<?= asset('faviconTH.png') ?>">
    <meta property="og:url" content="<?= $_ENV['APP_URL'] ?>">
    <meta name="twitter:card" content="summary_large_image">
</head>

<body>
    <!-- Incluir Sidebar Component -->
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sidebar Rediseñado - Instituto Tech Home</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="<?= asset('css/sidebar.css') ?>">

    </head>

    <body>

        <!-- ============================================================================
         SIDEBAR REDISEÑADO - Instituto Tech Home
         ============================================================================ -->
        <div class="ithr-navigation-panel">
            <!-- Fondo animado del sidebar -->
            <div class="ithr-animated-background">
                <div class="ithr-floating-element ithr-floating-element-1"></div>
                <div class="ithr-floating-element ithr-floating-element-2"></div>
                <div class="ithr-floating-element ithr-floating-element-3"></div>
            </div>

            <!-- ============================================================================
             SECCIÓN SUPERIOR - Logo y Branding
             ============================================================================ -->
            <div class="ithr-panel-header">
                <div class="ithr-brand-container">
                    <div class="ithr-brand-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="ithr-brand-text">
                        <h6 class="ithr-brand-title">TECH HOME</h6>
                        <span class="ithr-brand-subtitle">Instituto de Robótica</span>
                    </div>
                </div>
            </div>

            <!-- ============================================================================
             NAVEGACIÓN PRINCIPAL
             ============================================================================ -->
            <nav class="ithr-main-navigation">
                <?php if ($isAuth): ?>
                    <div class="ithr-nav-group">
                        <h6 class="ithr-nav-group-title">Panel Principal</h6>
                        <ul class="ithr-nav-list">
                            <li class="ithr-nav-item ithr-active">
                                <a href="<?= route(Dashboard()) ?>" class="ithr-nav-link">
                                    <i class="fas fa-tachometer-alt ithr-nav-icon"></i>
                                    <span class="ithr-nav-text">Dashboard</span>
                                    <div class="ithr-nav-indicator"></div>
                                </a>
                            </li>
                            <li class="ithr-nav-item">
                                <a href="<?= route('reportes') ?>" class="ithr-nav-link">
                                    <i class="fas fa-chart-bar ithr-nav-icon"></i>
                                    <span class="ithr-nav-text">Reportes</span>
                                </a>
                            </li>
                            <li class="ithr-nav-item">
                                <a href="<?= route('configuracion') ?>" class="ithr-nav-link">
                                    <i class="fas fa-cog ithr-nav-icon"></i>
                                    <span class="ithr-nav-text">Configuración</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>
                <div class="ithr-nav-group">
                    <h6 class="ithr-nav-group-title">Gestión Académica</h6>
                    <ul class="ithr-nav-list">
                        <li class="ithr-nav-item">
                            <a href="<?= route('docente.dashboard') ?>" class="ithr-nav-link">
                                <i class="fas fa-chalkboard-teacher ithr-nav-icon"></i>
                                <span class="ithr-nav-text">Docentes</span>
                                <span class="ithr-nav-badge"><?php
                                    try {
                                        // Obtener contador dinámico de docentes activos
                                        $contadorDocentes = \App\Models\User::where('estado', '=', 1)
                                            ->whereRaw('id IN (SELECT model_id FROM model_has_roles WHERE model_type = "App\\\\Models\\\\User" AND role_id = (SELECT id FROM roles WHERE nombre = "docente"))')
                                            ->count();
                                        echo $contadorDocentes;
                                    } catch (Exception $e) {
                                        echo '0';
                                    }
                                ?></span>
                            </a>
                        </li>
                        <li class="ithr-nav-item">
                            <a href="<?= route('estudiantes') ?>" class="ithr-nav-link">
                                <i class="fas fa-user-graduate ithr-nav-icon"></i>
                                <span class="ithr-nav-text">Estudiantes</span>
                                <span class="ithr-nav-badge"><?php
                                    try {
                                        // Obtener contador dinámico de estudiantes activos
                                        $contadorEstudiantes = \App\Models\User::where('estado', '=', 1)
                                            ->whereRaw('id IN (SELECT model_id FROM model_has_roles WHERE model_type = "App\\\\Models\\\\User" AND role_id = (SELECT id FROM roles WHERE nombre = "estudiante"))')
                                            ->count();
                                        echo $contadorEstudiantes;
                                    } catch (Exception $e) {
                                        echo '125';
                                    }
                                ?></span>
                            </a>
                        </li>
                        <li class="ithr-nav-item">
                            <a href="<?= route('cursos') ?>" class="ithr-nav-link">
                                <i class="fas fa-graduation-cap ithr-nav-icon"></i>
                                <span class="ithr-nav-text">Cursos</span>
                                <span class="ithr-nav-badge">35</span>
                            </a>
                        </li>
                        <li class="ithr-nav-item">
                            <a href="<?= route('usuarios') ?>" class="ithr-nav-link">
                                <i class="fas fa-users-cog ithr-nav-icon"></i>
                                <span class="ithr-nav-text">Usuarios</span>
                                <span class="ithr-nav-badge"><?php
                                    try {
                                        // Obtener contador dinámico de usuarios activos
                                        $contadorUsuarios = \App\Models\User::where('estado', '=', 1)->count();
                                        echo $contadorUsuarios;
                                    } catch (Exception $e) {
                                        echo '0';
                                    }
                                ?></span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="ithr-nav-group">
                    <h6 class="ithr-nav-group-title">Recursos</h6>
                    <ul class="ithr-nav-list">
                        <li class="ithr-nav-item">
                            <a href="<?= route('libros') ?>" class="ithr-nav-link">
                                <i class="fas fa-book ithr-nav-icon"></i>
                                <span class="ithr-nav-text">Biblioteca</span>
                                <span class="ithr-nav-badge"><?php
                                    try {
                                        // Obtener contador dinámico de libros disponibles
                                        $contadorLibros = \App\Models\Libro::disponibles()->count();
                                        echo $contadorLibros;
                                    } catch (Exception $e) {
                                        echo '0';
                                    }
                                ?></span>
                            </a>
                        </li>
                        <li class="ithr-nav-item">
                            <a href="<?= route('materiales') ?>" class="ithr-nav-link">
                                <i class="fas fa-file-alt ithr-nav-icon"></i>
                                <span class="ithr-nav-text">Materiales</span>
                                <span class="ithr-nav-badge"><?php
                                    try {
                                        // Obtener contador dinámico de materiales disponibles
                                        $contadorMateriales = \App\Models\Material::disponibles()->count();
                                        echo $contadorMateriales;
                                    } catch (Exception $e) {
                                        echo '0';
                                    }
                                ?></span>
                            </a>
                        </li>
                        <li class="ithr-nav-item">
                            <a href="<?= route('laboratorios') ?>" class="ithr-nav-link">
                                <i class="fas fa-flask ithr-nav-icon"></i>
                                <span class="ithr-nav-text">Laboratorios</span>
                                <span class="ithr-nav-badge"><?php
                                    try {
                                        // Obtener contador dinámico de laboratorios disponibles
                                        $contadorLaboratorios = \App\Models\Laboratorio::disponibles()->count();
                                        echo $contadorLaboratorios;
                                    } catch (Exception $e) {
                                        echo '0';
                                    }
                                ?></span>
                            </a>
                        </li>
                        <li class="ithr-nav-item">
                            <a href="<?= route('componentes') ?>" class="ithr-nav-link">
                                <i class="fas fa-microchip ithr-nav-icon"></i>
                                <span class="ithr-nav-text">Componentes</span>
                                <span class="ithr-nav-badge"><?php
                                    try {
                                        // Obtener contador dinámico de componentes disponibles
                                        $contadorComponentes = \App\Models\Componente::disponibles()->count();
                                        echo $contadorComponentes;
                                    } catch (Exception $e) {
                                        echo '0';
                                    }
                                ?></span>
                            </a>
                        </li>
                    </ul>
                </div>

            </nav>

            <!-- ============================================================================
             FOOTER REDISEÑADO CON NUEVAS FUNCIONES
             ============================================================================ -->
            <div class="ithr-panel-footer">
                <!-- Tarjeta de visita al sitio web -->
                <div class="ithr-website-promotion">
                    <a href="https://techhomebolivia.com/index.php" target="_blank" class="ithr-website-link">
                        <div class="ithr-website-card">
                            <div class="ithr-website-content">
                                <i class="fas fa-external-link-alt"></i>
                                <span>Visitar Sitio Web</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Control de tema mejorado -->
                <div class="ithr-theme-control">
                    <div class="ithr-theme-info">
                        <div class="ithr-theme-icon-container">
                            <i class="fas fa-palette"></i>
                        </div>
                        <div class="ithr-theme-details">
                            <span class="ithr-theme-label">Modo Oscuro</span>
                            <span class="ithr-theme-description">Cambia el tema</span>
                        </div>
                    </div>

                    <div class="ithr-theme-switch">
                        <input type="checkbox" id="ithrThemeToggle" class="ithr-theme-checkbox">
                        <label for="ithrThemeToggle" class="ithr-theme-slider">
                            <div class="ithr-theme-knob">
                                <i class="fas fa-sun ithr-switch-icon ithr-sun-icon"></i>
                                <i class="fas fa-moon ithr-switch-icon ithr-moon-icon"></i>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>


        <script src="<?= asset('js/bootstrap.bundle.min.js') ?>"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // ============================================================================
                // TOGGLE MODO OSCURO MEJORADO - AFECTA TODA LA PANTALLA
                // ============================================================================
                const themeToggle = document.getElementById('ithrThemeToggle');
                const themeLabel = document.querySelector('.ithr-theme-label');
                const themeDescription = document.querySelector('.ithr-theme-description');

                // Cargar tema guardado
                const savedTheme = localStorage.getItem('ithrGlobalTheme') || 'light';
                if (savedTheme === 'dark') {
                    themeToggle.checked = true;
                    themeLabel.textContent = 'Claro';
                    themeDescription.textContent = '';
                    document.body.classList.add('ithr-dark-mode');
                } else {
                    themeLabel.textContent = 'Oscuro';
                    themeDescription.textContent = '';
                }

                // Manejar cambio de tema
                themeToggle.addEventListener('change', function() {
                    if (this.checked) {
                        document.body.classList.add('ithr-dark-mode');
                        themeLabel.textContent = 'Oscuro';
                        themeDescription.textContent = '';
                        localStorage.setItem('ithrGlobalTheme', 'dark');
                    } else {
                        document.body.classList.remove('ithr-dark-mode');
                        themeLabel.textContent = 'Claro';
                        themeDescription.textContent = '';
                        localStorage.setItem('ithrGlobalTheme', 'light');
                    }
                });

                // ============================================================================
                // EFECTOS INTERACTIVOS DEL SIDEBAR
                // ============================================================================

                // Crear partículas flotantes
                function createNavigationParticle() {
                    const particle = document.createElement('div');
                    particle.style.position = 'absolute';
                    particle.style.width = Math.random() * 2 + 1 + 'px';
                    particle.style.height = particle.style.width;
                    particle.style.background = 'rgba(220, 38, 38, 0.3)';
                    particle.style.borderRadius = '50%';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = '100%';
                    particle.style.pointerEvents = 'none';
                    particle.style.animation = `ithr-particle-float ${Math.random() * 6 + 4}s linear forwards`;

                    const backgroundElement = document.querySelector('.ithr-animated-background');
                    if (backgroundElement) {
                        backgroundElement.appendChild(particle);
                        setTimeout(() => {
                            if (particle.parentNode) {
                                particle.remove();
                            }
                        }, 10000);
                    }
                }

                // Crear partícula cada 5 segundos
                setInterval(createNavigationParticle, 5000);

                // ============================================================================
                // EFECTOS HOVER Y NAVEGACIÓN
                // ============================================================================

                // Efectos hover para enlaces de navegación
                const navLinks = document.querySelectorAll('.ithr-nav-link');
                navLinks.forEach(link => {
                    link.addEventListener('mouseenter', function() {
                        const icon = this.querySelector('.ithr-nav-icon');
                        if (icon) {
                            icon.style.transform = 'scale(1.1) rotate(5deg)';
                            icon.style.transition = 'transform 0.3s ease';
                        }
                    });

                    link.addEventListener('mouseleave', function() {
                        const icon = this.querySelector('.ithr-nav-icon');
                        if (icon) {
                            icon.style.transform = 'scale(1) rotate(0deg)';
                        }
                    });
                });

                // Efecto para la tarjeta del sitio web
                const websiteCard = document.querySelector('.ithr-website-card');
                if (websiteCard) {
                    websiteCard.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-3px) scale(1.02)';
                    });

                    websiteCard.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0) scale(1)';
                    });
                }

                // Efecto para el control de tema
                const themeControl = document.querySelector('.ithr-theme-control');
                if (themeControl) {
                    themeControl.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 6px 20px rgba(220, 38, 38, 0.15)';
                    });

                    themeControl.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = 'none';
                    });
                }
            });
        </script>
    </body>

    </html>

    <!-- Incluir Header Component -->
    <div class="header-container">

        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Header Component - Tech Home</title>
            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
            <link rel="stylesheet" href="<?= asset('css/header.css') ?>">
        </head>

        <body style="margin: 2rem; background: linear-gradient(135deg, #dc2626 0%, #991b1b 50%, #1f2937 100%); min-height: 100vh;">

            <!-- ============================================
         COMPONENTE HEADER TECH HOME
         ============================================ -->
            <div class="tech-header" id="techHeader">
                <!-- Circuitos tecnológicos de fondo -->
                <div class="tech-circuit"></div>

                <!-- ============================================
             CONTENIDO PRINCIPAL DEL HEADER
             ============================================ -->
                <div class="header-content">

                    <!-- ============================================
                 SECCIÓN DEL LOGO (POSICIONADO AL INICIO)
                 ============================================ -->
                    <div class="welcome-section">
                        <div class="loga-container">
                            <img src="<?= asset('imagenes/logos/LogoTech.png') ?>" alt="Tech Home Logo" class="header-logo-img">
                        </div>
                    </div>


                    <!-- ============================================
                 SECCIÓN DERECHA: CONTROLES Y USUARIO
                 ============================================ -->
                    <div class="user-controls">

                        <!-- ============================================
                 BOTÓN DE NOTIFICACIONES
                 ============================================ -->
                        <?php if ($isAuth): ?>
                            <a href="#" class="notifications-btn" title="Notificaciones">
                                <i class="fas fa-bell"></i>
                                <span class="notification-badge" id="notification-count" style="display: none;">0</span>
                            </a>
                        <?php endif; ?>
                        <!-- ============================================
                     TARJETA DE USUARIO Y CONTROLES
                     ============================================ -->
                        <div class="user-info">
                            <?php if ($isAuth): ?>

                                <!-- ============================================
                         AVATAR DEL USUARIO
                         ============================================ -->
                                <div class="user-avatar" id="user-avatar">
                                    <?php
                                    // Mostrar avatar o iniciales del usuario
                                    if (!empty($_SESSION['usuario_avatar'])) {
                                        echo '<img src="' . htmlspecialchars($_SESSION['usuario_avatar']) . '" alt="Avatar">';
                                    } else {
                                        // Mostrar iniciales si no hay avatar
                                        $nombre = $_SESSION['usuario_nombre'] ?? 'U';
                                        $apellido = $_SESSION['usuario_apellido'] ?? 'S';
                                        $iniciales = strtoupper(substr($nombre, 0, 1) . substr($apellido, 0, 1));
                                        echo $iniciales;
                                    }
                                    ?>
                                </div>

                                <!-- ============================================
                         DATOS DEL USUARIO (DESDE SESIÓN PHP)
                         ============================================ -->
                                <div class="user-details">
                                    <h4 id="user-name">
                                        <?php 
                                        $user = auth();
                                        echo $user ? htmlspecialchars($user->nombre . ' ' . $user->apellido) : 'Usuario';
                                        ?>
                                    </h4>
                                    <span class="user-role" id="user-role">
                                        <?php 
                                        if ($user) {
                                            $roles = $user->roles();
                                            echo htmlspecialchars(!empty($roles) ? $roles[0]['nombre'] : 'Usuario');
                                        } else {
                                            echo 'Usuario';
                                        }
                                        ?>
                                    </span>
                                    <span class="user-email" id="user-email">
                                        <?php echo $user ? htmlspecialchars($user->email) : ''; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <!-- ============================================
                         INFORMACIÓN DE FECHA Y HORA
                         ============================================ -->
                            <div class="datetime-info">
                                <div class="datetime-item">
                                    <i class="fas fa-calendar"></i>
                                    <span id="current-date"></span>
                                </div>
                                <div class="datetime-item">
                                    <i class="fas fa-clock"></i>
                                    <span id="current-time"></span>
                                </div>
                            </div>

                            <!-- ============================================
                         BOTÓN CERRAR SESIÓN MEJORADO
                         ============================================ -->
                            <?php if ($isAuth): ?>

                                <form action="<?= route('logout') ?>" method="POST" class="logout-btn" title="Cerrar Sesión">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-sign-out-alt"></i>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            <?php else: ?>
                                <form action="<?= route('login') ?>" method="GET" class="login-btn" title="Iniciar Sesión">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt"></i>
                                        Iniciar Sesión
                                    </button>
                                </form>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>


            <!-- JavaScript del Componente -->
            <script>
                // ============================================
                // CLASE PRINCIPAL DEL HEADER COMPONENT
                // ============================================
                class TechHeaderComponent {
                    constructor() {
                        this.init();
                    }

                    // ============================================
                    // INICIALIZACIÓN DEL COMPONENTE
                    // ============================================
                    init() {
                        this.syncThemeWithSidebar();
                        this.updateDateTime();
                        this.startDateTimeUpdater();
                        this.updateNotificationCount();
                        this.initLogoutHandler();
                        this.startSessionVerification();
                        this.preventBackAfterLogout();
                        this.initPageAnimations();
                        this.listenForThemeChanges();

                        console.log('Tech Header Component initialized');
                        console.log('Usuario:', {
                            nombre: '<?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? ''); ?>',
                            rol: '<?php echo htmlspecialchars($_SESSION['usuario_rol'] ?? ''); ?>',
                            email: '<?php echo htmlspecialchars($_SESSION['usuario_email'] ?? ''); ?>'
                        });
                        console.log('Session ID:', '<?php echo session_id(); ?>');
                    }

                    // ============================================
                    // SINCRONIZACIÓN DE TEMA CON SIDEBAR
                    // ============================================
                    syncThemeWithSidebar() {
                        const savedTheme = localStorage.getItem('ithrGlobalTheme') || 'light';
                        if (savedTheme === 'dark') {
                            document.body.classList.add('ithr-dark-mode');
                        } else {
                            document.body.classList.remove('ithr-dark-mode');
                        }
                    }

                    listenForThemeChanges() {
                        // Escuchar cambios de tema desde el sidebar
                        document.addEventListener('themeChanged', () => {
                            this.syncThemeWithSidebar();
                        });

                        // Monitorear cambios en localStorage
                        window.addEventListener('storage', (e) => {
                            if (e.key === 'ithrGlobalTheme') {
                                this.syncThemeWithSidebar();
                            }
                        });
                    }

                    // ============================================
                    // FECHA Y HORA EN TIEMPO REAL
                    // ============================================
                    updateDateTime() {
                        const now = new Date();

                        const dateOptions = {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        };

                        const timeOptions = {
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: false
                        };

                        const formattedDate = now.toLocaleDateString('es-ES', dateOptions);
                        const formattedTime = now.toLocaleTimeString('es-ES', timeOptions);

                        const dateElement = document.getElementById('current-date');
                        const timeElement = document.getElementById('current-time');

                        if (dateElement) dateElement.textContent = formattedDate;
                        if (timeElement) timeElement.textContent = formattedTime;
                    }

                    startDateTimeUpdater() {
                        setInterval(() => this.updateDateTime(), 1000);
                    }

                    // ============================================
                    // CONTADOR DE NOTIFICACIONES
                    // ============================================
                    updateNotificationCount() {
                        // Obtener contador desde variable PHP o AJAX ligero
                        const notificationCount = <?php
                                                    // Aquí puedes agregar lógica para contar notificaciones desde la BD
                                                    // Por ejemplo: echo $notificacionesPendientes ?? 0;
                                                    echo 0; // Por defecto 0, cambiar según tu lógica
                                                    ?>;

                        this.setNotificationCount(notificationCount);
                    }

                    // ============================================
                    // MÉTODO PÚBLICO PARA ACTUALIZAR NOTIFICACIONES
                    // ============================================
                    setNotificationCount(count) {
                        const badge = document.getElementById('notification-count');

                        if (badge) {
                            if (count > 0) {
                                badge.textContent = count > 99 ? '99+' : count;
                                badge.style.display = 'flex';
                            } else {
                                badge.style.display = 'none';
                            }
                        }
                    }

                    // ============================================
                    // LÓGICA AVANZADA DE LOGOUT
                    // ============================================
                    initLogoutHandler() {
                        const logoutBtn = document.getElementById('logoutBtn');

                        if (logoutBtn) {
                            logoutBtn.addEventListener('click', (e) => {
                                e.preventDefault();
                                this.handleLogout();
                            });
                        }
                    }

                    handleLogout() {
                        const logoutBtn = document.getElementById('logoutBtn');
                        const logoutUrl = logoutBtn?.getAttribute('data-logout-url') || 'logout.php';

                        // Confirmación de logout con mensaje personalizado
                        const confirmMessage = '¿Estás seguro de que quieres cerrar sesión?\n\nSe perderán todos los datos no guardados.';

                        if (confirm(confirmMessage)) {
                            console.log('Iniciando proceso de logout...');
                            console.log('Redirect URL:', logoutUrl);

                            // Mostrar indicador de carga (opcional)
                            this.showLogoutLoader();

                            // Limpiar almacenamiento local
                            this.clearLocalStorage();

                            // Forzar limpieza de cache del navegador y redireccionar
                            setTimeout(() => {
                                window.location.href = logoutUrl + '?t=' + Date.now();
                            }, 500);
                        }
                    }

                    clearLocalStorage() {
                        try {
                            if (typeof(Storage) !== "undefined") {
                                localStorage.clear();
                                sessionStorage.clear();
                                console.log('Storage local limpiado');
                            }
                        } catch (error) {
                            console.warn('Error limpiando storage:', error);
                        }
                    }

                    showLogoutLoader() {
                        const logoutBtn = document.getElementById('logoutBtn');
                        if (logoutBtn) {
                            const originalContent = logoutBtn.innerHTML;
                            logoutBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cerrando...';
                            logoutBtn.style.pointerEvents = 'none';
                        }
                    }

                    // ============================================
                    // VERIFICACIÓN DE SESIÓN AUTOMÁTICA
                    // ============================================
                    startSessionVerification() {
                        // Verificar sesión cada 30 segundos
                        setInterval(() => {
                            this.verifySession();
                        }, 30000);
                    }

                    async verifySession() {
                        try {
                            const response = await fetch('<?= route('verify_session') ?>', {
                                method: 'GET',
                                cache: 'no-cache',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            });

                            if (response.ok) {
                                const data = await response.json();

                                if (!data.authenticated) {
                                    console.log('Sesión expirada, redirigiendo a login...');
                                    this.handleSessionExpired();
                                }
                            }
                        } catch (error) {
                            console.warn('Error verificando sesión:', error);
                            // No redirigir en caso de error de red
                        }
                    }

                    handleSessionExpired() {
                        alert('Tu sesión ha expirado. Serás redirigido al login.');
                        this.clearLocalStorage();
                        window.location.href = 'login.php?session_expired=1';
                    }

                    // ============================================
                    // PREVENIR NAVEGACIÓN HACIA ATRÁS DESPUÉS DE LOGOUT
                    // ============================================
                    preventBackAfterLogout() {
                        window.addEventListener('pageshow', function(event) {
                            if (event.persisted) {
                                console.log('Página cargada desde cache, recargando...');
                                window.location.reload();
                            }
                        });

                        // Prevenir cache de la página
                        window.addEventListener('beforeunload', function() {
                            // Forzar recarga en la próxima visita
                        });
                    }

                    // ============================================
                    // ANIMACIONES DE ENTRADA
                    // ============================================
                    initPageAnimations() {
                        const animatedElements = document.querySelectorAll('.user-info, .welcome-section, .notifications-btn');

                        animatedElements.forEach((element, index) => {
                            if (element) {
                                element.style.opacity = '0';
                                element.style.transform = 'translateY(-10px)';

                                setTimeout(() => {
                                    element.style.transition = 'all 0.3s ease';
                                    element.style.opacity = '1';
                                    element.style.transform = 'translateY(0)';
                                }, index * 100);
                            }
                        });
                    }

                    // ============================================
                    // MÉTODOS PÚBLICOS ADICIONALES
                    // ============================================
                    updateUserInfo(userData) {
                        const nameElement = document.getElementById('user-name');
                        const roleElement = document.getElementById('user-role');
                        const emailElement = document.getElementById('user-email');

                        if (nameElement && userData.nombre) {
                            nameElement.textContent = userData.nombre + ' ' + (userData.apellido || '');
                        }
                        if (roleElement && userData.rol) {
                            roleElement.textContent = userData.rol;
                        }
                        if (emailElement && userData.email) {
                            emailElement.textContent = userData.email;
                        }
                    }

                    setLogoutUrl(url) {
                        const logoutBtn = document.getElementById('logoutBtn');
                        if (logoutBtn) {
                            logoutBtn.setAttribute('data-logout-url', url);
                        }
                    }
                }

                // ============================================
                // INICIALIZACIÓN AUTOMÁTICA
                // ============================================
                document.addEventListener('DOMContentLoaded', function() {
                    // Crear instancia del componente
                    window.techHeader = new TechHeaderComponent();
                });

                // ============================================
                // API PÚBLICA SIMPLIFICADA
                // ============================================
                window.TechHeader = {
                    updateNotifications: (count) => window.techHeader?.setNotificationCount(count),
                    updateUserInfo: (userData) => window.techHeader?.updateUserInfo(userData),
                    setLogoutUrl: (url) => window.techHeader?.setLogoutUrl(url),
                    logout: () => window.techHeader?.handleLogout()
                };
            </script>
        </body>

        </html>
    </div>

    <div style="height: 180px;"></div>

    <!-- Área de Contenido Principal -->
    <div class="main-content-area">
        <?= $layoutContent ?? '' ?>

    </div>

    <!-- Incluir Footer Component -->
    <div class="footer-container-wrapper">
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Footer - Instituto Tech Home</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <link rel="stylesheet" href="<?= asset('css/footer.css') ?>">

        </head>

        <body style="min-height: 100vh; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); display: flex; flex-direction: column;">

            <!-- ============================================================================
         FOOTER TECH HOME - Instituto de Robótica
         ============================================================================ -->
            <footer class="tech-home-footer">
                <div class="footer-container">
                    <!-- ============================================================================
                 CONTENIDO PRINCIPAL DEL FOOTER
                 ============================================================================ -->
                    <div class="footer-main-content <?= $isAuth ? 'authenticated' : 'non-authenticated' ?>">
                        <!-- Columna 1: Información de Contacto -->
                        <div class="footer-column">
                            <h6 class="footer-column-title">Contacto</h6>
                            <div class="footer-contact-info">
                                <!-- Dirección física -->
                                <div class="contact-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>Av. Tecnológica #456<br>Santa Cruz, Bolivia</span>
                                </div>
                                <!-- Número de teléfono -->
                                <div class="contact-item">
                                    <i class="fas fa-phone"></i>
                                    <span>+591 3 789-0123</span>
                                </div>
                                <!-- Correo electrónico -->
                                <div class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>info@techhome.edu.bo</span>
                                </div>
                                <!-- Horarios de atención -->
                                <div class="contact-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Lun - Vie: 7:00 - 19:00<br>Sáb: 8:00 - 12:00</span>
                                </div>
                            </div>
                        </div>

                        <!-- Línea divisoria vertical -->
                        <div class="footer-divider"></div>

                        <?php if ($isAuth): ?>
                            <!-- Columna 2: Enlaces de Navegación del Sistema -->
                            <div class="footer-column">
                                <h6 class="footer-column-title">Sistema</h6>
                                <div class="footer-nav-links">
                                    <!-- Enlace al Dashboard principal -->
                                    <a href="<?= route(Dashboard()) ?>" class="footer-nav-link">
                                        <i class="fas fa-tachometer-alt"></i>
                                        Dashboard
                                    </a>
                                    <!-- Gestión de Estudiantes -->
                                    <a href="<?= route('estudiantes') ?>" class="footer-nav-link">
                                        <i class="fas fa-user-graduate"></i>
                                        Estudiantes
                                    </a>
                                    <!-- Catálogo de Cursos -->
                                    <a href="<?= route('cursos') ?>" class="footer-nav-link">
                                        <i class="fas fa-graduation-cap"></i>
                                        Cursos
                                    </a>
                                    <!-- Biblioteca Digital -->
                                    <a href="<?= route('libros') ?>" class="footer-nav-link">
                                        <i class="fas fa-book"></i>
                                        Biblioteca
                                    </a>
                                    <!-- Materiales Educativos -->
                                    <a href="<?= route('materiales') ?>" class="footer-nav-link">
                                        <i class="fas fa-file-alt"></i>
                                        Materiales
                                    </a>
                                    <!-- Administración de Usuarios -->
                                    <a href="<?= route('usuarios') ?>" class="footer-nav-link">
                                        <i class="fas fa-users-cog"></i>
                                        Usuarios
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>


                        <!-- Línea divisoria vertical -->
                        <div class="footer-divider"></div>

                        <!-- Columna 4: Información Acerca del Instituto -->
                        <div class="footer-column">
                            <h6 class="footer-column-title">TECH HOME</h6>
                            <div class="footer-about">
                                <!-- Descripción del instituto -->
                                <p class="footer-about-text">
                                    Instituto de excelencia en robótica y tecnología.
                                    Formamos profesionales capacitados para liderar
                                    la revolución tecnológica del futuro.
                                </p>
                                <!-- Características destacadas -->
                                <div class="footer-features">
                                    <div class="feature-item">
                                        <i class="fas fa-shield-alt"></i>
                                        <span>Certificado</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-rocket"></i>
                                        <span>Innovador</span>
                                    </div>
                                    <div class="feature-item">
                                        <i class="fas fa-mobile-alt"></i>
                                        <span>Accesible</span>
                                    </div>
                                </div>
                                <!-- Información de versión -->
                                <div class="footer-version">
                                    <span class="version-badge">Versión 2.0</span>
                                    <span class="build-info">Build #2025.1</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Separador principal horizontal -->
                    <div class="footer-main-divider"></div>

                    <!-- ============================================================================
                 FOOTER INFERIOR
                 ============================================================================ -->
                    <div class="footer-bottom">
                        <!-- Sección izquierda: Marca y logo -->
                        <div class="footer-bottom-left">
                            <div class="footer-brand-section">
                                <!-- Icono de la marca -->
                                <div class="footer-logo-icon">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <!-- Texto de la marca -->
                                <div class="footer-brand-text">
                                    <h5 class="footer-brand-name">TECH HOME</span></h5>
                                    <p class="footer-brand-tagline">Instituto de Robótica y Tecnología</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sección central: Redes sociales -->
                        <div class="footer-bottom-center">
                            <div class="footer-social-links">
                                <!-- Enlaces a redes sociales -->
                                <a href="#" class="social-link facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-link instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="social-link twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link whatsapp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <a href="#" class="social-link linkedin">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-link youtube">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Sección derecha: Información legal -->
                        <div class="footer-bottom-right">
                            <div class="footer-legal">
                                <!-- Copyright con año dinámico -->
                                <p class="copyright-text">
                                    &copy; <?= date('Y') ?> Instituto Tech Home. Todos los derechos reservados.
                                </p>
                                <!-- Enlaces legales -->
                                <div class="legal-links">
                                    <a href="#" class="legal-link">Política de Privacidad</a>
                                    <span class="legal-separator">|</span>
                                    <a href="#" class="legal-link">Términos de Servicio</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>

            <!-- Scripts de Bootstrap -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

            <!-- Efectos interactivos del footer -->
            <script>
                document.addEventListener('DOMContentLoaded', function() {

                    // ============================================
                    // SINCRONIZACIÓN DE TEMA CON SIDEBAR
                    // ============================================
                    function syncFooterTheme() {
                        const savedTheme = localStorage.getItem('ithrGlobalTheme') || 'light';
                        if (savedTheme === 'dark') {
                            document.body.classList.add('ithr-dark-mode');
                        } else {
                            document.body.classList.remove('ithr-dark-mode');
                        }
                    }

                    // Sincronizar tema al cargar
                    syncFooterTheme();

                    // Escuchar cambios de tema desde el sidebar
                    document.addEventListener('themeChanged', function() {
                        syncFooterTheme();
                    });

                    // Monitorear cambios en localStorage
                    window.addEventListener('storage', function(e) {
                        if (e.key === 'ithrGlobalTheme') {
                            syncFooterTheme();
                        }
                    });

                    // ============================================
                    // EFECTOS INTERACTIVOS ORIGINALES
                    // ============================================

                    // Función para crear partículas flotantes
                    function createFooterParticle() {
                        const particle = document.createElement('div');
                        particle.style.position = 'absolute';
                        particle.style.width = Math.random() * 3 + 1 + 'px';
                        particle.style.height = particle.style.width;
                        particle.style.background = 'rgba(220, 38, 38, 0.4)';
                        particle.style.borderRadius = '50%';
                        particle.style.left = Math.random() * 100 + '%';
                        particle.style.top = '100%';
                        particle.style.pointerEvents = 'none';
                        particle.style.animation = `footerParticleFloat ${Math.random() * 4 + 3}s linear forwards`;

                        const footerBg = document.querySelector('.footer-bg-animation');
                        if (footerBg) {
                            footerBg.appendChild(particle);
                            setTimeout(() => {
                                if (particle.parentNode) {
                                    particle.remove();
                                }
                            }, 7000);
                        }
                    }

                    // Crear partículas cada 4 segundos
                    setInterval(createFooterParticle, 4000);

                    // Crear estilos de animación para partículas
                    const footerStyle = document.createElement('style');
                    footerStyle.textContent = `
                @keyframes footerParticleFloat {
                    0% {
                        transform: translateY(0) rotate(0deg);
                        opacity: 0;
                    }
                    10% {
                        opacity: 1;
                    }
                    90% {
                        opacity: 1;
                    }
                    100% {
                        transform: translateY(-250px) rotate(360deg);
                        opacity: 0;
                    }
                }
            `;
                    document.head.appendChild(footerStyle);

                    // Efectos hover interactivos
                    const footerLinks = document.querySelectorAll('.footer-nav-link, .contact-item');
                    footerLinks.forEach(link => {
                        link.addEventListener('mouseenter', function() {
                            const icon = this.querySelector('i');
                            if (icon) {
                                icon.style.transform = 'scale(1.2) rotate(10deg)';
                                icon.style.transition = 'transform 0.3s ease';
                            }
                        });

                        link.addEventListener('mouseleave', function() {
                            const icon = this.querySelector('i');
                            if (icon) {
                                icon.style.transform = 'scale(1) rotate(0deg)';
                            }
                        });
                    });

                    console.log('Footer component initialized with theme sync');
                });
            </script>
        </body>

        </html>
    </div>

    <!-- Scripts -->
    <script src="<?= asset('js/admin.js') ?>"></script>
    <script>
        // Inicializar el dashboard cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Preparar datos del usuario para JavaScript
            const userData = {
                nombre: '<?= htmlspecialchars(auth() ? auth()->nombre : "") ?>',
                apellido: '<?= htmlspecialchars(auth() ? auth()->apellido : "") ?>',
                roles: JSON.stringify(<?php
                                        if (auth()) {
                                            $userRoles = auth()->roles();
                                            $roleNames = [];
                                            foreach ($userRoles as $role) {
                                                $roleNames[] = $role['nombre'];
                                            }
                                            echo json_encode($roleNames);
                                        } else {
                                            echo json_encode(['Sin rol']);
                                        }
                                        ?>),
                email: '<?= htmlspecialchars(auth() ? auth()->email : "") ?>',
                sessionId: '<?= session_id() ?>'
            };

            // Inicializar el dashboard con los datos del usuario
            if (typeof initAdminDashboard === 'function') {
                initAdminDashboard(userData);
            }

            // Inicializar efectos adicionales
            setTimeout(() => {
                if (typeof initAdvancedEffects === 'function') {
                    initAdvancedEffects();
                }
            }, 500);
        });
    </script>
</body>

</html>