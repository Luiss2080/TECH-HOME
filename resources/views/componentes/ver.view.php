<?php
$title = $title ?? 'Ver Componente';
$componente = $componente ?? null;
$historial_ventas = $historial_ventas ?? [];
$movimientos_stock = $movimientos_stock ?? [];
$user = auth();
$isAdmin = $user && $user->hasRole('administrador');
$puedeEditar = $isAdmin || ($user && $user->hasRole('docente'));

// Función helper para acceder a propiedades de objeto o array
function getComponenteValue($componente, $key, $default = '') {
    if (is_array($componente)) {
        return $componente[$key] ?? $default;
    } elseif (is_object($componente)) {
        return $componente->$key ?? $default;
    }
    return $default;
}

// Verificar que el componente existe
if (!$componente) {
    header('Location: ' . route('componentes'));
    exit();
}

// Procesar especificaciones si existen
$especificaciones = [];
$especificacionesJson = getComponenteValue($componente, 'especificaciones');
if (!empty($especificacionesJson)) {
    if (is_string($especificacionesJson)) {
        $especificaciones = json_decode($especificacionesJson, true) ?: [];
    }
}
?>

<!-- Estilos especificos para el modulo CRUD - Ver Componente -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">

<!-- Estilos especificos para visualizacion de componentes -->
<style>
/* ============================================
   ESTILOS ESPECIFICOS PARA VER COMPONENTE
   ============================================ */

/* Imagen principal del componente */
.componente-imagen-principal {
    width: 100%;
    max-width: 350px;
    height: 350px;
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    border-radius: var(--border-radius-lg);
    border: 2px solid rgba(59, 130, 246, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    margin: 0 auto 1.5rem;
    box-shadow: var(--shadow-medium);
    transition: var(--transition-base);
}

.componente-imagen-principal:hover {
    transform: scale(1.02);
    box-shadow: var(--shadow-large);
}

.componente-imagen-principal img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: var(--border-radius-lg);
}

.componente-placeholder {
    text-align: center;
    color: var(--text-secondary);
}

.componente-placeholder i {
    font-size: 4rem;
    color: var(--secondary-blue);
    margin-bottom: 1rem;
    display: block;
}

.componente-placeholder h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

/* Badges de estado */
.estado-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius-full);
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.estado-disponible {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.08));
    color: var(--success-color);
    border: 2px solid rgba(16, 185, 129, 0.3);
}

.estado-agotado {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.08));
    color: var(--warning-color);
    border: 2px solid rgba(245, 158, 11, 0.3);
}

.estado-descontinuado {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.08));
    color: var(--danger-color);
    border: 2px solid rgba(239, 68, 68, 0.3);
}

/* Informaci�n del componente */
.info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 1rem;
    background: rgba(248, 250, 252, 0.3);
    border-radius: var(--border-radius-md);
    margin-bottom: 1rem;
    border-left: 4px solid var(--secondary-blue);
    transition: var(--transition-base);
}

.info-item:hover {
    background: rgba(248, 250, 252, 0.6);
    transform: translateX(5px);
}

.info-icon {
    background: var(--secondary-blue);
    color: white;
    width: 40px;
    height: 40px;
    border-radius: var(--border-radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
}

.info-content {
    flex: 1;
}

.info-label {
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.info-value {
    color: var(--text-primary);
    font-size: 1rem;
    font-weight: 500;
    line-height: 1.4;
}

/* Especificaciones t�cnicas */
.especificaciones-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.especificacion-card {
    background: white;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: var(--border-radius-md);
    padding: 1rem;
    transition: var(--transition-base);
    position: relative;
    overflow: hidden;
}

.especificacion-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-red), var(--secondary-blue));
}

.especificacion-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

.especificacion-titulo {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.especificacion-valor {
    color: var(--text-secondary);
    font-size: 0.95rem;
    font-weight: 500;
}

/* Estad�sticas de inventario */
.stock-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.stock-card {
    background: white;
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: var(--border-radius-md);
    padding: 1.5rem;
    text-align: center;
    transition: var(--transition-base);
    position: relative;
    overflow: hidden;
}

.stock-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-red);
}

.stock-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-large);
}

.stock-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    display: block;
}

.stock-disponible .stock-icon { color: var(--success-color); }
.stock-minimo .stock-icon { color: var(--warning-color); }
.stock-reservado .stock-icon { color: var(--primary-red); }
.stock-valor .stock-icon { color: var(--secondary-blue); }

.stock-value {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.stock-label {
    font-size: 0.9rem;
    color: var(--text-secondary);
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Tabla de historial */
.historial-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
    background: white;
    border-radius: var(--border-radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-light);
}

.historial-table th {
    background: var(--primary-red);
    color: white;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.historial-table td {
    padding: 1rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    color: var(--text-secondary);
}

.historial-table tbody tr:hover {
    background: rgba(248, 250, 252, 0.5);
}

.historial-table tbody tr:last-child td {
    border-bottom: none;
}

/* Badge de tipo de movimiento */
.movimiento-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: var(--border-radius-sm);
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: capitalize;
}

.movimiento-entrada {
    background: rgba(16, 185, 129, 0.1);
    color: var(--success-color);
}

.movimiento-salida {
    background: rgba(239, 68, 68, 0.1);
    color: var(--danger-color);
}

.movimiento-ajuste {
    background: rgba(59, 130, 246, 0.1);
    color: var(--secondary-blue);
}

/* Acciones r�pidas */
.acciones-rapidas {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1.5rem;
}

.accion-card {
    background: white;
    border: 2px solid rgba(0, 0, 0, 0.1);
    border-radius: var(--border-radius-md);
    padding: 1rem;
    text-align: center;
    transition: var(--transition-bounce);
    cursor: pointer;
    text-decoration: none;
    color: inherit;
}

.accion-card:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: var(--shadow-large);
    border-color: var(--primary-red);
    text-decoration: none;
    color: inherit;
}

.accion-icon {
    font-size: 2rem;
    color: var(--primary-red);
    margin-bottom: 0.5rem;
    display: block;
}

.accion-titulo {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
    font-size: 0.95rem;
}

.accion-descripcion {
    color: var(--text-secondary);
    font-size: 0.85rem;
}

/* Responsive */
@media (max-width: 768px) {
    .especificaciones-grid,
    .stock-stats,
    .acciones-rapidas {
        grid-template-columns: 1fr;
    }
    
    .componente-imagen-principal {
        max-width: 280px;
        height: 280px;
    }
    
    .historial-table {
        font-size: 0.8rem;
    }
    
    .historial-table th,
    .historial-table td {
        padding: 0.5rem;
    }
}

/* ============================================
   MODO OSCURO - TEMA DARK
   ============================================ */
body.ithr-dark-mode .componente-imagen-principal,
body.dark-theme .componente-imagen-principal {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.9));
    border-color: rgba(71, 85, 105, 0.4);
}

body.ithr-dark-mode .info-item,
body.dark-theme .info-item {
    background: rgba(30, 41, 59, 0.3);
    border-left-color: var(--secondary-blue);
}

body.ithr-dark-mode .especificacion-card,
body.dark-theme .especificacion-card {
    background: rgba(30, 41, 59, 0.4);
    border-color: rgba(71, 85, 105, 0.4);
}

body.ithr-dark-mode .stock-card,
body.dark-theme .stock-card {
    background: rgba(30, 41, 59, 0.4);
    border-color: rgba(71, 85, 105, 0.4);
}

body.ithr-dark-mode .historial-table,
body.dark-theme .historial-table {
    background: rgba(30, 41, 59, 0.4);
}

body.ithr-dark-mode .historial-table td,
body.dark-theme .historial-table td {
    border-bottom-color: rgba(71, 85, 105, 0.2);
}

body.ithr-dark-mode .accion-card,
body.dark-theme .accion-card {
    background: rgba(30, 41, 59, 0.4);
    border-color: rgba(71, 85, 105, 0.4);
}

/* ============================================
   ESTILOS PARA SCROLL PERSONALIZADO
   ============================================ */

/* Scroll personalizado para contenedores con altura fija */
.crud-form-body::-webkit-scrollbar {
    width: 8px;
}

.crud-form-body::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
    border-radius: 4px;
}

.crud-form-body::-webkit-scrollbar-thumb {
    background: var(--primary-red);
    border-radius: 4px;
}

.crud-form-body::-webkit-scrollbar-thumb:hover {
    background: #cc1f3c;
}

/* Firefox */
.crud-form-body {
    scrollbar-width: thin;
    scrollbar-color: var(--primary-red) rgba(0, 0, 0, 0.1);
}
</style>

<!-- Contenedor principal del CRUD de visualizacion -->
<div class="crud-edit-container">
    <div class="crud-edit-wrapper">

        <!-- Header principal con informacion del componente -->
        <div class="crud-section-card">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="crud-section-title-group">
                        <nav aria-label="breadcrumb" class="crud-breadcrumb-nav">
                            <ol class="crud-breadcrumb">
                                <li class="crud-breadcrumb-item">
                                    <a href="<?= route('componentes') ?>">
                                        <i class="fas fa-microchip"></i>
                                        Componentes
                                    </a>
                                </li>
                                <li class="crud-breadcrumb-item active">
                                    <i class="fas fa-eye"></i>
                                    Ver Componente
                                </li>
                            </ol>
                        </nav>
                        <h1 class="crud-section-title"><?= htmlspecialchars(getComponenteValue($componente, 'nombre')) ?></h1>
                        <p class="crud-section-subtitle">
                            Detalles completos del componente 
                            <?php if (!empty(getComponenteValue($componente, 'codigo_producto'))): ?>
                                - Codigo: <strong><?= htmlspecialchars(getComponenteValue($componente, 'codigo_producto')) ?></strong>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <div class="crud-section-header-actions">
                    <a href="<?= route('componentes') ?>" class="crud-section-action-header crud-btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Volver al Inventario
                    </a>
                    <?php if ($puedeEditar): ?>
                        <a href="<?= route('componentes.editar', ['id' => getComponenteValue($componente, 'id')]) ?>" class="crud-section-action-header crud-btn-primary">
                            <i class="fas fa-edit"></i>
                            Editar Componente
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Alertas de sesi�n -->
        <?php if (flashGet('error')): ?>
            <div class="crud-alert crud-alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><strong>ERROR:</strong> <?= htmlspecialchars(flashGet('error')) ?></span>
                <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (flashGet('success')): ?>
            <div class="crud-alert crud-alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars(flashGet('success')) ?></span>
                <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (flashGet('warning')): ?>
            <div class="crud-alert crud-alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <span><strong>ADVERTENCIA:</strong> <?= htmlspecialchars(flashGet('warning')) ?></span>
                <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Fila superior: Vista del Componente e Informacion del Componente -->
        <div class="crud-form-grid" style="grid-template-columns: 1fr 1fr; gap: 2rem; align-items: stretch;">
            
            <!-- Columna izquierda: Vista del Componente -->
            <div class="crud-section-card" style="height: 500px; display: flex; flex-direction: column;">
                <div class="crud-form-header">
                    <h2 class="crud-section-title">
                        <i class="fas fa-image"></i>
                        Vista del Componente
                    </h2>
                </div>
                
                <div class="crud-form-body" style="flex: 1; overflow: hidden; padding: 1rem; display: flex; flex-direction: column; justify-content: center;">
                    <div class="componente-imagen-principal" style="max-width: 280px; height: 280px; margin-bottom: 1rem;">
                        <?php if (!empty(getComponenteValue($componente, 'imagen_principal'))): ?>
                            <img src="<?= asset('images/componentes/' . getComponenteValue($componente, 'imagen_principal')) ?>" 
                                 alt="<?= htmlspecialchars(getComponenteValue($componente, 'nombre')) ?>"
                                 onerror="this.parentElement.innerHTML='<div class=&quot;componente-placeholder&quot;><i class=&quot;fas fa-exclamation-triangle&quot;></i><h4>Imagen no disponible</h4><p>El archivo de imagen no se encontro</p></div>'">
                        <?php else: ?>
                            <div class="componente-placeholder">
                                <i class="fas fa-microchip"></i>
                                <h4>Sin Imagen</h4>
                                <p>No se ha asignado imagen a este componente</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div style="text-align: center;">
                        <div class="estado-badge estado-<?= strtolower(getComponenteValue($componente, 'estado')) ?>">
                            <?php 
                            $estadoIconos = [
                                'Disponible' => 'fa-check-circle',
                                'Agotado' => 'fa-times-circle', 
                                'Descontinuado' => 'fa-ban'
                            ];
                            ?>
                            <i class="fas <?= $estadoIconos[getComponenteValue($componente, 'estado')] ?? 'fa-question-circle' ?>"></i>
                            <?= htmlspecialchars(getComponenteValue($componente, 'estado')) ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna derecha: Informacion del Componente -->
            <div class="crud-section-card" style="height: 500px; display: flex; flex-direction: column;">
                <div class="crud-form-header">
                    <h2 class="crud-section-title">
                        <i class="fas fa-info-circle"></i>
                        Informacion del Componente
                    </h2>
                </div>
                
                <div class="crud-form-body" style="flex: 1; overflow-y: auto; padding: 1rem;">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Nombre del Componente</div>
                            <div class="info-value"><?= htmlspecialchars(getComponenteValue($componente, 'nombre')) ?></div>
                        </div>
                    </div>

                    <?php if (!empty(getComponenteValue($componente, 'codigo_producto'))): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-barcode"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Codigo de Producto</div>
                                <div class="info-value"><?= htmlspecialchars(getComponenteValue($componente, 'codigo_producto')) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty(getComponenteValue($componente, 'marca'))): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-tag"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Marca</div>
                                <div class="info-value"><?= htmlspecialchars(getComponenteValue($componente, 'marca')) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty(getComponenteValue($componente, 'modelo'))): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-code-branch"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Modelo</div>
                                <div class="info-value"><?= htmlspecialchars(getComponenteValue($componente, 'modelo')) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Precio Unitario</div>
                            <div class="info-value">Bs. <?= number_format(getComponenteValue($componente, 'precio'), 2) ?></div>
                        </div>
                    </div>

                    <?php if (!empty(getComponenteValue($componente, 'proveedor'))): ?>
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="info-content">
                                <div class="info-label">Proveedor</div>
                                <div class="info-value"><?= htmlspecialchars(getComponenteValue($componente, 'proveedor')) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Fecha de Registro</div>
                            <div class="info-value">
                                <?= date('d/m/Y H:i', strtotime(getComponenteValue($componente, 'fecha_creacion', 'now'))) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seccion: Estadisticas de Inventario (ocupa toda la fila) -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-chart-bar"></i>
                    Estadisticas de Inventario
                </h2>
            </div>
            
            <div class="crud-form-body">
                <div class="stock-stats">
                    <div class="stock-card stock-disponible">
                        <i class="fas fa-boxes stock-icon"></i>
                        <div class="stock-value"><?= number_format(getComponenteValue($componente, 'stock')) ?></div>
                        <div class="stock-label">Stock Disponible</div>
                    </div>
                    
                    <div class="stock-card stock-minimo">
                        <i class="fas fa-exclamation-triangle stock-icon"></i>
                        <div class="stock-value"><?= number_format(getComponenteValue($componente, 'stock_minimo')) ?></div>
                        <div class="stock-label">Stock Minimo</div>
                    </div>
                    
                    <?php if (getComponenteValue($componente, 'stock_reservado') > 0): ?>
                        <div class="stock-card stock-reservado">
                            <i class="fas fa-lock stock-icon"></i>
                            <div class="stock-value"><?= number_format(getComponenteValue($componente, 'stock_reservado')) ?></div>
                            <div class="stock-label">Stock Reservado</div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="stock-card stock-valor">
                        <i class="fas fa-dollar-sign stock-icon"></i>
                        <div class="stock-value">Bs. <?= number_format(getComponenteValue($componente, 'precio') * getComponenteValue($componente, 'stock'), 2) ?></div>
                        <div class="stock-label">Valor Total</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seccion: Descripcion -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-align-left"></i>
                    Descripcion del Componente
                </h2>
            </div>
            
            <div class="crud-form-body">
                <div class="info-value" style="padding: 1rem; background: rgba(248, 250, 252, 0.3); border-radius: var(--border-radius-md); line-height: 1.6;">
                    <?= nl2br(htmlspecialchars(getComponenteValue($componente, 'descripcion', 'Sin descripcion disponible.'))) ?>
                </div>
            </div>
        </div>

        <!-- Seccion: Especificaciones Tecnicas -->
        <?php if (!empty($especificaciones)): ?>
            <div class="crud-section-card">
                <div class="crud-form-header">
                    <h2 class="crud-section-title">
                        <i class="fas fa-cog"></i>
                        Especificaciones Tecnicas
                    </h2>
                    <p class="crud-section-subtitle">Caracteristicas tecnicas y parametros del componente</p>
                </div>
                
                <div class="crud-form-body">
                    <div class="especificaciones-grid">
                        <?php foreach ($especificaciones as $caracteristica => $valor): ?>
                            <div class="especificacion-card">
                                <div class="especificacion-titulo"><?= htmlspecialchars($caracteristica) ?></div>
                                <div class="especificacion-valor"><?= htmlspecialchars($valor) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Seccion: Historial de Movimientos -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-history"></i>
                    Historial de Movimientos de Stock
                </h2>
                <p class="crud-section-subtitle">Registro de entradas, salidas y ajustes del componente</p>
            </div>
            
            <div class="crud-form-body">
                <?php if (!empty($movimientos_stock)): ?>
                    <div style="overflow-x: auto;">
                        <table class="historial-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Stock Anterior</th>
                                    <th>Stock Nuevo</th>
                                    <th>Motivo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movimientos_stock as $movimiento): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($movimiento['fecha'])) ?></td>
                                        <td>
                                            <span class="movimiento-badge movimiento-<?= strtolower($movimiento['tipo_movimiento']) ?>">
                                                <i class="fas fa-<?= $movimiento['tipo_movimiento'] === 'entrada' ? 'plus' : ($movimiento['tipo_movimiento'] === 'salida' ? 'minus' : 'edit') ?>"></i>
                                                <?= htmlspecialchars($movimiento['tipo_movimiento']) ?>
                                            </span>
                                        </td>
                                        <td><?= number_format($movimiento['cantidad']) ?></td>
                                        <td><?= number_format($movimiento['stock_anterior']) ?></td>
                                        <td><?= number_format($movimiento['stock_nuevo']) ?></td>
                                        <td><?= htmlspecialchars($movimiento['motivo'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="crud-alert crud-alert-info">
                        <i class="fas fa-info-circle"></i>
                        <span>No hay movimientos de stock registrados para este componente.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seccion: Historial de Ventas -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-shopping-cart"></i>
                    Historial de Ventas
                </h2>
                <p class="crud-section-subtitle">Registro de ventas del componente</p>
            </div>
            
            <div class="crud-form-body">
                <?php if (!empty($historial_ventas)): ?>
                    <div style="overflow-x: auto;">
                        <table class="historial-table">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unitario</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($historial_ventas as $venta): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($venta['fecha'])) ?></td>
                                        <td><?= htmlspecialchars($venta['cliente'] ?? 'Sin especificar') ?></td>
                                        <td><?= number_format($venta['cantidad']) ?></td>
                                        <td>Bs. <?= number_format($venta['precio_unitario'], 2) ?></td>
                                        <td>Bs. <?= number_format($venta['cantidad'] * $venta['precio_unitario'], 2) ?></td>
                                        <td>
                                            <span class="estado-badge estado-<?= strtolower($venta['estado'] ?? 'completada') ?>">
                                                <?= htmlspecialchars($venta['estado'] ?? 'Completada') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="crud-alert crud-alert-info">
                        <i class="fas fa-info-circle"></i>
                        <span>No hay ventas registradas para este componente.</span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Seccion: Acciones Rapidas -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-bolt"></i>
                    Acciones Rapidas
                </h2>
                <p class="crud-section-subtitle">Operaciones disponibles para este componente</p>
            </div>
            
            <div class="crud-form-body">
                <div class="acciones-rapidas">
                    <?php if ($puedeEditar): ?>
                        <a href="<?= route('componentes.editar', ['id' => getComponenteValue($componente, 'id')]) ?>" class="accion-card">
                            <i class="fas fa-edit accion-icon"></i>
                            <div class="accion-titulo">Editar</div>
                            <div class="accion-descripcion">Modificar informacion del componente</div>
                        </a>
                    <?php endif; ?>
                    
                    <div class="accion-card" onclick="window.print()">
                        <i class="fas fa-print accion-icon"></i>
                        <div class="accion-titulo">Imprimir</div>
                        <div class="accion-descripcion">Generar reporte imprimible</div>
                    </div>
                    
                    <div class="accion-card" onclick="exportarPDF()">
                        <i class="fas fa-file-pdf accion-icon"></i>
                        <div class="accion-titulo">Exportar PDF</div>
                        <div class="accion-descripcion">Descargar ficha tecnica</div>
                    </div>
                    
                    <div class="accion-card" onclick="compartirComponente()">
                        <i class="fas fa-share-alt accion-icon"></i>
                        <div class="accion-titulo">Compartir</div>
                        <div class="accion-descripcion">Enviar informacion del componente</div>
                    </div>
                    
                    <?php if ($isAdmin): ?>
                        <div class="accion-card" onclick="ajustarStock()">
                            <i class="fas fa-balance-scale accion-icon"></i>
                            <div class="accion-titulo">Ajustar Stock</div>
                            <div class="accion-descripcion">Modificar cantidad en inventario</div>
                        </div>
                        
                        <div class="accion-card" onclick="verReportes()">
                            <i class="fas fa-chart-line accion-icon"></i>
                            <div class="accion-titulo">Reportes</div>
                            <div class="accion-descripcion">Estadisticas y analisis</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Espacio de separaci�n -->
        <div style="height: 20px;"></div> 

    </div>
</div>

<!-- JavaScript espec�fico para ver componente -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss de alertas
    const alerts = document.querySelectorAll('.crud-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px) scale(0.95)';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 300);
        }, 6000);
    });

    // Funciones para acciones rapidas
    window.exportarPDF = function() {
        const nombre = '<?= htmlspecialchars(getComponenteValue($componente, 'nombre')) ?>';
        const codigo = '<?= htmlspecialchars(getComponenteValue($componente, 'codigo_producto', '')) ?>';
        
        alert(`Generando PDF para: ${nombre}\nCodigo: ${codigo}\n\nEsta funcionalidad estara disponible proximamente.`);
    };

    window.compartirComponente = function() {
        if (navigator.share) {
            navigator.share({
                title: 'Componente: <?= htmlspecialchars(getComponenteValue($componente, 'nombre')) ?>',
                text: 'Informacion del componente <?= htmlspecialchars(getComponenteValue($componente, 'nombre')) ?>',
                url: window.location.href
            });
        } else {
            // Fallback - copiar URL al portapapeles
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('URL copiada al portapapeles');
            });
        }
    };

    window.ajustarStock = function() {
        const stockActual = <?= getComponenteValue($componente, 'stock') ?>;
        const nuevoStock = prompt(`Stock actual: ${stockActual} unidades\n\nIngresa el nuevo stock:`);
        
        if (nuevoStock !== null && !isNaN(nuevoStock) && nuevoStock >= 0) {
            const motivo = prompt('Motivo del ajuste de stock:');
            if (motivo) {
                alert(`Ajuste de stock registrado:\nStock anterior: ${stockActual}\nStock nuevo: ${nuevoStock}\nMotivo: ${motivo}\n\nEsta funcionalidad sera implementada proximamente.`);
            }
        }
    };

    window.verReportes = function() {
        alert('Redirigiendo a reportes y estadisticas del componente...\n\nEsta funcionalidad estara disponible proximamente.');
    };

    // Animaciones suaves al cargar
    const cards = document.querySelectorAll('.crud-section-card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    // Efecto hover para tarjetas de especificaciones
    const especCards = document.querySelectorAll('.especificacion-card');
    especCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Efectos para estad�sticas de stock
    const stockCards = document.querySelectorAll('.stock-card');
    stockCards.forEach((card, index) => {
        setTimeout(() => {
            const value = card.querySelector('.stock-value');
            const finalValue = value.textContent;
            
            // Animaci�n de conteo para n�meros
            if (finalValue.match(/^\d+$/)) {
                let currentValue = 0;
                const increment = Math.ceil(parseInt(finalValue) / 20);
                const timer = setInterval(() => {
                    currentValue += increment;
                    if (currentValue >= parseInt(finalValue)) {
                        currentValue = parseInt(finalValue);
                        clearInterval(timer);
                    }
                    value.textContent = currentValue.toLocaleString();
                }, 50);
            }
        }, index * 200);
    });

    // Smooth scroll para links internos
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});

// Funci�n para imprimir con estilos espec�ficos
function imprimirComponente() {
    const printContent = document.querySelector('.crud-edit-wrapper').innerHTML;
    const printWindow = window.open('', '', 'height=600,width=800');
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Componente: <?= htmlspecialchars(getComponenteValue($componente, 'nombre')) ?></title>
                <link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">
                <style>
                    @media print {
                        .crud-section-header-actions,
                        .acciones-rapidas,
                        .crud-btn { display: none !important; }
                        .crud-edit-container { max-width: none; }
                        body { font-size: 12pt; }
                    }
                </style>
            </head>
            <body>
                <div class="crud-edit-container">
                    <div class="crud-edit-wrapper">
                        ${printContent}
                    </div>
                </div>
            </body>
        </html>
    `);
    
    printWindow.document.close();
    setTimeout(() => {
        printWindow.print();
        printWindow.close();
    }, 500);
}
</script>