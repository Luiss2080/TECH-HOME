<?php
/**
 * ============================================================================
 * TECH HOME BOLIVIA - DASHBOARD ADMINISTRADOR LIMPIO
 * Instituto: Tech Home Bolivia – Escuela de Robótica y Tecnología Avanzada
 * ============================================================================
 */

// Iniciar sesión de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Función de debug
function logDebug($mensaje) {
    error_log("[ADMIN DASHBOARD] " . $mensaje);
}

logDebug("Dashboard admin accedido");
logDebug("Session ID: " . session_id());
logDebug("Usuario en sesión: " . ($_SESSION['usuario_id'] ?? 'no definido'));

// Verificar autenticación
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    logDebug("Usuario no autenticado, redirigiendo a login");
    header("Location: ../../login.php");
    exit();
}

// Verificar rol de administrador
$rol = strtolower($_SESSION['usuario_rol'] ?? '');
logDebug("Rol del usuario: " . $rol);

if ($rol !== 'administrador') {
    logDebug("Usuario sin permisos de administrador, redirigiendo según rol");
    // Redirigir al dashboard apropiado según el rol
    switch ($rol) {
        case 'docente':
            header("Location: docente.php");
            break;
        case 'estudiante':
        default:
            header("Location: estudiante.php");
            break;
    }
    exit();
}

// Obtener datos del usuario
$usuario = [
    'nombre' => $_SESSION['usuario_nombre'] ?? '',
    'apellido' => $_SESSION['usuario_apellido'] ?? '',
    'email' => $_SESSION['usuario_email'] ?? '',
    'rol' => $_SESSION['usuario_rol'] ?? ''
];

logDebug("Usuario admin cargado: " . $usuario['nombre']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - Tech Home Bolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Evitar cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Estilos -->
    <link rel="stylesheet" href="../../publico/css/admin/Admin.css">

</head>
<body>
    <!-- Incluir Sidebar Component con máxima prioridad -->
    <?php
    // Ruta al componente sidebar reutilizable
    $sidebar_path = '../../vistas/layouts/sidebar.php';
    
    // Verificar si el archivo existe antes de incluirlo
    if (file_exists($sidebar_path)) {
        include_once $sidebar_path;
    } else {
        // Sidebar alternativo si no se encuentra el archivo
        echo '<div class="sidebar-placeholder">
                <div style="text-align: center;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px;"></i><br>
                    <strong>Sidebar no encontrado</strong><br>
                    <small>Archivo: sidebar.php</small>
                </div>
              </div>';
    }
    ?>

    <!-- Incluir Header Component -->
    <div class="header-container">
        <?php
        // Ruta al componente header reutilizable
        $header_path = '../../vistas/layouts/header.php';
        
        // Verificar si el archivo existe antes de incluirlo
        if (file_exists($header_path)) {
            include_once $header_path;
        } else {
            // Header alternativo si no se encuentra el archivo
            echo '<div class="header-placeholder" style="background: rgba(220, 38, 38, 0.1); border-radius: 25px; padding: 20px; color: #dc2626; font-weight: bold; text-align: center;">Header no encontrado</div>';
        }
        ?>
    </div>

   <div style="height: 180px;"></div>

    <!-- Área de Contenido Principal -->
    <div class="main-content-area">
        <div class="dashboard-content">
            <h2>Dashboard Administrador</h2>
            <p>
                Bienvenido al panel de administración de Tech Home Bolivia. 
            </p>
            <p>
                <strong>Usuario:</strong> <?php echo htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']); ?><br>
                <strong>Rol:</strong> <?php echo htmlspecialchars($usuario['rol']); ?><br>
                <strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?>
            </p>
        </div>
    </div>

    <!-- Incluir Footer Component -->
    <div class="footer-container">
        <?php
        // Ruta al componente footer reutilizable
        $footer_path = '../../vistas/layouts/footer.php';
        
        // Verificar si el archivo existe antes de incluirlo
        if (file_exists($footer_path)) {
            include_once $footer_path;
        } else {
            // Footer alternativo si no se encuentra el archivo
            echo '<div class="footer-placeholder" style="background: rgba(220, 38, 38, 0.1); border-radius: 25px; padding: 20px; color: #dc2626; font-weight: bold; text-align: center; margin: 20px;">Footer no encontrado</div>';
        }
        ?>
    </div>

    <script>
        console.log('Dashboard Administrador limpio cargado correctamente');
        console.log('Usuario:', <?php echo json_encode($usuario); ?>);
        console.log('Session ID:', '<?php echo session_id(); ?>');
        
        // Configurar el header para admin
        document.addEventListener('DOMContentLoaded', function() {
            
            // ASEGURAR QUE EL SIDEBAR TENGA LA MÁXIMA PRIORIDAD
            setTimeout(() => {
                const sidebarElements = document.querySelectorAll('.ithr-navigation-panel, .tech-home-sidebar, [class*="sidebar"]');
                sidebarElements.forEach(sidebar => {
                    sidebar.style.zIndex = '2000';
                    sidebar.style.position = 'fixed';
                });
                
                // Verificar que no haya elementos con z-index mayor
                const allElements = document.querySelectorAll('*');
                allElements.forEach(element => {
                    const zIndex = window.getComputedStyle(element).zIndex;
                    if (zIndex && parseInt(zIndex) > 2000 && !element.classList.contains('ithr-navigation-panel') && !element.classList.contains('tech-home-sidebar')) {
                        console.warn('Elemento con z-index mayor al sidebar detectado:', element);
                    }
                });

                // APLICAR PARÁMETROS UNIFICADOS BASADOS EN EL HEADER
                const footerContainer = document.querySelector('.footer-container');
                const footer = document.querySelector('.tech-home-footer');
                const headerContainer = document.querySelector('.header-container');
                const mainContent = document.querySelector('.main-content-area');
                const dashboardContent = document.querySelector('.dashboard-content');
                
                // PARÁMETROS UNIFICADOS PARA TODOS LOS CONTENEDORES PRINCIPALES
                const unifiedStyles = {
                    marginLeft: '250px',
                    marginRight: '20px',
                    transition: 'all 0.3s ease',
                    position: 'relative',
                    zIndex: '1'
                };
                
                // Aplicar estilos unificados a todos los contenedores principales
                [footerContainer, headerContainer, mainContent].forEach(element => {
                    if (element) {
                        Object.assign(element.style, unifiedStyles);
                    }
                });
                
                // PARÁMETROS INTERNOS UNIFICADOS (del CSS tech-header)
                const internalStyles = {
                    width: '100%',
                    maxWidth: '1600px',
                    margin: '0 auto',
                    position: 'relative'
                };
                
                // HEADER con parámetros específicos pero manteniendo consistencia
                if (headerContainer) {
                    headerContainer.style.position = 'fixed';
                    headerContainer.style.top = '0';
                    headerContainer.style.left = '0';
                    headerContainer.style.right = '0';
                    headerContainer.style.zIndex = '999'; 
                }

                // TECH-HEADER dentro del header-container
                const techHeader = headerContainer?.querySelector('.tech-header');
                if (techHeader) {
                    Object.assign(techHeader.style, internalStyles);
                }

                // FOOTER con exactamente los mismos parámetros internos que el header
                if (footer) {
                    Object.assign(footer.style, internalStyles);
                    footer.style.zIndex = '998'; 
                }

                // DASHBOARD CONTENT con parámetros similares
                if (dashboardContent) {
                    Object.assign(dashboardContent.style, internalStyles);
                }

                // FORZAR RECALCULO DE ESTILOS PARA EL FOOTER (soluciona el zoom)
                if (footer && footerContainer) {
                    // Forzar reflow del footer
                    footer.style.display = 'none';
                    footer.offsetHeight; 
                    footer.style.display = '';
                    
                    // Aplicar estilos adicionales al footer para zoom
                    footer.style.boxSizing = 'border-box';
                    footer.style.minWidth = '0';
                    footer.style.transform = 'translateZ(0)'; 
                }

                console.log('Layout unificado aplicado - Header y Footer con parámetros idénticos');

                // SINCRONIZAR TEMA CORRECTAMENTE AL INICIALIZAR
                syncThemeCorrectly();
                
                console.log('Layout unificado: todos los elementos con parámetros del header');
            }, 50);
            
            // Esperar a que el header se inicialice
            setTimeout(() => {
                if (window.TechHeader) {
                    // Configurar URL de logout específica para admin
                    window.TechHeader.setLogoutUrl('../../logout.php');
                    
                    // Actualizar información del usuario si es necesario
                    window.TechHeader.updateUserInfo({
                        nombre: '<?php echo htmlspecialchars($usuario['nombre']); ?>',
                        apellido: '<?php echo htmlspecialchars($usuario['apellido']); ?>',
                        rol: '<?php echo htmlspecialchars($usuario['rol']); ?>',
                        email: '<?php echo htmlspecialchars($usuario['email']); ?>'
                    });
                }

                // Configurar el sidebar si tiene funciones de inicialización
                if (window.TechSidebar) {
                    window.TechSidebar.init();
                }
            }, 100);
        });

        // Función para verificar espacios y posicionamiento 
        function debugSpacing() {
            const sidebar = document.querySelector('.ithr-navigation-panel, .tech-home-sidebar, [class*="sidebar"]');
            const header = document.querySelector('.header-container');
            const content = document.querySelector('.main-content-area');
            const footerContainer = document.querySelector('.footer-container');
            const footer = document.querySelector('.tech-home-footer');
            
            console.log('=== DEBUG TECH HOME - SEPARACIÓN ÚNICA 250PX ===');
            
            if (sidebar) {
                console.log('Sidebar:', {
                    position: window.getComputedStyle(sidebar).position,
                    zIndex: window.getComputedStyle(sidebar).zIndex,
                    width: sidebar.offsetWidth,
                    left: sidebar.offsetLeft
                });
            }
            
            if (header) {
                console.log('Header:', {
                    marginLeft: window.getComputedStyle(header).marginLeft,
                    position: window.getComputedStyle(header).position,
                    top: window.getComputedStyle(header).top,
                    zIndex: window.getComputedStyle(header).zIndex,
                    isFixed: window.getComputedStyle(header).position === 'fixed'
                });
            }
            
            if (content) {
                console.log('Content:', {
                    marginLeft: window.getComputedStyle(content).marginLeft,
                    marginTop: window.getComputedStyle(content).marginTop,
                    position: window.getComputedStyle(content).position
                });
            }
            
            
            if (footer) {
                console.log('Footer Element:', {
                    marginLeft: window.getComputedStyle(footer).marginLeft,
                    marginRight: window.getComputedStyle(footer).marginRight,
                    marginTop: window.getComputedStyle(footer).marginTop,
                    position: window.getComputedStyle(footer).position,
                    zIndex: window.getComputedStyle(footer).zIndex,
                    maxWidth: window.getComputedStyle(footer).maxWidth,
                    width: window.getComputedStyle(footer).width
                });
            }

            // Verificar tema actual
            const currentTheme = localStorage.getItem('ithrGlobalTheme') || 'light';
            const hasIthrDark = document.body.classList.contains('ithr-dark-mode');
            const hasDarkTheme = document.body.classList.contains('dark-theme');
            
            console.log('Tema:', {
                localStorage: currentTheme,
                ithrDarkMode: hasIthrDark,
                darkTheme: hasDarkTheme,
                sincronizado: (currentTheme === 'dark' && hasIthrDark) || (currentTheme === 'light' && !hasIthrDark)
            });
            
            console.log('=== SEPARACIÓN ESPERADA: 250px ===');
        }

        // Función para sincronizar el tema CORRECTAMENTE (sin inversión)
        function syncThemeCorrectly() {
            const savedTheme = localStorage.getItem('ithrGlobalTheme') || 'light';
            
            // Limpiar clases anteriores
            document.body.classList.remove('ithr-dark-mode', 'dark-theme');
            
            // Aplicar tema correcto
            if (savedTheme === 'dark') {
                document.body.classList.add('ithr-dark-mode');
                document.body.classList.add('dark-theme');
            }
            
            console.log('Tema sincronizado:', savedTheme, 'Body classes:', Array.from(document.body.classList));
        }

        // Función para forzar actualización del tema en el sidebar
        function updateSidebarTheme() {
            setTimeout(() => {
                // Disparar evento personalizado para notificar cambio de tema
                const themeEvent = new CustomEvent('themeChanged', {
                    detail: { theme: localStorage.getItem('ithrGlobalTheme') || 'light' }
                });
                document.dispatchEvent(themeEvent);
            }, 100);
        }

        // Sincronizar tema al cargar
        document.addEventListener('DOMContentLoaded', function() {
            syncThemeCorrectly();
        });

        // Escuchar cambios de tema del sidebar 
        document.addEventListener('themeChanged', function(event) {
            console.log('Evento de cambio de tema recibido');
            syncThemeCorrectly();
        });

        // Monitorear cambios en localStorage 
        window.addEventListener('storage', function(e) {
            if (e.key === 'ithrGlobalTheme') {
                console.log('Cambio en localStorage detectado:', e.newValue);
                syncThemeCorrectly();
            }
        });

        // Verificar tema cada 500ms para asegurar sincronización
        let themeCheckInterval = setInterval(() => {
            const currentTheme = localStorage.getItem('ithrGlobalTheme') || 'light';
            const bodyHasDark = document.body.classList.contains('ithr-dark-mode');
            
            // Si hay desincronización, corregir
            if ((currentTheme === 'dark' && !bodyHasDark) || (currentTheme === 'light' && bodyHasDark)) {
                console.log('Desincronización detectada, corrigiendo...');
                syncThemeCorrectly();
            }
        }, 500);

        // ESCUCHAR CAMBIOS DE ZOOM PARA RECALCULAR FOOTER
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                // Recalcular posicionamiento después del zoom
                const footer = document.querySelector('.tech-home-footer');
                const footerContainer = document.querySelector('.footer-container');
                
                if (footer && footerContainer) {
                    // Reaplicar estilos del footer para zoom
                    footer.style.width = '100%';
                    footer.style.maxWidth = '1600px';
                    footer.style.margin = '0 auto';
                    footer.style.position = 'relative';
                    footer.style.boxSizing = 'border-box';
                    
                    // Forzar recalculo
                    footer.style.transform = 'translateZ(0)';
                    footer.offsetHeight; // Trigger reflow
                    
                    console.log('Footer reajustado después del zoom');
                }
            }, 100);
        });

        // Función global para debug (disponible en consola)
        window.debugTechHome = debugSpacing;

        // Animación de entrada para el contenido
        setTimeout(() => {
            const content = document.querySelector('.dashboard-content');
            if (content) {
                content.style.opacity = '0';
                content.style.transform = 'translateY(20px)';
                content.style.transition = 'all 0.6s ease';
                
                setTimeout(() => {
                    content.style.opacity = '1';
                    content.style.transform = 'translateY(0)';
                }, 100);
            }
        }, 200);
    </script>
</body>
</html>