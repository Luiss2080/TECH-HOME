<?php
$title = $title ?? 'Error 403 - Acceso Denegado';
$user = $user ?? auth();
$allRoles = $allRoles ?? ($user ? ($user->roles() ? $user->roles() : []) : []);
$isAdmin = $isAdmin ?? ($user && in_array('admin', array_column($allRoles, 'nombre')));
$isEstudiante = $isEstudiante ?? ($user && in_array('estudiante', array_column($allRoles, 'nombre')));
$isDocente = $isDocente ?? ($user && in_array('docente', array_column($allRoles, 'nombre')));
$isInvitado = !$user || empty($allRoles);
?>

<!-- Estilos específicos para errores -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">
<link rel="stylesheet" href="<?= asset('css/errores.css'); ?>">

<!-- Contenedor principal del error 403 -->
<div class="crud-edit-container error-403-container">
    <div class="crud-edit-wrapper">

        <!-- Header principal del error 403 -->
        <div class="crud-section-card error-403-hero">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon error-403-icon">
                        <i class="fas fa-shield-alt"></i>
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
                                    <i class="fas fa-ban"></i>
                                    Error 403
                                </li>
                            </ol>
                        </nav>
                        <h1 class="crud-section-title error-403-title">
                            <span class="error-403-number">403</span>
                            <span class="error-403-text">Acceso Denegado</span>
                        </h1>
                        <p class="crud-section-subtitle error-403-subtitle">
                            Sistema de seguridad TECH HOME: permisos insuficientes para acceder a esta área restringida
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Escudo de seguridad animado -->
            <div class="error-403-shield">
                <div class="shield-container">
                    <div class="shield-body">
                        <div class="shield-lock">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="shield-waves">
                            <div class="wave wave-1"></div>
                            <div class="wave wave-2"></div>
                            <div class="wave wave-3"></div>
                        </div>
                    </div>
                    <div class="access-denied-text">ACCESO<br>DENEGADO</div>
                </div>
            </div>

            <!-- Elementos decorativos de seguridad -->
            <div class="error-403-decorations">
                <div class="floating-security security-1">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div class="floating-security security-2">
                    <i class="fas fa-key"></i>
                </div>
                <div class="floating-security security-3">
                    <i class="fas fa-ban"></i>
                </div>
                <div class="security-grid">
                    <div class="grid-line horizontal line-h1"></div>
                    <div class="grid-line horizontal line-h2"></div>
                    <div class="grid-line vertical line-v1"></div>
                    <div class="grid-line vertical line-v2"></div>
                </div>
            </div>
        </div>

        <!-- Sección: Información del Usuario Actual -->
        <?php if ($user): ?>
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-user-circle"></i>
                    Tu Perfil Actual
                </h2>
                <p class="crud-section-subtitle">Información de tu cuenta y roles asignados en el sistema</p>
            </div>
            
            <div class="crud-form-body">
                <div class="user-profile-card">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                        <div class="profile-status <?= $user ? 'authenticated' : 'guest' ?>">
                            <i class="fas fa-<?= $user ? 'check-circle' : 'question-circle' ?>"></i>
                        </div>
                    </div>
                    <div class="profile-info">
                        <div class="profile-name"><?= htmlspecialchars($user->nombre . ' ' . ($user->apellido ?? '')) ?></div>
                        <div class="profile-email"><?= htmlspecialchars($user->email ?? 'Sin email') ?></div>
                        <div class="profile-roles">
                            <?php if (!empty($allRoles)): ?>
                                <?php foreach ($allRoles as $role): ?>
                                    <span class="role-badge role-<?= strtolower($role['nombre'] ?? 'guest') ?>">
                                        <i class="fas fa-<?= $role['nombre'] === 'admin' ? 'crown' : ($role['nombre'] === 'docente' ? 'chalkboard-teacher' : ($role['nombre'] === 'estudiante' ? 'graduation-cap' : 'user')) ?>"></i>
                                        <?= htmlspecialchars(ucfirst($role['nombre'] ?? 'Invitado')) ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="role-badge role-guest">
                                    <i class="fas fa-user-circle"></i>
                                    Invitado
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="profile-permissions">
                        <div class="permission-level">
                            <span class="level-label">Nivel de Acceso:</span>
                            <span class="level-indicator level-<?= $isAdmin ? 'admin' : ($isDocente ? 'docente' : ($isEstudiante ? 'estudiante' : 'guest')) ?>">
                                <?= $isAdmin ? 'Administrador' : ($isDocente ? 'Docente' : ($isEstudiante ? 'Estudiante' : 'Invitado')) ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Sección: Análisis de Permisos -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-clipboard-check"></i>
                    Análisis de Permisos
                </h2>
                <p class="crud-section-subtitle">Comprende por qué no puedes acceder y qué opciones tienes disponibles</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-info-panel">
                    <div class="crud-info-tabs">
                        <button class="crud-info-tab active" data-tab="diagnostico">
                            <i class="fas fa-stethoscope"></i>
                            Diagnóstico
                        </button>
                        <button class="crud-info-tab" data-tab="recomendaciones">
                            <i class="fas fa-lightbulb"></i>
                            Recomendaciones
                        </button>
                        <button class="crud-info-tab" data-tab="escalacion">
                            <i class="fas fa-arrow-up"></i>
                            Solicitar Acceso
                        </button>
                    </div>
                    
                    <div class="crud-info-pane active" id="diagnostico">
                        <div class="permission-analysis">
                            <?php if (!$user): ?>
                                <div class="analysis-item critical">
                                    <div class="analysis-icon">
                                        <i class="fas fa-user-times"></i>
                                    </div>
                                    <div class="analysis-content">
                                        <h4>Usuario No Autenticado</h4>
                                        <p>No has iniciado sesión en el sistema. Muchas funciones requieren autenticación.</p>
                                        <div class="analysis-action">
                                            <a href="<?= route('login') ?>" class="quick-action-btn">
                                                <i class="fas fa-sign-in-alt"></i>
                                                Iniciar Sesión
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($isInvitado): ?>
                                <div class="analysis-item warning">
                                    <div class="analysis-icon">
                                        <i class="fas fa-user-clock"></i>
                                    </div>
                                    <div class="analysis-content">
                                        <h4>Rol de Invitado</h4>
                                        <p>Tienes acceso limitado como invitado. Contacta al administrador para obtener más permisos.</p>
                                    </div>
                                </div>
                            <?php elseif ($isEstudiante): ?>
                                <div class="analysis-item info">
                                    <div class="analysis-icon">
                                        <i class="fas fa-graduation-cap"></i>
                                    </div>
                                    <div class="analysis-content">
                                        <h4>Acceso de Estudiante</h4>
                                        <p>Como estudiante, tienes acceso a cursos, materiales y tu progreso académico, pero no a funciones administrativas.</p>
                                        <div class="student-permissions">
                                            <div class="permission-item allowed">
                                                <i class="fas fa-check"></i> Biblioteca Digital
                                            </div>
                                            <div class="permission-item allowed">
                                                <i class="fas fa-check"></i> Cursos Asignados
                                            </div>
                                            <div class="permission-item allowed">
                                                <i class="fas fa-check"></i> Laboratorios Virtuales
                                            </div>
                                            <div class="permission-item denied">
                                                <i class="fas fa-times"></i> Panel Administrativo
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($isDocente): ?>
                                <div class="analysis-item info">
                                    <div class="analysis-icon">
                                        <i class="fas fa-chalkboard-teacher"></i>
                                    </div>
                                    <div class="analysis-content">
                                        <h4>Acceso de Docente</h4>
                                        <p>Como docente, puedes gestionar cursos y estudiantes, pero algunas funciones administrativas están restringidas.</p>
                                        <div class="teacher-permissions">
                                            <div class="permission-item allowed">
                                                <i class="fas fa-check"></i> Gestión de Cursos
                                            </div>
                                            <div class="permission-item allowed">
                                                <i class="fas fa-check"></i> Seguimiento de Estudiantes
                                            </div>
                                            <div class="permission-item allowed">
                                                <i class="fas fa-check"></i> Recursos Educativos
                                            </div>
                                            <div class="permission-item denied">
                                                <i class="fas fa-times"></i> Configuración del Sistema
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php elseif ($isAdmin): ?>
                                <div class="analysis-item warning">
                                    <div class="analysis-icon">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <div class="analysis-content">
                                        <h4>Administrador con Restricciones</h4>
                                        <p>Aunque eres administrador, esta página específica podría tener restricciones adicionales o estar en mantenimiento.</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="recomendaciones">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Regresar al Dashboard:</strong> Vuelve a tu panel principal donde tienes acceso garantizado<br>
                                    Desde allí puedes navegar a las secciones disponibles para tu rol
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Explorar contenido disponible:</strong> Revisa las secciones públicas del sistema<br>
                                    Biblioteca, cursos abiertos y recursos generales están siempre disponibles
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-question-circle"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Solicitar ayuda:</strong> Contacta al soporte técnico o administrador<br>
                                    Puede ser un error temporal o necesitas permisos adicionales
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="escalacion">
                        <div class="escalation-form">
                            <div class="escalation-info">
                                <h4><i class="fas fa-paper-plane"></i> Solicitar Permisos Adicionales</h4>
                                <p>Si consideras que deberías tener acceso a esta sección, puedes enviar una solicitud al administrador del sistema.</p>
                            </div>
                            
                            <div class="escalation-actions">
                                <button type="button" class="escalation-btn" onclick="requestAccess('email')">
                                    <i class="fas fa-envelope"></i>
                                    <div class="btn-content">
                                        <div class="btn-title">Enviar Email</div>
                                        <div class="btn-subtitle">support@techhome.bo</div>
                                    </div>
                                </button>
                                
                                <button type="button" class="escalation-btn" onclick="requestAccess('ticket')">
                                    <i class="fas fa-ticket-alt"></i>
                                    <div class="btn-content">
                                        <div class="btn-title">Crear Ticket</div>
                                        <div class="btn-subtitle">Sistema de soporte</div>
                                    </div>
                                </button>
                                
                                <button type="button" class="escalation-btn" onclick="requestAccess('phone')">
                                    <i class="fas fa-phone"></i>
                                    <div class="btn-content">
                                        <div class="btn-title">Llamar Soporte</div>
                                        <div class="btn-subtitle">+591 3-123-4567</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Accesos Alternativos -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-door-open"></i>
                    Áreas Disponibles
                </h2>
                <p class="crud-section-subtitle">Explora las secciones a las que sí tienes acceso</p>
            </div>
            
            <div class="crud-form-body">
                <div class="alternative-access-grid">
                    <?php if (!$user): ?>
                        <div class="access-card guest-access">
                            <div class="access-icon">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div class="access-content">
                                <h4>Contenido Público</h4>
                                <p>Información general sobre TECH HOME</p>
                            </div>
                            <button class="access-btn" onclick="navigateToPublic()">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                        
                        <div class="access-card login-access">
                            <div class="access-icon">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="access-content">
                                <h4>Iniciar Sesión</h4>
                                <p>Accede con tu cuenta para más funciones</p>
                            </div>
                            <a href="<?= route('login') ?>" class="access-btn">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="access-card dashboard-access">
                            <div class="access-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <div class="access-content">
                                <h4>Tu Dashboard</h4>
                                <p>Panel principal personalizado</p>
                            </div>
                            <a href="<?= route('dashboard') ?>" class="access-btn">
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        
                        <?php if ($isEstudiante || $isDocente || $isAdmin): ?>
                            <div class="access-card courses-access">
                                <div class="access-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="access-content">
                                    <h4>Mis Cursos</h4>
                                    <p>Cursos asignados y progreso</p>
                                </div>
                                <button class="access-btn" onclick="navigateToCourses()">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                        
                        <div class="access-card library-access">
                            <div class="access-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="access-content">
                                <h4>Biblioteca Digital</h4>
                                <p>Recursos y materiales de estudio</p>
                            </div>
                            <button class="access-btn" onclick="navigateToLibrary()">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                        
                        <div class="access-card profile-access">
                            <div class="access-icon">
                                <i class="fas fa-user-cog"></i>
                            </div>
                            <div class="access-content">
                                <h4>Mi Perfil</h4>
                                <p>Configuración de cuenta</p>
                            </div>
                            <button class="access-btn" onclick="navigateToProfile()">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    <?php endif; ?>
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
                <a href="<?= $user ? route('dashboard') : route('home') ?>" class="crud-btn crud-btn-primary">
                    <i class="fas fa-<?= $user ? 'tachometer-alt' : 'home' ?>"></i>
                    <?= $user ? 'Ir al Dashboard' : 'Ir a Inicio' ?>
                </a>
            </div>
        </div>

        <!-- Espacio de separación -->
        <div style="height: 20px;"></div>

    </div>
</div>

<!-- JavaScript específico para la vista Error 403 -->
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
    window.navigateToPublic = function() {
        window.location.href = '<?= route("home") ?>';
    };

    window.navigateToCourses = function() {
        alert('Redirigiendo a Mis Cursos...\n\nEsta funcionalidad se implementará próximamente.');
    };

    window.navigateToLibrary = function() {
        alert('Redirigiendo a la Biblioteca Digital...\n\nEsta funcionalidad se implementará próximamente.');
    };

    window.navigateToProfile = function() {
        alert('Redirigiendo a Mi Perfil...\n\nEsta funcionalidad se implementará próximamente.');
    };

    // Función para regresar a la página anterior
    window.goBack = function() {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '<?= $user ? route("dashboard") : route("home") ?>';
        }
    };

    // Funciones de escalación
    window.requestAccess = function(method) {
        const messages = {
            email: 'Se abrirá tu cliente de email para enviar una solicitud de acceso a support@techhome.bo',
            ticket: 'Redirigiendo al sistema de tickets de soporte...\n\nEsta funcionalidad se implementará próximamente.',
            phone: 'Puedes contactar al soporte técnico llamando al +591 3-123-4567\nHorario: Lunes a Viernes de 8:00 AM a 6:00 PM'
        };
        
        if (method === 'email') {
            const subject = encodeURIComponent('Solicitud de Acceso - Error 403');
            const body = encodeURIComponent(`Hola,\n\nSolicito acceso a la página que me muestra error 403.\n\nDetalles:\n- Usuario: <?= $user ? htmlspecialchars($user->email) : 'No autenticado' ?>\n- URL solicitada: ${window.location.href}\n- Fecha: ${new Date().toLocaleString()}\n\nGracias.`);
            window.location.href = `mailto:support@techhome.bo?subject=${subject}&body=${body}`;
        } else {
            alert(messages[method]);
        }
    };

    // Animación del escudo de seguridad
    function animateShieldWaves() {
        const waves = document.querySelectorAll('.wave');
        waves.forEach((wave, index) => {
            wave.style.animation = `securityWave 2s ease-in-out infinite`;
            wave.style.animationDelay = `${index * 0.3}s`;
        });
    }

    // Efecto de parpadeo del candado
    function animateLock() {
        const lock = document.querySelector('.shield-lock i');
        if (lock) {
            setInterval(() => {
                lock.style.color = '#ef4444';
                setTimeout(() => {
                    lock.style.color = '#ffffff';
                }, 500);
            }, 2000);
        }
    }

    // Inicializar animaciones
    animateShieldWaves();
    animateLock();

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
    document.querySelectorAll('.access-card, .analysis-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(el);
    });

    console.log('🛡️ Error 403 - Sistema de Seguridad TECH HOME activo');
});
</script>