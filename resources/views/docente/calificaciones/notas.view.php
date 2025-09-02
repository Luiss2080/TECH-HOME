<?php
// Vista para ver notas de un curso específico
?>
<link rel="stylesheet" href="<?= asset('css/docente/docentes.css') ?>">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-list-alt"></i>
                        <?= $title ?? 'Notas del Curso' ?>
                    </h3>
                    <div>
                        <a href="<?= route('docente.calificaciones') ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="button" class="btn btn-success btn-sm" 
                                onclick="abrirModalCalificar(<?= $curso_id ?>, 'Curso Actual')">
                            <i class="fas fa-plus"></i> Nueva Calificación
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($notas)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Estudiante</th>
                                        <th>Email</th>
                                        <th>Calificación</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($notas as $nota): ?>
                                        <tr>
                                            <td>
                                                <strong><?= htmlspecialchars($nota['estudiante']) ?></strong>
                                            </td>
                                            <td><?= htmlspecialchars($nota['email']) ?></td>
                                            <td>
                                                <span class="badge badge-<?= getNotaBadgeColor($nota['nota']) ?> badge-lg">
                                                    <?= number_format($nota['nota'], 1) ?>/100
                                                </span>
                                            </td>
                                            <td>
                                                <?php 
                                                $estado = getEstadoNota($nota['nota']);
                                                $colorEstado = getEstadoColor($nota['nota']);
                                                ?>
                                                <span class="badge badge-<?= $colorEstado ?>">
                                                    <?= $estado ?>
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('d/m/Y H:i', strtotime($nota['fecha_calificacion'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-warning btn-sm"
                                                            onclick="editarNota(<?= $nota['estudiante_id'] ?>, <?= $nota['nota'] ?>, '<?= htmlspecialchars($nota['estudiante']) ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Estadísticas del curso -->
                        <div class="row mt-4">
                            <div class="col-md-3">
                                <div class="card bg-info">
                                    <div class="card-body text-center text-white">
                                        <h4><?= count($notas) ?></h4>
                                        <small>Estudiantes Calificados</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success">
                                    <div class="card-body text-center text-white">
                                        <h4><?= number_format(calcularPromedio($notas), 1) ?></h4>
                                        <small>Promedio General</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning">
                                    <div class="card-body text-center text-white">
                                        <h4><?= contarAprobados($notas) ?></h4>
                                        <small>Estudiantes Aprobados</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger">
                                    <div class="card-body text-center text-white">
                                        <h4><?= contarReprobados($notas) ?></h4>
                                        <small>Estudiantes Reprobados</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="empty-state text-center py-4">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <h4>No hay calificaciones registradas</h4>
                            <p class="text-muted">Aún no has calificado estudiantes en este curso.</p>
                            <button type="button" class="btn btn-primary"
                                    onclick="abrirModalCalificar(<?= $curso_id ?>, 'Curso Actual')">
                                <i class="fas fa-plus"></i> Agregar Primera Calificación
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Calificar/Editar -->
<div class="modal fade" id="modalCalificar" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-star"></i> <span id="modal-title">Calificar Estudiante</span>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?= route('docente.calificar.estudiante') ?>" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="curso_id" name="curso_id" value="<?= $curso_id ?>">
                    
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
                        <div class="mt-2">
                            <small class="text-muted">
                                <strong>Escala:</strong> 
                                0-59: Reprobado | 60-69: Suficiente | 70-79: Bueno | 80-89: Notable | 90-100: Excelente
                            </small>
                        </div>
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

<?php
// Funciones auxiliares para la vista
function getNotaBadgeColor($nota) {
    if ($nota >= 90) return 'success';
    if ($nota >= 80) return 'info';
    if ($nota >= 70) return 'warning';
    if ($nota >= 60) return 'secondary';
    return 'danger';
}

function getEstadoNota($nota) {
    if ($nota >= 90) return 'Excelente';
    if ($nota >= 80) return 'Notable';
    if ($nota >= 70) return 'Bueno';
    if ($nota >= 60) return 'Suficiente';
    return 'Reprobado';
}

function getEstadoColor($nota) {
    if ($nota >= 60) return 'success';
    return 'danger';
}

function calcularPromedio($notas) {
    if (empty($notas)) return 0;
    $suma = array_sum(array_column($notas, 'nota'));
    return $suma / count($notas);
}

function contarAprobados($notas) {
    return count(array_filter($notas, fn($n) => $n['nota'] >= 60));
}

function contarReprobados($notas) {
    return count(array_filter($notas, fn($n) => $n['nota'] < 60));
}
?>

<script>
function abrirModalCalificar(cursoId, cursoTitulo, estudianteId = null, notaActual = null) {
    document.getElementById('curso_id').value = cursoId;
    
    if (estudianteId && notaActual) {
        // Modo edición
        document.getElementById('modal-title').textContent = 'Editar Calificación';
        document.getElementById('estudiante_id').value = estudianteId;
        document.getElementById('nota').value = notaActual;
        // Deshabilitar selector de estudiante en modo edición
        document.getElementById('estudiante_id').disabled = true;
    } else {
        // Modo nueva calificación
        document.getElementById('modal-title').textContent = 'Calificar Estudiante';
        document.getElementById('estudiante_id').disabled = false;
        document.getElementById('nota').value = '';
    }
    
    cargarEstudiantesCurso(cursoId);
    $('#modalCalificar').modal('show');
}

function editarNota(estudianteId, nota, nombreEstudiante) {
    abrirModalCalificar(<?= $curso_id ?>, 'Curso Actual', estudianteId, nota);
}

function cargarEstudiantesCurso(cursoId) {
    // Implementar llamada AJAX real
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