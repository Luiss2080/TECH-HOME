<?php

use App\Services\AdminService;
?>
<link rel="stylesheet" href="<?= asset('css/admin/admin.css') ?>">

<div class="dashboard-content">

    <!-- Sección 1: Acciones Rápidas -->
    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-bolt"></i>
            Acciones Rápidas
        </h2>
        <p class="section-subtitle">Accede rápidamente a las funciones principales del sistema</p>

        <div class="quick-actions-grid">
            <a href="<?= route('usuarios.crear'); ?>" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="action-title">Nuevo Usuario</h3>
                <p class="action-description">Registrar un nuevo usuario en el sistema</p>
            </a>

            <a href="<?= route('cursos.crear'); ?>" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h3 class="action-title">Nuevo Curso</h3>
                <p class="action-description">Crear un nuevo curso en la plataforma</p>
            </a>

            <a href="<?= route('componentes.crear'); ?>" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-microchip"></i>
                </div>
                <h3 class="action-title">Nuevo Componente</h3>
                <p class="action-description">Agregar componente al inventario</p>
            </a>

            <a href="<?= route('ventas.crear'); ?>" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h3 class="action-title">Nueva Venta</h3>
                <p class="action-description">Procesar una nueva orden de venta</p>
            </a>

            <a href="<?= route('libros.crear'); ?>" class="action-card">
                <div class="action-icon">
                    <i class="fas fa-book"></i> <!-- ÍCONO DE LIBRO AGREGADO -->
                </div>
                <h3 class="action-title">Nuevo Libro</h3>
                <p class="action-description">Agregar libro a la biblioteca</p>
            </a>
        </div>
    </div>

    <!-- Sección 2: Métricas del Sistema -->
    <div class="section-card">
        <h2 class="section-title">
            <i class="fas fa-chart-bar"></i>
            Métricas del Sistema
        </h2>

        <div class="metrics-grid">
            <!-- Primera fila: Estudiantes, Docentes, Reportes -->
            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon students">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value"><?= $estadisticas['estudiantes_total']; ?></div>
                        <div class="metric-label">Estudiantes Registrados</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-arrow-up"></i>
                        <span><?= $estadisticas['estudiantes_activos']; ?> activos</span>
                    </div>
                    <a href="<?= route('estudiantes.index'); ?>" class="metric-action">
                        <i class="fas fa-users-cog"></i>
                        Gestionar
                    </a>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon teachers">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value"><?= $estadisticas['docentes_total']; ?></div>
                        <div class="metric-label">Docentes Certificados</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-check-circle"></i>
                        <span><?= $estadisticas['docentes_activos']; ?> activos</span>
                    </div>
                    <a href="<?= route('docentes.index'); ?>" class="metric-action">
                        <i class="fas fa-user-tie"></i>
                        Ver Docentes
                    </a>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon reports">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value"><?= $estadisticas['reportes_generados']; ?></div>
                        <div class="metric-label">Reportes del Mes</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?= $estadisticas['reportes_pendientes']; ?> pendientes</span>
                    </div>
                    <a href="<?= route('reportes'); ?>" class="metric-action">
                        <i class="fas fa-chart-line"></i>
                        Ver Reportes
                    </a>
                </div>
            </div>

            <!-- Segunda fila: Cursos, Libros, Componentes -->
            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon courses">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value"><?= $estadisticas['cursos_total']; ?></div>
                        <div class="metric-label">Cursos Disponibles</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-positive">
                        <i class="fas fa-check-circle"></i>
                        <span><?= $estadisticas['cursos_publicados']; ?> publicados</span>
                    </div>
                    <a href="<?= route('cursos'); ?>" class="metric-action">
                        <i class="fas fa-book-reader"></i>
                        Ver Cursos
                    </a>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon books">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value"><?= $estadisticas['libros_total']; ?></div>
                        <div class="metric-label">Libros en Biblioteca</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?= $estadisticas['libros_stock_bajo']; ?> stock bajo</span>
                    </div>
                    <a href="<?= route('libros'); ?>" class="metric-action">
                        <i class="fas fa-book-open"></i>
                        Ver Biblioteca
                    </a>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-header">
                    <div class="metric-icon components">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="metric-info">
                        <div class="metric-value"><?= $estadisticas['componentes_total']; ?></div>
                        <div class="metric-label">Componentes Electrónicos</div>
                    </div>
                </div>
                <div class="metric-footer">
                    <div class="metric-trend trend-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?= $estadisticas['componentes_stock_bajo']; ?> stock bajo</span>
                    </div>
                    <a href="<?= route('componentes'); ?>" class="metric-action">
                        <i class="fas fa-warehouse"></i>
                        Ver Inventario
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección 3: Actividad Reciente y Sesiones Activas -->
    <div class="section-card">
        <div class="widgets-grid">

            <!-- Widget de Actividad Reciente -->
            <div class="widget">
                <h3 class="widget-title">
                    <i class="fas fa-clock"></i>
                    Actividad Reciente
                </h3>

                <?php if (!empty($actividades_recientes)): ?>
                    <?php foreach ($actividades_recientes as $actividad): ?>
                        <div class="activity-item">
                            <div class="activity-icon" style="background: <?= $actividad['color'] ?? '#3b82f6'; ?>;">
                                <i class="fas fa-<?= $actividad['icono'] ?? 'info-circle'; ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title"><?= htmlspecialchars($actividad['titulo'] ?? 'Sin título'); ?></div>
                                <div class="activity-description"><?= htmlspecialchars($actividad['descripcion'] ?? 'Sin descripción'); ?></div>
                            </div>
                            <div class="activity-time">Hace <?= tiempoTranscurrido($actividad['fecha'] ?? date('Y-m-d H:i:s')); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-info-circle"></i>
                        <p>No hay actividad reciente</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Widget de Sesiones Activas -->
            <div class="widget">
                <h3 class="widget-title">
                    <i class="fas fa-wifi"></i>
                    Sesiones Activas (<?= count($sesiones_activas); ?>)
                </h3>

                <?php if (!empty($sesiones_activas)): ?>
                    <?php foreach ($sesiones_activas as $sesion): ?>
                        <div class="session-item">
                            <div class="session-user">
                                <div class="status-indicator"></div>
                                <div>
                                    <div class="session-name"><?= htmlspecialchars($sesion['usuario'] ?? 'Usuario desconocido'); ?></div>
                                    <div class="session-role"><?= htmlspecialchars($sesion['rol'] ?? 'Sin rol'); ?></div>
                                </div>
                            </div>
                            <div class="session-info">
                                <div class="session-time"><?= tiempoTranscurrido($sesion['ultimo_acceso'] ?? date('Y-m-d H:i:s')); ?></div>
                                <div class="session-device"><?= htmlspecialchars($sesion['dispositivo'] ?? 'Desconocido'); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-user-times"></i>
                        <p>No hay sesiones activas</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sección 4: Resumen del Sistema y Ventas Recientes -->
    <div class="section-card">
        <div class="widgets-grid">

            <!-- Widget de Resumen del Sistema -->
            <div class="widget summary-widget">
                <h3 class="widget-title">
                    <i class="fas fa-chart-pie"></i>
                    Resumen del Sistema
                    <a href="<?= route('reportes'); ?>" class="widget-action">Ver reportes</a>
                </h3>

                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Promedio por venta</div>
                            <div class="summary-value"><?= formatearMoneda($resumen_sistema['promedio_venta'] ?? 0); ?></div>
                            <div class="summary-description">Valor promedio de transacción</div>
                        </div>
                        <div class="summary-badge trend-positive">Promedio</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                            <i class="fas fa-tags"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Categorías activas</div>
                            <div class="summary-value"><?php echo $resumen_sistema['categorias_activas'] ?? 0; ?></div>
                            <div class="summary-description">Robótica, Electrónica, IoT, etc.</div>
                        </div>
                        <div class="summary-badge trend-positive">Activas</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Usuarios del sistema</div>
                            <div class="summary-value"><?php echo $resumen_sistema['total_usuarios'] ?? 0; ?></div>
                            <div class="summary-description">Admin, supervisores, vendedores</div>
                        </div>
                        <div class="summary-badge trend-warning">Personal</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #10b981, #047857);">
                            <i class="fas fa-warehouse"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Valor total inventario</div>
                            <div class="summary-value">Bs. <?= formatearNumero(($resumen_sistema['valor_inventario'] ?? 0) / 1000, 0); ?>K</div>
                            <div class="summary-description">Valor comercial del stock</div>
                        </div>
                        <div class="summary-badge trend-positive">Inventario</div>
                    </div>

                    <div class="summary-item">
                        <div class="summary-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="summary-content">
                            <div class="summary-label">Tasa de conversión</div>
                            <div class="summary-value"><?php echo $resumen_sistema['tasa_conversion'] ?? 0; ?>%</div>
                            <div class="summary-description">Visitantes que realizan compras</div>
                        </div>
                        <div class="summary-badge trend-positive"><?php echo $resumen_sistema['tasa_conversion'] ?? 0; ?>%</div>
                    </div>
                </div>
            </div>

            <!-- Widget de Ventas Recientes -->
            <div class="widget sales-widget">
                <h3 class="widget-title">
                    <i class="fas fa-shopping-cart"></i>
                    Ventas Recientes
                    <a href="<?= route('ventas'); ?>" class="widget-action">Ver todas</a>
                </h3>

                <div class="sales-scroll">
                    <?php if (!empty($ventas_recientes)): ?>
                        <?php foreach ($ventas_recientes as $venta): ?>
                            <div class="sale-item">
                                <div class="sale-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="sale-content">
                                    <div class="sale-customer"><?= htmlspecialchars($venta['cliente'] ?? 'Cliente desconocido'); ?></div>
                                    <div class="sale-product"><?= htmlspecialchars($venta['producto'] ?? 'Producto desconocido'); ?></div>
                                    <div class="sale-date">Hace <?= tiempoTranscurrido($venta['fecha'] ?? date('Y-m-d H:i:s')); ?></div>
                                </div>
                                <div class="sale-details">
                                    <div class="sale-amount"><?= formatearMoneda($venta['monto'] ?? 0); ?></div>
                                    <div class="sale-location"><?= htmlspecialchars($venta['ciudad'] ?? ''); ?></div>
                                    <div class="sale-status <?= strtolower(str_replace(' ', '-', $venta['estado'] ?? 'pendiente')); ?>">
                                        <?= $venta['estado'] ?? 'Pendiente'; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart"></i>
                            <p>No hay ventas recientes</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección 5: Libros Recientemente Registrados -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-header-content">
                <h2 class="section-title">
                    <i class="fas fa-book"></i>
                    Libros Recientemente Registrados
                </h2>
                <p class="section-subtitle">Últimas incorporaciones a la biblioteca digital de Tech Home Bolivia</p>
            </div>
            <div class="section-header-actions">
                <a href="<?= route('libros'); ?>" class="section-action-header">
                    <i class="fas fa-book-open"></i>
                    Ver toda la biblioteca
                </a>
            </div>
        </div>

        <div class="products-scroll">
            <div class="products-grid">
                <?php if (!empty($libros_recientes)): ?>
                    <?php foreach ($libros_recientes as $libro): ?>
                        <div class="product-card book-card">
                            <div class="product-image">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="product-content">
                                <div class="product-category"><?= htmlspecialchars($libro['categoria'] ?? 'Sin categoría'); ?></div>
                                <div class="product-title"><?= htmlspecialchars($libro['titulo'] ?? 'Sin título'); ?></div>
                                <div class="product-author">Por: <?= htmlspecialchars($libro['autor'] ?? 'Autor desconocido'); ?></div>
                                <div class="product-price"><?= formatearMoneda($libro['precio'] ?? 0); ?></div>
                                <div class="product-footer">
                                    <div class="product-stock">Stock: <?= $libro['stock'] ?? 0; ?> unidades</div>
                                    <div class="product-status <?= strtolower(str_replace(' ', '-', $libro['estado'] ?? 'disponible')); ?>">
                                        <?= $libro['estado'] ?? 'Disponible'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-book"></i>
                        <p>No hay libros registrados recientemente</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sección 6: Componentes Registrados Recientemente -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-header-content">
                <h2 class="section-title">
                    <i class="fas fa-microchip"></i>
                    Componentes Registrados Recientemente
                </h2>
                <p class="section-subtitle">Últimos componentes electrónicos agregados al inventario</p>
            </div>
            <div class="section-header-actions">
                <a href="<?= route('componentes'); ?>" class="section-action-header">
                    <i class="fas fa-warehouse"></i>
                    Ver inventario completo
                </a>
            </div>
        </div>

        <div class="products-scroll">
            <div class="products-grid">
                <?php if (!empty($componentes_recientes)): ?>
                    <?php foreach ($componentes_recientes as $componente): ?>
                        <div class="product-card component-card">
                            <div class="product-image">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <div class="product-content">
                                <div class="product-category"><?= htmlspecialchars($componente['categoria'] ?? 'Sin categoría'); ?></div>
                                <div class="product-title"><?= htmlspecialchars($componente['nombre'] ?? 'Sin nombre'); ?></div>
                                <div class="product-code">Código: <?= htmlspecialchars($componente['codigo'] ?? 'Sin código'); ?></div>
                                <div class="product-price"><?= formatearMoneda($componente['precio'] ?? 0); ?></div>
                                <div class="product-footer">
                                    <div class="product-stock">Stock: <?= $componente['stock'] ?? 0; ?> unidades</div>
                                    <div class="product-status <?= strtolower(str_replace(' ', '-', $componente['estado'] ?? 'disponible')); ?>">
                                        <?= $componente['estado'] ?? 'Disponible'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-microchip"></i>
                        <p>No hay componentes registrados recientemente</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>