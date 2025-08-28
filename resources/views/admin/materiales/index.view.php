<?php
$title = $title ?? 'Gestión de Materiales';
$materiales = $materiales ?? [];
$categorias = $categorias ?? [];
$docentes = $docentes ?? [];
?>

<!-- Estilos específicos para el módulo CRUD -->
<link rel="stylesheet" href="<?= asset('css/index.css'); ?>">

<!-- CSS específico para el módulo de materiales -->
<style>
/* ============================================
   ESTILOS ESPECÍFICOS PARA GESTIÓN DE MATERIALES
   ============================================ */

/* Previews de materiales */
.crud-material-preview {
    width: 60px;
    height: 45px;
    border-radius: var(--border-radius-sm);
    object-fit: cover;
    border: 2px solid rgba(220, 38, 38, 0.2);
    transition: var(--transition-base);
    box-shadow: var(--shadow-light);
}

.crud-material-preview:hover {
    transform: scale(1.1);
    border-color: var(--primary-red);
    box-shadow: var(--shadow-medium);
}

.crud-material-placeholder {
    width: 60px;
    height: 45px;
    background: linear-gradient(135deg, var(--text-light), #cbd5e1);
    border-radius: var(--border-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    border: 2px solid rgba(220, 38, 38, 0.2);
    transition: var(--transition-base);
    position: relative;
}

.crud-material-placeholder:hover {
    transform: scale(1.1);
    border-color: var(--primary-red);
    box-shadow: var(--shadow-medium);
}

/* Información del material */
.crud-material-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.crud-material-details {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.crud-material-title {
    font-weight: 700;
    font-size: 1rem;
    color: var(--text-primary);
    transition: var(--transition-base);
    line-height: 1.2;
}

.crud-data-table tbody tr:hover .crud-material-title {
    color: var(--primary-red);
}

.crud-material-type {
    font-size: 0.8rem;
    color: var(--text-light);
    font-style: italic;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.crud-material-docente {
    font-size: 0.75rem;
    color: var(--text-secondary);
}

/* Control de descargas visual */
.crud-downloads-control {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
}

.crud-downloads-number {
    font-weight: 800;
    font-size: 1.1rem;
    color: var(--text-primary);
    transition: var(--transition-base);
}

.crud-downloads-bar {
    width: 60px;
    height: 6px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.crud-downloads-fill {
    height: 100%;
    border-radius: 10px;
    transition: var(--transition-base);
    position: relative;
    background: linear-gradient(90deg, var(--accent-teal), #22d3ee);
}

.crud-downloads-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: downloadsShimmer 2s ease-in-out infinite;
}

.crud-downloads-status {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-top: 0.2rem;
    color: var(--accent-teal);
}

/* Badges para tipos de material */
.crud-badge-video {
    background: linear-gradient(135deg, var(--danger-color), #f87171);
    color: white;
}

.crud-badge-documento {
    background: linear-gradient(135deg, var(--secondary-blue), var(--secondary-blue-light));
    color: white;
}

.crud-badge-presentacion {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24);
    color: white;
}

.crud-badge-audio {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: white;
}

.crud-badge-enlace {
    background: linear-gradient(135deg, var(--info-color), #60a5fa);
    color: white;
}

.crud-badge-otro {
    background: linear-gradient(135deg, var(--text-light), #94a3b8);
    color: white;
}

.crud-badge-publico {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: white;
}

.crud-badge-privado {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24);
    color: white;
}

/* Información de tamaño y duración */
.crud-material-size {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.2rem;
}

.crud-size-amount {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--text-primary);
}

.crud-size-label {
    font-size: 0.7rem;
    color: var(--text-light);
    text-transform: uppercase;
}

.crud-duration-amount {
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--accent-purple);
}

/* Iconos de tipo de material */
.crud-type-icon {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.7rem;
    font-weight: bold;
}

.crud-type-icon-video { background: var(--danger-color); }
.crud-type-icon-documento { background: var(--secondary-blue); }
.crud-type-icon-presentacion { background: var(--warning-color); }
.crud-type-icon-audio { background: var(--success-color); }
.crud-type-icon-enlace { background: var(--info-color); }
.crud-type-icon-otro { background: var(--text-light); }

/* Animaciones específicas para materiales */
@keyframes downloadsShimmer {
    0%, 100% { left: -100%; }
    50% { left: 100%; }
}

@keyframes materialFloat {
    0%, 100% { 
        transform: translateY(0) rotate(0deg); 
    }
    50% { 
        transform: translateY(-3px) rotate(0.5deg); 
    }
}

/* Efecto hover para las filas de materiales */
.crud-data-table tbody tr:hover .crud-material-preview,
.crud-data-table tbody tr:hover .crud-material-placeholder {
    animation: materialFloat 1s ease-in-out;
}

.crud-data-table tbody tr:hover .crud-downloads-number {
    color: var(--primary-red);
    transform: scale(1.1);
}

/* Estado de acceso */
.crud-access-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Responsive específico para materiales */
@media (max-width: 768px) {
    .crud-material-preview,
    .crud-material-placeholder {
        width: 45px;
        height: 35px;
    }
    
    .crud-material-title {
        font-size: 0.9rem;
    }
    
    .crud-material-type {
        font-size: 0.7rem;
    }
    
    .crud-downloads-control {
        gap: 0.2rem;
    }
    
    .crud-downloads-number {
        font-size: 1rem;
    }
    
    .crud-downloads-bar {
        width: 40px;
        height: 4px;
    }
}

/* Modo oscuro específico para materiales */
body.ithr-dark-mode .crud-material-placeholder,
body.dark-theme .crud-material-placeholder {
    background: linear-gradient(135deg, #475569, #64748b);
}

body.ithr-dark-mode .crud-downloads-bar,
body.dark-theme .crud-downloads-bar {
    background: rgba(255, 255, 255, 0.1);
}
</style>

<!-- Contenedor principal del CRUD -->
<div class="crud-container">
    <div class="crud-content-wrapper">

        <!-- Header principal con icono y acciones -->
        <div class="crud-section-card">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="crud-section-title-group">
                        <h1 class="crud-section-title">Gestión de Materiales</h1>
                        <p class="crud-section-subtitle">Administra los recursos educativos y controla el acceso y distribución</p>
                    </div>
                </div>
                <div class="crud-section-header-actions">
                    <a href="<?= route('materiales.crear'); ?>" class="crud-section-action-header">
                        <i class="fas fa-plus-circle"></i>
                        Agregar Nuevo Material
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas de materiales -->
        <div class="crud-section-card">
            <div class="crud-stats-grid">
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-blue">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Total Materiales</h4>
                        <div class="crud-stat-number"><?= count($materiales) ?></div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-green">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Públicos</h4>
                        <div class="crud-stat-number">
                            <?= count(array_filter($materiales, fn($m) => $m['publico'] == 1)) ?>
                        </div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-yellow">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Total Descargas</h4>
                        <div class="crud-stat-number">
                            <?= array_sum(array_column($materiales, 'descargas')) ?>
                        </div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-red">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Privados</h4>
                        <div class="crud-stat-number">
                            <?= count(array_filter($materiales, fn($m) => $m['publico'] == 0)) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de búsqueda modernos -->
        <div class="crud-section-card">
            <div class="crud-filters-container">
                <h5 class="crud-filters-title">
                    <i class="fas fa-filter"></i>
                    Filtros de Búsqueda
                </h5>
                <div class="crud-filters-row">
                    <div class="crud-form-group">
                        <label for="crudFilterTitle">Buscar por título:</label>
                        <input type="text" class="crud-form-control" id="crudFilterTitle" placeholder="Filtrar por título...">
                    </div>
                    <div class="crud-form-group">
                        <label for="crudFilterType">Filtrar por tipo:</label>
                        <select class="crud-form-control" id="crudFilterType">
                            <option value="">Todos los tipos</option>
                            <option value="video">Video</option>
                            <option value="documento">Documento</option>
                            <option value="presentacion">Presentación</option>
                            <option value="audio">Audio</option>
                            <option value="enlace">Enlace</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label for="crudFilterVisibility">Filtrar por visibilidad:</label>
                        <select class="crud-form-control" id="crudFilterVisibility">
                            <option value="">Todas las visibilidades</option>
                            <option value="1">Público</option>
                            <option value="0">Privado</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <button type="button" class="crud-btn crud-btn-clear" onclick="crudClearFilters()">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensajes de éxito/error modernizados -->
        <?php if (flashGet('success')): ?>
            <div class="crud-alert crud-alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars(flashGet('success')) ?></span>
                <button type="button" class="crud-btn-close" data-bs-dismiss="alert">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (flashGet('error')): ?>
            <div class="crud-alert crud-alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <span><strong>ERROR:</strong> <?= htmlspecialchars(flashGet('error')) ?></span>
                <button type="button" class="crud-btn-close" data-bs-dismiss="alert">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Tabla de materiales modernizada -->
        <div class="crud-section-card">
            <div class="crud-table-container">
                <?php if (!empty($materiales)): ?>
                    <table class="crud-data-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-file"></i> MATERIAL</th>
                                <th><i class="fas fa-tag"></i> TIPO</th>
                                <th><i class="fas fa-user-tie"></i> DOCENTE</th>
                                <th><i class="fas fa-download"></i> DESCARGAS</th>
                                <th><i class="fas fa-hdd"></i> TAMAÑO</th>
                                <th><i class="fas fa-eye"></i> VISIBILIDAD</th>
                                <th><i class="fas fa-toggle-on"></i> ESTADO</th>
                                <th><i class="fas fa-cogs"></i> ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($materiales as $material):
                                $material = (object) $material;
                                $maxDescargas = 100; // Valor base para el cálculo del porcentaje
                                $descargasPorcentaje = $material->descargas > 0 ? min(($material->descargas / $maxDescargas) * 100, 100) : 0;
                                ?>
                                <tr data-material-id="<?= $material->id ?>" class="crud-table-row">
                                    <td class="crud-text-muted">#<?= $material->id ?></td>
                                    <td>
                                        <div class="crud-material-info">
                                            <div class="crud-material-preview-container">
                                                <?php if (!empty($material->imagen_preview)): ?>
                                                    <img src="<?= asset('materiales/previews/' . $material->imagen_preview) ?>" 
                                                         alt="Preview de <?= htmlspecialchars($material->titulo) ?>"
                                                         class="crud-material-preview">
                                                <?php else: ?>
                                                    <div class="crud-material-placeholder">
                                                        <i class="<?= getMaterialIcon($material->tipo ?? 'otro') ?>"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="crud-material-details">
                                                <div class="crud-material-title"><?= htmlspecialchars($material->titulo) ?></div>
                                                <div class="crud-material-type">
                                                    <div class="crud-type-icon crud-type-icon-<?= $material->tipo ?? 'otro' ?>">
                                                        <i class="<?= getMaterialIcon($material->tipo ?? 'otro') ?>"></i>
                                                    </div>
                                                    <?= ucfirst($material->tipo ?? 'Otro') ?>
                                                </div>
                                                <?php if (!empty($material->descripcion)): ?>
                                                    <div class="crud-material-docente" title="<?= htmlspecialchars($material->descripcion) ?>">
                                                        <?= htmlspecialchars(substr($material->descripcion, 0, 50)) ?><?= strlen($material->descripcion) > 50 ? '...' : '' ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="crud-badge crud-badge-<?= $material->tipo ?? 'otro' ?>">
                                            <i class="<?= getMaterialIcon($material->tipo ?? 'otro') ?>"></i>
                                            <?= ucfirst($material->tipo ?? 'Otro') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="crud-material-size">
                                            <span class="crud-size-amount"><?= htmlspecialchars($material->docente_nombre ?? 'N/A') ?></span>
                                            <span class="crud-size-label">Docente</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="crud-downloads-control">
                                            <span class="crud-downloads-number"><?= $material->descargas ?? 0 ?></span>
                                            <div class="crud-downloads-bar">
                                                <div class="crud-downloads-fill" style="width: <?= $descargasPorcentaje ?>%"></div>
                                            </div>
                                            <span class="crud-downloads-status">Descargas</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="crud-material-size">
                                            <?php if (!empty($material->tamaño_archivo) && $material->tamaño_archivo > 0): ?>
                                                <span class="crud-size-amount"><?= formatBytes($material->tamaño_archivo) ?></span>
                                                <span class="crud-size-label">Archivo</span>
                                            <?php elseif (!empty($material->duracion) && $material->duracion > 0): ?>
                                                <span class="crud-duration-amount"><?= formatDuration($material->duracion) ?></span>
                                                <span class="crud-size-label">Duración</span>
                                            <?php else: ?>
                                                <span class="crud-size-amount">N/A</span>
                                                <span class="crud-size-label">Tamaño</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($material->publico == 1): ?>
                                            <span class="crud-badge crud-badge-publico">
                                                <i class="fas fa-globe"></i>
                                                Público
                                            </span>
                                        <?php else: ?>
                                            <span class="crud-badge crud-badge-privado">
                                                <i class="fas fa-lock"></i>
                                                Privado
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($material->estado == 1): ?>
                                            <span class="crud-badge crud-badge-success">
                                                <i class="fas fa-check-circle"></i>
                                                Activo
                                            </span>
                                        <?php else: ?>
                                            <span class="crud-badge crud-badge-danger">
                                                <i class="fas fa-times-circle"></i>
                                                Inactivo
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="crud-action-buttons">
                                            <a href="<?= route('materiales.editar', ['id' => $material->id]) ?>" 
                                               class="crud-btn-sm crud-btn-outline-primary" 
                                               title="Editar Material">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= route('materiales.ver', ['id' => $material->id]) ?>" 
                                               class="crud-btn-sm crud-btn-outline-info" 
                                               title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="crud-btn-sm crud-btn-outline-warning crud-btn-visibility" 
                                                    data-material-id="<?= $material->id ?>" 
                                                    data-material-title="<?= htmlspecialchars($material->titulo) ?>"
                                                    data-current-visibility="<?= $material->publico ?>"
                                                    data-visibility-url="<?= route('materiales.visibilidad', ['id' => $material->id]) ?>"
                                                    title="Cambiar Visibilidad">
                                                <i class="fas fa-<?= $material->publico == 1 ? 'lock' : 'globe' ?>"></i>
                                            </button>
                                            <a href="<?= route('materiales.descargar', ['id' => $material->id]) ?>" 
                                               class="crud-btn-sm crud-btn-outline-success" 
                                               title="Descargar Material">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            <button type="button" 
                                                    class="crud-btn-sm <?= $material->estado == 1 ? 'crud-btn-outline-warning' : 'crud-btn-outline-success' ?> crud-btn-toggle-status" 
                                                    data-material-id="<?= $material->id ?>" 
                                                    data-material-title="<?= htmlspecialchars($material->titulo) ?>"
                                                    data-current-status="<?= $material->estado ?>"
                                                    data-status-url="<?= route('materiales.estado', ['id' => $material->id]) ?>"
                                                    title="<?= $material->estado == 1 ? 'Desactivar' : 'Activar' ?> Material">
                                                <i class="fas fa-<?= $material->estado == 1 ? 'ban' : 'check' ?>"></i>
                                            </button>
                                            <button type="button" 
                                                    class="crud-btn-sm crud-btn-outline-danger crud-btn-delete-material" 
                                                    data-material-id="<?= $material->id ?>" 
                                                    data-material-title="<?= htmlspecialchars($material->titulo) ?>"
                                                    data-delete-url="<?= route('materiales.delete', ['id' => $material->id]) ?>"
                                                    title="Eliminar Material">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="crud-empty-state">
                        <div class="crud-empty-state-icon">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <h3>No hay materiales registrados</h3>
                        <p>Comienza agregando el primer material educativo para gestionar los recursos y controlar el acceso.</p>
                        <a href="<?= route('materiales.crear'); ?>" class="crud-btn-primary">
                            <i class="fas fa-plus"></i>
                            Agregar Primer Material
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Espacio de separación -->
        <div style="height: 40px;"></div>

    </div>
</div>

<!-- Modal de confirmación de eliminación -->
<div class="crud-modal" id="crudDeleteMaterialModal" tabindex="-1" role="dialog" aria-labelledby="crudDeleteMaterialModalLabel" aria-hidden="true">
    <div class="crud-modal-dialog" role="document">
        <div class="crud-modal-content">
            <form id="crudDeleteMaterialForm" method="POST" action="">
                <?= CSRF() ?>
                <input type="hidden" name="_method" value="DELETE">
                
                <div class="crud-modal-header">
                    <h5 class="crud-modal-title" id="crudDeleteMaterialModalLabel">
                        <i class="fas fa-exclamation-triangle"></i>
                        Confirmar Eliminación de Material
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="text-center">
                        <div class="crud-warning-icon">
                            <i class="fas fa-file-times"></i>
                        </div>
                        <h4 class="crud-mb-3">¿Estás seguro de eliminar este material?</h4>
                        <p class="crud-text-muted crud-mb-2">Esta acción no se puede deshacer.</p>
                        <div class="crud-user-to-delete-info">
                            <strong>Material: <span id="crudMaterialTitleToDelete" class="crud-text-danger"></span></strong>
                        </div>
                    </div>
                    <div class="crud-alert crud-alert-danger crud-mt-3" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>¡Atención!</strong> El material será eliminado permanentemente junto con:
                            <ul class="crud-mt-2 crud-mb-0">
                                <li>Todo el contenido del material</li>
                                <li>Los archivos asociados (preview, archivos)</li>
                                <li>El historial de descargas</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="crud-modal-footer">
                    <button type="button" class="crud-btn crud-btn-clear" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="crud-btn crud-btn-outline-danger">
                        <i class="fas fa-file-times"></i>
                        Eliminar Material
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de cambio de visibilidad -->
<div class="crud-modal" id="crudChangeVisibilityModal" tabindex="-1" role="dialog" aria-labelledby="crudChangeVisibilityModalLabel" aria-hidden="true">
    <div class="crud-modal-dialog" role="document">
        <div class="crud-modal-content">
            <form id="crudChangeVisibilityForm" method="POST" action="">
                <?= CSRF() ?>
                
                <div class="crud-modal-header">
                    <h5 class="crud-modal-title" id="crudChangeVisibilityModalLabel">
                        <i class="fas fa-eye"></i>
                        Cambiar Visibilidad del Material
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="text-center">
                        <div class="crud-status-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <h4 class="crud-mb-3" id="crudVisibilityModalTitle">¿Cambiar visibilidad del material?</h4>
                        <div class="crud-user-status-info">
                            <strong>Material: <span id="crudMaterialTitleToToggleVisibility" class="crud-text-info"></span></strong>
                            <p class="crud-mt-2" id="crudVisibilityModalDescription">El material será público/privado en el sistema.</p>
                        </div>
                    </div>
                </div>
                
                <div class="crud-modal-footer">
                    <button type="button" class="crud-btn crud-btn-clear" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="crud-btn crud-btn-outline-warning" id="crudConfirmVisibilityToggle">
                        <i class="fas fa-check"></i>
                        Confirmar Cambio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de cambio de estado -->
<div class="crud-modal" id="crudToggleStatusModal" tabindex="-1" role="dialog" aria-labelledby="crudToggleStatusModalLabel" aria-hidden="true">
    <div class="crud-modal-dialog" role="document">
        <div class="crud-modal-content">
            <form id="crudToggleStatusForm" method="POST" action="">
                <?= CSRF() ?>
                
                <div class="crud-modal-header">
                    <h5 class="crud-modal-title" id="crudToggleStatusModalLabel">
                        <i class="fas fa-toggle-on"></i>
                        Cambiar Estado del Material
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="text-center">
                        <div class="crud-status-icon">
                            <i class="fas fa-file"></i>
                        </div>
                        <h4 class="crud-mb-3" id="crudStatusModalTitle">¿Cambiar estado del material?</h4>
                        <div class="crud-user-status-info">
                            <strong>Material: <span id="crudMaterialTitleToToggle" class="crud-text-info"></span></strong>
                            <p class="crud-mt-2" id="crudStatusModalDescription">El material será activado/desactivado en el sistema.</p>
                        </div>
                    </div>
                </div>
                
                <div class="crud-modal-footer">
                    <button type="button" class="crud-btn crud-btn-clear" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="crud-btn crud-btn-outline-info" id="crudConfirmStatusToggle">
                        <i class="fas fa-check"></i>
                        Confirmar Cambio
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Overlay personalizado -->
<div class="crud-custom-modal-overlay" id="crudCustomModalOverlay"></div>

<!-- JavaScript específico para materiales CRUD -->
<script>
<?php
// Funciones PHP auxiliares para la vista
function formatBytes($bytes) {
    if ($bytes === 0) return '0 B';
    $k = 1024;
    $sizes = ['B', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes) / log($k));
    return round(($bytes / pow($k, $i)), 2) . ' ' . $sizes[$i];
}

function formatDuration($seconds) {
    if (!$seconds || $seconds === 0) return '0:00';
    $minutes = floor($seconds / 60);
    $remainingSeconds = $seconds % 60;
    return $minutes . ':' . ($remainingSeconds < 10 ? '0' : '') . $remainingSeconds;
}

function getMaterialIcon($type) {
    $icons = [
        'video' => 'fas fa-play-circle',
        'documento' => 'fas fa-file-pdf',
        'presentacion' => 'fas fa-file-powerpoint',
        'audio' => 'fas fa-file-audio',
        'enlace' => 'fas fa-link',
        'otro' => 'fas fa-file'
    ];
    return $icons[$type] ?? $icons['otro'];
}
?>

// Funciones auxiliares JavaScript
function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function formatDuration(seconds) {
    if (!seconds || seconds === 0) return '0:00';
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = seconds % 60;
    return minutes + ':' + (remainingSeconds < 10 ? '0' : '') + remainingSeconds;
}

function getMaterialIcon(type) {
    const icons = {
        'video': 'fas fa-play-circle',
        'documento': 'fas fa-file-pdf',
        'presentacion': 'fas fa-file-powerpoint',
        'audio': 'fas fa-file-audio',
        'enlace': 'fas fa-link',
        'otro': 'fas fa-file'
    };
    return icons[type] || icons['otro'];
}

// Funcionalidad específica para gestión de materiales
document.addEventListener('DOMContentLoaded', function() {
    const crudFilterTitle = document.getElementById('crudFilterTitle');
    const crudFilterType = document.getElementById('crudFilterType');
    const crudFilterVisibility = document.getElementById('crudFilterVisibility');
    const crudTableRows = document.querySelectorAll('.crud-data-table tbody tr');

    function crudApplyFilters() {
        const titleFilter = crudFilterTitle.value.toLowerCase();
        const typeFilter = crudFilterType.value.toLowerCase();
        const visibilityFilter = crudFilterVisibility.value;

        crudTableRows.forEach(row => {
            const titleCell = row.querySelector('.crud-material-title').textContent.toLowerCase();
            const typeCell = row.querySelector('.crud-material-type').textContent.toLowerCase();
            const visibilityBadge = row.querySelector('.crud-badge-publico, .crud-badge-privado');
            const isPublic = visibilityBadge && visibilityBadge.classList.contains('crud-badge-publico') ? '1' : '0';

            let showRow = true;

            // Filtro por título
            if (titleFilter && !titleCell.includes(titleFilter)) {
                showRow = false;
            }

            // Filtro por tipo
            if (typeFilter && !typeCell.includes(typeFilter)) {
                showRow = false;
            }

            // Filtro por visibilidad
            if (visibilityFilter && isPublic !== visibilityFilter) {
                showRow = false;
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    // Event listeners para los filtros
    if (crudFilterTitle) crudFilterTitle.addEventListener('keyup', crudApplyFilters);
    if (crudFilterType) crudFilterType.addEventListener('change', crudApplyFilters);
    if (crudFilterVisibility) crudFilterVisibility.addEventListener('change', crudApplyFilters);

    // Función para limpiar filtros
    window.crudClearFilters = function() {
        if (crudFilterTitle) crudFilterTitle.value = '';
        if (crudFilterType) crudFilterType.value = '';
        if (crudFilterVisibility) crudFilterVisibility.value = '';
        
        crudTableRows.forEach(row => {
            row.style.display = '';
        });
    };

    // Manejo de modales
    const crudDeleteModal = document.getElementById('crudDeleteMaterialModal');
    const crudStatusModal = document.getElementById('crudToggleStatusModal');
    const crudVisibilityModal = document.getElementById('crudChangeVisibilityModal');
    const crudOverlay = document.getElementById('crudCustomModalOverlay');

    // Botones de eliminar material
    document.querySelectorAll('.crud-btn-delete-material').forEach(button => {
        button.addEventListener('click', function() {
            const materialId = this.dataset.materialId;
            const materialTitle = this.dataset.materialTitle;
            const deleteUrl = this.dataset.deleteUrl;

            document.getElementById('crudMaterialTitleToDelete').textContent = materialTitle;
            document.getElementById('crudDeleteMaterialForm').action = deleteUrl;

            crudShowModal(crudDeleteModal);
        });
    });

    // Botones de cambio de estado
    document.querySelectorAll('.crud-btn-toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const materialId = this.dataset.materialId;
            const materialTitle = this.dataset.materialTitle;
            const currentStatus = this.dataset.currentStatus;
            const statusUrl = this.dataset.statusUrl;

            document.getElementById('crudMaterialTitleToToggle').textContent = materialTitle;
            document.getElementById('crudToggleStatusForm').action = statusUrl;

            const modalTitle = document.getElementById('crudStatusModalTitle');
            const modalDescription = document.getElementById('crudStatusModalDescription');
            
            if (currentStatus === '1') {
                modalTitle.textContent = '¿Desactivar material?';
                modalDescription.textContent = 'El material será desactivado y no estará disponible.';
            } else {
                modalTitle.textContent = '¿Activar material?';
                modalDescription.textContent = 'El material será activado y estará disponible.';
            }

            crudShowModal(crudStatusModal);
        });
    });

    // Botones de cambio de visibilidad
    document.querySelectorAll('.crud-btn-visibility').forEach(button => {
        button.addEventListener('click', function() {
            const materialId = this.dataset.materialId;
            const materialTitle = this.dataset.materialTitle;
            const currentVisibility = this.dataset.currentVisibility;
            const visibilityUrl = this.dataset.visibilityUrl;

            document.getElementById('crudMaterialTitleToToggleVisibility').textContent = materialTitle;
            document.getElementById('crudChangeVisibilityForm').action = visibilityUrl;

            const modalTitle = document.getElementById('crudVisibilityModalTitle');
            const modalDescription = document.getElementById('crudVisibilityModalDescription');
            
            if (currentVisibility === '1') {
                modalTitle.textContent = '¿Hacer privado el material?';
                modalDescription.textContent = 'El material será privado y solo accesible para usuarios autorizados.';
            } else {
                modalTitle.textContent = '¿Hacer público el material?';
                modalDescription.textContent = 'El material será público y accesible para todos.';
            }

            crudShowModal(crudVisibilityModal);
        });
    });

    // Funciones para mostrar/ocultar modales
    function crudShowModal(modal) {
        if (modal && crudOverlay) {
            crudOverlay.classList.add('active');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function crudHideModal(modal) {
        if (modal && crudOverlay) {
            crudOverlay.classList.remove('active');
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    // Event listeners para cerrar modales
    document.querySelectorAll('.crud-modal-close, [data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modal = this.closest('.crud-modal');
            crudHideModal(modal);
        });
    });

    // Cerrar modal al hacer clic en el overlay
    if (crudOverlay) {
        crudOverlay.addEventListener('click', function() {
            document.querySelectorAll('.crud-modal.show').forEach(modal => {
                crudHideModal(modal);
            });
        });
    }

    // Auto-dismiss de alertas después de 5 segundos
    document.querySelectorAll('.crud-alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });

    // Manejo manual de cierre de alertas
    document.querySelectorAll('.crud-btn-close').forEach(button => {
        button.addEventListener('click', function() {
            const alert = this.closest('.crud-alert');
            if (alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    alert.remove();
                }, 300);
            }
        });
    });

    // Efecto de animación para las barras de descargas al cargar la página
    setTimeout(() => {
        document.querySelectorAll('.crud-downloads-fill').forEach(fill => {
            const width = fill.style.width;
            fill.style.width = '0%';
            setTimeout(() => {
                fill.style.width = width;
            }, 100);
        });
    }, 500);
});
</script>