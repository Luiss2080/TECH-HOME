<?php
$title = $title ?? 'Gestión de Laboratorios';
$laboratorios = $laboratorios ?? [];
$categorias = $categorias ?? [];
$docentes = $docentes ?? [];

// Funciones PHP auxiliares para la vista
function getLabProgress($laboratorio) {
    switch ($laboratorio->estado ?? 'Planificado') {
        case 'Planificado':
            return 0;
        case 'En Progreso':
            return 50;
        case 'Completado':
            return 100;
        case 'Suspendido':
        case 'Cancelado':
            return 25;
        default:
            return 0;
    }
}

function getLabParticipants($laboratorio) {
    if (empty($laboratorio->participantes)) {
        return [];
    }
    $participantIds = json_decode($laboratorio->participantes, true) ?: [];
    $participants = [];
    foreach ($participantIds as $id) {
        $participants[] = ['id' => $id, 'nombre' => 'Usuario ' . $id];
    }
    return $participants;
}

function getLabIconClass($categoria) {
    $classes = [
        'robotica' => 'robotica',
        'programacion' => 'programacion', 
        'electronica' => 'electronica',
        'ai' => 'ai',
        'machine learning' => 'ai',
        'default' => 'default'
    ];
    return $classes[strtolower($categoria)] ?? $classes['default'];
}

function getLabIcon($categoria) {
    $icons = [
        'robotica' => 'fas fa-robot',
        'programacion' => 'fas fa-code',
        'electronica' => 'fas fa-microchip',
        'ai' => 'fas fa-brain',
        'machine learning' => 'fas fa-brain',
        'default' => 'fas fa-flask'
    ];
    return $icons[strtolower($categoria)] ?? $icons['default'];
}

function getLevelIcon($nivel) {
    $icons = [
        'Básico' => 'fas fa-seedling',
        'Intermedio' => 'fas fa-chart-line',
        'Avanzado' => 'fas fa-rocket',
        'Experto' => 'fas fa-crown'
    ];
    return $icons[$nivel] ?? $icons['Básico'];
}

function getStatusIcon($estado) {
    $icons = [
        'Planificado' => 'fas fa-calendar-alt',
        'En Progreso' => 'fas fa-play-circle',
        'Completado' => 'fas fa-check-circle',
        'Suspendido' => 'fas fa-pause-circle',
        'Cancelado' => 'fas fa-times-circle'
    ];
    return $icons[$estado] ?? $icons['Planificado'];
}
?>

<!-- Estilos específicos para el módulo CRUD -->
<link rel="stylesheet" href="<?= asset('css/index.css'); ?>">

<!-- CSS específico para el módulo de laboratorios -->
<style>
/* ============================================
   ESTILOS ESPECÍFICOS PARA GESTIÓN DE LABORATORIOS
   ============================================ */

/* Información del laboratorio */
.crud-lab-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.crud-lab-icon {
    width: 60px;
    height: 60px;
    border-radius: var(--border-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.8rem;
    box-shadow: var(--shadow-light);
    transition: var(--transition-base);
}

.crud-lab-icon:hover {
    transform: scale(1.1) rotate(5deg);
    box-shadow: var(--shadow-medium);
}

.crud-lab-details {
    display: flex;
    flex-direction: column;
    gap: 0.3rem;
}

.crud-lab-title {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--text-primary);
    transition: var(--transition-base);
    line-height: 1.2;
}

.crud-data-table tbody tr:hover .crud-lab-title {
    color: var(--primary-red);
}

.crud-lab-description {
    font-size: 0.85rem;
    color: var(--text-secondary);
    line-height: 1.3;
}

.crud-lab-objectives {
    font-size: 0.8rem;
    color: var(--text-light);
    font-style: italic;
}

/* Control de progreso visual */
.crud-progress-control {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
}

.crud-progress-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.8rem;
    color: white;
    position: relative;
    overflow: hidden;
    background: conic-gradient(
        from 0deg,
        var(--success-color) 0deg,
        var(--success-color) calc(var(--progress, 0) * 3.6deg),
        rgba(255, 255, 255, 0.2) calc(var(--progress, 0) * 3.6deg),
        rgba(255, 255, 255, 0.2) 360deg
    );
}

.crud-progress-text {
    position: relative;
    z-index: 2;
}

.crud-progress-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: var(--text-secondary);
}

/* Badges para estados de laboratorio */
.crud-badge-planificado {
    background: linear-gradient(135deg, var(--secondary-blue), var(--secondary-blue-light));
    color: white;
}

.crud-badge-en-progreso {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24);
    color: white;
}

.crud-badge-completado {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: white;
}

.crud-badge-suspendido {
    background: linear-gradient(135deg, var(--text-light), #94a3b8);
    color: white;
}

.crud-badge-cancelado {
    background: linear-gradient(135deg, var(--danger-color), #f87171);
    color: white;
}

/* Badges para niveles de dificultad */
.crud-badge-basico {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: white;
}

.crud-badge-intermedio {
    background: linear-gradient(135deg, var(--info-color), #60a5fa);
    color: white;
}

.crud-badge-avanzado {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24);
    color: white;
}

.crud-badge-experto {
    background: linear-gradient(135deg, var(--danger-color), #f87171);
    color: white;
}

/* Información de participantes */
.crud-participants-control {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
}

.crud-participants-number {
    font-weight: 800;
    font-size: 1.2rem;
    color: var(--text-primary);
    transition: var(--transition-base);
}

.crud-participants-avatars {
    display: flex;
    gap: 0.2rem;
}

.crud-participant-avatar {
    width: 25px;
    height: 25px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--accent-teal), #22d3ee);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.7rem;
    font-weight: 700;
    border: 2px solid white;
    margin-left: -8px;
    transition: var(--transition-base);
}

.crud-participant-avatar:first-child {
    margin-left: 0;
}

.crud-participant-avatar:hover {
    transform: scale(1.2);
    z-index: 10;
}

.crud-participants-label {
    font-size: 0.7rem;
    color: var(--text-secondary);
    text-transform: uppercase;
}

/* Información de duración */
.crud-duration-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
}

.crud-duration-number {
    font-weight: 700;
    font-size: 1rem;
    color: var(--accent-purple);
}

.crud-duration-label {
    font-size: 0.7rem;
    color: var(--text-light);
    text-transform: uppercase;
}

/* Fechas del laboratorio */
.crud-lab-dates {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
}

.crud-date-item {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.8rem;
}

.crud-date-icon {
    width: 12px;
    color: var(--text-light);
}

.crud-date-text {
    color: var(--text-secondary);
}

/* Iconos de laboratorio por categoría */
.crud-lab-icon-robotica { background: linear-gradient(135deg, #e74c3c, #c0392b); }
.crud-lab-icon-programacion { background: linear-gradient(135deg, #3498db, #2980b9); }
.crud-lab-icon-electronica { background: linear-gradient(135deg, #f39c12, #e67e22); }
.crud-lab-icon-ai { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
.crud-lab-icon-default { background: linear-gradient(135deg, #95a5a6, #7f8c8d); }

/* Estados destacados */
.crud-highlighted-lab {
    position: relative;
}

.crud-highlighted-lab::before {
    content: '⭐';
    position: absolute;
    top: -5px;
    right: -5px;
    font-size: 1rem;
    z-index: 10;
    animation: starPulse 2s ease-in-out infinite;
}

/* Animaciones específicas para laboratorios */
@keyframes starPulse {
    0%, 100% { 
        transform: scale(1);
        opacity: 1;
    }
    50% { 
        transform: scale(1.2);
        opacity: 0.8;
    }
}

@keyframes labFloat {
    0%, 100% { 
        transform: translateY(0) rotate(0deg); 
    }
    50% { 
        transform: translateY(-3px) rotate(1deg); 
    }
}

/* Responsive específico para laboratorios */
@media (max-width: 768px) {
    .crud-lab-icon {
        width: 45px;
        height: 45px;
        font-size: 1.3rem;
    }
    
    .crud-lab-title {
        font-size: 1rem;
    }
    
    .crud-lab-description {
        font-size: 0.8rem;
    }
    
    .crud-progress-circle {
        width: 40px;
        height: 40px;
        font-size: 0.7rem;
    }
    
    .crud-participants-number {
        font-size: 1rem;
    }
    
    .crud-participant-avatar {
        width: 20px;
        height: 20px;
        font-size: 0.6rem;
    }
}

/* Modo oscuro específico para laboratorios */
body.ithr-dark-mode .crud-participant-avatar,
body.dark-theme .crud-participant-avatar {
    border-color: var(--background-card);
}

body.ithr-dark-mode .crud-progress-circle,
body.dark-theme .crud-progress-circle {
    background: conic-gradient(
        from 0deg,
        var(--success-color) 0deg,
        var(--success-color) calc(var(--progress, 0) * 3.6deg),
        rgba(255, 255, 255, 0.1) calc(var(--progress, 0) * 3.6deg),
        rgba(255, 255, 255, 0.1) 360deg
    );
}

/* Estilos para lista de participantes en modal */
.crud-participants-list {
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: var(--border-radius-sm);
    padding: 0.5rem;
}

.crud-participant-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.crud-participant-item:last-child {
    border-bottom: none;
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
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="crud-section-title-group">
                        <h1 class="crud-section-title">Gestión de Laboratorios</h1>
                        <p class="crud-section-subtitle">Administra los laboratorios virtuales y controla su progreso y participantes</p>
                    </div>
                </div>
                <div class="crud-section-header-actions">
                    <a href="<?= route('laboratorios.crear'); ?>" class="crud-section-action-header">
                        <i class="fas fa-plus-circle"></i>
                        Crear Nuevo Laboratorio
                    </a>
                </div>
            </div> 
        </div>

        <!-- Estadísticas de laboratorios -->
        <div class="crud-section-card">
            <div class="crud-stats-grid">
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-blue">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Total Laboratorios</h4>
                        <div class="crud-stat-number"><?= count($laboratorios) ?></div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-yellow">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>En Progreso</h4>
                        <div class="crud-stat-number">
                            <?= count(array_filter($laboratorios, function($l) { return $l['estado'] === 'En Progreso'; })) ?>
                        </div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Completados</h4>
                        <div class="crud-stat-number">
                            <?= count(array_filter($laboratorios, function($l) { return $l['estado'] === 'Completado'; })) ?>
                        </div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-red">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Destacados</h4>
                        <div class="crud-stat-number">
                            <?= count(array_filter($laboratorios, function($l) { return isset($l['destacado']) && $l['destacado'] == 1; })) ?>
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
                        <label for="crudFilterName">Buscar por nombre:</label>
                        <input type="text" class="crud-form-control" id="crudFilterName" placeholder="Filtrar por nombre o descripción...">
                    </div>
                    <div class="crud-form-group">
                        <label for="crudFilterStatus">Filtrar por estado:</label>
                        <select class="crud-form-control" id="crudFilterStatus">
                            <option value="">Todos los estados</option>
                            <option value="Planificado">Planificado</option>
                            <option value="En Progreso">En Progreso</option>
                            <option value="Completado">Completado</option>
                            <option value="Suspendido">Suspendido</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label for="crudFilterLevel">Filtrar por nivel:</label>
                        <select class="crud-form-control" id="crudFilterLevel">
                            <option value="">Todos los niveles</option>
                            <option value="Básico">Básico</option>
                            <option value="Intermedio">Intermedio</option>
                            <option value="Avanzado">Avanzado</option>
                            <option value="Experto">Experto</option>
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

        <!-- Tabla de laboratorios modernizada -->
        <div class="crud-section-card">
            <div class="crud-table-container">
                <?php if (!empty($laboratorios)): ?>
                    <!-- DEBUG: Encontrados <?= count($laboratorios) ?> laboratorios en la vista -->
                    <table class="crud-data-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-flask"></i> LABORATORIO</th>
                                <th><i class="fas fa-chart-line"></i> PROGRESO</th>
                                <th><i class="fas fa-users"></i> PARTICIPANTES</th>
                                <th><i class="fas fa-layer-group"></i> NIVEL</th>
                                <th><i class="fas fa-clock"></i> DURACIÓN</th>
                                <th><i class="fas fa-toggle-on"></i> ESTADO</th>
                                <th><i class="fas fa-calendar"></i> FECHAS</th>
                                <th><i class="fas fa-cogs"></i> ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($laboratorios as $laboratorio):
                                $laboratorio = (object) $laboratorio;
                                $progreso = getLabProgress($laboratorio);
                                $participantes = getLabParticipants($laboratorio);
                                $isDestacado = $laboratorio->destacado ?? 0;
                                ?>
                                <tr data-lab-id="<?= $laboratorio->id ?>" class="crud-table-row <?= $isDestacado ? 'crud-highlighted-lab' : '' ?>">
                                    <td class="crud-text-muted">#<?= $laboratorio->id ?></td>
                                    <td>
                                        <div class="crud-lab-info">
                                            <div class="crud-lab-icon crud-lab-icon-<?= getLabIconClass($laboratorio->categoria_nombre ?? 'default') ?>">
                                                <i class="<?= getLabIcon($laboratorio->categoria_nombre ?? 'default') ?>"></i>
                                            </div>
                                            <div class="crud-lab-details">
                                                <div class="crud-lab-title"><?= htmlspecialchars($laboratorio->nombre) ?></div>
                                                <?php if (!empty($laboratorio->descripcion)): ?>
                                                    <div class="crud-lab-description" title="<?= htmlspecialchars($laboratorio->descripcion) ?>">
                                                        <?= htmlspecialchars(substr($laboratorio->descripcion, 0, 80)) ?><?= strlen($laboratorio->descripcion) > 80 ? '...' : '' ?>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($laboratorio->objetivos)): ?>
                                                    <div class="crud-lab-objectives" title="<?= htmlspecialchars($laboratorio->objetivos) ?>">
                                                        Obj: <?= htmlspecialchars(substr($laboratorio->objetivos, 0, 50)) ?><?= strlen($laboratorio->objetivos) > 50 ? '...' : '' ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="crud-progress-control">
                                            <div class="crud-progress-circle" style="--progress: <?= $progreso ?>">
                                                <span class="crud-progress-text"><?= $progreso ?>%</span>
                                            </div>
                                            <span class="crud-progress-label">Progreso</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="crud-participants-control">
                                            <span class="crud-participants-number"><?= count($participantes) ?></span>
                                            <div class="crud-participants-avatars">
                                                <?php $maxShow = 3; $count = 0; ?>
                                                <?php foreach (array_slice($participantes, 0, $maxShow) as $participant): ?>
                                                    <div class="crud-participant-avatar" title="<?= htmlspecialchars($participant['nombre'] ?? 'Participante') ?>">
                                                        <?= strtoupper(substr($participant['nombre'] ?? 'P', 0, 1)) ?>
                                                    </div>
                                                    <?php $count++; ?>
                                                <?php endforeach; ?>
                                                <?php if (count($participantes) > $maxShow): ?>
                                                    <div class="crud-participant-avatar" title="+<?= count($participantes) - $maxShow ?> más">
                                                        +<?= count($participantes) - $maxShow ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <span class="crud-participants-label">Participantes</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="crud-badge crud-badge-<?= strtolower(str_replace(' ', '-', $laboratorio->nivel_dificultad ?? 'basico')) ?>">
                                            <i class="<?= getLevelIcon($laboratorio->nivel_dificultad ?? 'Básico') ?>"></i>
                                            <?= htmlspecialchars($laboratorio->nivel_dificultad ?? 'Básico') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="crud-duration-info">
                                            <?php if (!empty($laboratorio->duracion_dias) && $laboratorio->duracion_dias > 0): ?>
                                                <span class="crud-duration-number"><?= $laboratorio->duracion_dias ?></span>
                                                <span class="crud-duration-label"><?= $laboratorio->duracion_dias == 1 ? 'DÍA' : 'DÍAS' ?></span>
                                            <?php else: ?>
                                                <span class="crud-duration-number">N/A</span>
                                                <span class="crud-duration-label">DURACIÓN</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="crud-badge crud-badge-<?= strtolower(str_replace(' ', '-', $laboratorio->estado ?? 'planificado')) ?>">
                                            <i class="<?= getStatusIcon($laboratorio->estado ?? 'Planificado') ?>"></i>
                                            <?= htmlspecialchars($laboratorio->estado ?? 'Planificado') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="crud-lab-dates">
                                            <?php if (!empty($laboratorio->fecha_inicio)): ?>
                                                <div class="crud-date-item">
                                                    <i class="fas fa-play crud-date-icon"></i>
                                                    <span class="crud-date-text"><?= date('d/m/Y', strtotime($laboratorio->fecha_inicio)) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($laboratorio->fecha_fin)): ?>
                                                <div class="crud-date-item">
                                                    <i class="fas fa-stop crud-date-icon"></i>
                                                    <span class="crud-date-text"><?= date('d/m/Y', strtotime($laboratorio->fecha_fin)) ?></span>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (empty($laboratorio->fecha_inicio) && empty($laboratorio->fecha_fin)): ?>
                                                <span class="crud-text-muted">Sin fechas</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="crud-action-buttons">
                                            <a href="<?= route('laboratorios.editar', ['id' => $laboratorio->id]) ?>" 
                                               class="crud-btn-sm crud-btn-outline-primary" 
                                               title="Editar Laboratorio">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= route('laboratorios.ver', ['id' => $laboratorio->id]) ?>" 
                                               class="crud-btn-sm crud-btn-outline-info" 
                                               title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="crud-btn-sm crud-btn-outline-warning crud-btn-participants" 
                                                    data-lab-id="<?= $laboratorio->id ?>" 
                                                    data-lab-name="<?= htmlspecialchars($laboratorio->nombre) ?>"
                                                    data-participants-url="<?= route('laboratorios.participantes', ['id' => $laboratorio->id]) ?>"
                                                    title="Gestionar Participantes">
                                                <i class="fas fa-users"></i>
                                            </button>
                                            <button type="button" 
                                                    class="crud-btn-sm crud-btn-outline-success crud-btn-status" 
                                                    data-lab-id="<?= $laboratorio->id ?>" 
                                                    data-lab-name="<?= htmlspecialchars($laboratorio->nombre) ?>"
                                                    data-current-status="<?= $laboratorio->estado ?>"
                                                    data-status-url="<?= route('laboratorios.estado', ['id' => $laboratorio->id]) ?>"
                                                    title="Cambiar Estado">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <button type="button" 
                                                    class="crud-btn-sm crud-btn-outline-danger crud-btn-delete-lab" 
                                                    data-lab-id="<?= $laboratorio->id ?>" 
                                                    data-lab-name="<?= htmlspecialchars($laboratorio->nombre) ?>"
                                                    data-delete-url="<?= route('laboratorios.delete', ['id' => $laboratorio->id]) ?>"
                                                    title="Eliminar Laboratorio">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <!-- DEBUG: La variable $laboratorios está vacía. Tipo: <?= gettype($laboratorios) ?>, Count: <?= is_array($laboratorios) ? count($laboratorios) : 'N/A' ?> -->
                    <div class="crud-empty-state">
                        <div class="crud-empty-state-icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h3>No hay laboratorios registrados</h3>
                        <p>Comienza creando el primer laboratorio virtual para gestionar proyectos educativos y experimentales.</p>
                        <a href="<?= route('laboratorios.crear'); ?>" class="crud-btn-primary">
                            <i class="fas fa-plus"></i>
                            Crear Primer Laboratorio
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
<div class="crud-modal" id="crudDeleteLabModal" tabindex="-1" role="dialog" aria-labelledby="crudDeleteLabModalLabel" aria-hidden="true">
    <div class="crud-modal-dialog" role="document">
        <div class="crud-modal-content">
            <form id="crudDeleteLabForm" method="POST" action="">
                <?= CSRF() ?>
                <input type="hidden" name="_method" value="DELETE">
                
                <div class="crud-modal-header">
                    <h5 class="crud-modal-title" id="crudDeleteLabModalLabel">
                        <i class="fas fa-exclamation-triangle"></i>
                        Confirmar Eliminación de Laboratorio
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="text-center">
                        <div class="crud-warning-icon">
                            <i class="fas fa-flask"></i>
                        </div>
                        <h4 class="crud-mb-3">¿Estás seguro de eliminar este laboratorio?</h4>
                        <p class="crud-text-muted crud-mb-2">Esta acción no se puede deshacer.</p>
                        <div class="crud-user-to-delete-info">
                            <strong>Laboratorio: <span id="crudLabNameToDelete" class="crud-text-danger"></span></strong>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="crud-btn-danger">
                            <i class="fas fa-trash"></i>
                            Eliminar Laboratorio
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
