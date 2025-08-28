<?php
// Vista para gestión de estudiantes - puede ser usado por docentes o administradores
?>
<link rel="stylesheet" href="<?= asset('css/docente/docentes.css') ?>">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i>
                        <?= $title ?? 'Mis Estudiantes' ?>
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($estudiantes)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Curso</th>
                                        <th>Progreso</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($estudiantes as $estudiante): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($estudiante['nombre'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($estudiante['email'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($estudiante['curso'] ?? 'N/A') ?></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" role="progressbar" 
                                                         style="width: <?= $estudiante['progreso'] ?? 0 ?>%">
                                                        <?= $estudiante['progreso'] ?? 0 ?>%
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= ($estudiante['activo'] ?? false) ? 'success' : 'secondary' ?>">
                                                    <?= ($estudiante['activo'] ?? false) ? 'Activo' : 'Inactivo' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i> Ver
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i> Evaluar
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="empty-state text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h4>No hay estudiantes registrados</h4>
                            <p class="text-muted">Aún no tienes estudiantes asignados a tus cursos.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
