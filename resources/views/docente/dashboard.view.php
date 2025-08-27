<?php

use App\Services\DocenteService;
?>

<!-- Estilos específicos para el módulo  -->
<link rel="stylesheet" href="<?= asset('css/admin/admin.css'); ?>">


<div class="dashboard-content">

    <!-- Sección 1: Acciones Rápidas para Docente -->
    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-bolt"></i>
            Acciones Rápidas
        </h2>
        <p class="section-subtitle">Gestiona tus contenidos educativos y supervisa el progreso de tus estudiantes</p>

        <div class="quick-actions-grid">
            <a href="<?= route('docente.cursos.crear'); ?>" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h3 class="action-title">Nuevo Curso</h3>
                <p class="action-description">Crear un nuevo curso de robótica o tecnología</p>
            </a>

            <a href="<?= route('docente.lecciones.crear'); ?>" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h3 class="action-title">Nueva Lección</h3>
                <p class="action-description">Agregar lección a tus cursos existentes</p>
            </a>

            <a href="<?= route('docente.materiales.crear'); ?>" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-upload"></i>
                </div>
                <h3 class="action-title">Subir Material</h3>
                <p class="action-description">Cargar documentos, códigos o recursos</p>
            </a>

            <a href="<?= route('docente.tareas.revision'); ?>" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-tasks"></i>
                </div>
                <h3 class="action-title">Revisar Tareas</h3>
                <p class="action-description">Evaluar trabajos pendientes de estudiantes</p>
            </a>

            <a href="<?= route('docente.evaluaciones.crear'); ?>" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h3 class="action-title">Crear Evaluación</h3>
                <p class="action-description">Diseñar exámenes y cuestionarios</p>
            </a>
        </div>
    </div>

    <!-- Sección 2: Métricas del Docente -->
    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-chart-bar"></i>
            Mis Métricas Educativas
        </h2>

        <div class="metrics-grid">
            <!-- Primera fila: Estudiantes, Cursos, Materiales -->
            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon students">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value"><?= $metricas_docente['estudiantes_totales']; ?></div>
                        <div class="metric-label">Estudiantes en mis Cursos</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-arrow-up"></i>
                        <span><?= $metricas_docente['estudiantes_activos']; ?> activos</span>
                    </div>
                    <a href="<?= route('docente.estudiantes'); ?>" class="metric-action">
                        <i class="fas fa-users"></i>
                        Ver Estudiantes
                    </a>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon courses">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value"><?= $metricas_docente['cursos_creados']; ?></div>
                        <div class="metric-label">Cursos Publicados</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-check-circle"></i>
                        <span><?= $metricas_docente['cursos_activos']; ?> activos</span>
                    </div>
                    <a href="<?= route('docente.cursos'); ?>" class="metric-action">
                        <i class="fas fa-book-reader"></i>
                        Gestionar
                    </a>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon components">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value"><?= $metricas_docente['materiales_subidos']; ?></div>
                        <div class="metric-label">Materiales Subidos</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span><?= $metricas_docente['materiales_mes']; ?> este mes</span>
                    </div>
                    <a href="<?= route('docente.materiales'); ?>" class="metric-action">
                        <i class="fas fa-archive"></i>
                        Ver Materiales
                    </a>
                </div>
            </div>

            <!-- Segunda fila: Tareas, Evaluaciones, Progreso -->
            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon reports">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value"><?= $metricas_docente['tareas_pendientes']; ?></div>
                        <div class="metric-label">Tareas por Revisar</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-warning">
                        <i class="fas fa-clock"></i>
                        <span><?= $metricas_docente['tareas_urgentes']; ?> urgentes</span>
                    </div>
                    <a href="<?= route('docente.tareas.revision'); ?>" class="metric-action">
                        <i class="fas fa-edit"></i>
                        Revisar Ahora
                    </a>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon teachers">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value"><?= $metricas_docente['evaluaciones_creadas']; ?></div>
                        <div class="metric-label">Evaluaciones Creadas</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-star"></i>
                        <span><?= $metricas_docente['evaluaciones_activas']; ?> activas</span>
                    </div>
                    <a href="<?= route('docente.evaluaciones'); ?>" class="metric-action">
                        <i class="fas fa-chart-pie"></i>
                        Ver Resultados
                    </a>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon books">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value"><?= $metricas_docente['progreso_promedio']; ?>%</div>
                        <div class="metric-label">Progreso Promedio</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-trending-up"></i>
                        <span>+<?= $metricas_docente['mejora_promedio']; ?>% este mes</span>
                    </div>
                    <a href="<?= route('docente.progreso'); ?>" class="metric-action">
                        <i class="fas fa-analytics"></i>
                        Ver Detalles
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección 3: Actividad de Estudiantes y Estudiantes Conectados -->
    <div class="section-card">
        <div class="widgets-grid">

            <!-- Widget de Actividad de Estudiantes -->
            <div class="widget">
                <h3 class="widget-title">
                    <i class="fas fa-user-graduate"></i>
                    Actividad de mis Estudiantes
                </h3>

                <?php if (!empty($actividad_estudiantes)): ?>
                    <?php foreach ($actividad_estudiantes as $actividad): ?>
                        <div class="activity-item">
                            <div class="activity-icon" style="background: <?= $actividad['color'] ?? '#10b981'; ?>;">
                                <i class="fas fa-<?= $actividad['icono'] ?? 'user-graduate'; ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title"><?= htmlspecialchars($actividad['estudiante'] ?? 'Estudiante'); ?></div>
                                <div class="activity-description"><?= htmlspecialchars($actividad['accion'] ?? 'Realizó una actividad'); ?></div>
                            </div>
                            <div class="activity-time">Hace <?= tiempoTranscurrido($actividad['fecha'] ?? date('Y-m-d H:i:s')); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-info-circle"></i>
                        <p>No hay actividad reciente de estudiantes</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Widget de Estudiantes Conectados -->
            <div class="widget">
                <h3 class="widget-title">
                    <i class="fas fa-wifi"></i>
                    Estudiantes Conectados (<?= count($estudiantes_conectados); ?>)
                </h3>

                <?php if (!empty($estudiantes_conectados)): ?>
                    <?php foreach ($estudiantes_conectados as $estudiante): ?>
                        <div class="session-item">
                            <div class="session-user">
                                <div class="status-indicator"></div>
                                <div>
                                    <div class="session-name"><?= htmlspecialchars($estudiante['nombre'] ?? 'Estudiante'); ?></div>
                                    <div class="session-role"><?= htmlspecialchars($estudiante['curso_actual'] ?? 'Sin curso'); ?></div>
                                </div>
                            </div>
                            <div class="session-info">
                                <div class="session-time">Activo hace <?= tiempoTranscurrido($estudiante['ultima_actividad'] ?? date('Y-m-d H:i:s')); ?></div>
                                <div class="session-device"><?= htmlspecialchars($estudiante['leccion_actual'] ?? 'Navegando'); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-user-times"></i>
                        <p>No hay estudiantes conectados</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sección 4: Rendimiento de Cursos y Comentarios/Preguntas -->
    <div class="section-card">
        <div class="widgets-grid">

            <!-- Widget de Rendimiento de Cursos -->
            <div class="widget summary-widget">
                <h3 class="widget-title">
                    <i class="fas fa-chart-line"></i>
                    Rendimiento de mis Cursos
                    <a href="<?= route('docente.estadisticas'); ?>" class="widget-action">Ver estadísticas</a>
                </h3>

                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Calificación promedio</div>
                            <div class="summary-value"><?= number_format($rendimiento_cursos['calificacion_promedio'] ?? 4.5, 1); ?>/5</div>
                            <div class="summary-description">Valoración de estudiantes</div>
                        </div>
                        <div class="summary-badge trend-positive">Excelente</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #10b981, #047857);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Tasa de finalización</div>
                            <div class="summary-value"><?= $rendimiento_cursos['tasa_finalizacion'] ?? 85; ?>%</div>
                            <div class="summary-description">Estudiantes que completan cursos</div>
                        </div>
                        <div class="summary-badge trend-positive"><?= $rendimiento_cursos['tasa_finalizacion'] ?? 85; ?>%</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Visualizaciones totales</div>
                            <div class="summary-value"><?= number_format($rendimiento_cursos['visualizaciones'] ?? 2450); ?></div>
                            <div class="summary-description">Total de reproducciones</div>
                        </div>
                        <div class="summary-badge trend-positive">Popular</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Tiempo promedio</div>
                            <div class="summary-value"><?= $rendimiento_cursos['tiempo_promedio'] ?? 45; ?> min</div>
                            <div class="summary-description">Por lección completada</div>
                        </div>
                        <div class="summary-badge trend-warning">Tiempo</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Certificados emitidos</div>
                            <div class="summary-value"><?= $rendimiento_cursos['certificados'] ?? 123; ?></div>
                            <div class="summary-description">Estudiantes certificados</div>
                        </div>
                        <div class="summary-badge trend-positive">Logro</div>
                    </div>
                </div>
            </div>

            <!-- Widget de Comentarios y Preguntas -->
            <div class="widget sales-widget">
                <h3 class="widget-title">
                    <i class="fas fa-comments"></i>
                    Comentarios y Preguntas Recientes
                    <a href="<?= route('docente.comentarios'); ?>" class="widget-action">Ver todos</a>
                </h3>

                <div class="sales-scroll">
                    <?php if (!empty($comentarios_recientes)): ?>
                        <?php foreach ($comentarios_recientes as $comentario): ?>
                            <div class="sale-item">
                                <div class="sale-avatar">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                                <div class="sale-content">
                                    <div class="sale-customer"><?= htmlspecialchars($comentario['estudiante'] ?? 'Estudiante'); ?></div>
                                    <div class="sale-product"><?= htmlspecialchars($comentario['curso'] ?? 'Curso'); ?></div>
                                    <div class="sale-date">Hace <?= tiempoTranscurrido($comentario['fecha'] ?? date('Y-m-d H:i:s')); ?></div>
                                </div>
                                <div class="sale-details">
                                    <div class="sale-amount">
                                        <?php if ($comentario['tipo'] == 'pregunta'): ?>
                                            <i class="fas fa-question-circle text-warning"></i>
                                        <?php else: ?>
                                            <i class="fas fa-comment text-info"></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="sale-location"><?= htmlspecialchars($comentario['leccion'] ?? ''); ?></div>
                                    <div class="sale-status <?= strtolower($comentario['estado'] ?? 'pendiente'); ?>">
                                        <?= ucfirst($comentario['estado'] ?? 'Pendiente'); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-comments"></i>
                            <p>No hay comentarios recientes</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección 5: Mis Cursos Recientes -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-header-content">
                <h2 class="section-title">
                    <i class="fas fa-graduation-cap"></i>
                    Mis Cursos Recientes
                </h2>
                <p class="section-subtitle">Cursos de robótica y tecnología que has creado recientemente</p>
            </div>
            <div class="section-header-actions">
                <a href="<?= route('docente.cursos'); ?>" class="section-action-header">
                    <i class="fas fa-book-reader"></i>
                    Ver todos mis cursos
                </a>
            </div>
        </div>

        <div class="products-scroll">
            <div class="products-grid">
                <?php if (!empty($cursos_recientes)): ?>
                    <?php foreach ($cursos_recientes as $curso): ?>
                        <div class="product-card book-card">
                            <div class="product-image">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="product-content">
                                <div class="product-category"><?= htmlspecialchars($curso['categoria'] ?? 'Robótica'); ?></div>
                                <div class="product-title"><?= htmlspecialchars($curso['titulo'] ?? 'Curso de Robótica'); ?></div>
                                <div class="product-author">Nivel: <?= htmlspecialchars($curso['nivel'] ?? 'Intermedio'); ?></div>
                                <div class="product-price"><?= $curso['estudiantes_inscritos'] ?? 0; ?> estudiantes</div>
                                <div class="product-footer">
                                    <div class="product-stock"><?= $curso['lecciones'] ?? 0; ?> lecciones</div>
                                    <div class="product-status <?= strtolower(str_replace(' ', '-', $curso['estado'] ?? 'publicado')); ?>">
                                        <?= $curso['estado'] ?? 'Publicado'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-graduation-cap"></i>
                        <p>No has creado cursos recientemente</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sección 6: Materiales Subidos Recientemente -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-header-content">
                <h2 class="section-title">
                    <i class="fas fa-folder-open"></i>
                    Materiales Educativos Recientes
                </h2>
                <p class="section-subtitle">Últimos recursos, códigos y documentos que has compartido</p>
            </div>
            <div class="section-header-actions">
                <a href="<?= route('docente.materiales'); ?>" class="section-action-header">
                    <i class="fas fa-archive"></i>
                    Ver todos los materiales
                </a>
            </div>
        </div>

        <div class="products-scroll">
            <div class="products-grid">
                <?php if (!empty($materiales_recientes)): ?>
                    <?php foreach ($materiales_recientes as $material): ?>
                        <div class="product-card component-card">
                            <div class="product-image">
                                <i class="fas fa-<?= $material['icono'] ?? 'file-alt'; ?>"></i>
                            </div>
                            <div class="product-content">
                                <div class="product-category"><?= htmlspecialchars($material['tipo'] ?? 'Documento'); ?></div>
                                <div class="product-title"><?= htmlspecialchars($material['nombre'] ?? 'Material educativo'); ?></div>
                                <div class="product-code">Curso: <?= htmlspecialchars($material['curso'] ?? 'General'); ?></div>
                                <div class="product-price"><?= ($material['tamaño'] ?? 0) > 0 ? round($material['tamaño']/1024, 1) . ' KB' : 'N/A'; ?></div>
                                <div class="product-footer">
                                    <div class="product-stock"><?= $material['descargas'] ?? 0; ?> descargas</div>
                                    <div class="product-status <?= strtolower(str_replace(' ', '-', $material['estado'] ?? 'disponible')); ?>">
                                        <?= $material['estado'] ?? 'Disponible'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        <p>No has subido materiales recientemente</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>