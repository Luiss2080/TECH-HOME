<?php
$title = $title ?? 'Ver Curso';
$curso = $curso ?? [];
?>

<!-- Estilos específicos -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">

<style>
/* ============================================
   ESTILOS PARA VER CURSO DOCENTE
   ============================================ */

.curso-header {
    background: linear-gradient(135deg, var(--primary-color), #1e40af);
    color: white;
    padding: 2rem;
    border-radius: var(--border-radius-lg);
    margin-bottom: 2rem;
}

.curso-content {
    background: var(--background-card);
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-medium);
    margin-bottom: 2rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.info-item:last-child {
    border-bottom: none;
}

.curso-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}

.stat-card {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: var(--border-radius-md);
    padding: 1.5rem;
    text-align: center;
}

.badge-estado {
    padding: 0.5rem 1rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.875rem;
}

.estado-borrador { background: #fbbf24; color: #92400e; }
.estado-publicado { background: #34d399; color: #065f46; }
.estado-archivado { background: #94a3b8; color: #475569; }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">
                <i class="fas fa-eye me-2"></i>
                Ver Curso
            </h1>
            <p class="text-muted">Detalles del curso seleccionado</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= route('docente.cursos.editar', $curso['id'] ?? 0) ?>" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>
                Editar
            </a>
            <a href="<?= route('docente.cursos') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- Alertas -->
    <?php if (Session::hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?= Session::flashGet('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (Session::hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= Session::flashGet('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($curso)): ?>
    <!-- Header del Curso -->
    <div class="curso-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2"><?= htmlspecialchars($curso['titulo']) ?></h2>
                <p class="mb-3 opacity-75"><?= htmlspecialchars($curso['descripcion']) ?></p>
                
                <div class="d-flex flex-wrap gap-3 align-items-center">
                    <span class="badge-estado estado-<?= strtolower($curso['estado']) ?>">
                        <?= htmlspecialchars($curso['estado']) ?>
                    </span>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-layer-group me-1"></i>
                        <?= htmlspecialchars($curso['nivel']) ?>
                    </span>
                    <?php if (isset($curso['categoria_nombre'])): ?>
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-tag me-1"></i>
                        <?= htmlspecialchars($curso['categoria_nombre']) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-4">
                <!-- Estadísticas rápidas -->
                <div class="curso-stats">
                    <div class="stat-card">
                        <div class="h4 mb-1"><?= $curso['total_estudiantes'] ?? '0' ?></div>
                        <div class="small opacity-75">Estudiantes</div>
                    </div>
                    <div class="stat-card">
                        <div class="h4 mb-1"><?= $curso['progreso_promedio'] ?? '0' ?>%</div>
                        <div class="small opacity-75">Progreso Promedio</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="row">
        <!-- Información General -->
        <div class="col-lg-6">
            <div class="curso-content">
                <h5 class="mb-3">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Información General
                </h5>
                
                <div class="info-item">
                    <span><i class="fas fa-calendar me-2"></i>Fecha de Creación:</span>
                    <span><?= date('d/m/Y H:i', strtotime($curso['fecha_creacion'])) ?></span>
                </div>
                
                <div class="info-item">
                    <span><i class="fas fa-edit me-2"></i>Última Actualización:</span>
                    <span><?= date('d/m/Y H:i', strtotime($curso['fecha_actualizacion'])) ?></span>
                </div>
                
                <?php if (isset($curso['duracion_horas']) && $curso['duracion_horas'] > 0): ?>
                <div class="info-item">
                    <span><i class="fas fa-clock me-2"></i>Duración:</span>
                    <span><?= $curso['duracion_horas'] ?> horas</span>
                </div>
                <?php endif; ?>
                
                <?php if (isset($curso['precio'])): ?>
                <div class="info-item">
                    <span><i class="fas fa-dollar-sign me-2"></i>Precio:</span>
                    <span>
                        <?php if ($curso['es_gratuito'] ?? true): ?>
                            <span class="badge bg-success">Gratuito</span>
                        <?php else: ?>
                            Bs. <?= number_format($curso['precio'], 2) ?>
                        <?php endif; ?>
                    </span>
                </div>
                <?php endif; ?>
                
                <div class="info-item">
                    <span><i class="fas fa-globe me-2"></i>Estado:</span>
                    <span class="badge-estado estado-<?= strtolower($curso['estado']) ?>">
                        <?= htmlspecialchars($curso['estado']) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="col-lg-6">
            <div class="curso-content">
                <h5 class="mb-3">
                    <i class="fas fa-chart-bar me-2 text-primary"></i>
                    Estadísticas del Curso
                </h5>
                
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <div class="h3 mb-1 text-primary"><?= $curso['total_inscripciones'] ?? '0' ?></div>
                            <div class="small text-muted">Inscripciones Totales</div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="p-3 bg-light rounded">
                            <div class="h3 mb-1 text-success"><?= $curso['estudiantes_activos'] ?? '0' ?></div>
                            <div class="small text-muted">Estudiantes Activos</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded">
                            <div class="h3 mb-1 text-warning"><?= $curso['calificacion_promedio'] ?? '0.0' ?></div>
                            <div class="small text-muted">Calificación Promedio</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded">
                            <div class="h3 mb-1 text-info"><?= $curso['completados'] ?? '0' ?></div>
                            <div class="small text-muted">Completados</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido del Curso -->
    <?php if (!empty($curso['contenido'])): ?>
    <div class="curso-content">
        <h5 class="mb-3">
            <i class="fas fa-file-alt me-2 text-primary"></i>
            Contenido del Curso
        </h5>
        <div class="content-preview">
            <?= nl2br(htmlspecialchars($curso['contenido'])) ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Video del Curso -->
    <?php if (!empty($curso['video_url'])): ?>
    <div class="curso-content">
        <h5 class="mb-3">
            <i class="fas fa-play-circle me-2 text-primary"></i>
            Video del Curso
        </h5>
        <div class="ratio ratio-16x9">
            <?php
            // Convertir URL de YouTube a formato embed
            $videoUrl = $curso['video_url'];
            if (strpos($videoUrl, 'youtube.com/watch?v=') !== false) {
                $videoId = substr($videoUrl, strpos($videoUrl, 'v=') + 2);
                $videoUrl = "https://www.youtube.com/embed/" . $videoId;
            } elseif (strpos($videoUrl, 'youtu.be/') !== false) {
                $videoId = substr($videoUrl, strrpos($videoUrl, '/') + 1);
                $videoUrl = "https://www.youtube.com/embed/" . $videoId;
            }
            ?>
            <iframe src="<?= htmlspecialchars($videoUrl) ?>" 
                    title="Video del curso" 
                    allowfullscreen></iframe>
        </div>
    </div>
    <?php endif; ?>

    <!-- Acciones Rápidas -->
    <div class="curso-content">
        <h5 class="mb-3">
            <i class="fas fa-cogs me-2 text-primary"></i>
            Acciones Rápidas
        </h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="<?= route('docente.cursos.editar', $curso['id']) ?>" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>
                Editar Curso
            </a>
            
            <?php if ($curso['estado'] === 'Borrador'): ?>
            <button class="btn btn-success" onclick="cambiarEstado(<?= $curso['id'] ?>, 'Publicado')">
                <i class="fas fa-upload me-2"></i>
                Publicar
            </button>
            <?php endif; ?>
            
            <?php if ($curso['estado'] === 'Publicado'): ?>
            <button class="btn btn-secondary" onclick="cambiarEstado(<?= $curso['id'] ?>, 'Archivado')">
                <i class="fas fa-archive me-2"></i>
                Archivar
            </button>
            <?php endif; ?>
            
            <button class="btn btn-danger" onclick="eliminarCurso(<?= $curso['id'] ?>)">
                <i class="fas fa-trash me-2"></i>
                Eliminar
            </button>
        </div>
    </div>

    <?php else: ?>
    <!-- Mensaje si no hay curso -->
    <div class="curso-content text-center">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h4>Curso no encontrado</h4>
        <p class="text-muted">El curso que buscas no existe o no tienes permisos para verlo.</p>
        <a href="<?= route('docente.cursos') ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>
            Volver a Mis Cursos
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
function cambiarEstado(cursoId, nuevoEstado) {
    if (confirm(`¿Estás seguro de que quieres cambiar el estado del curso a "${nuevoEstado}"?`)) {
        // Aquí podrías implementar una llamada AJAX
        window.location.href = `/docente/cursos/${cursoId}/estado?estado=${nuevoEstado}`;
    }
}

function eliminarCurso(cursoId) {
    if (confirm('⚠️ ¿Estás seguro de que quieres eliminar este curso?\n\nEsta acción no se puede deshacer y se perderán todos los datos asociados.')) {
        // Crear formulario para DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/docente/cursos/${cursoId}`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>