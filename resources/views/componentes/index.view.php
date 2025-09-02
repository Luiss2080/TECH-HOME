<?php
$title = $title ?? 'Gestión de Componentes';
$componentes = $componentes ?? [];
$estadisticas = $estadisticas ?? [
    'total_componentes' => 0,
    'agotados' => 0,
    'stock_bajo' => 0,
    'valor_inventario' => 0
];
$categorias = $categorias ?? [];
$marcas = $marcas ?? [];
$filtros = $filtros ?? [];
?>

<!-- Estilos específicos para el módulo CRUD -->
<link rel="stylesheet" href="<?= asset('css/index.css'); ?>">

<!-- Estilos específicos para Componentes -->
<style>
/* ============================================
   ESTILOS ESPECÍFICOS PARA COMPONENTES
   ============================================ */

/* Tarjetas de componente con imagen */
.componente-card {
    background: var(--background-card);
    border-radius: var(--border-radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow-medium);
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: var(--transition-base);
    position: relative;
    overflow: hidden;
}

.componente-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(220, 38, 38, 0.06), transparent);
    animation: shimmerEffect 4s ease-in-out infinite;
    pointer-events: none;
}

.componente-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: var(--shadow-strong), 0 0 50px rgba(220, 38, 38, 0.15);
}

.componente-imagen {
    position: relative;
    width: 100%;
    height: 180px;
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    border-radius: var(--border-radius-sm);
    overflow: hidden;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.componente-imagen img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition-base);
}

.componente-card:hover .componente-imagen img {
    transform: scale(1.1);
}

.componente-imagen-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--text-light), rgba(156, 163, 175, 0.3));
    color: white;
    font-size: 2.5rem;
}

.componente-imagen-placeholder i {
    margin-bottom: 0.5rem;
}

.componente-imagen-placeholder span {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Badge de stock */
.componente-stock-badge {
    position: absolute;
    top: 0.8rem;
    right: 0.8rem;
    padding: 0.3rem 0.8rem;
    border-radius: var(--border-radius-sm);
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    backdrop-filter: blur(10px);
}

.stock-disponible {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: white;
}

.stock-bajo {
    background: linear-gradient(135deg, var(--warning-color), #fbbf24);
    color: white;
    animation: pulseWarning 2s ease-in-out infinite;
}

.stock-agotado {
    background: linear-gradient(135deg, var(--danger-color), #f87171);
    color: white;
}

/* Código de producto */
.componente-codigo {
    position: absolute;
    top: 0.8rem;
    left: 0.8rem;
    padding: 0.2rem 0.6rem;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    font-size: 0.65rem;
    font-weight: 600;
    border-radius: var(--border-radius-sm);
    font-family: 'Courier New', monospace;
    backdrop-filter: blur(10px);
}

.componente-info {
    flex: 1;
}

.componente-titulo {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    line-height: 1.3;
    transition: var(--transition-base);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.componente-card:hover .componente-titulo {
    color: var(--primary-red);
}

.componente-descripcion {
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

/* Meta información del componente */
.componente-meta {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.8rem;
    margin-bottom: 1rem;
}

.componente-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.85rem;
}

.componente-meta-item i {
    color: var(--primary-red);
    width: 12px;
}

.componente-marca {
    font-weight: 600;
    color: var(--text-primary);
}

/* Stock visual con barras */
.componente-stock-visual {
    margin-bottom: 1rem;
    padding: 0.8rem;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
    border-radius: var(--border-radius-sm);
    border: 1px solid rgba(0, 0, 0, 0.08);
}

.componente-stock-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.componente-stock-label {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.componente-stock-numeros {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
}

.stock-actual {
    font-weight: 700;
    color: var(--text-primary);
}

.stock-minimo {
    color: var(--text-light);
    font-size: 0.75rem;
}

.componente-stock-barra {
    width: 100%;
    height: 6px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 3px;
    overflow: hidden;
    position: relative;
}

.stock-barra-progreso {
    height: 100%;
    border-radius: 3px;
    transition: var(--transition-base);
    position: relative;
}

.stock-barra-progreso.stock-normal {
    background: linear-gradient(90deg, var(--success-color), #34d399);
}

.stock-barra-progreso.stock-warning {
    background: linear-gradient(90deg, var(--warning-color), #fbbf24);
}

.stock-barra-progreso.stock-critico {
    background: linear-gradient(90deg, var(--danger-color), #f87171);
}

/* Precio destacado */
.componente-precio {
    font-size: 1.4rem;
    font-weight: 900;
    color: var(--primary-red);
    text-align: right;
    margin-bottom: 1rem;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.precio-principal {
    line-height: 1;
}

.precio-unitario {
    font-size: 0.7rem;
    color: var(--text-light);
    font-weight: 500;
    margin-top: 0.2rem;
}

/* Categoría específica para componentes */
.componente-categoria-badge {
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
    color: white;
}

.categoria-electronica {
    background: linear-gradient(135deg, #3b82f6, #60a5fa);
}

.categoria-mecanica {
    background: linear-gradient(135deg, #6b7280, #9ca3af);
}

.categoria-sensores {
    background: linear-gradient(135deg, #8b5cf6, #a855f7);
}

.categoria-actuadores {
    background: linear-gradient(135deg, #f59e0b, #fbbf24);
}

.categoria-controladores {
    background: linear-gradient(135deg, #10b981, #34d399);
}

.categoria-comunicacion {
    background: linear-gradient(135deg, #06b6d4, #22d3ee);
}

/* Estados específicos de componente */
.componente-estado-disponible {
    background: linear-gradient(135deg, var(--success-color), #34d399);
    color: white;
}

.componente-estado-agotado {
    background: linear-gradient(135deg, var(--danger-color), #f87171);
    color: white;
}

.componente-estado-descontinuado {
    background: linear-gradient(135deg, #6b7280, #9ca3af);
    color: white;
}

/* Grid responsivo de componentes */
.componentes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

/* Filtros específicos para componentes */
.componente-filters-advanced {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto;
    gap: 1rem;
    align-items: end;
}

/* Vista en lista para componentes */
.componentes-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.componente-list-item {
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

.componente-list-item:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-medium);
}

.componente-list-imagen {
    width: 120px;
    height: 80px;
    border-radius: var(--border-radius-sm);
    overflow: hidden;
    flex-shrink: 0;
    position: relative;
}

.componente-list-info {
    flex: 1;
    display: grid;
    grid-template-columns: 2fr 1fr 1fr 1fr 1fr auto;
    gap: 1rem;
    align-items: center;
}

/* Botón de inventario especial */
.crud-btn-inventario {
    background: linear-gradient(135deg, var(--accent-purple), #a855f7);
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

.crud-btn-inventario::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: var(--transition-base);
}

.crud-btn-inventario:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: var(--shadow-strong);
    background: linear-gradient(135deg, #7c3aed, var(--accent-purple));
}

.crud-btn-inventario:hover::before {
    left: 100%;
}

/* Estadísticas específicas para inventario */
.componente-valor-inventario {
    font-size: 1.8rem;
    font-weight: 900;
    background: linear-gradient(135deg, var(--success-color), #34d399);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Alerta de stock */
.stock-alerta {
    position: absolute;
    top: -8px;
    right: -8px;
    width: 20px;
    height: 20px;
    background: var(--danger-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.6rem;
    font-weight: 700;
    animation: pulseWarning 2s ease-in-out infinite;
    border: 2px solid white;
    box-shadow: var(--shadow-medium);
}

/* Responsive específico para componentes */
@media (max-width: 1200px) {
    .componente-filters-advanced {
        grid-template-columns: 2fr 1fr 1fr;
        gap: 1rem;
    }
    
    .componentes-grid {
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1rem;
    }
}

@media (max-width: 768px) {
    .componente-filters-advanced {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .componentes-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .componente-list-info {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .componente-list-item {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .componente-list-imagen {
        width: 100%;
        height: 120px;
    }
    
    .componente-meta {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .componente-stock-visual {
        padding: 0.6rem;
    }
}

/* Dark mode específico para componentes */
body.ithr-dark-mode .componente-card,
body.dark-theme .componente-card,
body.ithr-dark-mode .componente-list-item,
body.dark-theme .componente-list-item {
    background: var(--background-card);
    border-color: rgba(71, 85, 105, 0.3);
}

body.ithr-dark-mode .componente-imagen,
body.dark-theme .componente-imagen {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(30, 41, 59, 0.7));
}

body.ithr-dark-mode .componente-stock-visual,
body.dark-theme .componente-stock-visual {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(30, 41, 59, 0.7));
    border-color: rgba(71, 85, 105, 0.3);
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
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="crud-section-title-group">
                        <h1 class="crud-section-title">Gestión de Componentes</h1>
                        <p class="crud-section-subtitle">
                            Administra el inventario de componentes electrónicos, mecánicos y tecnológicos
                        </p>
                    </div>
                </div>
                <div class="crud-section-header-actions">
                    <a href="<?= route('componentes.inventario'); ?>" class="crud-btn-inventario">
                        <i class="fas fa-boxes"></i>
                        Control de Inventario
                    </a>
                    <a href="<?= route('componentes.crear'); ?>" class="crud-section-action-header">
                        <i class="fas fa-plus-circle"></i>
                        Agregar Componente
                    </a>
                </div>
            </div>
        </div>

        <!-- Estadísticas de componentes -->
        <div class="crud-section-card">
            <div class="crud-stats-grid">
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-blue">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Total Componentes</h4>
                        <div class="crud-stat-number"><?= $estadisticas['total_componentes'] ?? count($componentes) ?></div>
                    </div>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-red">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Stock Agotado</h4>
                        <div class="crud-stat-number"><?= $estadisticas['agotados'] ?? 0 ?></div>
                    </div>
                    <?php if (($estadisticas['agotados'] ?? 0) > 0): ?>
                        <div class="stock-alerta">!</div>
                    <?php endif; ?>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-yellow">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Stock Bajo</h4>
                        <div class="crud-stat-number"><?= $estadisticas['stock_bajo'] ?? 0 ?></div>
                    </div>
                    <?php if (($estadisticas['stock_bajo'] ?? 0) > 0): ?>
                        <div class="stock-alerta">!</div>
                    <?php endif; ?>
                </div>
                <div class="crud-stat-item">
                    <div class="crud-stat-icon bg-green">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="crud-stat-content">
                        <h4>Valor Inventario</h4>
                        <div class="crud-stat-number componente-valor-inventario">
                            $<?= number_format($estadisticas['valor_inventario'] ?? 0, 2) ?>
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
                <div class="componente-filters-advanced">
                    <div class="crud-form-group">
                        <label for="componenteFilterName">Buscar componente:</label>
                        <input type="text" class="crud-form-control" id="componenteFilterName" 
                               placeholder="Buscar por nombre, código o descripción..."
                               value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>">
                    </div>
                    <div class="crud-form-group">
                        <label for="componenteFilterCategoria">Categoría:</label>
                        <select class="crud-form-control" id="componenteFilterCategoria">
                            <option value="">Todas las categorías</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria->id ?>" <?= ($filtros['categoria_id'] ?? '') == $categoria->id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria->nombre) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label for="componenteFilterMarca">Marca:</label>
                        <select class="crud-form-control" id="componenteFilterMarca">
                            <option value="">Todas las marcas</option>
                            <?php foreach ($marcas as $marca): ?>
                                <option value="<?= htmlspecialchars($marca) ?>" <?= ($filtros['marca'] ?? '') == $marca ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($marca) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label for="componenteFilterEstado">Estado:</label>
                        <select class="crud-form-control" id="componenteFilterEstado">
                            <option value="">Todos los estados</option>
                            <option value="Disponible" <?= ($filtros['estado'] ?? '') == 'Disponible' ? 'selected' : '' ?>>Disponible</option>
                            <option value="Agotado" <?= ($filtros['estado'] ?? '') == 'Agotado' ? 'selected' : '' ?>>Agotado</option>
                            <option value="Descontinuado" <?= ($filtros['estado'] ?? '') == 'Descontinuado' ? 'selected' : '' ?>>Descontinuado</option>
                        </select>
                    </div>
                    <div class="crud-form-group">
                        <label>
                            <input type="checkbox" id="componenteFilterStockBajo" <?= ($filtros['stock_bajo'] ?? false) ? 'checked' : '' ?>>
                            Solo stock bajo
                        </label>
                    </div>
                    <div class="crud-form-group">
                        <button type="button" class="crud-btn crud-btn-clear" onclick="componentesClearFilters()">
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

        <!-- Lista de componentes -->
        <div class="crud-section-card">
            <?php if (!empty($componentes)): ?>
                <!-- Vista en Grid (por defecto) -->
                <div class="componentes-grid" id="componentesGridView">
                    <?php foreach ($componentes as $componente): ?>
                        <div class="componente-card" data-componente-id="<?= $componente->id ?>">
                            <div class="componente-imagen">
                                <?php if (!empty($componente->imagen_principal)): ?>
                                    <img src="<?= asset('imagenes/componentes/' . $componente->imagen_principal) ?>" alt="<?= htmlspecialchars($componente->nombre) ?>">
                                <?php else: ?>
                                    <div class="componente-imagen-placeholder">
                                        <i class="fas fa-microchip"></i>
                                        <span>Sin Imagen</span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Badge de stock -->
                                <?php 
                                $stock = (int)($componente->stock ?? 0);
                                $stockMinimo = (int)($componente->stock_minimo ?? 0);
                                $stockClase = 'stock-disponible';
                                $stockTexto = 'Disponible';
                                
                                if ($stock <= 0) {
                                    $stockClase = 'stock-agotado';
                                    $stockTexto = 'Agotado';
                                } elseif ($stock <= $stockMinimo) {
                                    $stockClase = 'stock-bajo';
                                    $stockTexto = 'Stock Bajo';
                                }
                                ?>
                                <span class="componente-stock-badge <?= $stockClase ?>">
                                    <?= $stockTexto ?>
                                </span>
                                
                                <!-- Código de producto -->
                                <?php if (!empty($componente->codigo_producto)): ?>
                                    <div class="componente-codigo">
                                        <?= htmlspecialchars($componente->codigo_producto) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($componente->categoria_nombre)): ?>
                                <div class="componente-categoria-badge categoria-<?= strtolower(str_replace(' ', '-', $componente->categoria_nombre)) ?>">
                                    <i class="<?= $componente->categoria_icono ?? 'fas fa-tag' ?>"></i>
                                    <?= htmlspecialchars($componente->categoria_nombre) ?>
                                </div>
                            <?php endif; ?>

                            <div class="componente-info">
                                <h3 class="componente-titulo"><?= htmlspecialchars($componente->nombre) ?></h3>
                                <p class="componente-descripcion"><?= htmlspecialchars($componente->descripcion ?? '') ?></p>
                                
                                <div class="componente-meta">
                                    <div class="componente-meta-item">
                                        <i class="fas fa-industry"></i>
                                        <span class="componente-marca"><?= htmlspecialchars($componente->marca ?? 'Sin marca') ?></span>
                                    </div>
                                    <div class="componente-meta-item">
                                        <i class="fas fa-barcode"></i>
                                        <span><?= htmlspecialchars($componente->modelo ?? 'Sin modelo') ?></span>
                                    </div>
                                </div>

                                <!-- Información visual de stock -->
                                <div class="componente-stock-visual">
                                    <div class="componente-stock-info">
                                        <span class="componente-stock-label">Stock Disponible</span>
                                        <div class="componente-stock-numeros">
                                            <span class="stock-actual"><?= $stock ?></span>
                                            <span class="stock-minimo">/ min: <?= $stockMinimo ?></span>
                                        </div>
                                    </div>
                                    <div class="componente-stock-barra">
                                        <?php
                                        $porcentajeStock = $stockMinimo > 0 ? ($stock / $stockMinimo) * 100 : 100;
                                        $barraClase = 'stock-normal';
                                        if ($stock <= 0) {
                                            $porcentajeStock = 0;
                                            $barraClase = 'stock-critico';
                                        } elseif ($stock <= $stockMinimo) {
                                            $barraClase = 'stock-warning';
                                        }
                                        ?>
                                        <div class="stock-barra-progreso <?= $barraClase ?>" 
                                             style="width: <?= min($porcentajeStock, 100) ?>%"></div>
                                    </div>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div class="crud-action-buttons">
                                        <a href="<?= route('componentes.ver', ['id' => $componente->id]) ?>" 
                                           class="crud-btn-sm crud-btn-outline-primary" 
                                           title="Ver Componente">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= route('componentes.editar', ['id' => $componente->id]) ?>" 
                                           class="crud-btn-sm crud-btn-outline-info" 
                                           title="Editar Componente">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="crud-btn-sm crud-btn-outline-success componente-btn-ajustar-stock" 
                                                data-componente-id="<?= $componente->id ?>" 
                                                data-componente-nombre="<?= htmlspecialchars($componente->nombre) ?>"
                                                data-stock-actual="<?= $stock ?>"
                                                title="Ajustar Stock">
                                            <i class="fas fa-boxes"></i>
                                        </button>
                                        <button type="button" 
                                                class="crud-btn-sm crud-btn-outline-danger componente-btn-delete" 
                                                data-componente-id="<?= $componente->id ?>" 
                                                data-componente-nombre="<?= htmlspecialchars($componente->nombre) ?>"
                                                data-delete-url="<?= route('componentes.delete', ['id' => $componente->id]) ?>"
                                                title="Eliminar Componente">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="componente-precio">
                                        <div class="precio-principal">
                                            $<?= number_format($componente->precio ?? 0, 2) ?>
                                        </div>
                                        <div class="precio-unitario">por unidad</div>
                                    </div>
                                </div>
                                
                                <div style="margin-top: 0.8rem;">
                                    <span class="crud-badge componente-estado-<?= strtolower($componente->estado ?? 'disponible') ?>">
                                        <?php
                                        $iconosEstado = [
                                            'Disponible' => 'fas fa-check-circle',
                                            'Agotado' => 'fas fa-exclamation-triangle',
                                            'Descontinuado' => 'fas fa-ban'
                                        ];
                                        ?>
                                        <i class="<?= $iconosEstado[$componente->estado] ?? 'fas fa-question-circle' ?>"></i>
                                        <?= htmlspecialchars($componente->estado ?? 'Disponible') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Vista en Lista (oculta por defecto) -->
                <div class="componentes-list" id="componentesListView" style="display: none;">
                    <?php foreach ($componentes as $componente): ?>
                        <div class="componente-list-item" data-componente-id="<?= $componente->id ?>">
                            <div class="componente-list-imagen">
                                <?php if (!empty($componente->imagen_principal)): ?>
                                    <img src="<?= asset('imagenes/componentes/' . $componente->imagen_principal) ?>" alt="<?= htmlspecialchars($componente->nombre) ?>">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--text-light), rgba(156, 163, 175, 0.3)); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                                        <i class="fas fa-microchip"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="componente-list-info">
                                <div>
                                    <h3 class="componente-titulo" style="margin-bottom: 0.3rem;"><?= htmlspecialchars($componente->nombre) ?></h3>
                                    <div class="componente-meta-item" style="margin-bottom: 0.5rem;">
                                        <i class="fas fa-industry"></i>
                                        <span><?= htmlspecialchars($componente->marca ?? 'Sin marca') ?></span>
                                        <?php if (!empty($componente->modelo)): ?>
                                            - <?= htmlspecialchars($componente->modelo) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <?php if (!empty($componente->categoria_nombre)): ?>
                                        <span class="componente-categoria-badge categoria-<?= strtolower(str_replace(' ', '-', $componente->categoria_nombre)) ?>">
                                            <?= htmlspecialchars($componente->categoria_nombre) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="text-center">
                                    <?php 
                                    $stock = (int)($componente->stock ?? 0);
                                    $stockMinimo = (int)($componente->stock_minimo ?? 0);
                                    $stockClase = 'stock-disponible';
                                    $stockTexto = 'Disponible';
                                    
                                    if ($stock <= 0) {
                                        $stockClase = 'stock-agotado';
                                        $stockTexto = 'Agotado';
                                    } elseif ($stock <= $stockMinimo) {
                                        $stockClase = 'stock-bajo';
                                        $stockTexto = 'Stock Bajo';
                                    }
                                    ?>
                                    <span class="componente-stock-badge <?= $stockClase ?>">
                                        <?= $stockTexto ?>
                                    </span>
                                    <div class="crud-text-muted" style="font-size: 0.8rem; margin-top: 0.3rem;">
                                        Stock: <?= $stock ?> / Min: <?= $stockMinimo ?>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <div class="componente-precio">
                                        <div class="precio-principal">$<?= number_format($componente->precio ?? 0, 2) ?></div>
                                        <div class="precio-unitario">por unidad</div>
                                    </div>
                                </div>
                                
                                <div class="text-center">
                                    <span class="crud-badge componente-estado-<?= strtolower($componente->estado ?? 'disponible') ?>">
                                        <?= htmlspecialchars($componente->estado ?? 'Disponible') ?>
                                    </span>
                                </div>
                                
                                <div class="crud-action-buttons">
                                    <a href="<?= route('componentes.ver', ['id' => $componente->id]) ?>" 
                                       class="crud-btn-sm crud-btn-outline-primary" 
                                       title="Ver Componente">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= route('componentes.editar', ['id' => $componente->id]) ?>" 
                                       class="crud-btn-sm crud-btn-outline-info" 
                                       title="Editar Componente">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="crud-btn-sm crud-btn-outline-danger componente-btn-delete" 
                                            data-componente-id="<?= $componente->id ?>" 
                                            data-componente-nombre="<?= htmlspecialchars($componente->nombre) ?>"
                                            data-delete-url="<?= route('componentes.delete', ['id' => $componente->id]) ?>"
                                            title="Eliminar Componente">
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
                        <i class="fas fa-microchip"></i>
                    </div>
                    <h3>No hay componentes disponibles</h3>
                    <p>
                        Comienza agregando componentes al inventario. Gestiona componentes electrónicos, 
                        mecánicos y tecnológicos para tus proyectos.
                    </p>
                    <a href="<?= route('componentes.crear'); ?>" class="crud-btn-primary">
                        <i class="fas fa-plus"></i>
                        Agregar Primer Componente
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Espacio de separación -->
        <div style="height: 40px;"></div>

    </div>
</div>

<!-- Modal de confirmación de eliminación de componente -->
<div class="crud-modal" id="crudDeleteComponenteModal" tabindex="-1" role="dialog" aria-labelledby="crudDeleteComponenteModalLabel" aria-hidden="true">
    <div class="crud-modal-dialog" role="document">
        <div class="crud-modal-content">
            <form id="crudDeleteComponenteForm" method="POST" action="">
                <?= CSRF() ?>
                <input type="hidden" name="_method" value="DELETE">
                
                <div class="crud-modal-header">
                    <h5 class="crud-modal-title" id="crudDeleteComponenteModalLabel">
                        <i class="fas fa-exclamation-triangle"></i>
                        Confirmar Eliminación de Componente
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="text-center">
                        <div class="crud-warning-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h4 class="crud-mb-3">¿Estás seguro de eliminar este componente?</h4>
                        <p class="crud-text-muted crud-mb-2">Esta acción no se puede deshacer.</p>
                        <div class="crud-user-to-delete-info">
                            <strong>Componente: <span id="crudComponenteNombreToDelete" class="crud-text-danger"></span></strong>
                        </div>
                    </div>
                    <div class="crud-alert crud-alert-danger crud-mt-3" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div>
                            <strong>¡Atención!</strong> El componente será eliminado permanentemente junto con:
                            <ul class="crud-mt-2 crud-mb-0">
                                <li>Todo su historial de stock</li>
                                <li>Imágenes y archivos asociados</li>
                                <li>Registros de ventas e inventario</li>
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
                        Eliminar Componente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de ajuste de stock -->
<div class="crud-modal" id="crudAjustarStockModal" tabindex="-1" role="dialog" aria-labelledby="crudAjustarStockModalLabel" aria-hidden="true">
    <div class="crud-modal-dialog" role="document">
        <div class="crud-modal-content">
            <form id="crudAjustarStockForm" method="POST" action="">
                <?= CSRF() ?>
                
                <div class="crud-modal-header">
                    <h5 class="crud-modal-title" id="crudAjustarStockModalLabel">
                        <i class="fas fa-boxes"></i>
                        Ajustar Stock de Componente
                    </h5>
                    <button type="button" class="crud-modal-close" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="crud-modal-body">
                    <div class="text-center">
                        <div class="crud-status-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h4 class="crud-mb-3" id="crudStockModalTitle">Ajustar stock del componente</h4>
                        <div class="crud-user-status-info">
                            <strong>Componente: <span id="crudComponenteNombreToStock" class="crud-text-info"></span></strong>
                            <p class="crud-mt-2">Stock actual: <span id="crudStockActual" class="crud-text-primary"></span> unidades</p>
                        </div>
                        
                        <div class="crud-form-group crud-mt-3">
                            <label for="crudTipoMovimiento">Tipo de Movimiento:</label>
                            <select class="crud-form-control" id="crudTipoMovimiento" name="tipo_movimiento" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="entrada">Entrada de Stock (+)</option>
                                <option value="salida">Salida de Stock (-)</option>
                                <option value="ajuste">Ajuste Manual</option>
                            </select>
                        </div>
                        
                        <div class="crud-form-group">
                            <label for="crudCantidadStock">Cantidad:</label>
                            <input type="number" class="crud-form-control" id="crudCantidadStock" 
                                   name="cantidad" min="1" required 
                                   placeholder="Ingrese la cantidad">
                        </div>
                        
                        <div class="crud-form-group">
                            <label for="crudMotivoStock">Motivo:</label>
                            <textarea class="crud-form-control" id="crudMotivoStock" 
                                      name="motivo" rows="3" required
                                      placeholder="Describa el motivo del ajuste de stock..."></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="crud-modal-footer">
                    <button type="button" class="crud-btn crud-btn-clear" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="crud-btn crud-btn-outline-success" id="crudConfirmStockAjuste">
                        <i class="fas fa-check"></i>
                        Confirmar Ajuste
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Overlay personalizado -->
<div class="crud-custom-modal-overlay" id="crudCustomModalOverlay"></div>

<!-- JavaScript específico para componentes CRUD -->
<script>
// Funcionalidad de filtros y vista modernizada para componentes
document.addEventListener('DOMContentLoaded', function() {
    const componenteFilterName = document.getElementById('componenteFilterName');
    const componenteFilterCategoria = document.getElementById('componenteFilterCategoria');
    const componenteFilterMarca = document.getElementById('componenteFilterMarca');
    const componenteFilterEstado = document.getElementById('componenteFilterEstado');
    const componenteFilterStockBajo = document.getElementById('componenteFilterStockBajo');
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const componentesGridView = document.getElementById('componentesGridView');
    const componentesListView = document.getElementById('componentesListView');
    const componenteCards = document.querySelectorAll('.componente-card, .componente-list-item');

    // Toggle entre vista de grid y lista
    gridViewBtn?.addEventListener('click', function() {
        gridViewBtn.classList.add('active');
        listViewBtn.classList.remove('active');
        componentesGridView.style.display = 'grid';
        componentesListView.style.display = 'none';
    });

    listViewBtn?.addEventListener('click', function() {
        listViewBtn.classList.add('active');
        gridViewBtn.classList.remove('active');
        componentesGridView.style.display = 'none';
        componentesListView.style.display = 'flex';
    });

    function componentesApplyFilters() {
        const nameFilter = componenteFilterName?.value.toLowerCase() || '';
        const categoriaFilter = componenteFilterCategoria?.value || '';
        const marcaFilter = componenteFilterMarca?.value.toLowerCase() || '';
        const estadoFilter = componenteFilterEstado?.value || '';
        const stockBajoFilter = componenteFilterStockBajo?.checked || false;

        componenteCards.forEach(card => {
            const nombre = card.querySelector('.componente-titulo')?.textContent.toLowerCase() || '';
            const descripcion = card.querySelector('.componente-descripcion')?.textContent.toLowerCase() || '';
            const codigoProducto = card.querySelector('.componente-codigo')?.textContent.toLowerCase() || '';
            const categoria = card.querySelector('.componente-categoria-badge')?.textContent.toLowerCase() || '';
            const marca = card.querySelector('.componente-meta-item .componente-marca')?.textContent.toLowerCase() || '';
            const estadoBadge = card.querySelector('[class*="componente-estado-"]')?.textContent || '';
            const stockBadge = card.querySelector('.componente-stock-badge')?.textContent.toLowerCase() || '';

            let showCard = true;

            // Filtro por nombre/descripción/código
            if (nameFilter && 
                !nombre.includes(nameFilter) && 
                !descripcion.includes(nameFilter) && 
                !codigoProducto.includes(nameFilter)) {
                showCard = false;
            }

            // Filtro por categoría
            if (categoriaFilter && !categoria.includes(categoriaFilter.toLowerCase())) {
                showCard = false;
            }

            // Filtro por marca
            if (marcaFilter && !marca.includes(marcaFilter)) {
                showCard = false;
            }

            // Filtro por estado
            if (estadoFilter && estadoBadge !== estadoFilter) {
                showCard = false;
            }

            // Filtro por stock bajo
            if (stockBajoFilter && !stockBadge.includes('bajo') && !stockBadge.includes('agotado')) {
                showCard = false;
            }

            card.style.display = showCard ? '' : 'none';
        });
    }

    // Event listeners para los filtros
    componenteFilterName?.addEventListener('keyup', componentesApplyFilters);
    componenteFilterCategoria?.addEventListener('change', componentesApplyFilters);
    componenteFilterMarca?.addEventListener('change', componentesApplyFilters);
    componenteFilterEstado?.addEventListener('change', componentesApplyFilters);
    componenteFilterStockBajo?.addEventListener('change', componentesApplyFilters);

    // Función para limpiar filtros
    window.componentesClearFilters = function() {
        if (componenteFilterName) componenteFilterName.value = '';
        if (componenteFilterCategoria) componenteFilterCategoria.value = '';
        if (componenteFilterMarca) componenteFilterMarca.value = '';
        if (componenteFilterEstado) componenteFilterEstado.value = '';
        if (componenteFilterStockBajo) componenteFilterStockBajo.checked = false;
        
        // Mostrar todas las tarjetas
        componenteCards.forEach(card => {
            card.style.display = '';
        });
    };

    // Manejo de modales modernos
    const crudDeleteModal = document.getElementById('crudDeleteComponenteModal');
    const crudStockModal = document.getElementById('crudAjustarStockModal');
    const crudOverlay = document.getElementById('crudCustomModalOverlay');

    // Botones de eliminar componente
    document.querySelectorAll('.componente-btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            const componenteId = this.dataset.componenteId;
            const componenteNombre = this.dataset.componenteNombre;
            const deleteUrl = this.dataset.deleteUrl;

            document.getElementById('crudComponenteNombreToDelete').textContent = componenteNombre;
            document.getElementById('crudDeleteComponenteForm').action = deleteUrl;

            crudShowModal(crudDeleteModal);
        });
    });

    // Botones de ajustar stock
    document.querySelectorAll('.componente-btn-ajustar-stock').forEach(button => {
        button.addEventListener('click', function() {
            const componenteId = this.dataset.componenteId;
            const componenteNombre = this.dataset.componenteNombre;
            const stockActual = this.dataset.stockActual;
            const ajustarUrl = `<?= route('componentes.ajustar-stock', ['id' => '']) ?>${componenteId}`;

            document.getElementById('crudComponenteNombreToStock').textContent = componenteNombre;
            document.getElementById('crudStockActual').textContent = stockActual;
            document.getElementById('crudAjustarStockForm').action = ajustarUrl;

            // Limpiar formulario
            document.getElementById('crudTipoMovimiento').value = '';
            document.getElementById('crudCantidadStock').value = '';
            document.getElementById('crudMotivoStock').value = '';

            crudShowModal(crudStockModal);
        });
    });

    // Validación en tiempo real del formulario de stock
    const tipoMovimiento = document.getElementById('crudTipoMovimiento');
    const cantidadInput = document.getElementById('crudCantidadStock');

    tipoMovimiento?.addEventListener('change', function() {
        const tipo = this.value;
        const label = cantidadInput?.parentElement?.querySelector('label');
        
        if (label) {
            switch(tipo) {
                case 'entrada':
                    label.textContent = 'Cantidad a Agregar (+):';
                    cantidadInput.placeholder = 'Unidades a agregar al stock';
                    break;
                case 'salida':
                    label.textContent = 'Cantidad a Reducir (-):';
                    cantidadInput.placeholder = 'Unidades a quitar del stock';
                    break;
                case 'ajuste':
                    label.textContent = 'Nueva Cantidad Total:';
                    cantidadInput.placeholder = 'Stock total después del ajuste';
                    break;
                default:
                    label.textContent = 'Cantidad:';
                    cantidadInput.placeholder = 'Ingrese la cantidad';
            }
        }
    });

    // Manejo del envío del formulario de stock via AJAX
    document.getElementById('crudAjustarStockForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const actionUrl = this.action;
        
        // Deshabilitar botón de envío
        const submitBtn = document.getElementById('crudConfirmStockAjuste');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        submitBtn.disabled = true;
        
        fetch(actionUrl, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cerrar modal
                crudHideModal(crudStockModal);
                
                // Mostrar mensaje de éxito
                showAlert('success', data.message || 'Stock ajustado correctamente');
                
                // Actualizar la vista (recargar página)
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showAlert('danger', data.message || 'Error al ajustar el stock');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Error de conexión al ajustar el stock');
        })
        .finally(() => {
            // Restaurar botón
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
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

    // Función para mostrar alertas dinámicas
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'crud-alert-success' : 'crud-alert-danger';
        const iconClass = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
        
        const alertHTML = `
            <div class="crud-alert ${alertClass}" style="position: fixed; top: 20px; right: 20px; z-index: 10000; max-width: 400px;">
                <i class="${iconClass}"></i>
                <span>${message}</span>
                <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', alertHTML);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            const alert = document.body.lastElementChild;
            if (alert && alert.classList.contains('crud-alert')) {
                alert.remove();
            }
        }, 5000);
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
    componenteCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Actualizar barras de stock en tiempo real
    function updateStockBars() {
        document.querySelectorAll('.componente-stock-visual').forEach(stockVisual => {
            const stockActual = parseInt(stockVisual.querySelector('.stock-actual')?.textContent || '0');
            const stockMinimo = parseInt(stockVisual.querySelector('.stock-minimo')?.textContent.replace('/ min: ', '') || '0');
            const barra = stockVisual.querySelector('.stock-barra-progreso');
            
            if (barra && stockMinimo > 0) {
                const porcentaje = Math.min((stockActual / stockMinimo) * 100, 100);
                barra.style.width = porcentaje + '%';
                
                // Actualizar clase de color
                barra.classList.remove('stock-normal', 'stock-warning', 'stock-critico');
                if (stockActual <= 0) {
                    barra.classList.add('stock-critico');
                } else if (stockActual <= stockMinimo) {
                    barra.classList.add('stock-warning');
                } else {
                    barra.classList.add('stock-normal');
                }
            }
        });
    }

    // Inicializar barras de stock
    updateStockBars();
});
</script>