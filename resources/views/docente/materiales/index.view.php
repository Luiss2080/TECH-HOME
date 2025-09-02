<?php
// Vista para gestión de materiales del docente
?>
<link rel="stylesheet" href="<?= asset('css/docente/docentes.css') ?>">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-folder-open"></i>
                        <?= $title ?? 'Mis Materiales' ?>
                    </h3>
                    <a href="<?= route('docente.materiales.subir') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Subir Material
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($materiales)): ?>
                        <div class="row">
                            <?php foreach ($materiales as $material): ?>
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card material-card h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="badge badge-<?= getTipoBadgeColor($material['tipo']) ?>">
                                                    <?= strtoupper($material['tipo']) ?>
                                                </span>
                                                <div class="dropdown">
                                                    <button class="btn btn-link btn-sm" type="button" data-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="#">
                                                            <i class="fas fa-eye"></i> Ver
                                                        </a>
                                                        <a class="dropdown-item" href="#">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="#" 
                                                           onclick="confirmarEliminar(<?= $material['id'] ?>, '<?= htmlspecialchars($material['nombre']) ?>')">
                                                            <i class="fas fa-trash"></i> Eliminar
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <h5 class="card-title" title="<?= htmlspecialchars($material['nombre']) ?>">
                                                <?= truncateText($material['nombre'], 40) ?>
                                            </h5>
                                            
                                            <p class="card-text text-muted small mb-2">
                                                <i class="fas fa-tag"></i> <?= htmlspecialchars($material['categoria'] ?? 'Sin categoría') ?>
                                            </p>
                                            
                                            <div class="material-stats">
                                                <div class="d-flex justify-content-between text-muted small">
                                                    <span>
                                                        <i class="fas fa-download"></i> 
                                                        <?= $material['descargas'] ?? 0 ?> descargas
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-hdd"></i> 
                                                        <?= formatFileSize($material['tamaño']) ?>
                                                    </span>
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    <i class="fas fa-calendar"></i> 
                                                    <?= date('d/m/Y', strtotime($material['fecha_creacion'])) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-transparent">
                                            <div class="btn-group btn-group-sm w-100" role="group">
                                                <button type="button" class="btn btn-outline-primary">
                                                    <i class="fas fa-eye"></i> Ver
                                                </button>
                                                <button type="button" class="btn btn-outline-secondary">
                                                    <i class="fas fa-download"></i> Descargar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Resumen de estadísticas -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-md-3">
                                                <h4 class="text-primary"><?= count($materiales) ?></h4>
                                                <small class="text-muted">Total Materiales</small>
                                            </div>
                                            <div class="col-md-3">
                                                <h4 class="text-success"><?= array_sum(array_column($materiales, 'descargas')) ?></h4>
                                                <small class="text-muted">Total Descargas</small>
                                            </div>
                                            <div class="col-md-3">
                                                <h4 class="text-info"><?= count(array_unique(array_column($materiales, 'tipo'))) ?></h4>
                                                <small class="text-muted">Tipos Diferentes</small>
                                            </div>
                                            <div class="col-md-3">
                                                <h4 class="text-warning"><?= formatFileSize(array_sum(array_column($materiales, 'tamaño'))) ?></h4>
                                                <small class="text-muted">Espacio Total</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="empty-state text-center py-4">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <h4>No tienes materiales subidos</h4>
                            <p class="text-muted">Comienza subiendo tu primer material educativo para tus estudiantes.</p>
                            <a href="<?= route('docente.materiales.subir') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Subir Primer Material
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Funciones auxiliares para la vista
function getTipoBadgeColor($tipo) {
    $colores = [
        'pdf' => 'danger',
        'video' => 'primary', 
        'codigo' => 'success',
        'guia' => 'info',
        'dataset' => 'warning'
    ];
    return $colores[strtolower($tipo)] ?? 'secondary';
}

function truncateText($text, $length = 50) {
    return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
}

function formatFileSize($bytes) {
    if ($bytes == 0) return '0 B';
    
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    
    $bytes /= pow(1024, $pow);
    
    return round($bytes, 2) . ' ' . $units[$pow];
}
?>

<script>
function confirmarEliminar(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar el material "${nombre}"?\n\nEsta acción no se puede deshacer.`)) {
        // Implementar eliminación via AJAX o formulario
        console.log('Eliminar material ID:', id);
    }
}
</script>