<?php
$title = $title ?? 'Gestión de Cursos';
$cursos = $cursos ?? [];
$estadisticas = $estadisticas ?? [
    'total' => 0,
    'publicados' => 0,
    'borradores' => 0,
    'archivados' => 0
];
$isDocente = $isDocente ?? false;
?>

<!-- Estilos específicos para el módulo CRUD -->
<link rel="stylesheet" href="<?= asset('css/index.css'); ?>">

<!-- Estilos específicos para Cursos -->
<style>
/* ============================================
   ESTILOS ESPECÍFICOS PARA CURSOS
   ============================================ */

/* Tarjetas de curso con video thumbnail */
.curso-card {
    background: var(--background-card);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-medium);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: var(--transition-base);
    position: relative;
    overflow: hidden;
}

.curso-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.06), transparent);
    animation: shimmerEffect 4s ease-in-out infinite;
    pointer-events: none;
}

.curso-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: var(--shadow-strong), 0 0 50px rgba(59, 130, 246, 0.15);
}

.curso-thumbnail {
    position: relative;
    width: 100%;
    height: 160px;
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    border-radius: var(--border-radius-sm);
    overflow: hidden;
    margin-bottom: 1rem;
}

.curso-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition-base);
}

.curso-card:hover .curso-thumbnail img {
    transform: scale(1.1);
}

.curso-play-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60px;
    height: 60px;
    background: rgba(220, 38, 38, 0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    transition: var(--transition-bounce);
    backdrop-filter: blur(10px);
}

.curso-card:hover .curso-play-overlay {
    transform: translate(-50%, -50%) scale(1.2);
    background: rgba(220, 38, 38, 1);
}

.curso-nivel-badge {
    position: absolute;
    top: 0.8rem;
    right: 0.8rem;
    padding: 0.3rem 0.8rem;
    border-radius: var(--border-radius-sm);
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.curso-nivel-principiante {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: white;
}

.curso-nivel-intermedio {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24);
    color: white;
}

.curso-nivel-avanzado {
    background: linear-gradient(135deg, var(--danger-color), #f87171);
    color: white;
}

.curso-info {
    flex: 1;
}

.curso-titulo {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    line-height: 1.3;
    transition: var(--transition-base);
}

.curso-card:hover .curso-titulo {
    color: var(--secondary-blue);
}

.curso-descripcion {
    color: var(--text-secondary);
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.curso-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.curso-docente {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.85rem;
}

.curso-docente i {
    color: var(--primary-red);
}

.curso-duracion {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.85rem;
}

.curso-duracion i {
    color: var(--accent-orange);
}

.curso-precio {
    font-size: 1.2rem;
    font-weight: 900;
    color: var(--primary-red);
    text-align: right;
}

.curso-precio-free {
    color: var(--success-color);
}

/* Grid responsivo de cursos */
.cursos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

/* Filtros específicos para cursos */
.curso-filters-advanced {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr auto;
    gap: 1rem;
    align-items: end;
}

/* Categoría badge mejorado */
.curso-categoria-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.3rem 0.8rem;
    border-radius: var(--border-radius-sm);
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.8rem;
}

/* Estados específicos de curso */
.curso-estado-publicado {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: white;
}

.curso-estado-borrador {
    background: linear-gradient(135deg, #6b7280, #9ca3af);
    color: white;
}

.curso-estado-archivado {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24);
    color: white;
}

/* Vista de lista vs grid */
.curso-view-toggle {
    display: flex;
    gap: 0.5rem;
    margin-left: auto;
}

.curso-view-btn {
    padding: 0.5rem;
    border: 2px solid rgba(0, 0, 0, 0.1);
    background: transparent;
    border-radius: var(--border-radius-sm);
    cursor: pointer;
    transition: var(--transition-base);
    color: var(--text-secondary);
}

.curso-view-btn.active {
    background: var(--secondary-blue);
    color: white;
    border-color: var(--secondary-blue);
}

.curso-view-btn:hover {
    background: var(--secondary-blue-light);
    color: white;
}

/* Vista en lista */
.cursos-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.curso-list-item {
    display: flex;
    background: var(--background-card);
    border-radius: var(--border-radius-md);
    padding: 1.5rem;
    box-shadow: var(--shadow-light);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: var(--transition-base);
    align-items: center;
    gap: 1.5rem;
}

.curso-list-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-medium);
}

.curso-list-thumbnail {
    width: 120px;
    height: 80px;
    border-radius: var(--border-radius-sm);
    overflow: hidden;
    flex-shrink: 0;
    position: relative;
}

.curso-list-info {
    flex: 1;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr auto;
    gap: 1rem;
    align-items: center;
}

/* Catálogo botón especial */
.crud-btn-catalogo {
    background: linear-gradient(135deg, var(--accent-teal), var(--accent-cyan));
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: var(--border-radius-md);
    font-weight: 700;
    font-size: 0.9rem;
    cursor: pointer;
    transition: var(--transition-bounce);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.8rem;
    box-shadow: var(--shadow-medium);
    position: relative;
    overflow: hidden;
}

.crud-btn-catalogo::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: var(--transition-base);
}

.crud-btn-catalogo:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: var(--shadow-strong);
    background: linear-gradient(135deg, #0f766e, var(--accent-teal));
}

.crud-btn-catalogo:hover::before {
    left: 100%;
}

/* Responsive específico para cursos */
@media (max-width: 1200px) {
    .curso-filters-advanced {
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .cursos-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }
}

@media (max-width: 768px) {
    .curso-filters-advanced {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .cursos-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .curso-list-info {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .curso-list-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .curso-list-thumbnail {
        width: 100%;
        height: 160px;
    }
}

/* Dark mode específico para cursos */
body.ithr-dark-mode .curso-card,
body.dark-theme .curso-card,
body.ithr-dark-mode .curso-list-item,
body.dark-theme .curso-list-item {
    background: var(--background-card);
    border-color: rgba(71, 85, 105, 0.3);
}

body.ithr-dark-mode .curso-thumbnail,
body.dark-theme .curso-thumbnail {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(30, 41, 59, 0.7));
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
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="crud-section-title-group">
                        <h1 class="crud-section-title">Gestión de Cursos</h1>
                        <p class="crud-section-subtitle">
                            <?= $isDocente ? 'Administra tus cursos asignados' : 'Administra todos los cursos de la plataforma' ?>
                        </p>
                    </div>
                </div>
                <div class="crud-section-header-actions">
                    <a href="<?= route('cursos.catalogo'); ?>" class="crud-btn-catalogo">
                        <i class="fas fa-eye"></i>
                        Catálogo de cursos
                    </a>
                    <a href="<?= route('cursos.crear'); ?>" class="crud-section-action-header">
                        <i class="fas fa-plus-circle"></i>
                        Crear Nuevo Curso
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas de cursos -->
        <div class="crud-section-card">
            <div class="crud-stats-grid">
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-blue">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Total Cursos</h4>
                        <div class="crud-stat-number"><?= $estadisticas['total'] ?? count($cursos) ?></div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Publicados</h4>
                        <div class="crud-stat-number">
                            <?= $estadisticas['publicados'] ?? count(array_filter($cursos, fn($c) => ($c['estado'] ?? '') == 'Publicado')) ?>
                        </div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-yellow">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Borradores</h4>
                        <div class="crud-stat-number">
                            <?= $estadisticas['borradores'] ?? count(array_filter($cursos, fn($c) => ($c['estado'] ?? '') == 'Borrador')) ?>
                        </div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-red">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Archivados</h4>
                        <div class="crud-stat-number">
                            <?= $estadisticas['archivados'] ?? count(array_filter($cursos, fn($c) => ($c['estado'] ?? '') == 'Archivado')) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de búsqueda modernos -->
        <div class="crud-section-card">
            <div class="crud-filters-container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h5 class="crud-filters-title">
                        <i class="fas fa-filter"></i>
                        Filtros de Búsqueda
                    </h5>
                    <div class="curso-view-toggle">
                        <button type="button" class="curso-view-btn active" id="gridViewBtn" title="Vista en Grid">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button" class="curso-view-btn" id="listViewBtn" title="Vista en Lista">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
                <div class="curso-filters-advanced">
                    <div class="crud-form-group">
                        <label for="cursoFilterName">Buscar curso:</label>
                        <input type="text" class="crud-form-control" id="cursoFilterName" placeholder="Buscar por título o descripción...">
                    </div>
                    <div class="crud-form-group">
                        <label for="cursoFilterCategoria">Categoría:</label>
                        <select class="crud-form-control" id="cursoFilterCategoria">
                            <option value="">Todas las categorías</option>
                            <option value="robotica">Robótica</option>
                            <option value="programacion">Programación</option>
                            <option value="electronica">Electrónica</option>
                            <option value="ia">Inteligencia Artificial</option>
                            <option value="datos">Ciencias de Datos</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label for="cursoFilterNivel">Nivel:</label>
                        <select class="crud-form-control" id="cursoFilterNivel">
                            <option value="">Todos los niveles</option>
                            <option value="Principiante">Principiante</option>
                            <option value="Intermedio">Intermedio</option>
                            <option value="Avanzado">Avanzado</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label for="cursoFilterEstado">Estado:</label>
                        <select class="crud-form-control" id="cursoFilterEstado">
                            <option value="">Todos los estados</option>
                            <option value="Publicado">Publicado</option>
                            <option value="Borrador">Borrador</option>
                            <option value="Archivado">Archivado</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <button type="button" class="crud-btn crud-btn-clear" onclick="cursosClearFilters()">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensajes de éxito/error -->
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

        <!-- Lista de cursos -->
        <div class="crud-section-card">
            <?php if (!empty($cursos)): ?>
                <!-- Vista en Grid (por defecto) -->
                <div class="cursos-grid" id="cursosGridView">
                    <?php foreach ($cursos as $curso): ?>
                        <div class="curso-card" data-curso-id="<?= $curso['id'] ?>">
                            <div class="curso-thumbnail">
                                <?php if (!empty($curso['imagen_portada'])): ?>
                                    <img src="<?= asset('imagenes/cursos/' . $curso['imagen_portada']) ?>" alt="<?= htmlspecialchars($curso['titulo']) ?>">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, <?= $curso['categoria_color'] ?? '#3498db' ?>, <?= $curso['categoria_color'] ?? '#3498db' ?>33); display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                        <i class="<?= $curso['categoria_icono'] ?? 'fas fa-play-circle' ?>"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="curso-play-overlay">
                                    <i class="fas fa-play"></i>
                                </div>
                                <span class="curso-nivel-badge curso-nivel-<?= strtolower($curso['nivel']) ?>">
                                    <?= htmlspecialchars($curso['nivel']) ?>
                                </span>
                            </div>
                            
                            <?php if (!empty($curso['categoria_nombre'])): ?>
                                <div class="curso-categoria-badge" style="background-color: <?= $curso['categoria_color'] ?? '#3498db' ?>;">
                                    <i class="<?= $curso['categoria_icono'] ?? 'fas fa-tag' ?>"></i>
                                    <?= htmlspecialchars($curso['categoria_nombre']) ?>
                                </div>
                            <?php endif; ?>

                            <div class="curso-info">
                                <h3 class="curso-titulo"><?= htmlspecialchars($curso['titulo']) ?></h3>
                                <p class="curso-descripcion"><?= htmlspecialchars($curso['descripcion']) ?></p>
                                
                                <div class="curso-meta">
                                    <div class="curso-docente">
                                        <i class="fas fa-user-tie"></i>
                                        <span><?= htmlspecialchars(($curso['docente_nombre'] ?? 'No asignado') . ' ' . ($curso['docente_apellido'] ?? '')) ?></span>
                                    </div>
                                    <div class="curso-duracion">
                                        <i class="fas fa-clock"></i>
                                        <span><?= $curso['duracion_horas'] ?? 0 ?>h</span>
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div class="crud-action-buttons">
                                        <a href="<?= route('cursos.ver', ['id' => $curso['id']]) ?>" 
                                           class="crud-btn-sm crud-btn-outline-primary" 
                                           title="Ver Curso">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= route('cursos.editar', ['id' => $curso['id']]) ?>" 
                                           class="crud-btn-sm crud-btn-outline-info" 
                                           title="Editar Curso">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="crud-btn-sm crud-btn-outline-warning curso-btn-toggle-status" 
                                                data-curso-id="<?= $curso['id'] ?>" 
                                                data-curso-titulo="<?= htmlspecialchars($curso['titulo']) ?>"
                                                data-current-status="<?= $curso['estado'] ?>"
                                                data-status-url="<?= route('cursos.estado', ['id' => $curso['id']]) ?>"
                                                title="Cambiar Estado">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                        <button type="button" 
                                                class="crud-btn-sm crud-btn-outline-danger curso-btn-delete" 
                                                data-curso-id="<?= $curso['id'] ?>" 
                                                data-curso-titulo="<?= htmlspecialchars($curso['titulo']) ?>"
                                                data-delete-url="<?= route('cursos.delete', ['id' => $curso['id']]) ?>"
                                                title="Eliminar Curso">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="curso-precio <?= empty($curso['precio']) || $curso['precio'] == 0 ? 'curso-precio-free' : '' ?>">
                                        <?= empty($curso['precio']) || $curso['precio'] == 0 ? 'Gratis' : '$' . number_format($curso['precio'], 2) ?>
                                    </div>
                                </div>
                                
                                <div style="margin-top: 0.8rem;">
                                    <span class="crud-badge curso-estado-<?= strtolower($curso['estado']) ?>">
                                        <?php
                                        $iconos = [
                                            'Publicado' => 'fas fa-check-circle',
                                            'Borrador' => 'fas fa-edit',
                                            'Archivado' => 'fas fa-archive'
                                        ];
                                        ?>
                                        <i class="<?= $iconos[$curso['estado']] ?? 'fas fa-question-circle' ?>"></i>
                                        <?= htmlspecialchars($curso['estado']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Vista en Lista (oculta por defecto) -->
                <div class="cursos-list" id="cursosListView" style="display: none;">
                    <?php foreach ($cursos as $curso): ?>
                        <div class="curso-list-item" data-curso-id="<?= $curso['id'] ?>">
                            <div class="curso-list-thumbnail">
                                <?php if (!empty($curso['imagen_portada'])): ?>
                                    <img src="<?= asset('imagenes/cursos/' . $curso['imagen_portada']) ?>" alt="<?= htmlspecialchars($curso['titulo']) ?>">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, <?= $curso['categoria_color'] ?? '#3498db' ?>, <?= $curso['categoria_color'] ?? '#3498db' ?>33); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                        <i class="<?= $curso['categoria_icono'] ?? 'fas fa-play-circle' ?>"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="curso-list-info">
                                <div>
                                    <h3 class="curso-titulo" style="margin-bottom: 0.3rem;"><?= htmlspecialchars($curso['titulo']) ?></h3>
                                    <div class="curso-docente" style="margin-bottom: 0.5rem;">
                                        <i class="fas fa-user-tie"></i>
                                        <span><?= htmlspecialchars(($curso['docente_nombre'] ?? 'No asignado') . ' ' . ($curso['docente_apellido'] ?? '')) ?></span>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <?php if (!empty($curso['categoria_nombre'])): ?>
                                        <span class="crud-badge" style="background-color: <?= $curso['categoria_color'] ?? '#3498db' ?>;">
                                            <?= htmlspecialchars($curso['categoria_nombre']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="text-center">
                                    <span class="curso-nivel-badge curso-nivel-<?= strtolower($curso['nivel']) ?>">
                                        <?= htmlspecialchars($curso['nivel']) ?>
                                    </span>
                                </div>
                                
                                <div class="text-center">
                                    <span class="crud-badge curso-estado-<?= strtolower($curso['estado']) ?>">
                                        <?= htmlspecialchars($curso['estado']) ?>
                                    </span>
                                </div>
                                
                                <div class="crud-action-buttons">
                                    <a href="<?= route('cursos.ver', ['id' => $curso['id']]) ?>" 
                                       class="crud-btn-sm crud-btn-outline-primary" 
                                       title="Ver Curso">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= route('cursos.editar', ['id' => $curso['id']]) ?>" 
                                       class="crud-btn-sm crud-btn-outline-info" 
                                       title="Editar Curso">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="crud-btn-sm crud-btn-outline-danger curso-btn-delete" 
                                            data-curso-id="<?= $curso['id'] ?>" 
                                            data-curso-titulo="<?= htmlspecialchars($curso['titulo']) ?>"
                                            data-delete-url="<?= route('cursos.delete', ['id' => $curso['id']]) ?>"
                                            title="Eliminar Curso">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="crud-empty-state">
                    <div class="crud-empty-state-icon">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <h3>No hay cursos disponibles</h3>
                    <p>
                        <?= $isDocente ? 'Aún no tienes cursos asignados.' : 'Comienza creando el primer curso de la plataforma.' ?> 
                        Publica contenido educativo de calidad para estudiantes.
                    </p>
                    <a href="<?= route('cursos.crear'); ?>" class="crud-btn-primary">
                        <i class="fas fa-plus"></i>
                        Crear Primer Curso
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Espacio de separación -->
        <div style="height: 40px;"></div>

    </div>
</div>

<!-- Modal de confirmación de eliminación de curso -->
<div class="crud-modal" id="crudDeleteCursoModal" tabindex="-1" role="dialog" aria-labelledby="crudDeleteCursoModalLabel" aria-hidden="true">
    <div class="crud-modal-dialog" role="document">
        <div class="crud-modal-content">
            <form id="crudDeleteCursoForm" method="POST" action="">
                <?= CSRF() ?>
                <input type="hidden" name="_method" value="DELETE">
                
                <div class="crud-modal-header">
                    <h5 class="crud-modal-title" id="crudDeleteCursoModalLabel">
                        <i class="fas fa-exclamation-triangle"></i>
                        Confirmar Eliminación de Curso
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="text-center">
                        <div class="crud-warning-icon">
                            <i class="fas fa-play-circle"></i>
                        </div>
                        <h4 class="crud-mb-3">¿Estás seguro de eliminar este curso?</h4>
                        <p class="crud-text-muted crud-mb-2">Esta acción no se puede deshacer.</p>
                        <div class="crud-user-to-delete-info">
                            <strong>Curso: <span id="crudCursoTituloToDelete" class="crud-text-danger"></span></strong>
                        </div>
                    </div>
                    <div class="crud-alert crud-alert-danger crud-mt-3" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>¡Atención!</strong> El curso será eliminado permanentemente junto con:
                            <ul class="crud-mt-2 crud-mb-0">
                                <li>Todo su contenido educativo</li>
                                <li>Archivos y recursos adjuntos</li>
                                <li>Estadísticas e información asociada</li>
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
                        <i class="fas fa-trash"></i>
                        Eliminar Curso
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de cambio de estado de curso -->
<div class="crud-modal" id="crudToggleEstadoCursoModal" tabindex="-1" role="dialog" aria-labelledby="crudToggleEstadoCursoModalLabel" aria-hidden="true">
    <div class="crud-modal-dialog" role="document">
        <div class="crud-modal-content">
            <form id="crudToggleEstadoCursoForm" method="POST" action="">
                <?= CSRF() ?>
                
                <div class="crud-modal-header">
                    <h5 class="crud-modal-title" id="crudToggleEstadoCursoModalLabel">
                        <i class="fas fa-exchange-alt"></i>
                        Cambiar Estado del Curso
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="text-center">
                        <div class="crud-status-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <h4 class="crud-mb-3" id="crudEstadoModalTitle">¿Cambiar estado del curso?</h4>
                        <div class="crud-user-status-info">
                            <strong>Curso: <span id="crudCursoTituloToToggle" class="crud-text-info"></span></strong>
                            <p class="crud-mt-2" id="crudEstadoModalDescription">Selecciona el nuevo estado para el curso.</p>
                        </div>
                        
                        <div class="crud-form-group crud-mt-3">
                            <label for="crudNuevoEstadoCurso">Nuevo Estado:</label>
                            <select class="crud-form-control" id="crudNuevoEstadoCurso" name="estado" required>
                                <option value="Borrador">Borrador</option>
                                <option value="Publicado">Publicado</option>
                                <option value="Archivado">Archivado</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="crud-modal-footer">
                    <button type="button" class="crud-btn crud-btn-clear" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="crud-btn crud-btn-outline-warning" id="crudConfirmEstadoToggle">
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

<!-- JavaScript específico para cursos CRUD -->
<script>
// Funcionalidad de filtros y vista modernizada para cursos
document.addEventListener('DOMContentLoaded', function() {
    const cursoFilterName = document.getElementById('cursoFilterName');
    const cursoFilterCategoria = document.getElementById('cursoFilterCategoria');
    const cursoFilterNivel = document.getElementById('cursoFilterNivel');
    const cursoFilterEstado = document.getElementById('cursoFilterEstado');
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const cursosGridView = document.getElementById('cursosGridView');
    const cursosListView = document.getElementById('cursosListView');
    const cursoCards = document.querySelectorAll('.curso-card, .curso-list-item');

    // Toggle entre vista de grid y lista
    gridViewBtn?.addEventListener('click', function() {
        gridViewBtn.classList.add('active');
        listViewBtn.classList.remove('active');
        cursosGridView.style.display = 'grid';
        cursosListView.style.display = 'none';
    });

    listViewBtn?.addEventListener('click', function() {
        listViewBtn.classList.add('active');
        gridViewBtn.classList.remove('active');
        cursosGridView.style.display = 'none';
        cursosListView.style.display = 'flex';
    });

    function cursosApplyFilters() {
        const nameFilter = cursoFilterName?.value.toLowerCase() || '';
        const categoriaFilter = cursoFilterCategoria?.value.toLowerCase() || '';
        const nivelFilter = cursoFilterNivel?.value || '';
        const estadoFilter = cursoFilterEstado?.value || '';

        cursoCards.forEach(card => {
            const titulo = card.querySelector('.curso-titulo')?.textContent.toLowerCase() || '';
            const descripcion = card.querySelector('.curso-descripcion')?.textContent.toLowerCase() || '';
            const categoria = card.querySelector('[style*="background-color"]')?.textContent.toLowerCase() || '';
            const nivelBadge = card.querySelector('.curso-nivel-badge')?.textContent || '';
            const estadoBadge = card.querySelector('[class*="curso-estado-"]')?.textContent || '';

            let showCard = true;

            // Filtro por nombre/descripción
            if (nameFilter && !titulo.includes(nameFilter) && !descripcion.includes(nameFilter)) {
                showCard = false;
            }

            // Filtro por categoría
            if (categoriaFilter && !categoria.includes(categoriaFilter)) {
                showCard = false;
            }

            // Filtro por nivel
            if (nivelFilter && nivelBadge !== nivelFilter) {
                showCard = false;
            }

            // Filtro por estado
            if (estadoFilter && estadoBadge !== estadoFilter) {
                showCard = false;
            }

            card.style.display = showCard ? '' : 'none';
        });
    }

    // Event listeners para los filtros
    cursoFilterName?.addEventListener('keyup', cursosApplyFilters);
    cursoFilterCategoria?.addEventListener('change', cursosApplyFilters);
    cursoFilterNivel?.addEventListener('change', cursosApplyFilters);
    cursoFilterEstado?.addEventListener('change', cursosApplyFilters);

    // Función para limpiar filtros
    window.cursosClearFilters = function() {
        if (cursoFilterName) cursoFilterName.value = '';
        if (cursoFilterCategoria) cursoFilterCategoria.value = '';
        if (cursoFilterNivel) cursoFilterNivel.value = '';
        if (cursoFilterEstado) cursoFilterEstado.value = '';
        
        // Mostrar todas las tarjetas
        cursoCards.forEach(card => {
            card.style.display = '';
        });
    };

    // Manejo de modales modernos
    const crudDeleteModal = document.getElementById('crudDeleteCursoModal');
    const crudEstadoModal = document.getElementById('crudToggleEstadoCursoModal');
    const crudOverlay = document.getElementById('crudCustomModalOverlay');

    // Botones de eliminar curso
    document.querySelectorAll('.curso-btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const cursoId = this.dataset.cursoId;
            const cursoTitulo = this.dataset.cursoTitulo;
            const deleteUrl = this.dataset.deleteUrl;

            document.getElementById('crudCursoTituloToDelete').textContent = cursoTitulo;
            document.getElementById('crudDeleteCursoForm').action = deleteUrl;

            crudShowModal(crudDeleteModal);
        });
    });

    // Botones de cambio de estado
    document.querySelectorAll('.curso-btn-toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const cursoId = this.dataset.cursoId;
            const cursoTitulo = this.dataset.cursoTitulo;
            const currentStatus = this.dataset.currentStatus;
            const statusUrl = this.dataset.statusUrl;

            document.getElementById('crudCursoTituloToToggle').textContent = cursoTitulo;
            document.getElementById('crudToggleEstadoCursoForm').action = statusUrl;

            // Preseleccionar el estado actual
            const selectEstado = document.getElementById('crudNuevoEstadoCurso');
            if (selectEstado) {
                selectEstado.value = currentStatus;
            }

            crudShowModal(crudEstadoModal);
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
    crudOverlay?.addEventListener('click', function() {
        document.querySelectorAll('.crud-modal.show').forEach(modal => {
            crudHideModal(modal);
        });
    });

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

    // Animaciones de hover mejoradas para las tarjetas
    cursoCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>