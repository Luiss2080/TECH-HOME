<?php
// Vista para gestión de calificaciones
?>
<link rel="stylesheet" href="<?= asset('css/docente/docentes.css') ?>">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-star"></i>
                        <?= $title ?? 'Calificaciones' ?>
                    </h3>
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
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card course-card">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= htmlspecialchars($curso['titulo'] ?? 'Curso sin título') ?></h5>
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-users"></i> 
                                                    <?= $curso['estudiantes_inscritos'] ?? 0 ?> estudiantes
                                                </small><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-tag"></i> 
                                                    <?= htmlspecialchars($curso['categoria'] ?? 'Sin categoría') ?>
                                                </small><br>
                                                <small class="text-muted">
                                                    <i class="fas fa-level-up-alt"></i> 
                                                    <?= htmlspecialchars($curso['nivel'] ?? 'Sin nivel') ?>
                                                </small>
                                            </p>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?= route('docente.calificaciones.notas', ['id' => $curso['id']]) ?>" 
                                                   class="btn btn-primary">
                                                    <i class="fas fa-list"></i> Ver Notas
                                                </a>
                                                <button type="button" class="btn btn-success" 
                                                        onclick="abrirModalCalificar(<?= $curso['id'] ?>, '<?= htmlspecialchars($curso['titulo']) ?>')">
                                                    <i class="fas fa-star"></i> Calificar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state text-center py-4">
                            <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                            <h4>No hay cursos disponibles</h4>
                            <p class="text-muted">Aún no tienes cursos creados para calificar.</p>
                            <a href="<?= route('docente.cursos.crear') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Primer Curso
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Calificar -->
<div class="modal fade" id="modalCalificar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-star"></i> Calificar Estudiante
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= route('docente.calificar.estudiante') ?>" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="curso_nombre">Curso</label>
                        <input type="text" class="form-control" id="curso_nombre" readonly>
                        <input type="hidden" id="curso_id" name="curso_id">
                    </div>
                    
                    <div class="form-group">
                        <label for="estudiante_id">Estudiante *</label>
                        <select class="form-control" id="estudiante_id" name="estudiante_id" required>
                            <option value="">Seleccionar estudiante...</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="nota">Calificación * (0-100)</label>
                        <input type="number" class="form-control" id="nota" name="nota" 
                               min="0" max="100" step="0.1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Guardar Calificación
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirModalCalificar(cursoId, cursoTitulo) {
    document.getElementById('curso_id').value = cursoId;
    document.getElementById('curso_nombre').value = cursoTitulo;
    
    // Cargar estudiantes del curso via AJAX (implementar según necesidad)
    cargarEstudiantesCurso(cursoId);
    
    $('#modalCalificar').modal('show');
}

function cargarEstudiantesCurso(cursoId) {
    // Implementar llamada AJAX para obtener estudiantes del curso
    // Por ahora, datos de ejemplo
    const estudiantes = [
        {id: 1, nombre: 'María González'},
        {id: 2, nombre: 'Carlos Mendoza'},
        {id: 3, nombre: 'Ana Rodríguez'}
    ];
    
    const select = document.getElementById('estudiante_id');
    select.innerHTML = '<option value="">Seleccionar estudiante...</option>';
    
    estudiantes.forEach(est => {
        const option = document.createElement('option');
        option.value = est.id;
        option.textContent = est.nombre;
        select.appendChild(option);
    });
}
</script>