<?php
$title = $title ?? 'Bienvenido a TECH HOME';
$usuario = $usuario ?? null;
$estadisticas = $estadisticas ?? [];
$actividades_recientes = $actividades_recientes ?? [];
$notificaciones = $notificaciones ?? [];
?>

<!-- Estilos específicos para el módulo CRUD - Vista Home -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">

<!-- Contenedor principal de la vista Home -->
<div class="crud-edit-container">
    <div class="crud-edit-wrapper">

        <!-- Header principal de bienvenida -->
        <div class="crud-section-card tech-home-hero">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon tech-home-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="crud-section-title-group">
                        <nav aria-label="breadcrumb" class="crud-breadcrumb-nav">
                            <ol class="crud-breadcrumb">
                                <li class="crud-breadcrumb-item active">
                                    <i class="fas fa-home"></i>
                                    Inicio
                                </li>
                            </ol>
                        </nav>
                        <h1 class="crud-section-title tech-home-title">
                            Bienvenido al Instituto de Robótica
                            <span class="tech-home-brand">TECH HOME</span>
                        </h1>
                        <p class="crud-section-subtitle tech-home-subtitle">
                            <?php if ($usuario): ?>
                                Hola <?= htmlspecialchars($usuario->nombre) ?>, estás conectado al futuro de la robótica y la tecnología
                            <?php else: ?>
                                Portal de acceso al ecosistema tecnológico más avanzado
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="crud-section-header-actions">
                    <div class="tech-home-status">
                        <div class="status-indicator online">
                            <i class="fas fa-wifi"></i>
                            <span>Sistema Online</span>
                        </div>
                        <div class="current-time" id="techHomeTime">
                            <!-- Se actualiza con JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Elementos decorativos robóticos -->
            <div class="tech-home-decorations">
                <div class="floating-robot robot-1">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="floating-robot robot-2">
                    <i class="fas fa-cog"></i>
                </div>
                <div class="floating-robot robot-3">
                    <i class="fas fa-microchip"></i>
                </div>
                <div class="circuit-lines">
                    <div class="circuit-line line-1"></div>
                    <div class="circuit-line line-2"></div>
                    <div class="circuit-line line-3"></div>
                </div>
            </div>
        </div>

        <!-- Sección: Explorar TECH HOME -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-compass"></i>
                    Explora el Ecosistema TECH HOME
                </h2>
                <p class="crud-section-subtitle">Descubre las áreas principales de nuestro instituto de robótica</p>
            </div>
            
            <div class="crud-form-body">
                <div class="tech-home-quick-actions">
                    <div class="quick-action-card" onclick="showInfo('biblioteca')">
                        <div class="quick-action-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="quick-action-content">
                            <h4>Biblioteca Digital</h4>
                            <p>Recursos académicos especializados</p>
                            <div class="quick-action-stats">
                                <span class="stat-number"><?= $estadisticas['libros'] ?? '2,847' ?></span>
                                <span class="stat-label">libros disponibles</span>
                            </div>
                        </div>
                        <div class="quick-action-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>

                    <div class="quick-action-card" onclick="showInfo('componentes')">
                        <div class="quick-action-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <div class="quick-action-content">
                            <h4>Centro de Componentes</h4>
                            <p>Hardware y tecnología robótica</p>
                            <div class="quick-action-stats">
                                <span class="stat-number"><?= $estadisticas['componentes'] ?? '1,523' ?></span>
                                <span class="stat-label">componentes activos</span>
                            </div>
                        </div>
                        <div class="quick-action-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>

                    <div class="quick-action-card" onclick="showInfo('cursos')">
                        <div class="quick-action-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="quick-action-content">
                            <h4>Cursos Especializados</h4>
                            <p>Formación en robótica e IA</p>
                            <div class="quick-action-stats">
                                <span class="stat-number"><?= $estadisticas['cursos'] ?? '45' ?></span>
                                <span class="stat-label">cursos activos</span>
                            </div>
                        </div>
                        <div class="quick-action-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>

                    <div class="quick-action-card" onclick="showInfo('proyectos')">
                        <div class="quick-action-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <div class="quick-action-content">
                            <h4>Proyectos Innovadores</h4>
                            <p>Desarrollos tecnológicos en curso</p>
                            <div class="quick-action-stats">
                                <span class="stat-number"><?= $estadisticas['proyectos'] ?? '128' ?></span>
                                <span class="stat-label">en desarrollo</span>
                            </div>
                        </div>
                        <div class="quick-action-arrow">
                            <i class="fas fa-arrow-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Estadísticas del Sistema -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-chart-line"></i>
                    Estado del Sistema
                </h2>
                <p class="crud-section-subtitle">Monitoreo en tiempo real del ecosistema TECH HOME</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-info-panel">
                    <div class="crud-info-tabs">
                        <button class="crud-info-tab active" data-tab="general">
                            <i class="fas fa-chart-pie"></i>
                            General
                        </button>
                        <button class="crud-info-tab" data-tab="rendimiento">
                            <i class="fas fa-tachometer-alt"></i>
                            Rendimiento
                        </button>
                        <button class="crud-info-tab" data-tab="actividad">
                            <i class="fas fa-activity"></i>
                            Actividad
                        </button>
                    </div>
                    
                    <div class="crud-info-pane active" id="general">
                        <div class="tech-home-stats-grid">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number"><?= $estadisticas['libros_total'] ?? '2,847' ?></div>
                                    <div class="stat-label">Libros Especializados</div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-plus"></i>
                                        +<?= $estadisticas['libros_nuevos'] ?? '15' ?> este mes
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-microchip"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number"><?= $estadisticas['componentes'] ?? '1,523' ?></div>
                                    <div class="stat-label">Componentes Disponibles</div>
                                    <div class="stat-change warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        <?= $estadisticas['stock_bajo'] ?? '12' ?> stock bajo
                                    </div>
                                </div>
                            </div>

                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="stat-info">
                                    <div class="stat-number"><?= $estadisticas['cursos'] ?? '45' ?></div>
                                    <div class="stat-label">Cursos Activos</div>
                                    <div class="stat-change positive">
                                        <i class="fas fa-users"></i>
                                        <?= $estadisticas['estudiantes_inscritos'] ?? '342' ?> inscritos
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="rendimiento">
                        <div class="performance-metrics">
                            <div class="metric-item">
                                <div class="metric-label">Tiempo de Respuesta del Sistema</div>
                                <div class="metric-bar">
                                    <div class="metric-progress" style="width: 85%;"></div>
                                </div>
                                <div class="metric-value">85ms - Excelente</div>
                            </div>

                            <div class="metric-item">
                                <div class="metric-label">Disponibilidad de Servidores</div>
                                <div class="metric-bar">
                                    <div class="metric-progress" style="width: 99%;"></div>
                                </div>
                                <div class="metric-value">99.8% - Óptimo</div>
                            </div>

                            <div class="metric-item">
                                <div class="metric-label">Conexiones Activas</div>
                                <div class="metric-bar">
                                    <div class="metric-progress" style="width: 67%;"></div>
                                </div>
                                <div class="metric-value">156/230 - Normal</div>
                            </div>

                            <div class="metric-item">
                                <div class="metric-label">Uso de Recursos</div>
                                <div class="metric-bar">
                                    <div class="metric-progress" style="width: 45%;"></div>
                                </div>
                                <div class="metric-value">45% - Óptimo</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="actividad">
                        <div class="activity-feed">
                            <?php if (!empty($actividades_recientes)): ?>
                                <?php foreach (array_slice($actividades_recientes, 0, 5) as $actividad): ?>
                                    <div class="activity-item">
                                        <div class="activity-icon">
                                            <i class="fas fa-<?= $actividad['tipo'] === 'usuario' ? 'user' : ($actividad['tipo'] === 'sistema' ? 'cog' : 'info-circle') ?>"></i>
                                        </div>
                                        <div class="activity-content">
                                            <div class="activity-title"><?= htmlspecialchars($actividad['titulo'] ?? 'Actividad del sistema') ?></div>
                                            <div class="activity-description"><?= htmlspecialchars($actividad['descripcion'] ?? 'Sin descripción') ?></div>
                                            <div class="activity-time"><?= $actividad['tiempo'] ?? 'Hace un momento' ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Sistema iniciado correctamente</div>
                                        <div class="activity-description">Todos los módulos robóticos están operativos</div>
                                        <div class="activity-time">Hace 2 minutos</div>
                                    </div>
                                </div>

                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Nuevo usuario registrado</div>
                                        <div class="activity-description">Usuario estudiante agregado al sistema</div>
                                        <div class="activity-time">Hace 15 minutos</div>
                                    </div>
                                </div>

                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-microchip"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title">Inventario actualizado</div>
                                        <div class="activity-description">Nuevos sensores Arduino agregados al stock</div>
                                        <div class="activity-time">Hace 1 hora</div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Biblioteca Digital -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-book-open"></i>
                    Biblioteca Digital Especializada
                </h2>
                <p class="crud-section-subtitle">Accede a recursos académicos de vanguardia en robótica e inteligencia artificial</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card tech-lib-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Robótica Avanzada</h4>
                            <p>Libros especializados en robótica móvil y manipuladores</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('robotica-libros')">
                                <i class="fas fa-book"></i>
                                Ver Catálogo
                            </button>
                        </div>
                        <div class="lib-count">
                            <i class="fas fa-bookmark"></i>
                            845 títulos
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lib-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Inteligencia Artificial</h4>
                            <p>Recursos sobre machine learning y deep learning</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('ia-libros')">
                                <i class="fas fa-brain"></i>
                                Explorar
                            </button>
                        </div>
                        <div class="lib-count">
                            <i class="fas fa-bookmark"></i>
                            632 títulos
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lib-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Programación Avanzada</h4>
                            <p>Lenguajes especializados para sistemas embebidos</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('programacion-libros')">
                                <i class="fas fa-code"></i>
                                Consultar
                            </button>
                        </div>
                        <div class="lib-count">
                            <i class="fas fa-bookmark"></i>
                            589 títulos
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lib-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Ingeniería de Control</h4>
                            <p>Sistemas de control automático y teoría</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('control-libros')">
                                <i class="fas fa-cogs"></i>
                                Acceder
                            </button>
                        </div>
                        <div class="lib-count">
                            <i class="fas fa-bookmark"></i>
                            781 títulos
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Centro de Componentes -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-microchip"></i>
                    Centro de Componentes Electrónicos
                </h2>
                <p class="crud-section-subtitle">Inventario completo de hardware especializado para proyectos robóticos</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card tech-comp-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Microcontroladores</h4>
                            <p>Arduino, Raspberry Pi y sistemas embebidos</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('microcontroladores')">
                                <i class="fas fa-cpu"></i>
                                Ver Stock
                            </button>
                        </div>
                        <div class="comp-status available">
                            <i class="fas fa-circle"></i>
                            Disponible
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-comp-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Sensores Avanzados</h4>
                            <p>Cámaras, LIDAR, ultrasónicos y táctiles</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('sensores')">
                                <i class="fas fa-search"></i>
                                Explorar
                            </button>
                        </div>
                        <div class="comp-status available">
                            <i class="fas fa-circle"></i>
                            Disponible
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-comp-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Actuadores y Motores</h4>
                            <p>Servos, motores paso a paso y lineales</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('actuadores')">
                                <i class="fas fa-play"></i>
                                Consultar
                            </button>
                        </div>
                        <div class="comp-status low-stock">
                            <i class="fas fa-circle"></i>
                            Stock Bajo
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-comp-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Fuentes y Baterías</h4>
                            <p>Sistemas de alimentación especializados</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('alimentacion')">
                                <i class="fas fa-battery-full"></i>
                                Ver Opciones
                            </button>
                        </div>
                        <div class="comp-status available">
                            <i class="fas fa-circle"></i>
                            Disponible
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Cursos Especializados -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-graduation-cap"></i>
                    Cursos de Robótica e IA
                </h2>
                <p class="crud-section-subtitle">Formación especializada desde nivel básico hasta investigación avanzada</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card tech-course-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Robótica Básica</h4>
                            <p>Introducción a la programación de robots</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('robotica-basica')">
                                <i class="fas fa-play"></i>
                                Comenzar
                            </button>
                        </div>
                        <div class="course-info">
                            <div class="course-level beginner">Básico</div>
                            <div class="course-duration">40 horas</div>
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-course-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Machine Learning</h4>
                            <p>Algoritmos de aprendizaje automático aplicado</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('machine-learning')">
                                <i class="fas fa-rocket"></i>
                                Inscribirse
                            </button>
                        </div>
                        <div class="course-info">
                            <div class="course-level intermediate">Intermedio</div>
                            <div class="course-duration">60 horas</div>
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-course-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Visión Artificial</h4>
                            <p>Procesamiento de imágenes y reconocimiento</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('vision-artificial')">
                                <i class="fas fa-camera"></i>
                                Ver Curso
                            </button>
                        </div>
                        <div class="course-info">
                            <div class="course-level advanced">Avanzado</div>
                            <div class="course-duration">80 horas</div>
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-course-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>IoT y Automatización</h4>
                            <p>Internet de las cosas y sistemas conectados</p>
                            <button type="button" class="crud-btn-action" onclick="showInfo('iot-automatizacion')">
                                <i class="fas fa-link"></i>
                                Explorar
                            </button>
                        </div>
                        <div class="course-info">
                            <div class="course-level intermediate">Intermedio</div>
                            <div class="course-duration">50 horas</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-rocket"></i>
                    Laboratorios Virtuales
                </h2>
                <p class="crud-section-subtitle">Accede a las herramientas de desarrollo y simulación</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card tech-lab-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>IDE de Programación</h4>
                            <p>Entorno integrado para desarrollo robótico</p>
                            <button type="button" class="crud-btn-action" onclick="openLab('programming')">
                                <i class="fas fa-play"></i>
                                Iniciar
                            </button>
                        </div>
                        <div class="lab-status online">
                            <i class="fas fa-circle"></i>
                            Online
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lab-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-cube"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Simulador 3D</h4>
                            <p>Pruebas virtuales de prototipos robóticos</p>
                            <button type="button" class="crud-btn-action" onclick="openLab('simulator')">
                                <i class="fas fa-cube"></i>
                                Simular
                            </button>
                        </div>
                        <div class="lab-status online">
                            <i class="fas fa-circle"></i>
                            Online
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lab-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>IA Training Hub</h4>
                            <p>Entrenamiento de modelos de inteligencia artificial</p>
                            <button type="button" class="crud-btn-action" onclick="openLab('ai')">
                                <i class="fas fa-brain"></i>
                                Entrenar
                            </button>
                        </div>
                        <div class="lab-status maintenance">
                            <i class="fas fa-circle"></i>
                            Mantenimiento
                        </div>
                    </div>
                    
                    <div class="crud-action-card tech-lab-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>IoT Dashboard</h4>
                            <p>Monitoreo de dispositivos conectados</p>
                            <button type="button" class="crud-btn-action" onclick="openLab('iot')">
                                <i class="fas fa-chart-line"></i>
                                Monitorear
                            </button>
                        </div>
                        <div class="lab-status online">
                            <i class="fas fa-circle"></i>
                            Online
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Notificaciones y Alertas -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-bell"></i>
                    Centro de Notificaciones
                </h2>
                <p class="crud-section-subtitle">Mantente informado de las últimas actualizaciones del sistema</p>
            </div>
            
            <div class="crud-form-body">
                <div class="notifications-container">
                    <?php if (!empty($notificaciones)): ?>
                        <?php foreach ($notificaciones as $notificacion): ?>
                            <div class="notification-item <?= $notificacion['tipo'] ?? 'info' ?>">
                                <div class="notification-icon">
                                    <i class="fas fa-<?= $notificacion['icono'] ?? 'info-circle' ?>"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title"><?= htmlspecialchars($notificacion['titulo'] ?? 'Notificación') ?></div>
                                    <div class="notification-message"><?= htmlspecialchars($notificacion['mensaje'] ?? 'Sin mensaje') ?></div>
                                    <div class="notification-time"><?= $notificacion['tiempo'] ?? 'Ahora' ?></div>
                                </div>
                                <button class="notification-close" onclick="dismissNotification(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="notification-item success">
                            <div class="notification-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">Sistema Operativo</div>
                                <div class="notification-message">Todos los módulos robóticos funcionan correctamente</div>
                                <div class="notification-time">Verificado hace 5 minutos</div>
                            </div>
                        </div>

                        <div class="notification-item info">
                            <div class="notification-icon">
                                <i class="fas fa-robot"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">Bienvenido a TECH HOME</div>
                                <div class="notification-message">Explora todas las funcionalidades disponibles en el sistema</div>
                                <div class="notification-time">Mensaje de bienvenida</div>
                            </div>
                        </div>

                        <div class="notification-item warning">
                            <div class="notification-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-title">Mantenimiento Programado</div>
                                <div class="notification-message">El módulo de IA estará en mantenimiento el domingo de 2:00 AM a 6:00 AM</div>
                                <div class="notification-time">Programado para este domingo</div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Espacio de separación -->
        <div style="height: 20px;"></div>

    </div>
</div>

<!-- JavaScript específico para la vista Home -->
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

    // Actualizar reloj en tiempo real
    function updateClock() {
        const now = new Date();
        const timeString = now.toLocaleTimeString('es-ES', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
        const dateString = now.toLocaleDateString('es-ES', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        const clockElement = document.getElementById('techHomeTime');
        if (clockElement) {
            clockElement.innerHTML = `
                <div class="time">${timeString}</div>
                <div class="date">${dateString}</div>
            `;
        }
    }
    
    updateClock();
    setInterval(updateClock, 1000);

    // Navegación a diferentes secciones (para vista generalizada)
    window.showInfo = function(seccion) {
        const infoSecciones = {
            // Biblioteca Digital
            'robotica-libros': {
                titulo: 'Robótica Avanzada - Biblioteca Digital',
                descripcion: 'Colección especializada en robótica móvil, manipuladores industriales y sistemas autónomos.',
                caracteristicas: [
                    '• 845 títulos especializados en robótica',
                    '• Desde fundamentos hasta aplicaciones industriales',
                    '• Incluye casos de estudio y proyectos prácticos',
                    '• Autores reconocidos internacionalmente',
                    '• Actualizaciones constantes con nuevas tecnologías'
                ]
            },
            'ia-libros': {
                titulo: 'Inteligencia Artificial - Recursos Académicos',
                descripcion: 'Biblioteca completa sobre machine learning, deep learning y sistemas inteligentes.',
                caracteristicas: [
                    '• 632 libros especializados en IA',
                    '• Algoritmos de aprendizaje automático',
                    '• Redes neuronales y deep learning',
                    '• Procesamiento de lenguaje natural',
                    '• Aplicaciones en robótica e industria'
                ]
            },
            'programacion-libros': {
                titulo: 'Programación Avanzada - Sistemas Embebidos',
                descripcion: 'Recursos especializados en lenguajes de programación para robótica y sistemas embebidos.',
                caracteristicas: [
                    '• 589 títulos de programación especializada',
                    '• C/C++, Python, ROS y MATLAB',
                    '• Programación de microcontroladores',
                    '• Algoritmos para sistemas en tiempo real',
                    '• Desarrollo de firmware especializado'
                ]
            },
            'control-libros': {
                titulo: 'Ingeniería de Control - Teoría y Práctica',
                descripcion: 'Biblioteca especializada en sistemas de control automático y teoría de control.',
                caracteristicas: [
                    '• 781 libros de teoría de control',
                    '• Control clásico y moderno',
                    '• Sistemas de control digital',
                    '• Control adaptativo y robusto',
                    '• Aplicaciones en robótica industrial'
                ]
            },
            
            // Centro de Componentes
            'microcontroladores': {
                titulo: 'Microcontroladores y Sistemas Embebidos',
                descripcion: 'Inventario completo de microcontroladores, SBCs y sistemas de desarrollo.',
                caracteristicas: [
                    '• Arduino (Uno, Mega, Nano, ESP32)',
                    '• Raspberry Pi (4B, Zero, Compute Module)',
                    '• STM32 y microcontroladores ARM',
                    '• Kits de desarrollo y shields especializados',
                    '• Sistemas de prototipado rápido'
                ]
            },
            'sensores': {
                titulo: 'Sensores Avanzados para Robótica',
                descripcion: 'Amplia gama de sensores especializados para percepción robótica.',
                caracteristicas: [
                    '• Cámaras RGB-D y sistemas de visión',
                    '• Sensores LIDAR y ultrasónicos',
                    '• IMUs y sensores de orientación',
                    '• Sensores táctiles y de fuerza',
                    '• Sensores ambientales y químicos'
                ]
            },
            'actuadores': {
                titulo: 'Actuadores y Motores Especializados',
                descripcion: 'Sistema completo de actuadores para aplicaciones robóticas.',
                caracteristicas: [
                    '• Servomotores de alta precisión',
                    '• Motores paso a paso y lineales',
                    '• Actuadores neumáticos e hidráulicos',
                    '• Sistemas de tracción y locomoción',
                    '• Controladores y drivers especializados'
                ]
            },
            'alimentacion': {
                titulo: 'Sistemas de Alimentación Robótica',
                descripcion: 'Soluciones completas de alimentación para sistemas robóticos.',
                caracteristicas: [
                    '• Baterías LiPo y Li-ion especializadas',
                    '• Fuentes de alimentación conmutadas',
                    '• Sistemas de carga inalámbrica',
                    '• Reguladores de voltaje especializados',
                    '• Sistemas de gestión de energía'
                ]
            },
            
            // Cursos Especializados
            'robotica-basica': {
                titulo: 'Curso: Robótica Básica',
                descripcion: 'Introducción completa al mundo de la robótica, desde conceptos fundamentales hasta proyectos prácticos.',
                caracteristicas: [
                    '• 40 horas de formación intensiva',
                    '• Programación básica de Arduino',
                    '• Construcción de robots móviles',
                    '• Sensores y actuadores básicos',
                    '• Proyecto final certificado'
                ]
            },
            'machine-learning': {
                titulo: 'Curso: Machine Learning Aplicado',
                descripcion: 'Formación avanzada en algoritmos de aprendizaje automático con aplicaciones robóticas.',
                caracteristicas: [
                    '• 60 horas de contenido especializado',
                    '• Python, TensorFlow y PyTorch',
                    '• Algoritmos supervisados y no supervisados',
                    '• Redes neuronales profundas',
                    '• Proyectos con datasets reales'
                ]
            },
            'vision-artificial': {
                titulo: 'Curso: Visión Artificial Avanzada',
                descripción: 'Curso especializado en procesamiento de imágenes y sistemas de visión para robótica.',
                caracteristicas: [
                    '• 80 horas de formación avanzada',
                    '• OpenCV y bibliotecas especializadas',
                    '• Detección y reconocimiento de objetos',
                    '• Sistemas de navegación visual',
                    '• Integración con sistemas robóticos'
                ]
            },
            'iot-automatizacion': {
                titulo: 'Curso: IoT y Automatización Industrial',
                descripcion: 'Formación en Internet de las Cosas aplicado a la automatización y robótica.',
                caracteristicas: [
                    '• 50 horas de contenido práctico',
                    '• Protocolos IoT (MQTT, CoAP, LoRaWAN)',
                    '• Sensores inalámbricos y conectividad',
                    '• Plataformas cloud y edge computing',
                    '• Proyectos de automatización industrial'
                ]
            },
            
            // Secciones generales
            'biblioteca': {
                titulo: 'Biblioteca Digital TECH HOME',
                descripcion: 'Accede a nuestra extensa colección de recursos académicos especializados en robótica, inteligencia artificial, programación y tecnología avanzada.',
                caracteristicas: [
                    '• Más de 2,800 libros especializados en formato digital',
                    '• Recursos actualizados constantemente',
                    '• Acceso 24/7 desde cualquier dispositivo',
                    '• Sistema de búsqueda avanzada por temas',
                    '• Biblioteca de proyectos y casos de estudio'
                ]
            },
            'componentes': {
                titulo: 'Centro de Componentes Robóticos',
                descripcion: 'Descubre nuestro inventario completo de componentes, sensores, actuadores y hardware especializado para desarrollo robótico.',
                caracteristicas: [
                    '• Más de 1,500 componentes disponibles',
                    '• Sensores Arduino, Raspberry Pi y microcontroladores',
                    '• Actuadores y motores especializados',
                    '• Sistemas de visión artificial',
                    '• Hardware para IA y machine learning'
                ]
            },
            'cursos': {
                titulo: 'Cursos de Robótica e IA',
                descripcion: 'Explora nuestro catálogo de cursos especializados, desde nivel básico hasta avanzado, impartidos por expertos en tecnología.',
                caracteristicas: [
                    '• 45 cursos activos en diferentes niveles',
                    '• Programación de robots y sistemas autónomos',
                    '• Inteligencia artificial y machine learning',
                    '• Visión por computadora y procesamiento de imágenes',
                    '• Certificaciones reconocidas internacionalmente'
                ]
            },
            'proyectos': {
                titulo: 'Proyectos de Innovación',
                descripcion: 'Conoce los proyectos innovadores que desarrollamos, desde prototipos académicos hasta soluciones industriales reales.',
                caracteristicas: [
                    '• 128 proyectos en desarrollo activo',
                    '• Colaboraciones con empresas tecnológicas',
                    '• Robots autónomos y sistemas inteligentes',
                    '• Soluciones IoT y automatización industrial',
                    '• Investigación en IA aplicada'
                ]
            }
        };

        const info = infoSecciones[seccion];
        if (info) {
            alert(`${info.titulo}\n\n${info.descripcion}\n\nCaracterísticas principales:\n${info.caracteristicas.join('\n')}\n\n¡Registrate para acceder a todo el contenido!`);
        }
    };

    // Funciones para laboratorios virtuales
    window.openLab = function(labType) {
        const labNames = {
            'programming': 'IDE de Programación',
            'simulator': 'Simulador 3D',
            'ai': 'IA Training Hub',
            'iot': 'IoT Dashboard'
        };
        
        if (labType === 'ai') {
            alert('El laboratorio de IA está actualmente en mantenimiento. Estará disponible próximamente.');
            return;
        }
        
        alert(`Iniciando ${labNames[labType]}...\n\nEsta funcionalidad se implementará próximamente.`);
    };

    // Función para cerrar notificaciones
    window.dismissNotification = function(button) {
        const notification = button.closest('.notification-item');
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    };

    // Animaciones de entrada para las tarjetas
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
    document.querySelectorAll('.quick-action-card, .stat-card, .crud-action-card, .notification-item').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
        observer.observe(el);
    });

    // Efecto hover mejorado para tarjetas de acción rápida
    document.querySelectorAll('.quick-action-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
            this.querySelector('.quick-action-arrow').style.transform = 'translateX(5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
            this.querySelector('.quick-action-arrow').style.transform = 'translateX(0)';
        });
    });

    // Actualizar estadísticas con animación
    function animateCounters() {
        const counters = document.querySelectorAll('.stat-number');
        counters.forEach(counter => {
            const target = parseInt(counter.textContent);
            let current = 0;
            const increment = target / 50;
            
            const updateCounter = () => {
                if (current < target) {
                    current += increment;
                    counter.textContent = Math.floor(current);
                    setTimeout(updateCounter, 30);
                } else {
                    counter.textContent = target;
                }
            };
            
            setTimeout(updateCounter, Math.random() * 2000);
        });
    }

    // Iniciar animación de contadores después de un delay
    setTimeout(animateCounters, 1000);

    // Efecto de respiración para elementos robóticos
    function breatheEffect() {
        const robots = document.querySelectorAll('.floating-robot');
        robots.forEach((robot, index) => {
            robot.style.transform = `translateY(${Math.sin(Date.now() * 0.001 + index) * 10}px)`;
        });
        requestAnimationFrame(breatheEffect);
    }
    breatheEffect();

    // Auto-dismiss para notificaciones después de 10 segundos
    setTimeout(() => {
        const notifications = document.querySelectorAll('.notification-item');
        notifications.forEach((notification, index) => {
            if (index > 0) { // Mantener la primera notificación
                setTimeout(() => {
                    notification.style.opacity = '0.7';
                }, index * 5000);
            }
        });
    }, 10000);
});
</script>

<style>
/* Estilos específicos para la vista Home de TECH HOME */
.tech-home-hero {
    background: linear-gradient(135deg, 
        rgba(220, 38, 38, 0.1) 0%, 
        rgba(14, 184, 166, 0.1) 50%, 
        rgba(139, 92, 246, 0.1) 100%);
    position: relative;
    overflow: hidden;
    min-height: 200px;
}

.tech-home-icon {
    background: linear-gradient(135deg, var(--primary-red), var(--accent-teal));
    animation: robotPulse 3s ease-in-out infinite;
}

.tech-home-title {
    background: linear-gradient(135deg, var(--text-primary), var(--primary-red));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.tech-home-brand {
    display: block;
    font-size: 1.8rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--primary-red), var(--accent-teal));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: 3px;
}

.tech-home-subtitle {
    color: var(--text-secondary);
    font-style: italic;
    position: relative;
    z-index: 2;
}

.tech-home-status {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.5rem;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: rgba(16, 185, 129, 0.1);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: var(--border-radius-sm);
    color: var(--success-color);
    font-size: 0.85rem;
    font-weight: 600;
}

.status-indicator.online i {
    animation: pulse 2s infinite;
}

.current-time {
    text-align: right;
    font-size: 0.9rem;
}

.current-time .time {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--text-primary);
}

.current-time .date {
    font-size: 0.8rem;
    color: var(--text-secondary);
    text-transform: capitalize;
}

/* Elementos decorativos robóticos */
.tech-home-decorations {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    overflow: hidden;
}

.floating-robot {
    position: absolute;
    font-size: 2rem;
    color: rgba(220, 38, 38, 0.1);
    animation: float 6s ease-in-out infinite;
}

.robot-1 {
    top: 20%;
    right: 10%;
    animation-delay: 0s;
}

.robot-2 {
    top: 60%;
    right: 5%;
    animation-delay: 2s;
    font-size: 1.5rem;
}

.robot-3 {
    top: 40%;
    right: 20%;
    animation-delay: 4s;
    font-size: 1.8rem;
}

.circuit-lines {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
}

.circuit-line {
    position: absolute;
    background: linear-gradient(90deg, transparent, rgba(220, 38, 38, 0.2), transparent);
    height: 2px;
    animation: circuit 8s linear infinite;
}

.line-1 {
    top: 30%;
    left: 0;
    right: 0;
    animation-delay: 0s;
}

.line-2 {
    top: 70%;
    left: 0;
    right: 0;
    animation-delay: 2s;
}

.line-3 {
    top: 50%;
    left: 0;
    right: 0;
    animation-delay: 4s;
}

/* Acciones rápidas */
.tech-home-quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.quick-action-card {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.8));
    border: 2px solid rgba(0, 0, 0, 0.08);
    border-radius: var(--border-radius-md);
    padding: 1.5rem;
    cursor: pointer;
    transition: var(--transition-bounce);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.quick-action-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-primary);
    transform: scaleX(0);
    transform-origin: left;
    transition: var(--transition-base);
}

.quick-action-card:hover::before {
    transform: scaleX(1);
}

.quick-action-card:hover {
    border-color: var(--primary-red);
    box-shadow: var(--shadow-strong);
}

.quick-action-icon {
    width: 60px;
    height: 60px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    flex-shrink: 0;
    transition: var(--transition-bounce);
}

.quick-action-card:hover .quick-action-icon {
    transform: scale(1.1) rotate(10deg);
}

.quick-action-content {
    flex: 1;
}

.quick-action-content h4 {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
}

.quick-action-content p {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin: 0 0 0.8rem 0;
}

.quick-action-stats {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stat-number {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--accent-teal);
}

.stat-label {
    font-size: 0.8rem;
    color: var(--text-light);
}

.quick-action-arrow {
    color: var(--text-light);
    font-size: 1.2rem;
    transition: var(--transition-base);
}

/* Estadísticas del sistema */
.tech-home-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.stat-card {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
    border: 2px solid rgba(0, 0, 0, 0.08);
    border-radius: var(--border-radius-md);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: var(--transition-base);
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-medium);
    border-color: var(--primary-red);
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.stat-info {
    flex: 1;
}

.stat-info .stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: var(--text-primary);
    line-height: 1;
}

.stat-info .stat-label {
    font-size: 0.9rem;
    color: var(--text-secondary);
    margin: 0.2rem 0;
}

.stat-change {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.8rem;
    font-weight: 600;
}

.stat-change.positive {
    color: var(--success-color);
}

.stat-change.warning {
    color: var(--warning-color);
}

/* Métricas de rendimiento */
.performance-metrics {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.metric-item {
    background: rgba(255, 255, 255, 0.5);
    border-radius: var(--border-radius-sm);
    padding: 1rem;
}

.metric-label {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.metric-bar {
    background: rgba(0, 0, 0, 0.1);
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.metric-progress {
    height: 100%;
    background: var(--gradient-primary);
    border-radius: 4px;
    transition: width 2s ease-out;
}

.metric-value {
    font-size: 0.9rem;
    color: var(--text-secondary);
    font-weight: 500;
}

/* Feed de actividad */
.activity-feed {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-height: 400px;
    overflow-y: auto;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.5);
    border-radius: var(--border-radius-sm);
    border-left: 4px solid var(--accent-teal);
}

.activity-icon {
    width: 35px;
    height: 35px;
    background: var(--gradient-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-title {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.95rem;
}

.activity-description {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin: 0.2rem 0;
}

.activity-time {
    font-size: 0.75rem;
    color: var(--text-light);
    font-style: italic;
}

/* Laboratorios tecnológicos */
.tech-lab-card {
    position: relative;
}

.lab-status {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.6rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
}

.lab-status.online {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.lab-status.maintenance {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning-color);
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.lab-status i {
    font-size: 0.6rem;
}

.lab-status.online i {
    animation: pulse 2s infinite;
}

/* Notificaciones */
.notifications-container {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    max-height: 500px;
    overflow-y: auto;
}

.notification-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    border-radius: var(--border-radius-sm);
    border-left: 4px solid;
    transition: var(--transition-base);
    position: relative;
}

.notification-item.success {
    background: rgba(16, 185, 129, 0.05);
    border-left-color: var(--success-color);
}

.notification-item.info {
    background: rgba(59, 130, 246, 0.05);
    border-left-color: var(--info-color);
}

.notification-item.warning {
    background: rgba(245, 158, 11, 0.05);
    border-left-color: var(--warning-color);
}

.notification-item.danger {
    background: rgba(239, 68, 68, 0.05);
    border-left-color: var(--danger-color);
}

.notification-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.notification-item.success .notification-icon {
    background: var(--success-color);
}

.notification-item.info .notification-icon {
    background: var(--info-color);
}

.notification-item.warning .notification-icon {
    background: var(--warning-color);
}

.notification-item.danger .notification-icon {
    background: var(--danger-color);
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    color: var(--text-primary);
    font-size: 0.95rem;
}

.notification-message {
    font-size: 0.85rem;
    color: var(--text-secondary);
    margin: 0.2rem 0;
    line-height: 1.4;
}

.notification-time {
    font-size: 0.75rem;
    color: var(--text-light);
    font-style: italic;
}

.notification-close {
    background: none;
    border: none;
    color: var(--text-light);
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 50%;
    transition: var(--transition-base);
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
}

.notification-close:hover {
    background: rgba(0, 0, 0, 0.1);
    color: var(--text-primary);
}

/* Animaciones específicas */
@keyframes robotPulse {
    0%, 100% {
        transform: scale(1);
        box-shadow: var(--shadow-medium);
    }
    50% {
        transform: scale(1.05);
        box-shadow: var(--shadow-strong), 0 0 20px rgba(220, 38, 38, 0.3);
    }
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-20px);
    }
}

@keyframes circuit {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* Responsive */
@media (max-width: 768px) {
    .tech-home-quick-actions {
        grid-template-columns: 1fr;
    }
    
    .tech-home-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-action-card {
        flex-direction: column;
        text-align: center;
        padding: 2rem 1rem;
    }
    
    .crud-section-header {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .tech-home-status {
        align-items: center;
    }
    
    .floating-robot {
        display: none;
    }
}

/* Modo oscuro - Corrección de fondos */
body.ithr-dark-mode .tech-home-hero,
body.dark-theme .tech-home-hero {
    background: linear-gradient(135deg, 
        rgba(220, 38, 38, 0.15) 0%, 
        rgba(14, 184, 166, 0.15) 50%, 
        rgba(139, 92, 246, 0.15) 100%);
}

body.ithr-dark-mode .quick-action-card,
body.dark-theme .quick-action-card,
body.ithr-dark-mode .stat-card,
body.dark-theme .stat-card {
    background: linear-gradient(145deg, rgba(30, 41, 59, 0.9), rgba(30, 41, 59, 0.7));
    border-color: rgba(71, 85, 105, 0.3);
}

body.ithr-dark-mode .notification-item,
body.dark-theme .notification-item,
body.ithr-dark-mode .activity-item,
body.dark-theme .activity-item,
body.ithr-dark-mode .metric-item,
body.dark-theme .metric-item {
    background: rgba(30, 41, 59, 0.5);
}

/* Corrección específica para tarjetas en modo oscuro */
body.ithr-dark-mode .crud-section-card,
body.dark-theme .crud-section-card {
    background: var(--background-card);
    border-color: rgba(71, 85, 105, 0.3);
}

body.ithr-dark-mode .crud-form-body,
body.dark-theme .crud-form-body,
body.ithr-dark-mode .crud-form-header,
body.dark-theme .crud-form-header {
    background: transparent;
}

/* Biblioteca Digital - Estilos específicos */
.tech-lib-card {
    position: relative;
    border-left: 4px solid var(--accent-teal);
}

.tech-lib-card:hover {
    border-left-color: var(--primary-red);
}

.lib-count {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.8rem;
    background: rgba(14, 184, 166, 0.1);
    color: var(--accent-teal);
    border: 1px solid rgba(14, 184, 166, 0.3);
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.lib-count i {
    font-size: 0.7rem;
}

/* Centro de Componentes - Estilos específicos */
.tech-comp-card {
    position: relative;
    border-left: 4px solid var(--warning-color);
}

.tech-comp-card:hover {
    border-left-color: var(--primary-red);
}

.comp-status {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.3rem 0.8rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.comp-status.available {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.comp-status.low-stock {
    background: rgba(245, 158, 11, 0.1);
    color: var(--warning-color);
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.comp-status i {
    font-size: 0.6rem;
    animation: pulse 2s infinite;
}

/* Cursos Especializados - Estilos específicos */
.tech-course-card {
    position: relative;
    border-left: 4px solid var(--secondary-blue);
}

.tech-course-card:hover {
    border-left-color: var(--primary-red);
}

.course-info {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 0.3rem;
}

.course-level {
    padding: 0.2rem 0.6rem;
    border-radius: 10px;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.course-level.beginner {
    background: rgba(16, 185, 129, 0.15);
    color: var(--success-color);
    border: 1px solid rgba(16, 185, 129, 0.3);
}

.course-level.intermediate {
    background: rgba(245, 158, 11, 0.15);
    color: var(--warning-color);
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.course-level.advanced {
    background: rgba(239, 68, 68, 0.15);
    color: var(--danger-color);
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.course-duration {
    font-size: 0.75rem;
    color: var(--text-secondary);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.course-duration::before {
    content: '⏱';
    font-size: 0.8rem;
}

/* Efectos específicos por tipo de sección */
.tech-lib-card .crud-action-icon {
    background: linear-gradient(135deg, var(--accent-teal), #2dd4bf);
}

.tech-comp-card .crud-action-icon {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24);
}

.tech-course-card .crud-action-icon {
    background: linear-gradient(135deg, var(--secondary-blue), #60a5fa);
}

/* Animaciones específicas */
.tech-lib-card:hover .lib-count,
.tech-comp-card:hover .comp-status,
.tech-course-card:hover .course-info {
    transform: scale(1.05);
}

/* Responsive específico para las nuevas secciones */
@media (max-width: 768px) {
    .lib-count,
    .comp-status,
    .course-info {
        position: static;
        justify-self: center;
        margin-top: 0.5rem;
    }
    
    .tech-lib-card,
    .tech-comp-card,
    .tech-course-card {
        border-left: none;
        border-top: 4px solid var(--primary-red);
        border-color: rgba(71, 85, 105, 0.3);
    }
}

body.ithr-dark-mode .crud-action-card:hover,
body.dark-theme .crud-action-card:hover {
    background: linear-gradient(145deg, rgba(30, 41, 59, 0.95), rgba(30, 41, 59, 0.85));
    border-color: var(--primary-red);
}
</style>