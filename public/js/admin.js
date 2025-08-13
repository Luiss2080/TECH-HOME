/**
 * TECH HOME BOLIVIA - Dashboard Administrador (JS Optimizado)
 * Funcionalidad esencial optimizada
 */

// Variables globales esenciales
let usuarioData = {};
let isAnimating = false;

/**
 * Función principal de inicialización
 */
function initAdminDashboard(userData) {
    usuarioData = userData;
    
    console.log('🚀 Dashboard Administrador cargado');
    console.log('👤 Usuario:', usuarioData);
    
    // Configuraciones esenciales
    setupLayout();
    setupComponents();
    setupEventListeners();
    initEffects();
    setupIntersectionObserver();
    
    console.log('✅ Dashboard inicializado correctamente');
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

        // Estilos unificados para contenedores
        const unifiedStyles = {
            marginLeft: '330px',
            marginRight: '20px',
            transition: 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)',
            position: 'relative',
            zIndex: '1'
        };
        
        // Aplicar a contenedores principales
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
        
        // Configurar header fijo
        const headerContainer = document.querySelector('.header-container');
        if (headerContainer) {
            Object.assign(headerContainer.style, {
                position: 'fixed',
                top: '0',
                left: '0',
                right: '0',
                zIndex: '999'
            });
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
        
        if (techHeader) Object.assign(techHeader.style, internalStyles);
        if (footer) Object.assign(footer.style, { ...internalStyles, zIndex: '998' });

        // Configurar responsive
        handleResponsiveLayout();
    }, 50);
}

/**
 * Manejar layout responsive
 */
function handleResponsiveLayout() {
    const updateLayout = () => {
        const isMobile = window.innerWidth <= 1024;
        const elements = [
            document.querySelector('.header-container'),
            document.querySelector('.main-content-area'),
            document.querySelector('.footer-container')
        ];
        
        elements.forEach(element => {
            if (element) {
                element.style.marginLeft = isMobile ? '20px' : '330px';
                element.style.marginRight = '20px';
            }
        });
    };
    
    updateLayout();
    window.addEventListener('resize', debounce(updateLayout, 150));
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
 * Configurar event listeners esenciales
 */
function setupEventListeners() {
    // Redimensionamiento
    window.addEventListener('resize', debounce(() => {
        const footer = document.querySelector('.tech-home-footer');
        if (footer) {
            Object.assign(footer.style, {
                width: '100%',
                maxWidth: '1600px',
                margin: '0 auto',
                position: 'relative',
                boxSizing: 'border-box'
            });
            footer.offsetHeight; // Trigger reflow
        }
        handleResponsiveLayout();
    }, 100));
    
    // Smooth scroll para enlaces internos
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
}

/**
 * Inicializar efectos optimizados
 */
function initEffects() {
    setupCardEffects();
    setupProductEffects();
    setupWidgetEffects();
    animateContent();
}

/**
 * Efectos unificados para tarjetas
 */
function setupCardEffects() {
    // Tarjetas de acción
    document.querySelectorAll('.action-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.style.transition = 'all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            
            const icon = this.querySelector('.action-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            
            const icon = this.querySelector('.action-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });

    // Tarjetas de métricas
    document.querySelectorAll('.metric-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.style.transition = 'all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            
            const value = this.querySelector('.metric-value');
            const icon = this.querySelector('.metric-icon');
            
            if (value) {
                value.style.color = '#dc2626';
                value.style.transform = 'scale(1.05)';
            }
            
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            
            const value = this.querySelector('.metric-value');
            const icon = this.querySelector('.metric-icon');
            
            if (value) {
                value.style.color = '#1f2937';
                value.style.transform = 'scale(1)';
            }
            
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });
}

/**
 * Efectos para productos (libros y componentes)
 */
function setupProductEffects() {
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.style.transition = 'all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
            this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
            this.style.borderColor = '#dc2626';
            
            const image = this.querySelector('.product-image');
            const title = this.querySelector('.product-title');
            const price = this.querySelector('.product-price');
            
            if (image) {
                image.style.transform = 'scale(1.1) rotate(5deg)';
                image.style.transition = 'all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
            }
            
            if (title) {
                title.style.color = '#dc2626';
            }
            
            if (price) {
                price.style.transform = 'scale(1.05)';
                price.style.color = '#dc2626';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
            this.style.borderColor = 'rgba(255, 255, 255, 0.3)';
            
            const image = this.querySelector('.product-image');
            const title = this.querySelector('.product-title');
            const price = this.querySelector('.product-price');
            
            if (image) {
                image.style.transform = 'scale(1) rotate(0deg)';
            }
            
            if (title) {
                title.style.color = '#1f2937';
            }
            
            if (price) {
                price.style.transform = 'scale(1)';
                price.style.color = '#10b981';
            }
        });
    });
}

/**
 * Efectos para widgets y elementos de listas
 */
function setupWidgetEffects() {
    // Widgets generales
    document.querySelectorAll('.widget').forEach(widget => {
        widget.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        });
        
        widget.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Elementos de actividad
    document.querySelectorAll('.activity-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.background = 'linear-gradient(135deg, rgba(220, 38, 38, 0.04), rgba(220, 38, 38, 0.02))';
            this.style.transform = 'translateX(8px)';
            this.style.borderRadius = '12px';
            this.style.boxShadow = '0 4px 15px rgba(220, 38, 38, 0.1)';
            
            const icon = this.querySelector('.activity-icon');
            if (icon) {
                icon.style.transform = 'scale(1.1) rotate(5deg)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.background = '';
            this.style.transform = 'translateX(0)';
            this.style.boxShadow = '';
            
            const icon = this.querySelector('.activity-icon');
            if (icon) {
                icon.style.transform = 'scale(1) rotate(0deg)';
            }
        });
    });

    // Elementos de sesiones
    document.querySelectorAll('.session-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.background = 'linear-gradient(135deg, rgba(16, 185, 129, 0.04), rgba(16, 185, 129, 0.02))';
            this.style.transform = 'translateY(-2px)';
            this.style.borderRadius = '12px';
            this.style.boxShadow = '0 4px 15px rgba(16, 185, 129, 0.1)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.background = '';
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });

    // Elementos de resumen
    document.querySelectorAll('.summary-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px) scale(1.01)';
            this.style.background = 'rgba(255, 255, 255, 0.8)';
            this.style.boxShadow = '0 4px 15px rgba(0, 0, 0, 0.1)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0) scale(1)';
            this.style.background = 'rgba(255, 255, 255, 0.6)';
            this.style.boxShadow = '';
        });
    });

    // Elementos de ventas
    document.querySelectorAll('.sale-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.background = 'linear-gradient(135deg, rgba(16, 185, 129, 0.06), rgba(16, 185, 129, 0.02))';
            this.style.transform = 'translateX(8px)';
            this.style.borderRadius = '12px';
            this.style.boxShadow = '0 4px 15px rgba(16, 185, 129, 0.1)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.background = '';
            this.style.transform = 'translateX(0)';
            this.style.boxShadow = '';
        });
    });
}

/**
 * Configurar observador de intersección para animaciones
 */
function setupIntersectionObserver() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
                
                // Animar elementos hijos secuencialmente
                const children = entry.target.querySelectorAll(
                    '.action-card, .metric-card, .activity-item, .session-item, .product-card, .summary-item, .sale-item'
                );
                children.forEach((child, index) => {
                    setTimeout(() => {
                        child.style.opacity = '1';
                        child.style.transform = 'translateY(0) scale(1)';
                        child.style.transition = 'all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                    }, index * 100);
                });
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    document.querySelectorAll('.section-card').forEach(card => {
        observer.observe(card);
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
 * Función utilitaria de debounce
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

/**
 * Función de limpieza
 */
function cleanup() {
    // Limpiar event listeners si es necesario
    console.log('🧹 Limpieza de dashboard completada');
}

// Event listeners principales
window.addEventListener('beforeunload', cleanup);

// Exponer funciones globales necesarias
window.initAdminDashboard = initAdminDashboard;

console.log('📝 Admin Dashboard JS optimizado cargado exitosamente');