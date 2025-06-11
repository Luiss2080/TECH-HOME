<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Rediseñado - Instituto Tech Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../publico/css/sidebar.css">

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
            <div class="ithr-nav-group">
                <h6 class="ithr-nav-group-title">Panel Principal</h6>
                <ul class="ithr-nav-list">
                    <li class="ithr-nav-item ithr-active">
                        <a href="../dashboard/index.php" class="ithr-nav-link">
                            <i class="fas fa-tachometer-alt ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Dashboard</span>
                            <div class="ithr-nav-indicator"></div>
                        </a>
                    </li>
                    <li class="ithr-nav-item">
                        <a href="../reportes/index.php" class="ithr-nav-link">
                            <i class="fas fa-chart-bar ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Reportes</span>
                        </a>
                    </li>
                    <li class="ithr-nav-item">
                        <a href="../configuracion/index.php" class="ithr-nav-link">
                            <i class="fas fa-cog ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Configuración</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="ithr-nav-group">
                <h6 class="ithr-nav-group-title">Gestión Académica</h6>
                <ul class="ithr-nav-list">
                    <li class="ithr-nav-item">
                        <a href="../estudiantes/index.php" class="ithr-nav-link">
                            <i class="fas fa-user-graduate ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Estudiantes</span>
                            <span class="ithr-nav-badge">125</span>
                        </a>
                    </li>
                    <li class="ithr-nav-item">
                        <a href="../cursos/index.php" class="ithr-nav-link">
                            <i class="fas fa-graduation-cap ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Cursos</span>
                            <span class="ithr-nav-badge">35</span>
                        </a>
                    </li>
                    <li class="ithr-nav-item">
                        <a href="../usuarios/index.php" class="ithr-nav-link">
                            <i class="fas fa-users-cog ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Usuarios</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="ithr-nav-group">
                <h6 class="ithr-nav-group-title">Recursos</h6>
                <ul class="ithr-nav-list">
                    <li class="ithr-nav-item">
                        <a href="../libros/index.php" class="ithr-nav-link">
                            <i class="fas fa-book ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Biblioteca</span>
                        </a>
                    </li>
                    <li class="ithr-nav-item">
                        <a href="../materiales/index.php" class="ithr-nav-link">
                            <i class="fas fa-file-alt ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Materiales</span>
                            <span class="ithr-nav-badge">450</span>
                        </a>
                    </li>
                    <li class="ithr-nav-item">
                        <a href="../laboratorios/index.php" class="ithr-nav-link">
                            <i class="fas fa-flask ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Laboratorios</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="ithr-nav-group">
                <h6 class="ithr-nav-group-title">Módulos</h6>
                <ul class="ithr-nav-list">
                    <li class="ithr-nav-item">
                        <a href="../cursos/lecciones.php" class="ithr-nav-link">
                            <i class="fas fa-chalkboard-teacher ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Aula Virtual</span>
                        </a>
                    </li>
                    <li class="ithr-nav-item">
                        <a href="../evaluaciones/index.php" class="ithr-nav-link">
                            <i class="fas fa-clipboard-check ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Evaluaciones</span>
                        </a>
                    </li>
                    <li class="ithr-nav-item">
                        <a href="../certificados/index.php" class="ithr-nav-link">
                            <i class="fas fa-certificate ithr-nav-icon"></i>
                            <span class="ithr-nav-text">Certificados</span>
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
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