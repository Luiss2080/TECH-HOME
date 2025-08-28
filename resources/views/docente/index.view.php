<?php
// Vista principal para docentes - puede mostrar cursos o gestión de docentes según el contexto
?>
<link rel="stylesheet" href="<?= asset('css/docente/docentes.css') ?>">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-book"></i>
                        <?= $title ?? 'Mis Cursos' ?>
                    </h3>
                    <a href="<?= route('docente.cursos.crear') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nuevo Curso
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($cursos)): ?>
                        <div class="row">
                            <?php foreach ($cursos as $curso): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card course-card">
                                        <div class="card-body">
                                            <h5 class="card-title">
                                                <?= htmlspecialchars($curso['nombre'] ?? 'Curso sin nombre') ?>
                                            </h5>
                                            <p class="card-text">
                                                <?= htmlspecialchars($curso['descripcion'] ?? 'Sin descripción') ?>
                                            </p>
                                            <div class="course-stats">
                                                <small class="text-muted">
                                                    <i class="fas fa-users"></i>
                                                    <?= $curso['estudiantes'] ?? 0 ?> estudiantes
                                                </small>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock"></i>
                                                    <?= $curso['duracion'] ?? 'N/A' ?> horas
                                                </small>
                                            </div>
                                            <div class="mt-3">
                                                <span class="badge badge-<?= strtolower($curso['estado'] ?? 'draft') == 'published' ? 'success' : 'warning' ?>">
                                                    <?= $curso['estado'] ?? 'Borrador' ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Ver
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-edit"></i> Editar
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-chart-bar"></i> Stats
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state text-center py-4">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h4>No tienes cursos creados</h4>
                            <p class="text-muted">Comienza creando tu primer curso para empezar a enseñar.</p>
                            <a href="<?= route('docente.cursos.crear') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear mi primer curso
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.course-card {
    transition: transform 0.2s ease-in-out;
    border: 1px solid #dee2e6;
}

.course-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.course-stats {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.course-stats small {
    display: flex;
    align-items: center;
    gap: 5px;
}

.empty-state {
    padding: 3rem 0;
}
</style>
