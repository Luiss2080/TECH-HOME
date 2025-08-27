<?php
$title = $title ?? 'Error 404 - Página no encontrada';
?>

<!-- Estilos específicos para el módulo CRUD - Vista Error 404 -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">
<link rel="stylesheet" href="<?= asset('css/errores.css'); ?>">


<!-- Contenedor principal del error 404 -->
<div class="crud-edit-container error-404-container">
    <div class="crud-edit-wrapper">

        <!-- Header principal del error 404 -->
        <div class="crud-section-card error-404-hero">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon error-404-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="crud-section-title-group">
                        <nav aria-label="breadcrumb" class="crud-breadcrumb-nav">
                            <ol class="crud-breadcrumb">
                                <li class="crud-breadcrumb-item">
                                    <a href="<?= route('home') ?>">
                                        <i class="fas fa-home"></i>
                                        Inicio
                                    </a>
                                </li>
                                <li class="crud-breadcrumb-item active">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Error 404
                                </li>
                            </ol>
                        </nav>
                        <h1 class="crud-section-title error-404-title">
                            <span class="error-404-number">404</span>
                            <span class="error-404-text">Página no encontrada</span>
                        </h1>
                        <p class="crud-section-subtitle error-404-subtitle">
                            Nuestros robots no pudieron localizar la página que buscas en el sistema TECH HOME
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Robot animado para el error -->
            <div class="error-404-robot">
                <div class="robot-body">
                    <div class="robot-head">
                        <div class="robot-eyes">
                            <div class="eye left-eye"></div>
                            <div class="eye right-eye"></div>
                        </div>
                        <div class="robot-mouth sad"></div>
                    </div>
                    <div class="robot-arms">
                        <div class="arm left-arm"></div>
                        <div class="arm right-arm"></div>
                    </div>
                </div>
                <div class="robot-shadow"></div>
            </div>

            <!-- Elementos decorativos tecnológicos -->
            <div class="error-404-decorations">
                <div class="floating-error error-1">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="floating-error error-2">
                    <i class="fas fa-question-circle"></i>
                </div>
                <div class="floating-error error-3">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="glitch-lines">
                    <div class="glitch-line line-1"></div>
                    <div class="glitch-line line-2"></div>
                    <div class="glitch-line line-3"></div>
                </div>
            </div>
        </div>

        <!-- Sección: Navegación Rápida -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-compass"></i>
                    ¿A dónde deseas ir?
                </h2>
                <p class="crud-section-subtitle">Explora las áreas principales de nuestro sistema</p>
            </div>
            
            <div class="crud-form-body">
                <div class="error-404-navigation">
                    <div class="nav-card" onclick="navigateToHome()">
                        <div class="nav-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <div class="nav-content">
                            <h4>Página Principal</h4>
                            <p>Regresa al inicio de TECH HOME</p>
                        </div>
                        <div class="nav-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>

                    <div class="nav-card" onclick="navigateToLibrary()">
                        <div class="nav-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="nav-content">
                            <h4>Biblioteca Digital</h4>
                            <p>Recursos académicos especializados</p>
                        </div>
                        <div class="nav-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>

                    <div class="nav-card" onclick="navigateToCourses()">
                        <div class="nav-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="nav-content">
                            <h4>Cursos Disponibles</h4>
                            <p>Formación en robótica e IA</p>
                        </div>
                        <div class="nav-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>

                    <div class="nav-card" onclick="navigateToLabs()">
                        <div class="nav-icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <div class="nav-content">
                            <h4>Laboratorios</h4>
                            <p>Herramientas de desarrollo</p>
                        </div>
                        <div class="nav-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Información de Ayuda -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-life-ring"></i>
                    Centro de Ayuda
                </h2>
                <p class="crud-section-subtitle">Posibles causas y soluciones para encontrar lo que buscas</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-info-panel">
                    <div class="crud-info-tabs">
                        <button class="crud-info-tab active" data-tab="causas">
                            <i class="fas fa-search"></i>
                            Posibles Causas
                        </button>
                        <button class="crud-info-tab" data-tab="soluciones">
                            <i class="fas fa-wrench"></i>
                            Soluciones
                        </button>
                        <button class="crud-info-tab" data-tab="contacto">
                            <i class="fas fa-headset"></i>
                            Contacto
                        </button>
                    </div>
                    
                    <div class="crud-info-pane active" id="causas">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>URL incorrecta:</strong> Verifica que la dirección esté escrita correctamente<br>
                                    Revisa caracteres especiales y mayúsculas/minúsculas
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-trash"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Página eliminada:</strong> El contenido pudo haber sido movido o eliminado<br>
                                    Algunos enlaces antiguos pueden estar obsoletos
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-lock"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Permisos insuficientes:</strong> Posiblemente no tienes acceso a esta sección<br>
                                    Contacta al administrador si crees que deberías tener acceso
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="soluciones">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Regresar al inicio:</strong> Vuelve a la página principal y navega desde allí<br>
                                    El menú principal te guiará a todas las secciones disponibles
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Usar el buscador:</strong> Utiliza la función de búsqueda del sistema<br>
                                    Busca por palabras clave del contenido que necesitas
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-history"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Revisar historial:</strong> Usa el botón "Atrás" de tu navegador<br>
                                    O revisa las páginas visitadas recientemente
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="contacto">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Soporte técnico:</strong> support@techhome.bo<br>
                                    Reporta problemas técnicos o errores del sistema
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Mesa de ayuda:</strong> +591 3-123-4567<br>
                                    Atención de lunes a viernes de 8:00 AM a 6:00 PM
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Chat en vivo:</strong> Disponible en horario laboral<br>
                                    Soporte inmediato para consultas urgentes
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Búsqueda Rápida -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-search"></i>
                    Buscador Inteligente
                </h2>
                <p class="crud-section-subtitle">Encuentra rápidamente lo que necesitas en el sistema</p>
            </div>
            
            <div class="crud-form-body">
                <div class="error-404-search">
                    <div class="search-container">
                        <div class="search-input-group">
                            <input type="text" 
                                   class="crud-form-control search-input" 
                                   placeholder="¿Qué estás buscando en TECH HOME?"
                                   id="quickSearch"
                                   onkeypress="handleSearchKey(event)">
                            <button type="button" class="search-button" onclick="performQuickSearch()">
                                <i class="fas fa-search"></i>
                                Buscar
                            </button>
                        </div>
                        <div class="search-suggestions">
                            <span class="suggestion-label">Sugerencias populares:</span>
                            <button class="suggestion-tag" onclick="searchFor('cursos de robotica')">Cursos de robótica</button>
                            <button class="suggestion-tag" onclick="searchFor('componentes arduino')">Componentes Arduino</button>
                            <button class="suggestion-tag" onclick="searchFor('libros IA')">Libros de IA</button>
                            <button class="suggestion-tag" onclick="searchFor('proyectos')">Proyectos</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Estadísticas del Error -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-chart-bar"></i>
                    Diagnóstico del Sistema
                </h2>
                <p class="crud-section-subtitle">Estado actual de los servicios de TECH HOME</p>
            </div>
            
            <div class="crud-form-body">
                <div class="error-404-diagnostics">
                    <div class="diagnostic-item">
                        <div class="diagnostic-icon status-ok">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="diagnostic-content">
                            <h4>Servicios Principales</h4>
                            <p>Operativo</p>
                        </div>
                        <div class="diagnostic-status">
                            <span class="status-indicator green">99.8%</span>
                        </div>
                    </div>

                    <div class="diagnostic-item">
                        <div class="diagnostic-icon status-ok">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="diagnostic-content">
                            <h4>Base de Datos</h4>
                            <p>Conectada</p>
                        </div>
                        <div class="diagnostic-status">
                            <span class="status-indicator green">Activa</span>
                        </div>
                    </div>

                    <div class="diagnostic-item">
                        <div class="diagnostic-icon status-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="diagnostic-content">
                            <h4>Página Solicitada</h4>
                            <p>No encontrada</p>
                        </div>
                        <div class="diagnostic-status">
                            <span class="status-indicator red">404</span>
                        </div>
                    </div>

                    <div class="diagnostic-item">
                        <div class="diagnostic-icon status-ok">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="diagnostic-content">
                            <h4>Seguridad</h4>
                            <p>Protegido</p>
                        </div>
                        <div class="diagnostic-status">
                            <span class="status-indicator green">SSL</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción principal -->
        <div class="crud-section-card">
            <div class="crud-form-actions">
                <button type="button" class="crud-btn crud-btn-secondary" onclick="goBack()">
                    <i class="fas fa-arrow-left"></i>
                    Página Anterior
                </button>
                <a href="<?= route('home') ?>" class="crud-btn crud-btn-primary">
                    <i class="fas fa-home"></i>
                    Ir a Inicio
                </a>
            </div>
        </div>

        <!-- Espacio de separación -->
        <div style="height: 20px;"></div>

    </div>
</div>

<!-- JavaScript específico para la vista Error 404 -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para cambiar tabs de información
    document.querySelectorAll('.crud-info-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.crud-info-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.crud-info-pane').forEach(p => p.classList.remove('active'));
            
            this.classList.add('active');
            const targetPane = document.getElementById(this.dataset.tab);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    // Funciones de navegación
    window.navigateToHome = function() {
        window.location.href = '<?= route("home") ?>';
    };

    window.navigateToLibrary = function() {
        alert('Redirigiendo a la Biblioteca Digital...\n\nEsta funcionalidad se implementará próximamente.');
    };

    window.navigateToCourses = function() {
        alert('Redirigiendo a Cursos Disponibles...\n\nEsta funcionalidad se implementará próximamente.');
    };

    window.navigateToLabs = function() {
        alert('Redirigiendo a Laboratorios...\n\nEsta funcionalidad se implementará próximamente.');
    };

    // Función para regresar a la página anterior
    window.goBack = function() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '<?= route("home") ?>';
        }
    };

    // Funciones del buscador
    window.handleSearchKey = function(event) {
        if (event.key === 'Enter') {
            performQuickSearch();
        }
    };

    window.performQuickSearch = function() {
        const searchTerm = document.getElementById('quickSearch').value.trim();
        if (searchTerm) {
            alert(`Buscando: "${searchTerm}"\n\nLa funcionalidad de búsqueda se implementará próximamente.\n\nPor ahora, puedes navegar usando el menú principal o las secciones sugeridas.`);
        } else {
            alert('Por favor ingresa un término de búsqueda');
            document.getElementById('quickSearch').focus();
        }
    };

    window.searchFor = function(term) {
        document.getElementById('quickSearch').value = term;
        performQuickSearch();
    };

    // Animación del robot triste
    function animateRobotEyes() {
        const leftEye = document.querySelector('.left-eye');
        const rightEye = document.querySelector('.right-eye');
        
        setInterval(() => {
            leftEye.style.animation = 'blink 0.3s ease-in-out';
            rightEye.style.animation = 'blink 0.3s ease-in-out';
            
            setTimeout(() => {
                leftEye.style.animation = '';
                rightEye.style.animation = '';
            }, 300);
        }, 3000);
    }

    // Efecto de movimiento del robot
    function animateRobot() {
        const robot = document.querySelector('.error-404-robot');
        let direction = 1;
        
        setInterval(() => {
            robot.style.transform = `translateY(${Math.sin(Date.now() * 0.001) * 10}px)`;
        }, 50);
    }

    // Inicializar animaciones
    animateRobotEyes();
    animateRobot();

    // Efecto de glitch para los elementos de error
    function glitchEffect() {
        const errorElements = document.querySelectorAll('.floating-error');
        errorElements.forEach((element, index) => {
            setTimeout(() => {
                element.style.animation = 'glitch 0.5s ease-in-out';
                setTimeout(() => {
                    element.style.animation = 'float 4s ease-in-out infinite';
                }, 500);
            }, index * 1000);
        });
    }

    // Inicializar efecto glitch cada 5 segundos
    setInterval(glitchEffect, 5000);
    
    // Animación de entrada para las tarjetas
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observar elementos para animación
    document.querySelectorAll('.nav-card, .diagnostic-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(el);
    });

    // Efecto hover mejorado para tarjetas de navegación
    document.querySelectorAll('.nav-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
            this.querySelector('.nav-arrow').style.transform = 'translateX(5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.querySelector('.nav-arrow').style.transform = 'translateX(0)';
        });
    });

    console.log('🤖 Error 404 - Sistema TECH HOME activo');
});
</script>

