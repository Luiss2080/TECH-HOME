<?php
// Vista Dashboard Estudiante - Tech Home Bolivia
// Datos de ejemplo estáticos para mostrar la estructura visual
?>

<!-- Estilos específicos para el módulo  -->
<link rel="stylesheet" href="<?= asset('css/admin/admin.css'); ?>">

<div class="dashboard-content">

    <!-- Sección 1: Acciones Rápidas para Estudiante -->
    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-rocket"></i>
            Acciones Rápidas
        </h2>
        <p class="section-subtitle">Accede rápidamente a tus actividades de aprendizaje y recursos educativos</p>

        <div class="quick-actions-grid">
            <a href="#" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-play-circle"></i>
                </div>
                <h3 class="action-title">Continuar Curso</h3>
                <p class="action-description">Retomar donde dejaste tu último curso</p>
            </a>

            <a href="#" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="action-title">Explorar Cursos</h3>
                <p class="action-description">Descubrir nuevos cursos de robótica</p>
            </a>

            <a href="#" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-upload"></i>
                </div>
                <h3 class="action-title">Subir Tarea</h3>
                <p class="action-description">Entregar tareas y proyectos pendientes</p>
            </a>

            <a href="#" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-download"></i>
                </div>
                <h3 class="action-title">Materiales</h3>
                <p class="action-description">Descargar recursos y documentos</p>
            </a>

            <a href="#" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-certificate"></i>
                </div>
                <h3 class="action-title">Mis Certificados</h3>
                <p class="action-description">Ver certificados obtenidos</p>
            </a>
        </div>
    </div>

    <!-- Sección 2: Mi Progreso Académico -->
    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-chart-line"></i>
            Mi Progreso Académico
        </h2>

        <div class="metrics-grid">
            <!-- Primera fila: Cursos, Progreso, Certificados -->
            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon courses">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value">5</div>
                        <div class="metric-label">Cursos Inscritos</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>3 en progreso</span>
                    </div>
                    <a href="#" class="metric-action">
                        <i class="fas fa-book-reader"></i>
                        Ver Cursos
                    </a>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon students">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value">78%</div>
                        <div class="metric-label">Progreso General</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-trending-up"></i>
                        <span>+15% este mes</span>
                    </div>
                    <a href="#" class="metric-action">
                        <i class="fas fa-chart-pie"></i>
                        Ver Detalles
                    </a>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon books">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value">3</div>
                        <div class="metric-label">Certificados Obtenidos</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-medal"></i>
                        <span>2 completados</span>
                    </div>
                    <a href="#" class="metric-action">
                        <i class="fas fa-award"></i>
                        Ver Certificados
                    </a>
                </div>
            </div>

            <!-- Segunda fila: Tareas, Calificaciones, Tiempo -->
            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon reports">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value">4</div>
                        <div class="metric-label">Tareas Pendientes</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-warning">
                        <i class="fas fa-clock"></i>
                        <span>2 próximas a vencer</span>
                    </div>
                    <a href="#" class="metric-action">
                        <i class="fas fa-tasks"></i>
                        Ver Tareas
                    </a>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon teachers">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value">8.7</div>
                        <div class="metric-label">Promedio General</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-arrow-up"></i>
                        <span>Subió 0.3 puntos</span>
                    </div>
                    <a href="#" class="metric-action">
                        <i class="fas fa-chart-bar"></i>
                        Ver Calificaciones
                    </a>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon components">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value">24h</div>
                        <div class="metric-label">Tiempo de Estudio</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-hourglass-half"></i>
                        <span>8h esta semana</span>
                    </div>
                    <a href="#" class="metric-action">
                        <i class="fas fa-history"></i>
                        Ver Historial
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección 3: Mi Actividad y Próximas Fechas -->
    <div class="section-card">
        <div class="widgets-grid">

            <!-- Widget de Mi Actividad Reciente -->
            <div class="widget">
                <h3 class="widget-title">
                    <i class="fas fa-history"></i>
                    Mi Actividad Reciente
                </h3>

                <div class="activity-item">
                    <div class="activity-icon" style="background: #10b981;">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Lección Completada</div>
                        <div class="activity-description">Terminaste "Programación Arduino con Sensores"</div>
                    </div>
                    <div class="activity-time">Hace 2 horas</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon" style="background: #3b82f6;">
                        <i class="fas fa-upload"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Tarea Entregada</div>
                        <div class="activity-description">Subiste el proyecto "Robot Seguidor de Línea"</div>
                    </div>
                    <div class="activity-time">Ayer</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon" style="background: #8b5cf6;">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Material Descargado</div>
                        <div class="activity-description">Descargaste "Guía de Sensores Ultrasónicos"</div>
                    </div>
                    <div class="activity-time">Hace 2 días</div>
                </div>

                <div class="activity-item">
                    <div class="activity-icon" style="background: #f59e0b;">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">Evaluación Aprobada</div>
                        <div class="activity-description">Obtuviste 9.2 en "Examen de Electrónica Básica"</div>
                    </div>
                    <div class="activity-time">Hace 3 días</div>
                </div>
            </div>

            <!-- Widget de Próximas Fechas Importantes -->
            <div class="widget">
                <h3 class="widget-title">
                    <i class="fas fa-calendar-alt"></i>
                    Próximas Fechas Importantes
                </h3>

                <div class="session-item">
                    <div class="session-user">
                        <div class="status-indicator" style="background: #ef4444;"></div>
                        <div>
                            <div class="session-name">Entrega de Proyecto</div>
                            <div class="session-role">Robot Autónomo - IoT Avanzado</div>
                        </div>
                    </div>
                    <div class="session-info">
                        <div class="session-time" style="color: #ef4444;">En 2 días</div>
                        <div class="session-device">Viernes 28 Ago</div>
                    </div>
                </div>

                <div class="session-item">
                    <div class="session-user">
                        <div class="status-indicator" style="background: #f59e0b;"></div>
                        <div>
                            <div class="session-name">Examen Final</div>
                            <div class="session-role">Arduino Intermedio</div>
                        </div>
                    </div>
                    <div class="session-info">
                        <div class="session-time" style="color: #f59e0b;">En 5 días</div>
                        <div class="session-device">Lunes 31 Ago</div>
                    </div>
                </div>

                <div class="session-item">
                    <div class="session-user">
                        <div class="status-indicator" style="background: #10b981;"></div>
                        <div>
                            <div class="session-name">Nueva Lección</div>
                            <div class="session-role">Inteligencia Artificial Básica</div>
                        </div>
                    </div>
                    <div class="session-info">
                        <div class="session-time" style="color: #10b981;">En 1 semana</div>
                        <div class="session-device">Miércoles 2 Sep</div>
                    </div>
                </div>

                <div class="session-item">
                    <div class="session-user">
                        <div class="status-indicator" style="background: #3b82f6;"></div>
                        <div>
                            <div class="session-name">Certificación</div>
                            <div class="session-role">Robótica Básica Completa</div>
                        </div>
                    </div>
                    <div class="session-info">
                        <div class="session-time" style="color: #3b82f6;">En 10 días</div>
                        <div class="session-device">Sábado 5 Sep</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección 4: Rendimiento y Calificaciones -->
    <div class="section-card">
        <div class="widgets-grid">

            <!-- Widget de Mi Rendimiento -->
            <div class="widget summary-widget">
                <h3 class="widget-title">
                    <i class="fas fa-chart-bar"></i>
                    Mi Rendimiento Académico
                    <a href="#" class="widget-action">Ver historial completo</a>
                </h3>

                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Lecciones completadas</div>
                            <div class="summary-value">47</div>
                            <div class="summary-description">De 60 lecciones totales</div>
                        </div>
                        <div class="summary-badge trend-positive">78%</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #10b981, #047857);">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Tareas entregadas</div>
                            <div class="summary-value">12</div>
                            <div class="summary-description">Todas a tiempo</div>
                        </div>
                        <div class="summary-badge trend-positive">100%</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                            <i class="fas fa-brain"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Conocimientos adquiridos</div>
                            <div class="summary-value">Arduino, IoT, Sensores</div>
                            <div class="summary-description">Especialización en robótica</div>
                        </div>
                        <div class="summary-badge trend-positive">Avanzado</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Tiempo promedio por día</div>
                            <div class="summary-value">2.3 horas</div>
                            <div class="summary-description">Constancia de estudio</div>
                        </div>
                        <div class="summary-badge trend-warning">Constante</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            <i class="fas fa-medal"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Ranking en la clase</div>
                            <div class="summary-value">3° lugar</div>
                            <div class="summary-description">De 45 estudiantes</div>
                        </div>
                        <div class="summary-badge trend-positive">Top 10%</div>
                    </div>
                </div>
            </div>

            <!-- Widget de Calificaciones Recientes -->
            <div class="widget sales-widget">
                <h3 class="widget-title">
                    <i class="fas fa-star"></i>
                    Mis Calificaciones Recientes
                    <a href="#" class="widget-action">Ver todas</a>
                </h3>

                <div class="sales-scroll">
                    <div class="sale-item">
                        <div class="sale-avatar">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="sale-content">
                            <div class="sale-customer">Proyecto Arduino</div>
                            <div class="sale-product">IoT con ESP32</div>
                            <div class="sale-date">Calificado hace 1 día</div>
                        </div>
                        <div class="sale-details">
                            <div class="sale-amount" style="color: #10b981; font-weight: 800;">9.5</div>
                            <div class="sale-location">Prof. García</div>
                            <div class="sale-status completada">Excelente</div>
                        </div>
                    </div>

                    <div class="sale-item">
                        <div class="sale-avatar">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="sale-content">
                            <div class="sale-customer">Robot Seguidor</div>
                            <div class="sale-product">Robótica Avanzada</div>
                            <div class="sale-date">Calificado hace 3 días</div>
                        </div>
                        <div class="sale-details">
                            <div class="sale-amount" style="color: #10b981; font-weight: 800;">8.8</div>
                            <div class="sale-location">Prof. Mendoza</div>
                            <div class="sale-status completada">Muy Bueno</div>
                        </div>
                    </div>

                    <div class="sale-item">
                        <div class="sale-avatar">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <div class="sale-content">
                            <div class="sale-customer">Examen Sensores</div>
                            <div class="sale-product">Electrónica Básica</div>
                            <div class="sale-date">Calificado hace 5 días</div>
                        </div>
                        <div class="sale-details">
                            <div class="sale-amount" style="color: #10b981; font-weight: 800;">9.2</div>
                            <div class="sale-location">Prof. Torres</div>
                            <div class="sale-status completada">Sobresaliente</div>
                        </div>
                    </div>

                    <div class="sale-item">
                        <div class="sale-avatar">
                            <i class="fas fa-laptop-code"></i>
                        </div>
                        <div class="sale-content">
                            <div class="sale-customer">Tarea Programación</div>
                            <div class="sale-product">Python para Robótica</div>
                            <div class="sale-date">Calificado hace 1 semana</div>
                        </div>
                        <div class="sale-details">
                            <div class="sale-amount" style="color: #f59e0b; font-weight: 800;">7.5</div>
                            <div class="sale-location">Prof. López</div>
                            <div class="sale-status procesando">Bueno</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección 5: Mis Cursos Actuales -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-header-content">
                <h2 class="section-title">
                    <i class="fas fa-book-open"></i>
                    Mis Cursos Actuales
                </h2>
                <p class="section-subtitle">Cursos en los que estás inscrito actualmente</p>
            </div>
            <div class="section-header-actions">
                <a href="#" class="section-action-header">
                    <i class="fas fa-search"></i>
                    Explorar más cursos
                </a>
            </div>
        </div>

        <div class="products-scroll">
            <div class="products-grid">
                <div class="product-card book-card">
                    <div class="product-image">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="product-content">
                        <div class="product-category">Arduino</div>
                        <div class="product-title">Arduino Intermedio</div>
                        <div class="product-author">Prof: Dr. García</div>
                        <div class="product-price">78% completado</div>
                        <div class="product-footer">
                            <div class="product-stock">12 de 15 lecciones</div>
                            <div class="product-status disponible">En Progreso</div>
                        </div>
                    </div>
                </div>

                <div class="product-card book-card">
                    <div class="product-image">
                        <i class="fas fa-wifi"></i>
                    </div>
                    <div class="product-content">
                        <div class="product-category">IoT</div>
                        <div class="product-title">IoT con ESP32</div>
                        <div class="product-author">Prof: Ing. Mendoza</div>
                        <div class="product-price">45% completado</div>
                        <div class="product-footer">
                            <div class="product-stock">9 de 20 lecciones</div>
                            <div class="product-status disponible">En Progreso</div>
                        </div>
                    </div>
                </div>

                <div class="product-card book-card">
                    <div class="product-image">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="product-content">
                        <div class="product-category">Robótica</div>
                        <div class="product-title">Robótica Avanzada</div>
                        <div class="product-author">Prof: Dr. Torres</div>
                        <div class="product-price">92% completado</div>
                        <div class="product-footer">
                            <div class="product-stock">11 de 12 lecciones</div>
                            <div class="product-status stock-bajo">Casi Terminado</div>
                        </div>
                    </div>
                </div>

                <div class="product-card book-card">
                    <div class="product-image">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="product-content">
                        <div class="product-category">IA</div>
                        <div class="product-title">IA Básica</div>
                        <div class="product-author">Prof: Dra. López</div>
                        <div class="product-price">15% completado</div>
                        <div class="product-footer">
                            <div class="product-stock">2 de 14 lecciones</div>
                            <div class="product-status disponible">Recién Iniciado</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección 6: Recursos y Materiales Descargables -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-header-content">
                <h2 class="section-title">
                    <i class="fas fa-download"></i>
                    Recursos y Materiales
                </h2>
                <p class="section-subtitle">Documentos, códigos y recursos disponibles para descargar</p>
            </div>
            <div class="section-header-actions">
                <a href="#" class="section-action-header">
                    <i class="fas fa-folder-open"></i>
                    Ver biblioteca completa
                </a>
            </div>
        </div>

        <div class="products-scroll">
            <div class="products-grid">
                <div class="product-card component-card">
                    <div class="product-image">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="product-content">
                        <div class="product-category">Manual</div>
                        <div class="product-title">Guía Completa Arduino</div>
                        <div class="product-code">Curso: Arduino Intermedio</div>
                        <div class="product-price">2.5 MB</div>
                        <div class="product-footer">
                            <div class="product-stock">Descargado</div>
                            <div class="product-status disponible">Disponible</div>
                        </div>
                    </div>
                </div>

                <div class="product-card component-card">
                    <div class="product-image">
                        <i class="fas fa-file-code"></i>
                    </div>
                    <div class="product-content">
                        <div class="product-category">Código</div>
                        <div class="product-title">Ejemplos_ESP32.zip</div>
                        <div class="product-code">Curso: IoT con ESP32</div>
                        <div class="product-price">1.8 MB</div>
                        <div class="product-footer">
                            <div class="product-stock">Nuevo</div>
                            <div class="product-status disponible">Disponible</div>
                        </div>
                    </div>
                </div>

                <div class="product-card component-card">
                    <div class="product-image">
                        <i class="fas fa-file-video"></i>
                    </div>
                    <div class="product-content">
                        <div class="product-category">Video</div>
                        <div class="product-title">Soldadura Básica</div>
                        <div class="product-code">Curso: Electrónica Práctica</div>
                        <div class="product-price">125 MB</div>
                        <div class="product-footer">
                            <div class="product-stock">HD Quality</div>
                            <div class="product-status disponible">Disponible</div>
                        </div>
                    </div>
                </div>

                <div class="product-card component-card">
                    <div class="product-image">
                        <i class="fas fa-file-image"></i>
                    </div>
                    <div class="product-content">
                        <div class="product-category">Diagramas</div>
                        <div class="product-title">Esquemas Circuitos</div>
                        <div class="product-code">Curso: Robótica Avanzada</div>
                        <div class="product-price">5.2 MB</div>
                        <div class="product-footer">
                            <div class="product-stock">15 imágenes</div>
                            <div class="product-status disponible">Disponible</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>