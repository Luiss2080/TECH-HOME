<div class="container-fluid">
    <div class="row">
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-play-circle text-primary"></i> 
                    <?= htmlspecialchars($curso['titulo']) ?>
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="<?= route('cursos') ?>" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <?php if ($puedeEditar): ?>
                        <a href="<?= route('cursos.editar', ['id' => $curso['id']]) ?>" class="btn btn-primary btn-sm ms-2">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    <?php endif; ?>
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

            <div class="row">
                <!-- Información principal del curso -->
                <div class="col-lg-8">
                    <!-- Video del curso -->
                    <?php if (!empty($curso['video_url'])): ?>
                        <div class="card mb-4">
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

                    <!-- Progreso del estudiante (solo si está inscrito) -->
                    <?php if ($estaInscrito && $progreso): ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-chart-line text-success"></i> 
                                    Tu progreso
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Progreso completado:</label>
                                            <div class="progress">
                                                <div class="progress-bar bg-success" 
                                                     role="progressbar" 
                                                     style="width: <?= $progreso['progreso_porcentaje'] ?>%">
                                                    <?= $progreso['progreso_porcentaje'] ?>%
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Tiempo estudiado:</label>
                                            <p class="text-muted">
                                                <?= floor($progreso['tiempo_estudiado'] / 60) ?> horas 
                                                <?= $progreso['tiempo_estudiado'] % 60 ?> minutos
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <?php if ($progreso['completado']): ?>
                                    <div class="alert alert-success">
                                        <i class="fas fa-trophy"></i> 
                                        ¡Felicitaciones! Has completado este curso.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Barra lateral con información del curso -->
                <div class="col-lg-4">
                    <!-- Card de información del curso -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle text-primary"></i> 
                                Información del curso
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-3">
                                    <strong>
                                        <i class="fas fa-user-tie text-secondary"></i> 
                                        Docente:
                                    </strong><br>
                                    <span class="text-muted"><?= htmlspecialchars($curso['docente']['nombre']) ?></span>
                                </li>
                                
                                <li class="mb-3">
                                    <strong>
                                        <i class="fas fa-folder text-secondary"></i> 
                                        Categoría:
                                    </strong><br>
                                    <span class="text-muted"><?= htmlspecialchars($curso['categoria']['nombre']) ?></span>
                                </li>
                                
                                <li class="mb-3">
                                    <strong>
                                        <i class="fas fa-chart-bar text-secondary"></i> 
                                        Nivel:
                                    </strong><br>
                                    <span class="badge bg-<?= $curso['nivel'] == 'Principiante' ? 'success' : ($curso['nivel'] == 'Intermedio' ? 'warning' : 'danger') ?>">
                                        <?= htmlspecialchars($curso['nivel']) ?>
                                    </span>
                                </li>
                                
                                <li class="mb-3">
                                    <strong>
                                        <i class="fas fa-toggle-on text-secondary"></i> 
                                        Estado:
                                    </strong><br>
                                    <span class="badge bg-<?= $curso['estado'] == 'Publicado' ? 'success' : ($curso['estado'] == 'Borrador' ? 'warning' : 'secondary') ?>">
                                        <?= htmlspecialchars($curso['estado']) ?>
                                    </span>
                                </li>
                                
                                <li class="mb-3">
                                    <strong>
                                        <i class="fas fa-money-bill-wave text-secondary"></i> 
                                        Tipo:
                                    </strong><br>
                                    <span class="badge bg-<?= $curso['es_gratuito'] ? 'success' : 'primary' ?>">
                                        <?= $curso['es_gratuito'] ? 'Gratuito' : 'De pago' ?>
                                    </span>
                                </li>
                                
                                <li>
                                    <strong>
                                        <i class="fas fa-calendar text-secondary"></i> 
                                        Creado:
                                    </strong><br>
                                    <span class="text-muted">
                                        <?= date('d/m/Y', strtotime($curso['fecha_creacion'])) ?>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Acciones del estudiante -->
                    <?php if (auth() && auth()->hasRole('estudiante')): ?>
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-play text-primary"></i> 
                                    Acciones
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if (!$estaInscrito && $puedeInscribirse): ?>
                                    <?php if ($curso['es_gratuito']): ?>
                                        <form method="POST" action="<?= route('cursos.inscribir', ['id' => $curso['id']]) ?>">
                                            <button type="submit" class="btn btn-success w-100 mb-2">
                                                <i class="fas fa-user-plus"></i> 
                                                Inscribirme gratis
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-primary w-100 mb-2" onclick="alert('Los cursos de pago estarán disponibles pronto.')">
                                            <i class="fas fa-credit-card"></i> 
                                            Comprar curso
                                        </button>
                                    <?php endif; ?>
                                <?php elseif ($estaInscrito): ?>
                                    <div class="alert alert-success text-center mb-3">
                                        <i class="fas fa-check-circle"></i><br>
                                        Ya estás inscrito en este curso
                                    </div>
                                    
                                    <!-- Botón para ir a la vista de cursando -->
                                    <div class="d-grid gap-2 mb-3">
                                        <a href="<?= route('cursos.cursando', ['id' => $curso['id']]) ?>" 
                                           class="btn btn-primary btn-lg">
                                            <i class="fas fa-play me-2"></i>Continuar Cursando
                                        </a>
                                    </div>
                                    
                                    <!-- Botones para actualizar progreso -->
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-outline-primary btn-sm" onclick="actualizarProgreso(25)">
                                            Marcar 25% completado
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="actualizarProgreso(50)">
                                            Marcar 50% completado
                                        </button>
                                        <button class="btn btn-outline-primary btn-sm" onclick="actualizarProgreso(75)">
                                            Marcar 75% completado
                                        </button>
                                        <button class="btn btn-success btn-sm" onclick="actualizarProgreso(100)">
                                            Marcar como completado
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning text-center">
                                        <i class="fas fa-lock"></i><br>
                                        Curso no disponible para inscripción
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif (!auth()): ?>
                        <div class="card">
                            <div class="card-body text-center">
                                <p class="text-muted">Inicia sesión para inscribirte a este curso</p>
                                <a href="<?= route('login') ?>" class="btn btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> 
                                    Iniciar sesión
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
function actualizarProgreso(porcentaje) {
    // Simular tiempo estudiado basado en el progreso
    const tiempoEstudiado = Math.floor(porcentaje * 2.4); // Aproximadamente 240 minutos para 100%
    
    fetch('<?= route('cursos.actualizar-progreso', ['id' => $curso['id']]) ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            progreso: porcentaje,
            tiempo_estudiado: tiempoEstudiado
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Recargar la página para ver los cambios
            location.reload();
        } else {
            alert('Error al actualizar progreso: ' + (data.error || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al actualizar progreso');
    });
}
</script>
