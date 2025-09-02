<div class="container-fluid">
    <div class="row">
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-graduation-cap text-success"></i> 
                    Cursando: <?= htmlspecialchars($curso['titulo']) ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= route('cursos.ver', ['id' => $curso['id']]) ?>" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-eye"></i> Ver Detalles
                    </a>
                    <a href="<?= route('cursos') ?>" class="btn btn-outline-secondary btn-sm ms-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Alertas -->
            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $type => $message): ?>
                    <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <!-- Información de estado del curso -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-trophy"></i> 
                                Estado de tu Progreso
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold">Progreso completado:</span>
                                            <span class="badge bg-primary fs-6"><?= $estadisticas['progreso_porcentaje'] ?>%</span>
                                        </div>
                                        <div class="progress" style="height: 15px;">
                                            <div class="progress-bar bg-gradient progress-bar-striped progress-bar-animated" 
                                                 role="progressbar" 
                                                 style="width: <?= $estadisticas['progreso_porcentaje'] ?>%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <?php if ($estadisticas['completado']): ?>
                                        <div class="text-center">
                                            <span class="badge bg-success fs-6 p-3">
                                                <i class="fas fa-check-circle"></i> COMPLETADO
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center">
                                            <span class="badge bg-warning fs-6 p-3">
                                                <i class="fas fa-play"></i> EN PROGRESO
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <hr>
                            
                            <!-- Estadísticas rápidas -->
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <h6 class="card-title text-primary">
                                                <i class="fas fa-clock"></i> Tiempo
                                            </h6>
                                            <p class="card-text fw-bold"><?= $estadisticas['tiempo_formateado'] ?></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <h6 class="card-title text-info">
                                                <i class="fas fa-calendar"></i> Días
                                            </h6>
                                            <p class="card-text fw-bold"><?= $estadisticas['dias_inscritos'] ?> días</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <h6 class="card-title text-warning">
                                                <i class="fas fa-star"></i> Promedio
                                            </h6>
                                            <p class="card-text fw-bold">
                                                <?= $estadisticas['promedio_notas'] ? number_format($estadisticas['promedio_notas'], 1) : 'N/A' ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-light">
                                        <div class="card-body py-2">
                                            <h6 class="card-title text-success">
                                                <i class="fas fa-user-check"></i> Estado
                                            </h6>
                                            <p class="card-text fw-bold">Inscrito</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Información principal del curso -->
                <div class="col-lg-8">
                    <!-- Video del curso -->
                    <?php if (!empty($curso['video_url'])): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-play-circle text-danger"></i> 
                                    Video del Curso
                                </h5>
                            </div>
                            <div class="card-body p-0">
                                <?php if ($curso['video_info']['es_youtube']): ?>
                                    <div class="ratio ratio-16x9">
                                        <iframe 
                                            src="<?= $curso['video_info']['embed_url'] ?>" 
                                            title="<?= htmlspecialchars($curso['titulo']) ?>"
                                            allowfullscreen>
                                        </iframe>
                                    </div>
                                <?php else: ?>
                                    <div class="ratio ratio-16x9">
                                        <video controls>
                                            <source src="<?= htmlspecialchars($curso['video_url']) ?>" type="video/mp4">
                                            Tu navegador no soporta el elemento video.
                                        </video>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Descripción del curso -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle text-info"></i> 
                                Descripción del curso
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="card-text"><?= nl2br(htmlspecialchars($curso['descripcion'])) ?></p>
                        </div>
                    </div>

                    <!-- Objetivos del curso -->
                    <?php if (!empty($curso['objetivos'])): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-bullseye text-success"></i> 
                                    Objetivos del curso
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><?= nl2br(htmlspecialchars($curso['objetivos'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Requisitos -->
                    <?php if (!empty($curso['requisitos'])): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-list-check text-warning"></i> 
                                    Requisitos
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="card-text"><?= nl2br(htmlspecialchars($curso['requisitos'])) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Acciones de progreso -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-tasks text-primary"></i> 
                                Continuar Estudiando
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-primary w-100 mb-2" onclick="actualizarProgreso(25)">
                                        <i class="fas fa-play"></i> Avanzar 25%
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-primary w-100 mb-2" onclick="actualizarProgreso(50)">
                                        <i class="fas fa-forward"></i> Avanzar 50%
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-primary w-100 mb-2" onclick="actualizarProgreso(75)">
                                        <i class="fas fa-fast-forward"></i> Avanzar 75%
                                    </button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-success w-100 mb-2" onclick="actualizarProgreso(100)">
                                        <i class="fas fa-check"></i> Completar Curso
                                    </button>
                                </div>
                            </div>
                            
                            <?php if ($estadisticas['fecha_ultimo_acceso']): ?>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-clock"></i>
                                <strong>Último acceso:</strong> <?= date('d/m/Y H:i', strtotime($estadisticas['fecha_ultimo_acceso'])) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Información del docente -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-user-tie text-primary"></i> 
                                Docente
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white fw-bold" 
                                         style="width: 50px; height: 50px; font-size: 1.2rem;">
                                        <?= strtoupper(substr($curso['docente']['nombre'] ?? 'D', 0, 1)) ?>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-1">
                                        <?= htmlspecialchars($curso['docente']['nombre'] ?? 'Sin nombre') ?> 
                                        <?= htmlspecialchars($curso['docente']['apellido'] ?? '') ?>
                                    </h6>
                                    <small class="text-muted"><?= htmlspecialchars($curso['docente']['email'] ?? '') ?></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles del curso -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info text-info"></i> 
                                Detalles
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-tag text-primary"></i>
                                    <strong>Categoría:</strong> 
                                    <span class="badge bg-secondary"><?= htmlspecialchars($curso['categoria']['nombre'] ?? 'Sin categoría') ?></span>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-signal text-warning"></i>
                                    <strong>Nivel:</strong> 
                                    <span class="badge bg-<?= $curso['nivel'] === 'Principiante' ? 'success' : ($curso['nivel'] === 'Intermedio' ? 'warning' : 'danger') ?>">
                                        <?= htmlspecialchars($curso['nivel']) ?>
                                    </span>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock text-info"></i>
                                    <strong>Duración:</strong> <?= htmlspecialchars($curso['duracion_horas']) ?> horas
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-money-bill text-success"></i>
                                    <strong>Precio:</strong> 
                                    <?php if ($curso['es_gratuito']): ?>
                                        <span class="badge bg-success">GRATUITO</span>
                                    <?php else: ?>
                                        Bs. <?= number_format($curso['precio'], 2) ?>
                                    <?php endif; ?>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calendar text-primary"></i>
                                    <strong>Inscrito:</strong> <?= date('d/m/Y', strtotime($estadisticas['fecha_inicio'] ?? 'now')) ?>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Calificaciones -->
                    <?php if (!empty($estadisticas['notas'])): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-star text-warning"></i> 
                                Mis Calificaciones
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($estadisticas['notas'] as $nota): ?>
                            <div class="d-flex justify-content-between align-items-center p-2 mb-2 bg-light rounded">
                                <div>
                                    <span class="fw-bold text-success"><?= number_format($nota['nota'], 1) ?>/100</span>
                                </div>
                                <small class="text-muted">
                                    <?= date('d/m/Y', strtotime($nota['fecha_calificacion'])) ?>
                                </small>
                            </div>
                            <?php endforeach; ?>
                            
                            <?php if ($estadisticas['promedio_notas']): ?>
                            <hr>
                            <div class="text-center">
                                <strong>Promedio: </strong>
                                <span class="badge bg-primary fs-6">
                                    <?= number_format($estadisticas['promedio_notas'], 1) ?>/100
                                </span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Otros cursos -->
                    <?php if (!empty($otrosCursos)): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-book text-primary"></i> 
                                Otros Cursos
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($otrosCursos as $otroCurso): ?>
                            <div class="d-flex align-items-center mb-3 p-2 border rounded hover-card" 
                                 style="transition: all 0.3s ease; cursor: pointer;"
                                 onclick="window.location.href='<?= route('cursos.cursando', ['id' => $otroCurso['id']]) ?>'">
                                <div class="me-3">
                                    <?php if (!empty($otroCurso['imagen'])): ?>
                                        <img src="<?= asset('imagenes/cursos/' . $otroCurso['imagen']) ?>" 
                                             alt="<?= htmlspecialchars($otroCurso['titulo']) ?>" 
                                             class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-primary rounded d-flex align-items-center justify-content-center text-white fw-bold" 
                                             style="width: 50px; height: 50px; font-size: 0.8rem;">
                                            <?= strtoupper(substr($otroCurso['titulo'], 0, 2)) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-truncate"><?= htmlspecialchars($otroCurso['titulo']) ?></h6>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar" style="width: <?= $otroCurso['progreso_porcentaje'] ?? 0 ?>%"></div>
                                    </div>
                                    <small class="text-muted"><?= $otroCurso['progreso_porcentaje'] ?? 0 ?>% completado</small>
                                </div>
                                <div class="ms-2">
                                    <i class="fas fa-chevron-right text-muted"></i>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="d-grid mt-3">
                                <a href="<?= route('cursos.catalogo') ?>" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i> Explorar más cursos
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>

<!-- Scripts específicos para cursando -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Efecto hover para las tarjetas
    const hoverCards = document.querySelectorAll('.hover-card');
    hoverCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
            this.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
            this.style.boxShadow = 'none';
        });
    });
    
    // Actualizar último acceso cada 5 minutos
    setInterval(actualizarUltimoAcceso, 300000);
    
    // Llamar al actualizar último acceso al cargar la página
    actualizarUltimoAcceso();
});

/**
 * Actualizar progreso del estudiante
 */
function actualizarProgreso(nuevoProgreso) {
    const porcentajeActual = <?= $estadisticas['progreso_porcentaje'] ?>;
    
    if (porcentajeActual >= 100) {
        Swal.fire({
            title: '¡Felicidades!',
            text: 'Ya has completado este curso al 100%',
            icon: 'success',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    // Asegurar que el progreso no baje
    const progresoFinal = Math.max(porcentajeActual, nuevoProgreso);
    
    fetch(`<?= route('cursos.actualizar-progreso', ['id' => $curso['id']]) ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            progreso: progresoFinal,
            tiempo_adicional: 15 // 15 minutos adicionales
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Actualizar visualmente
            const progressBars = document.querySelectorAll('.progress-bar');
            progressBars.forEach(bar => {
                bar.style.width = progresoFinal + '%';
            });
            
            const badges = document.querySelectorAll('.badge:contains("%")');
            badges.forEach(badge => {
                if (badge.textContent.includes('%')) {
                    badge.textContent = progresoFinal + '%';
                }
            });
            
            // Mostrar mensaje de éxito
            Swal.fire({
                title: '¡Progreso actualizado!',
                text: `Ahora tienes ${progresoFinal}% completado`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
            
            // Si completó el curso
            if (progresoFinal >= 100) {
                setTimeout(() => {
                    Swal.fire({
                        title: '🎉 ¡Curso Completado!',
                        text: 'Has terminado exitosamente este curso',
                        icon: 'success',
                        confirmButtonText: 'Ver más cursos',
                        showCancelButton: true,
                        cancelButtonText: 'Quedarme aquí'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '<?= route('cursos.catalogo') ?>';
                        } else {
                            // Recargar la página para mostrar el estado completado
                            window.location.reload();
                        }
                    });
                }, 2500);
            }
        } else {
            Swal.fire({
                title: 'Error',
                text: data.error || 'No se pudo actualizar el progreso',
                icon: 'error'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor',
            icon: 'error'
        });
    });
}

/**
 * Actualizar último acceso
 */
function actualizarUltimoAcceso() {
    fetch(`<?= route('cursos.actualizar-progreso', ['id' => $curso['id']]) ?>`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            solo_acceso: true
        })
    }).catch(error => {
        console.log('Error al actualizar último acceso:', error);
    });
}
</script>

<style>
/* Estilos adicionales para cursando */
.progress-sm {
    height: 6px;
}

.hover-card:hover {
    background-color: rgba(0,123,255,0.1) !important;
}

.card-header {
    background-color: rgba(0,123,255,0.1);
    border-bottom: 1px solid rgba(0,123,255,0.2);
}

.bg-gradient {
    background: linear-gradient(90deg, #007bff, #0056b3) !important;
}

/* Mejorar la apariencia de las progress bars */
.progress {
    background-color: rgba(0,123,255,0.1);
}

.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}

/* Efecto de las tarjetas al hover */
.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
