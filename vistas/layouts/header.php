
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Header Component - Tech Home</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../publico/css/header.css">
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
                    <img src="../../publico/imagenes/logos/LogoTech.png" alt="Tech Home Logo" class="header-logo-img">
                </div>
            </div>
            
            
            <!-- ============================================
                 SECCIÓN DERECHA: CONTROLES Y USUARIO
                 ============================================ -->
            <div class="user-controls">
                
                <!-- ============================================
                     BOTÓN DE NOTIFICACIONES
                     ============================================ -->
                <a href="index.php?controlador=Dashboard&accion=notificaciones" class="notifications-btn" title="Notificaciones">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge" id="notification-count" style="display: none;">0</span>
                </a>
                
                <!-- ============================================
                     TARJETA DE USUARIO Y CONTROLES
                     ============================================ -->
                <div class="user-info">
                    
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
                            <?php echo htmlspecialchars(($_SESSION['usuario_nombre'] ?? '') . ' ' . ($_SESSION['usuario_apellido'] ?? '')); ?>
                        </h4>
                        <span class="user-role" id="user-role">
                            <?php echo htmlspecialchars($_SESSION['usuario_rol'] ?? 'Usuario'); ?>
                        </span>
                        <span class="user-email" id="user-email">
                            <?php echo htmlspecialchars($_SESSION['usuario_email'] ?? ''); ?>
                        </span>
                    </div>
                    
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
                    <a href="#" class="logout-btn" id="logoutBtn" 
                       data-logout-url="logout.php" 
                       title="Cerrar Sesión">
                        <i class="fas fa-sign-out-alt"></i>
                        Cerrar Sesión
                    </a>
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
                    const response = await fetch('verify_session.php', {
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