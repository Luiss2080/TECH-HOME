/**
 * TECH HOME BOLIVIA - Dashboard Administrador (JS Mejorado)
 * Funcionalidad avanzada con animaciones y efectos interactivos
 */

// Variables globales
let usuarioData = {};
let resizeTimeout;
let animationQueue = [];
let isAnimating = false;

/**
 * Función principal de inicialización
 */
function initAdminDashboard(userData) {
    usuarioData = userData;
    
    console.log('🚀 Dashboard Administrador cargado correctamente');
    console.log('👤 Usuario:', usuarioData);
    
    // Configurar layout
    setupLayout();
    
    // Configurar componentes
    setupComponents();
    
    // Configurar eventos
    setupEventListeners();
    
    // Inicializar efectos
    initEffects();
    
    // Animación de entrada
    animateContent();
    
    // Configurar observadores de intersección
    setupIntersectionObservers();
    setupNewSectionsIntersectionObserver();
    
    // Inicializar efectos optimizados
    initOptimizedEffects();
    
    // Inicializar tooltips
    initTooltips();
}

/**
 * Configurar el layout principal
 */
function setupLayout() {
    setTimeout(() => {
        // Configurar sidebar
        const sidebarElements = document.querySelectorAll('.ithr-navigation-panel, .tech-home-sidebar, [class*="sidebar"]');
        sidebarElements.forEach(sidebar => {
            sidebar.style.zIndex = '2000';
            sidebar.style.position = 'fixed';
        });

        // Parámetros unificados para contenedores
        const unifiedStyles = {
            marginLeft: '330px',
            marginRight: '20px',
            transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
            position: 'relative',
            zIndex: '1'
        };
        
        // Aplicar estilos a contenedores principales
        const containers = [
            document.querySelector('.footer-container'),
            document.querySelector('.header-container'),
            document.querySelector('.main-content-area')
        ];
        
        containers.forEach(element => {
            if (element) {
                Object.assign(element.style, unifiedStyles);
            }
        });
        
        // Configurar header como fijo
        const headerContainer = document.querySelector('.header-container');
        if (headerContainer) {
            headerContainer.style.position = 'fixed';
            headerContainer.style.top = '0';
            headerContainer.style.left = '0';
            headerContainer.style.right = '0';
            headerContainer.style.zIndex = '999';
        }

        // Configurar elementos internos
        const internalStyles = {
            width: '100%',
            maxWidth: '1600px',
            margin: '0 auto',
            position: 'relative'
        };
        
        const techHeader = headerContainer?.querySelector('.tech-header');
        const footer = document.querySelector('.tech-home-footer');
        
        if (techHeader) {
            Object.assign(techHeader.style, internalStyles);
        }
        
        if (footer) {
            Object.assign(footer.style, internalStyles);
            footer.style.zIndex = '998';
        }

        console.log('✅ Layout unificado aplicado correctamente');
        
        // Configurar responsive
        handleResponsiveLayout();
    }, 50);
}

/**
 * Manejar layout responsive
 */
function handleResponsiveLayout() {
    const checkResponsive = () => {
        const isMobile = window.innerWidth <= 1024;
        const elements = [
            document.querySelector('.header-container'),
            document.querySelector('.main-content-area'),
            document.querySelector('.footer-container')
        ];
        
        elements.forEach(element => {
            if (element) {
                if (isMobile) {
                    element.style.marginLeft = '20px';
                    element.style.marginRight = '20px';
                } else {
                    element.style.marginLeft = '330px';
                    element.style.marginRight = '20px';
                }
            }
        });
    };
    
    checkResponsive();
    window.addEventListener('resize', debounce(checkResponsive, 150));
}

/**
 * Configurar componentes del header y sidebar
 */
function setupComponents() {
    setTimeout(() => {
        if (window.TechHeader) {
            window.TechHeader.setLogoutUrl('../../logout.php');
            window.TechHeader.updateUserInfo({
                nombre: usuarioData.nombre || '',
                apellido: usuarioData.apellido || '',
                rol: usuarioData.rol || '',
                email: usuarioData.email || ''
            });
        }

        if (window.TechSidebar) {
            window.TechSidebar.init();
        }
    }, 100);
}

/**
 * Configurar event listeners avanzados
 */
function setupEventListeners() {
    // Manejar redimensionamiento con debounce
    window.addEventListener('resize', debounce(() => {
        const footer = document.querySelector('.tech-home-footer');
        
        if (footer) {
            footer.style.width = '100%';
            footer.style.maxWidth = '1600px';
            footer.style.margin = '0 auto';
            footer.style.position = 'relative';
            footer.style.boxSizing = 'border-box';
            footer.style.transform = 'translateZ(0)';
            footer.offsetHeight; // Trigger reflow
        }
        
        handleResponsiveLayout();
    }, 100));
    
    // Smooth scroll para enlaces internos
    const internalLinks = document.querySelectorAll('a[href^="#"]');
    internalLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Manejar visibilidad de página
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            pauseAnimations();
        } else {
            resumeAnimations();
        }
    });

    // Precargar imágenes importantes
    preloadCriticalAssets();
}

/**
 * Inicializar efectos interactivos mejorados
 */
function initEffects() {
    // Efectos para tarjetas de acción
    setupActionCardEffects();
    
    // Efectos para métricas
    setupMetricCardEffects();
    
    // Efectos para widgets
    setupWidgetEffects();
    
    // Efectos para actividades y sesiones
    setupActivityEffects();
    
    // Efectos para nuevas secciones
    setupSummaryEffects();
    setupSalesEffects();
    setupProductsEffects();
    
    // Efectos de paralaje sutil
    setupParallaxEffects();
}

/**
 * Efectos avanzados para tarjetas de acción
 */
function setupActionCardEffects() {
    const actionCards = document.querySelectorAll('.action-card');
    
    actionCards.forEach((card, index) => {
        // Efecto de entrada con magnetismo
        card.addEventListener('mouseenter', function(e) {
            if (isAnimating) return;
            
            this.style.transform = 'translateY(-12px) scale(1.03) rotateX(5deg)';
            this.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            
            const icon = this.querySelector('.action-icon');
            if (icon) {
                icon.style.transform = 'scale(1.15) rotate(8deg)';
                icon.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            }
            
            // Efecto de brillo
            createShineEffect(this);
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1) rotateX(0deg)';
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            
            const icon = this.querySelector('.action-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
                icon.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            }
        });
        
        // Efecto de clic con ondas
        card.addEventListener('mousedown', function(e) {
            createRippleEffect(e, this);
            this.style.transform = 'translateY(-8px) scale(0.98) rotateX(2deg)';
        });
        
        card.addEventListener('mouseup', function() {
            this.style.transform = 'translateY(-12px) scale(1.03) rotateX(5deg)';
        });
        
        // Efecto de movimiento con cursor
        card.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;
            
            this.style.transform = `translateY(-12px) scale(1.03) rotateX(${5 + rotateX}deg) rotateY(${rotateY}deg)`;
        });
    });
}

/**
 * Efectos avanzados para tarjetas de métricas
 */
function setupMetricCardEffects() {
    const metricCards = document.querySelectorAll('.metric-card');
    
    metricCards.forEach((card, index) => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
            this.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            
            const value = this.querySelector('.metric-value');
            const icon = this.querySelector('.metric-icon');
            const label = this.querySelector('.metric-label');
            
            if (value) {
                value.style.color = '#dc2626';
                value.style.transform = 'scale(1.1)';
                value.style.transition = 'all 0.3s ease';
                
                // Efecto de conteo animado
                animateCounter(value);
            }
            
            if (icon) {
                icon.style.transform = 'scale(1.15) rotate(8deg)';
                icon.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            }
            
            if (label) {
                label.style.color = '#1f2937';
                label.style.transform = 'translateY(-2px)';
                label.style.transition = 'all 0.3s ease';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            
            const value = this.querySelector('.metric-value');
            const icon = this.querySelector('.metric-icon');
            const label = this.querySelector('.metric-label');
            
            if (value) {
                value.style.color = '#1f2937';
                value.style.transform = 'scale(1)';
            }
            
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
            
            if (label) {
                label.style.color = '#6b7280';
                label.style.transform = 'translateY(0)';
            }
        });
    });
}

/**
 * Efectos para widgets
 */
function setupWidgetEffects() {
    const widgets = document.querySelectorAll('.widget');
    
    widgets.forEach(widget => {
        widget.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
            this.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        });
        
        widget.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

/**
 * Efectos para elementos de actividad y sesiones
 */
function setupActivityEffects() {
    // Efectos para elementos de actividad
    const activityItems = document.querySelectorAll('.activity-item');
    activityItems.forEach((item, index) => {
        item.addEventListener('mouseenter', function() {
            this.style.background = 'linear-gradient(135deg, rgba(220, 38, 38, 0.06), rgba(220, 38, 38, 0.02))';
            this.style.transform = 'translateX(10px) scale(1.02)';
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            this.style.borderRadius = '12px';
            this.style.boxShadow = '0 8px 25px rgba(220, 38, 38, 0.1)';
            
            const icon = this.querySelector('.activity-icon');
            if (icon) {
                icon.style.transform = 'scale(1.2) rotate(5deg)';
                icon.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.background = '';
            this.style.transform = 'translateX(0) scale(1)';
            this.style.boxShadow = '';
            
            const icon = this.querySelector('.activity-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });

    // Efectos para sesiones activas
    const sessionItems = document.querySelectorAll('.session-item');
    sessionItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.background = 'linear-gradient(135deg, rgba(16, 185, 129, 0.06), rgba(16, 185, 129, 0.02))';
            this.style.transform = 'translateY(-3px) scale(1.01)';
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            this.style.borderRadius = '12px';
            this.style.boxShadow = '0 8px 25px rgba(16, 185, 129, 0.1)';
            
            const indicator = this.querySelector('.status-indicator');
            if (indicator) {
                indicator.style.transform = 'scale(1.5)';
                indicator.style.boxShadow = '0 0 0 8px rgba(16, 185, 129, 0.2)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.background = '';
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '';
            
            const indicator = this.querySelector('.status-indicator');
            if (indicator) {
                indicator.style.transform = 'scale(1)';
                indicator.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.2)';
            }
        });
    });
}

/**
 * Configurar efectos de paralaje sutil
 */
function setupParallaxEffects() {
    const parallaxElements = document.querySelectorAll('.section-card');
    
    window.addEventListener('scroll', throttle(() => {
        const scrolled = window.pageYOffset;
        
        parallaxElements.forEach((element, index) => {
            const rate = scrolled * -0.02 * (index + 1);
            element.style.transform = `translateY(${rate}px)`;
        });
    }, 16));
}

/**
 * Configurar observadores de intersección para animaciones
 */
function setupIntersectionObservers() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                
                // Animar elementos hijos secuencialmente
                const children = entry.target.querySelectorAll('.action-card, .metric-card, .activity-item, .session-item');
                children.forEach((child, index) => {
                    setTimeout(() => {
                        child.style.opacity = '1';
                        child.style.transform = 'translateY(0) scale(1)';
                    }, index * 100);
                });
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.section-card').forEach(card => {
        observer.observe(card);
    });
}

/**
 * Crear efecto de brillo
 */
function createShineEffect(element) {
    const shine = document.createElement('div');
    shine.style.cssText = `
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        pointer-events: none;
        transition: left 0.6s ease;
        z-index: 1;
    `;
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(shine);
    
    requestAnimationFrame(() => {
        shine.style.left = '100%';
    });
    
    setTimeout(() => {
        shine.remove();
    }, 600);
}

/**
 * Crear efecto ripple mejorado
 */
function createRippleEffect(e, element) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        width: ${size}px;
        height: ${size}px;
        left: ${x}px;
        top: ${y}px;
        background: radial-gradient(circle, rgba(220, 38, 38, 0.3) 0%, transparent 70%);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        pointer-events: none;
        z-index: 1000;
    `;
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 800);
}

/**
 * Animar contador
 */
function animateCounter(element) {
    const target = parseInt(element.textContent.replace(/[^\d]/g, ''));
    const duration = 800;
    const start = Date.now();
    const startValue = Math.max(0, target - 10);
    
    function updateCounter() {
        const elapsed = Date.now() - start;
        const progress = Math.min(elapsed / duration, 1);
        const easeProgress = 1 - Math.pow(1 - progress, 3); // Easing out cubic
        const current = Math.floor(startValue + (target - startValue) * easeProgress);
        
        const originalText = element.textContent;
        if (originalText.includes('Bs.')) {
            element.textContent = `Bs. ${current.toLocaleString()}`;
        } else {
            element.textContent = current;
        }
        
        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        }
    }
    
    updateCounter();
}

/**
 * Inicializar tooltips
 */
function initTooltips() {
    const elements = document.querySelectorAll('[data-tooltip]');
    
    elements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

/**
 * Animación de entrada para el contenido
 */
function animateContent() {
    isAnimating = true;
    
    setTimeout(() => {
        const sectionCards = document.querySelectorAll('.section-card');
        
        sectionCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0) scale(1)';
                card.style.transition = 'all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            }, 200 * (index + 1));
        });
        
        setTimeout(() => {
            isAnimating = false;
        }, 2000);
    }, 300);
}

/**
 * Funciones utilitarias
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

function pauseAnimations() {
    document.body.style.animationPlayState = 'paused';
}

function resumeAnimations() {
    document.body.style.animationPlayState = 'running';
}

function preloadCriticalAssets() {
    // Precargar fuentes críticas
    const fontLink = document.createElement('link');
    fontLink.rel = 'preload';
    fontLink.href = 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap';
    fontLink.as = 'style';
    document.head.appendChild(fontLink);
}

function showTooltip(e) {
    // Implementación de tooltip personalizado
    console.log('Showing tooltip:', e.target.dataset.tooltip);
}

function hideTooltip() {
    // Ocultar tooltip
    console.log('Hiding tooltip');
}

/**
 * Efectos avanzados adicionales
 */
function initAdvancedEffects() {
    // Agregar CSS para animaciones adicionales
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(2);
                opacity: 0;
            }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .floating {
            animation: float 3s ease-in-out infinite;
        }
    `;
    document.head.appendChild(style);
    
    // Aplicar efectos flotantes a íconos
    const icons = document.querySelectorAll('.action-icon, .metric-icon');
    icons.forEach((icon, index) => {
        setTimeout(() => {
            icon.classList.add('floating');
            icon.style.animationDelay = `${index * 0.5}s`;
        }, 1000 + index * 200);
    });
}

/**
 * Actualizar métricas con animación mejorada
 */
function updateMetricsWithAnimation() {
    const metricValues = document.querySelectorAll('.metric-value');
    
    metricValues.forEach((value, index) => {
        setTimeout(() => {
            const currentValue = parseInt(value.textContent.replace(/[^\d]/g, ''));
            const variation = Math.floor(Math.random() * 10) - 5;
            const newValue = Math.max(0, currentValue + variation);
            
            value.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            value.style.transform = 'scale(1.2) rotateX(360deg)';
            value.style.color = '#dc2626';
            
            setTimeout(() => {
                value.textContent = value.textContent.replace(/\d+/, newValue);
                value.style.transform = 'scale(1) rotateX(0deg)';
                value.style.color = '';
            }, 300);
        }, index * 150);
    });
}

/**
 * Función de limpieza
 */
function cleanup() {
    if (resizeTimeout) {
        clearTimeout(resizeTimeout);
    }
    
    // Limpiar event listeners
    window.removeEventListener('resize', handleResponsiveLayout);
    window.removeEventListener('scroll', setupParallaxEffects);
    
    // Limpiar animaciones
    animationQueue.forEach(clearTimeout);
    animationQueue = [];
}

// Event listeners principales
window.addEventListener('beforeunload', cleanup);

// Actualización automática de métricas cada 5 minutos
setInterval(() => {
    if (!document.hidden) {
        updateMetricsWithAnimation();
    }
}, 300000);

// Inicializar contadores después de cargar
setTimeout(() => {
    const metricValues = document.querySelectorAll('.metric-value');
    metricValues.forEach(value => {
        animateCounter(value);
    });
}, 1000);

// Exponer funciones globales necesarias
window.initAdminDashboard = initAdminDashboard;
window.initAdvancedEffects = initAdvancedEffects;
window.initOptimizedEffects = initOptimizedEffects;
window.updateMetricsWithAnimation = updateMetricsWithAnimation;
window.animateCounter = animateCounter;
window.setupSummaryEffects = setupSummaryEffects;
window.setupSalesEffects = setupSalesEffects;
window.setupProductsEffects = setupProductsEffects;
window.actualizarProductosAutomatico = actualizarProductosAutomatico;
window.animateEnhancedSections = animateEnhancedSections;
window.setupEnhancedParallax = setupEnhancedParallax;

console.log('📝 Admin Dashboard JS cargado exitosamente con diseño mejorado');

/**
 * Efectos para elementos de resumen del sistema
 */
function setupSummaryEffects() {
    const summaryItems = document.querySelectorAll('.summary-item');
    
    summaryItems.forEach((item, index) => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(8px) scale(1.02)';
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            this.style.background = 'rgba(255, 255, 255, 0.9)';
            this.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.1)';
            
            const icon = this.querySelector('.summary-icon');
            const value = this.querySelector('.summary-value');
            const badge = this.querySelector('.summary-badge');
            
            if (icon) {
                icon.style.transform = 'scale(1.15) rotate(8deg)';
                icon.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            }
            
            if (value) {
                value.style.color = '#dc2626';
                value.style.transform = 'scale(1.05)';
                
                // Animar contador si es numérico
                if (!isNaN(parseInt(value.textContent))) {
                    animateCounter(value);
                }
            }
            
            if (badge) {
                badge.style.transform = 'scale(1.05) translateY(-2px)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0) scale(1)';
            this.style.background = 'rgba(255, 255, 255, 0.6)';
            this.style.boxShadow = '';
            
            const icon = this.querySelector('.summary-icon');
            const value = this.querySelector('.summary-value');
            const badge = this.querySelector('.summary-badge');
            
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
            
            if (value) {
                value.style.color = '#1f2937';
                value.style.transform = 'scale(1)';
            }
            
            if (badge) {
                badge.style.transform = 'scale(1) translateY(0)';
            }
        });
        
        // Animación de entrada escalonada
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, 100 * (index + 1));
    });
}

/**
 * Efectos para elementos de ventas recientes
 */
function setupSalesEffects() {
    const salesItems = document.querySelectorAll('.sale-item');
    
    salesItems.forEach((item, index) => {
        item.addEventListener('mouseenter', function() {
            this.style.background = 'linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(16, 185, 129, 0.03))';
            this.style.transform = 'translateX(12px) scale(1.01)';
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            this.style.paddingLeft = '1rem';
            this.style.paddingRight = '1rem';
            this.style.boxShadow = '0 8px 25px rgba(16, 185, 129, 0.15)';
            this.style.borderRadius = '12px';
            
            const avatar = this.querySelector('.sale-avatar');
            const customer = this.querySelector('.sale-customer');
            const amount = this.querySelector('.sale-amount');
            const status = this.querySelector('.sale-status');
            
            if (avatar) {
                avatar.style.transform = 'scale(1.15)';
                avatar.style.boxShadow = '0 8px 20px rgba(16, 185, 129, 0.3)';
            }
            
            if (customer) {
                customer.style.color = '#10b981';
                customer.style.transform = 'translateX(5px)';
            }
            
            if (amount) {
                amount.style.transform = 'scale(1.08)';
                amount.style.color = '#dc2626';
            }
            
            if (status) {
                status.style.transform = 'scale(1.05) translateY(-1px)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.background = '';
            this.style.transform = 'translateX(0) scale(1)';
            this.style.paddingLeft = '';
            this.style.paddingRight = '';
            this.style.boxShadow = '';
            this.style.borderRadius = '';
            
            const avatar = this.querySelector('.sale-avatar');
            const customer = this.querySelector('.sale-customer');
            const amount = this.querySelector('.sale-amount');
            const status = this.querySelector('.sale-status');
            
            if (avatar) {
                avatar.style.transform = 'scale(1)';
                avatar.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
            }
            
            if (customer) {
                customer.style.color = '#1f2937';
                customer.style.transform = 'translateX(0)';
            }
            
            if (amount) {
                amount.style.transform = 'scale(1)';
                amount.style.color = '#10b981';
            }
            
            if (status) {
                status.style.transform = 'scale(1) translateY(0)';
            }
        });
        
        // Animación de entrada escalonada
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateX(0)';
        }, 150 * (index + 1));
    });
}

/**
 * Efectos para tarjetas de productos (libros y componentes)
 */
function setupProductsEffects() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach((card, index) => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-12px) scale(1.03)';
            this.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
            this.style.borderColor = '#dc2626';
            
            const image = this.querySelector('.product-image');
            const title = this.querySelector('.product-title');
            const price = this.querySelector('.product-price');
            const status = this.querySelector('.product-status');
            
            if (image) {
                image.style.transform = 'scale(1.15) rotate(8deg)';
                image.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
                image.style.boxShadow = '0 15px 30px rgba(0, 0, 0, 0.25)';
            }
            
            if (title) {
                title.style.color = '#dc2626';
                title.style.transform = 'translateY(-2px)';
            }
            
            if (price) {
                price.style.transform = 'scale(1.08)';
                price.style.color = '#dc2626';
            }
            
            if (status) {
                status.style.transform = 'scale(1.05) translateY(-1px)';
            }
            
            // Efecto de brillo
            createShineEffect(this);
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
            this.style.borderColor = 'rgba(255, 255, 255, 0.3)';
            
            const image = this.querySelector('.product-image');
            const title = this.querySelector('.product-title');
            const price = this.querySelector('.product-price');
            const status = this.querySelector('.product-status');
            
            if (image) {
                image.style.transform = 'scale(1) rotate(0deg)';
                image.style.boxShadow = '0 8px 25px rgba(0, 0, 0, 0.15)';
            }
            
            if (title) {
                title.style.color = '#1f2937';
                title.style.transform = 'translateY(0)';
            }
            
            if (price) {
                price.style.transform = 'scale(1)';
                price.style.color = '#10b981';
            }
            
            if (status) {
                status.style.transform = 'scale(1) translateY(0)';
            }
        });
        
        // Efecto de clic
        card.addEventListener('mousedown', function(e) {
            createRippleEffect(e, this);
            this.style.transform = 'translateY(-8px) scale(0.98)';
        });
        
        card.addEventListener('mouseup', function() {
            this.style.transform = 'translateY(-12px) scale(1.03)';
        });
        
        // Animación de entrada escalonada
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0) scale(1)';
        }, 100 * (index + 1));
    });
    
    // Configurar scroll horizontal suave para productos
    setupProductScroll();
}

/**
 * Configurar scroll horizontal suave para productos
 */
function setupProductScroll() {
    const productScrolls = document.querySelectorAll('.products-scroll');
    
    productScrolls.forEach(scroll => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        scroll.addEventListener('mousedown', (e) => {
            isDown = true;
            scroll.classList.add('dragging');
            startX = e.pageX - scroll.offsetLeft;
            scrollLeft = scroll.scrollLeft;
            scroll.style.cursor = 'grabbing';
        });
        
        scroll.addEventListener('mouseleave', () => {
            isDown = false;
            scroll.classList.remove('dragging');
            scroll.style.cursor = 'grab';
        });
        
        scroll.addEventListener('mouseup', () => {
            isDown = false;
            scroll.classList.remove('dragging');
            scroll.style.cursor = 'grab';
        });
        
        scroll.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scroll.offsetLeft;
            const walk = (x - startX) * 2;
            scroll.scrollLeft = scrollLeft - walk;
        });
        
        // Scroll con rueda del mouse
        scroll.addEventListener('wheel', (e) => {
            if (e.deltaY !== 0) {
                e.preventDefault();
                scroll.scrollLeft += e.deltaY;
            }
        });
    });
}

/**
 * Actualizar productos automáticamente
 */
function actualizarProductosAutomatico() {
    // Función para actualizar libros recientes
    function actualizarLibros() {
        fetch('admin.php?action=ajax&method=libros_recientes')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aquí se actualizaría la sección de libros
                    console.log('Libros actualizados:', data.data);
                }
            })
            .catch(error => {
                console.error('Error al actualizar libros:', error);
            });
    }
    
    // Función para actualizar componentes recientes
    function actualizarComponentes() {
        fetch('admin.php?action=ajax&method=componentes_recientes')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Aquí se actualizaría la sección de componentes
                    console.log('Componentes actualizados:', data.data);
                }
            })
            .catch(error => {
                console.error('Error al actualizar componentes:', error);
            });
    }
    
    // Actualizar cada 5 minutos
    setInterval(() => {
        if (!document.hidden) {
            actualizarLibros();
            actualizarComponentes();
        }
    }, 300000); // 5 minutos
}

/**
 * Efectos optimizados para el diseño compacto
 */
function setupCompactEffects() {
    // Efectos para tarjetas de productos compactas
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach((card, index) => {
        // Entrada escalonada optimizada
        card.style.opacity = '0';
        card.style.transform = 'translateX(30px) scale(0.95)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            card.style.opacity = '1';
            card.style.transform = 'translateX(0) scale(1)';
        }, index * 100);
        
        // Efectos hover optimizados para diseño compacto
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.03)';
            this.style.transition = 'all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            this.style.boxShadow = '0 15px 35px rgba(0, 0, 0, 0.15)';
            
            const image = this.querySelector('.product-image');
            const title = this.querySelector('.product-title');
            const price = this.querySelector('.product-price');
            
            if (image) {
                image.style.transform = 'scale(1.15) rotate(8deg)';
                image.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            }
            
            if (title) {
                title.style.color = '#dc2626';
                title.style.transform = 'translateY(-2px)';
            }
            
            if (price) {
                price.style.transform = 'scale(1.08)';
                price.style.color = '#dc2626';
            }
            
            // Efecto de brillo optimizado
            createShineEffect(this);
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
            
            const image = this.querySelector('.product-image');
            const title = this.querySelector('.product-title');
            const price = this.querySelector('.product-price');
            
            if (image) {
                image.style.transform = 'scale(1) rotate(0deg)';
            }
            
            if (title) {
                title.style.color = '#1f2937';
                title.style.transform = 'translateY(0)';
            }
            
            if (price) {
                price.style.transform = 'scale(1)';
                price.style.color = '#10b981';
            }
        });
    });
}

/**
 * Configurar efectos de scroll personalizados
 */
function setupCustomScrollEffects() {
    const scrollElements = document.querySelectorAll('.sales-scroll, .summary-grid, .products-scroll');
    
    scrollElements.forEach(element => {
        // Efecto de fade en los bordes al hacer scroll
        element.addEventListener('scroll', function() {
            const scrollTop = this.scrollTop;
            const scrollHeight = this.scrollHeight;
            const clientHeight = this.clientHeight;
            
            // Calcular porcentaje de scroll
            const scrollPercentage = scrollTop / (scrollHeight - clientHeight);
            
            // Aplicar efectos basados en el scroll
            if (scrollPercentage > 0.1) {
                this.style.maskImage = 'linear-gradient(to bottom, transparent 0%, black 10%, black 90%, transparent 100%)';
            } else {
                this.style.maskImage = 'none';
            }
        });
        
        // Indicador visual de scroll
        element.addEventListener('mouseenter', function() {
            if (this.scrollHeight > this.clientHeight) {
                this.style.boxShadow = 'inset 0 0 0 1px rgba(220, 38, 38, 0.1)';
            }
        });
        
        element.addEventListener('mouseleave', function() {
            this.style.boxShadow = '';
        });
    });
}

/**
 * Función para mostrar indicador de carga en productos
 */
function mostrarCargaProductos(seccion) {
    const elemento = document.querySelector(`.${seccion} .products-grid`);
    if (elemento) {
        elemento.classList.add('products-loading');
    }
}

/**
 * Función para ocultar indicador de carga en productos
 */
function ocultarCargaProductos(seccion) {
    const elemento = document.querySelector(`.${seccion} .products-grid`);
    if (elemento) {
        elemento.classList.remove('products-loading');
    }
}

/**
 * Optimizar rendimiento para dispositivos móviles
 */
function optimizarRendimiento() {
    // Reducir animaciones en dispositivos móviles
    if (window.innerWidth <= 768) {
        const style = document.createElement('style');
        style.textContent = `
            .product-card {
                transition: all 0.2s ease !important;
            }
            .product-card:hover {
                transform: translateY(-3px) scale(1.01) !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Usar requestAnimationFrame para animaciones suaves
    function smoothScroll() {
        const scrollElements = document.querySelectorAll('.products-scroll');
        scrollElements.forEach(element => {
            element.style.scrollBehavior = 'smooth';
        });
    }
    
    requestAnimationFrame(smoothScroll);
}

/**
 * Inicializar todas las funciones optimizadas
 */
function initOptimizedEffects() {
    setupCompactEffects();
    setupCustomScrollEffects();
    actualizarProductosAutomatico();
    optimizarRendimiento();
    animateEnhancedSections();
    setupEnhancedParallax();
    
    // Configurar observador de redimensionamiento
    const resizeObserver = new ResizeObserver(entries => {
        entries.forEach(entry => {
            // Reajustar efectos cuando cambie el tamaño
            if (entry.target === document.body) {
                optimizarRendimiento();
            }
        });
    });
    
    resizeObserver.observe(document.body);
    
    // Configurar observer para lazy loading de efectos
    const effectsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const productCards = entry.target.querySelectorAll('.product-card');
                productCards.forEach((card, index) => {
                    setTimeout(() => {
                        card.classList.add('animate-in');
                    }, index * 50);
                });
            }
        });
    }, { threshold: 0.1 });
    
    // Observar secciones de productos
    document.querySelectorAll('.products-scroll').forEach(section => {
        effectsObserver.observe(section);
    });
}

/**
 * Animar elementos de nuevas secciones al entrar en viewport
 */
function setupNewSectionsIntersectionObserver() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -30px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                
                // Animar elementos específicos según la sección
                if (entry.target.querySelector('.summary-item')) {
                    animateSummaryItems(entry.target);
                } else if (entry.target.querySelector('.sale-item')) {
                    animateSalesItems(entry.target);
                } else if (entry.target.querySelector('.product-card')) {
                    animateProductCards(entry.target);
                }
            }
        });
    }, observerOptions);
    
    // Observar las nuevas secciones
    const newSections = document.querySelectorAll('.section-card:nth-last-child(-n+4)');
    newSections.forEach(section => {
        observer.observe(section);
    });
}

/**
 * Animar elementos de resumen del sistema
 */
function animateSummaryItems(container) {
    const items = container.querySelectorAll('.summary-item');
    items.forEach((item, index) => {
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateX(0) scale(1)';
            item.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        }, index * 100);
    });
}

/**
 * Animar elementos de ventas
 */
function animateSalesItems(container) {
    const items = container.querySelectorAll('.sale-item');
    items.forEach((item, index) => {
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateX(0) scale(1)';
            item.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        }, index * 80);
    });
}

/**
 * Animar tarjetas de productos
 */
function animateProductCards(container) {
    const cards = container.querySelectorAll('.product-card');
    cards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0) scale(1)';
            card.style.transition = 'all 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        }, index * 120);
    });
}