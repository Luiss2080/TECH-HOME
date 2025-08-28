<?php
$title = $title ?? 'Gestión de Libros';
$libros = $libros ?? [];
$categorias = $categorias ?? [];
?>

<!-- FontAwesome para iconos -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Estilos específicos para el módulo CRUD -->
<link rel="stylesheet" href="<?= asset('css/index.css'); ?>">

<!-- CSS específico para el módulo de libros -->
<style>
/* ============================================
   ESTILOS ESPECÍFICOS PARA GESTIÓN DE LIBROS
   ============================================ */

/* Portadas de libros - TAMAÑO REDUCIDO */
.crud-book-cover {
    width: 35px;  /* Reducido de 50px a 35px */
    height: 50px; /* Reducido de 70px a 50px */
    border-radius: var(--border-radius-sm);
    object-fit: cover;
    border: 2px solid rgba(220, 38, 38, 0.2);
    transition: var(--transition-base);
    box-shadow: var(--shadow-light);
}

.crud-book-cover:hover {
    transform: scale(1.1);
    border-color: var(--primary-red);
    box-shadow: var(--shadow-medium);
}

.crud-book-placeholder {
    width: 35px;  /* Reducido de 50px a 35px */
    height: 50px; /* Reducido de 70px a 50px */
    background: linear-gradient(135deg, var(--text-light), #cbd5e1);
    border-radius: var(--border-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem; /* Reducido de 1.5rem a 1.2rem */
    border: 2px solid rgba(220, 38, 38, 0.2);
    transition: var(--transition-base);
}

.crud-book-placeholder:hover {
    transform: scale(1.1);
    border-color: var(--primary-red);
    box-shadow: var(--shadow-medium);
}

/* Información del libro */
.crud-book-info {
    display: flex;
    align-items: center;
    gap: 0.75rem; /* Reducido de 1rem a 0.75rem */
}

.crud-book-cover-container {
    flex-shrink: 0; /* Evita que la imagen se comprima */
}

.crud-book-details {
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    flex: 1;
}

.crud-book-title {
    font-weight: 700;
    font-size: 0.9rem; /* Reducido ligeramente */
    color: var(--text-primary);
    transition: var(--transition-base);
    line-height: 1.2;
}

.crud-data-table tbody tr:hover .crud-book-title {
    color: var(--primary-red);
}

.crud-book-author {
    font-size: 0.75rem; /* Reducido de 0.8rem */
    color: var(--text-light);
    font-style: italic;
}

.crud-book-isbn {
    font-size: 0.7rem; /* Reducido de 0.75rem */
    color: var(--text-secondary);
    font-family: 'Courier New', monospace;
}

/* Control de stock visual */
.crud-stock-control {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
}

.crud-stock-number {
    font-weight: 800;
    font-size: 1.1rem;
    color: var(--text-primary);
    transition: var(--transition-base);
}

.crud-stock-bar {
    width: 60px;
    height: 6px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.crud-stock-fill {
    height: 100%;
    border-radius: 10px;
    transition: var(--transition-base);
    position: relative;
}

.crud-stock-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
    animation: stockShimmer 2s ease-in-out infinite;
}

.crud-stock-high {
    background: linear-gradient(90deg, var(--success-color), #34d399);
}

.crud-stock-medium {
    background: linear-gradient(90deg, var(--warning-color), #fbbf24);
}

.crud-stock-low {
    background: linear-gradient(90deg, var(--danger-color), #f87171);
}

.crud-stock-status {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    margin-top: 0.2rem;
}

/* Badges para categorías */
.crud-badge-category {
    background: linear-gradient(135deg, var(--accent-teal), #22d3ee);
    color: white;
}

.crud-badge-gratuito {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: white;
}

.crud-badge-pago {
    background: linear-gradient(135deg, var(--accent-orange), #fbbf24);
    color: white;
}

/* Precio del libro */
.crud-book-price {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
}

.crud-price-amount {
    font-weight: 800;
    font-size: 1.1rem;
    color: var(--text-primary);
}

.crud-price-free {
    color: var(--success-color);
    font-weight: 700;
}

.crud-price-paid {
    color: var(--accent-orange);
    font-weight: 700;
}

/* Botón de catálogo */
.crud-btn-catalog {
    background: linear-gradient(135deg, var(--primary-red), var(--primary-red-light));
    color: white;
    border: none;
    padding: 1rem 1.5rem;
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

.crud-btn-catalog::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: var(--transition-base);
}

.crud-btn-catalog:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: var(--shadow-strong);
    background: linear-gradient(135deg, var(--primary-red-dark), var(--primary-red));
}

.crud-btn-catalog:hover::before {
    left: 100%;
}

/* Animaciones específicas para libros */
@keyframes stockShimmer {
    0%, 100% { left: -100%; }
    50% { left: 100%; }
}

@keyframes bookFloat {
    0%, 100% { 
        transform: translateY(0) rotate(0deg); 
    }
    50% { 
        transform: translateY(-5px) rotate(1deg); 
    }
}

/* Efecto hover para las filas de libros */
.crud-data-table tbody tr:hover .crud-book-cover,
.crud-data-table tbody tr:hover .crud-book-placeholder {
    animation: bookFloat 1s ease-in-out;
}

.crud-data-table tbody tr:hover .crud-stock-number {
    color: var(--primary-red);
    transform: scale(1.1);
}

/* Estado del libro con iconos */
.crud-book-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Responsive específico para libros */
@media (max-width: 768px) {
    .crud-book-cover,
    .crud-book-placeholder {
        width: 35px;
        height: 50px;
    }
    
    .crud-book-title {
        font-size: 0.9rem;
    }
    
    .crud-book-author {
        font-size: 0.7rem;
    }
    
    .crud-stock-control {
        gap: 0.2rem;
    }
    
    .crud-stock-number {
        font-size: 1rem;
    }
    
    .crud-stock-bar {
        width: 40px;
        height: 4px;
    }
    
    .crud-btn-catalog {
        padding: 0.8rem 1.2rem;
        font-size: 0.8rem;
    }
}

/* Modo oscuro específico para libros */
body.ithr-dark-mode .crud-book-placeholder,
body.dark-theme .crud-book-placeholder {
    background: linear-gradient(135deg, #475569, #64748b);
}

body.ithr-dark-mode .crud-stock-bar,
body.dark-theme .crud-stock-bar {
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
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="crud-section-title-group">
                        <h1 class="crud-section-title">Gestión de Libros</h1>
                        <p class="crud-section-subtitle">Administra el inventario de libros y controla el stock disponible</p>
                    </div>
                </div>
                <div class="crud-section-header-actions">
                    <a href="<?= route('libros.crear'); ?>" class="crud-section-action-header">
                        <i class="fas fa-plus"></i>
                        Agregar Nuevo Libro
                    </a>
                    <a href="<?= route('libros.catalogo'); ?>" class="crud-btn-catalog">
                        <i class="fas fa-list-ul"></i>
                        Catálogo de Libros
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas de libros -->
        <div class="crud-section-card">
            <div class="crud-stats-grid">
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-blue">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Total Libros</h4>
                        <div class="crud-stat-number"><?= count($libros) ?></div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>En Stock</h4>
                        <div class="crud-stat-number">
                            <?= count(array_filter($libros, fn($l) => $l['stock'] > 0)) ?>
                        </div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-yellow">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Stock Bajo</h4>
                        <div class="crud-stat-number">
                            <?= count(array_filter($libros, fn($l) => $l['stock'] > 0 && $l['stock'] <= ($l['stock_minimo'] ?? 5))) ?>
                        </div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-red">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Agotados</h4>
                        <div class="crud-stat-number">
                            <?= count(array_filter($libros, fn($l) => $l['stock'] == 0)) ?>
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
                        <input type="text" class="crud-form-control" id="crudFilterTitle" placeholder="Filtrar por título o autor...">
                    </div>
                    <div class="crud-form-group">
                        <label for="crudFilterCategory">Filtrar por categoría:</label>
                        <select class="crud-form-control" id="crudFilterCategory">
                            <option value="">Todas las categorías</option>
                            <option value="robótica">Robótica</option>
                            <option value="programación">Programación</option>
                            <option value="electrónica">Electrónica</option>
                            <option value="machine learning">Machine Learning</option>
                            <option value="matemáticas">Matemáticas</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label for="crudFilterStock">Filtrar por stock:</label>
                        <select class="crud-form-control" id="crudFilterStock">
                            <option value="">Todos los niveles</option>
                            <option value="disponible">Disponible</option>
                            <option value="bajo">Stock Bajo</option>
                            <option value="agotado">Agotado</option>
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

        <!-- Tabla de libros modernizada -->
        <div class="crud-section-card">
            <div class="crud-table-container">
                <?php if (!empty($libros)): ?>
                    <table class="crud-data-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-book"></i> LIBRO</th>
                                <th><i class="fas fa-tag"></i> CATEGORÍA</th>
                                <th><i class="fas fa-dollar-sign"></i> PRECIO</th>
                                <th><i class="fas fa-boxes"></i> STOCK</th>
                                <th><i class="fas fa-toggle-on"></i> ESTADO</th>
                                <th><i class="fas fa-calendar"></i> PUBLICADO</th>
                                <th><i class="fas fa-cogs"></i> ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($libros as $libro):
                                $libro = (object) $libro;
                                // Manejar stock_minimo con valor por defecto para evitar errores
                                $stock_minimo = $libro->stock_minimo ?? 5;
                                $stockPorcentaje = $stock_minimo > 0 ? ($libro->stock / ($stock_minimo * 2)) * 100 : 0;
                                $stockPorcentaje = min($stockPorcentaje, 100);
                                
                                $stockClass = 'crud-stock-high';
                                $stockStatus = 'ALTO';
                                if ($libro->stock == 0) {
                                    $stockClass = 'crud-stock-low';
                                    $stockStatus = 'AGOTADO';
                                    $stockPorcentaje = 0;
                                } elseif ($libro->stock <= $stock_minimo) {
                                    $stockClass = 'crud-stock-low';
                                    $stockStatus = 'BAJO';
                                } elseif ($libro->stock <= ($stock_minimo * 1.5)) {
                                    $stockClass = 'crud-stock-medium';
                                    $stockStatus = 'MEDIO';
                                }
                                ?>
                                <tr data-book-id="<?= $libro->id ?>" class="crud-table-row">
                                    <td class="crud-text-muted">#<?= $libro->id ?></td>
                                    <td>
                                        <div class="crud-book-info">
                                            <div class="crud-book-cover-container">
                                                <?php if (!empty($libro->imagen_portada)): ?>
                                                    <img src="<?= asset('imagenes/libros/' . $libro->imagen_portada) ?>" 
                                                         alt="Portada de <?= htmlspecialchars($libro->titulo) ?>"
                                                         class="crud-book-cover">
                                                <?php else: ?>
                                                    <div class="crud-book-placeholder">
                                                        <i class="fas fa-book"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="crud-book-details">
                                                <div class="crud-book-title"><?= htmlspecialchars($libro->titulo) ?></div>
                                                <div class="crud-book-author">Por: <?= htmlspecialchars($libro->autor) ?></div>
                                                <?php if (!empty($libro->isbn)): ?>
                                                    <div class="crud-book-isbn">ISBN: <?= htmlspecialchars($libro->isbn) ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="crud-badge crud-badge-category">
                                            <?= htmlspecialchars($libro->categoria_nombre ?? 'Sin categoría') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="crud-book-price">
                                            <?php if ($libro->es_gratuito): ?>
                                                <span class="crud-price-amount crud-price-free">GRATIS</span>
                                                <span class="crud-badge crud-badge-gratuito">
                                                    <i class="fas fa-gift"></i>
                                                    Gratuito
                                                </span>
                                            <?php else: ?>
                                                <span class="crud-price-amount crud-price-paid">$<?= number_format($libro->precio, 2) ?></span>
                                                <span class="crud-badge crud-badge-pago">
                                                    <i class="fas fa-dollar-sign"></i>
                                                    De Pago
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="crud-stock-control">
                                            <span class="crud-stock-number"><?= $libro->stock ?></span>
                                            <div class="crud-stock-bar">
                                                <div class="crud-stock-fill <?= $stockClass ?>" style="width: <?= $stockPorcentaje ?>%"></div>
                                            </div>
                                            <span class="crud-stock-status <?= strtolower($stockClass) ?>"><?= $stockStatus ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($libro->estado == 1): ?>
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
                                    <td class="crud-text-muted">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span><?= htmlspecialchars($libro->año_publicacion ?? 'N/A') ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="crud-action-buttons">
                                            <a href="<?= route('libros.editar', ['id' => $libro->id]) ?>" 
                                               class="crud-btn-sm crud-btn-outline-primary" 
                                               title="Editar Libro">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= route('libros.ver', ['id' => $libro->id]) ?>" 
                                               class="crud-btn-sm crud-btn-outline-info" 
                                               title="Ver Detalles">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button type="button" 
                                                    class="crud-btn-sm crud-btn-outline-warning crud-btn-stock" 
                                                    data-book-id="<?= $libro->id ?>" 
                                                    data-book-title="<?= htmlspecialchars($libro->titulo) ?>"
                                                    data-current-stock="<?= $libro->stock ?>"
                                                    data-stock-url="<?= route('libros.stock', ['id' => $libro->id]) ?>"
                                                    title="Actualizar Stock">
                                                <i class="fas fa-boxes"></i>
                                            </button>
                                            <button type="button" 
                                                    class="crud-btn-sm <?= $libro->estado == 1 ? 'crud-btn-outline-warning' : 'crud-btn-outline-success' ?> crud-btn-toggle-status" 
                                                    data-book-id="<?= $libro->id ?>" 
                                                    data-book-title="<?= htmlspecialchars($libro->titulo) ?>"
                                                    data-current-status="<?= $libro->estado ?>"
                                                    data-status-url="<?= route('libros.estado', ['id' => $libro->id]) ?>"
                                                    title="<?= $libro->estado == 1 ? 'Desactivar' : 'Activar' ?> Libro">
                                                <i class="fas fa-<?= $libro->estado == 1 ? 'ban' : 'check' ?>"></i>
                                            </button>
                                            <button type="button" 
                                                    class="crud-btn-sm crud-btn-outline-danger crud-btn-delete-book" 
                                                    data-book-id="<?= $libro->id ?>" 
                                                    data-book-title="<?= htmlspecialchars($libro->titulo) ?>"
                                                    data-delete-url="<?= route('libros.delete', ['id' => $libro->id]) ?>"
                                                    title="Eliminar Libro">
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
                            <i class="fas fa-book-open"></i>
                        </div>
                        <h3>No hay libros registrados</h3>
                        <p>Comienza agregando el primer libro al inventario para gestionar el catálogo y controlar el stock disponible.</p>
                        <a href="<?= route('libros.crear'); ?>" class="crud-btn-primary">
                            <i class="fas fa-plus"></i>
                            Agregar Primer Libro
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
<div class="crud-modal" id="crudDeleteBookModal" tabindex="-1" role="dialog" aria-labelledby="crudDeleteBookModalLabel" aria-hidden="true">
    <div class="crud-modal-dialog" role="document">
        <div class="crud-modal-content">
            <form id="crudDeleteBookForm" method="POST" action="">
                <?= CSRF() ?>
                <input type="hidden" name="_method" value="DELETE">
                
                <div class="crud-modal-header">
                    <h5 class="crud-modal-title" id="crudDeleteBookModalLabel">
                        <i class="fas fa-exclamation-triangle"></i>
                        Confirmar Eliminación de Libro
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="text-center">
                        <div class="crud-warning-icon">
                            <i class="fas fa-book-dead"></i>
                        </div>
                        <h4 class="crud-mb-3">¿Estás seguro de eliminar este libro?</h4>
                        <p class="crud-text-muted crud-mb-2">Esta acción no se puede deshacer.</p>
                        <div class="crud-user-to-delete-info">
                            <strong>Libro: <span id="crudBookTitleToDelete" class="crud-text-danger"></span></strong>
                        </div>
                    </div>
                    <div class="crud-alert crud-alert-danger crud-mt-3" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>¡Atención!</strong> El libro será eliminado permanentemente junto con:
                            <ul class="crud-mt-2 crud-mb-0">
                                <li>Toda la información del libro</li>
                                <li>Los archivos asociados (portada, PDF)</li>
                                <li>El historial de ventas relacionado</li>
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
                        <i class="fas fa-book-dead"></i>
                        Eliminar Libro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de actualización de stock -->
<div class="crud-modal" id="crudUpdateStockModal" tabindex="-1" role="dialog" aria-labelledby="crudUpdateStockModalLabel" aria-hidden="true">
    <div class="crud-modal-dialog" role="document">
        <div class="crud-modal-content">
            <form id="crudUpdateStockForm" method="POST" action="">
                <?= CSRF() ?>
                
                <div class="crud-modal-header">
                    <h5 class="crud-modal-title" id="crudUpdateStockModalLabel">
                        <i class="fas fa-boxes"></i>
                        Actualizar Stock del Libro
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="crud-form-group">
                        <label><strong>Libro: <span id="crudBookTitleToStock" class="crud-text-info"></span></strong></label>
                    </div>
                    <div class="crud-form-group">
                        <label>Stock Actual:</label>
                        <input type="number" class="crud-form-control" id="crudCurrentStock" readonly>
                    </div>
                    <div class="crud-form-group">
                        <label for="crudNewStock">Nuevo Stock:</label>
                        <input type="number" class="crud-form-control" id="crudNewStock" name="stock" min="0" required>
                    </div>
                    <div class="crud-form-group">
                        <label for="crudStockReason">Motivo del cambio:</label>
                        <textarea class="crud-form-control" id="crudStockReason" name="motivo" rows="3" placeholder="Ej: Ingreso de nuevos ejemplares, venta, devolución, etc."></textarea>
                    </div>
                </div>
                
                <div class="crud-modal-footer">
                    <button type="button" class="crud-btn crud-btn-clear" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="crud-btn crud-btn-outline-warning">
                        <i class="fas fa-save"></i>
                        Actualizar Stock
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
                        Cambiar Estado del Libro
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="text-center">
                        <div class="crud-status-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h4 class="crud-mb-3" id="crudStatusModalTitle">¿Cambiar estado del libro?</h4>
                        <div class="crud-user-status-info">
                            <strong>Libro: <span id="crudBookTitleToToggle" class="crud-text-info"></span></strong>
                            <p class="crud-mt-2" id="crudStatusModalDescription">El libro será activado/desactivado en el sistema.</p>
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

<!-- JavaScript específico para libros CRUD -->
<script>
// Funcionalidad específica para gestión de libros
document.addEventListener('DOMContentLoaded', function() {
    const crudFilterTitle = document.getElementById('crudFilterTitle');
    const crudFilterCategory = document.getElementById('crudFilterCategory');
    const crudFilterStock = document.getElementById('crudFilterStock');
    const crudTableRows = document.querySelectorAll('.crud-data-table tbody tr');

    function crudApplyFilters() {
        const titleFilter = crudFilterTitle.value.toLowerCase();
        const categoryFilter = crudFilterCategory.value.toLowerCase();
        const stockFilter = crudFilterStock.value;

        crudTableRows.forEach(row => {
            const titleCell = row.querySelector('.crud-book-title').textContent.toLowerCase();
            const authorCell = row.querySelector('.crud-book-author').textContent.toLowerCase();
            const categoryCell = row.querySelector('.crud-badge-category').textContent.toLowerCase();
            const stockNumber = parseInt(row.querySelector('.crud-stock-number').textContent);
            const stockStatus = row.querySelector('.crud-stock-status').textContent.toLowerCase();

            let showRow = true;

            // Filtro por título/autor
            if (titleFilter && !titleCell.includes(titleFilter) && !authorCell.includes(titleFilter)) {
                showRow = false;
            }

            // Filtro por categoría
            if (categoryFilter && !categoryCell.includes(categoryFilter)) {
                showRow = false;
            }

            // Filtro por stock
            if (stockFilter) {
                switch (stockFilter) {
                    case 'disponible':
                        if (stockNumber === 0) showRow = false;
                        break;
                    case 'bajo':
                        if (!stockStatus.includes('bajo')) showRow = false;
                        break;
                    case 'agotado':
                        if (stockNumber > 0) showRow = false;
                        break;
                }
            }

            row.style.display = showRow ? '' : 'none';
        });
    }

    // Event listeners para los filtros
    if (crudFilterTitle) crudFilterTitle.addEventListener('keyup', crudApplyFilters);
    if (crudFilterCategory) crudFilterCategory.addEventListener('change', crudApplyFilters);
    if (crudFilterStock) crudFilterStock.addEventListener('change', crudApplyFilters);

    // Función para limpiar filtros
    window.crudClearFilters = function() {
        if (crudFilterTitle) crudFilterTitle.value = '';
        if (crudFilterCategory) crudFilterCategory.value = '';
        if (crudFilterStock) crudFilterStock.value = '';
        
        crudTableRows.forEach(row => {
            row.style.display = '';
        });
    };

    // Manejo de modales
    const crudDeleteModal = document.getElementById('crudDeleteBookModal');
    const crudStatusModal = document.getElementById('crudToggleStatusModal');
    const crudStockModal = document.getElementById('crudUpdateStockModal');
    const crudOverlay = document.getElementById('crudCustomModalOverlay');

    // Botones de eliminar libro
    document.querySelectorAll('.crud-btn-delete-book').forEach(button => {
        button.addEventListener('click', function() {
            const bookId = this.dataset.bookId;
            const bookTitle = this.dataset.bookTitle;
            const deleteUrl = this.dataset.deleteUrl;

            document.getElementById('crudBookTitleToDelete').textContent = bookTitle;
            document.getElementById('crudDeleteBookForm').action = deleteUrl;

            crudShowModal(crudDeleteModal);
        });
    });

    // Botones de cambio de estado
    document.querySelectorAll('.crud-btn-toggle-status').forEach(button => {
        button.addEventListener('click', function() {
            const bookId = this.dataset.bookId;
            const bookTitle = this.dataset.bookTitle;
            const currentStatus = this.dataset.currentStatus;
            const statusUrl = this.dataset.statusUrl;

            document.getElementById('crudBookTitleToToggle').textContent = bookTitle;
            document.getElementById('crudToggleStatusForm').action = statusUrl;

            const modalTitle = document.getElementById('crudStatusModalTitle');
            const modalDescription = document.getElementById('crudStatusModalDescription');
            
            if (currentStatus === '1') {
                modalTitle.textContent = '¿Desactivar libro?';
                modalDescription.textContent = 'El libro será desactivado y no aparecerá en el catálogo.';
            } else {
                modalTitle.textContent = '¿Activar libro?';
                modalDescription.textContent = 'El libro será activado y aparecerá en el catálogo.';
            }

            crudShowModal(crudStatusModal);
        });
    });

    // Botones de actualizar stock
    document.querySelectorAll('.crud-btn-stock').forEach(button => {
        button.addEventListener('click', function() {
            const bookId = this.dataset.bookId;
            const bookTitle = this.dataset.bookTitle;
            const currentStock = this.dataset.currentStock;
            const stockUrl = this.dataset.stockUrl;

            document.getElementById('crudBookTitleToStock').textContent = bookTitle;
            document.getElementById('crudCurrentStock').value = currentStock;
            document.getElementById('crudNewStock').value = currentStock;
            document.getElementById('crudUpdateStockForm').action = stockUrl;

            crudShowModal(crudStockModal);
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

    // Validación del formulario de stock
    const stockForm = document.getElementById('crudUpdateStockForm');
    if (stockForm) {
        stockForm.addEventListener('submit', function(e) {
            const newStock = document.getElementById('crudNewStock').value;
            
            if (newStock < 0) {
                e.preventDefault();
                alert('El stock no puede ser negativo');
                return false;
            }
            
            if (newStock === '') {
                e.preventDefault();
                alert('Por favor ingresa un valor de stock válido');
                return false;
            }
        });
    }

    // Efecto de animación para las barras de stock al cargar la página
    setTimeout(() => {
        document.querySelectorAll('.crud-stock-fill').forEach(fill => {
            const width = fill.style.width;
            fill.style.width = '0%';
            setTimeout(() => {
                fill.style.width = width;
            }, 100);
        });
    }, 500);
});
</script>