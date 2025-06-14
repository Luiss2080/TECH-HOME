<?php
/**
 * ============================================================================
 * TECH HOME BOLIVIA - Dashboard Administrador Unificado
 * Instituto: Tech Home Bolivia – Escuela de Robótica y Tecnología Avanzada
 * ============================================================================
 */

// Incluir el controlador
require_once __DIR__ . '/../../controladores/AdminControlador.php';

// Crear instancia del controlador
$adminControlador = new AdminControlador();

// Obtener todos los datos del dashboard
$datosDashboard = $adminControlador->prepararDatosDashboard();

// Extraer variables para usar en la vista
$estadisticas = $datosDashboard['estadisticas'];
$actividades_recientes = $datosDashboard['actividades_recientes'];
$sesiones_activas = $datosDashboard['sesiones_activas'];
$ventas_recientes = $datosDashboard['ventas_recientes'];
$libros_recientes = $datosDashboard['libros_recientes'];
$componentes_recientes = $datosDashboard['componentes_recientes'];
$resumen_sistema = $datosDashboard['resumen_sistema'];
$usuario = $datosDashboard['usuario'];

// Función para debug
function logDebug($mensaje) {
    error_log("[ADMIN DASHBOARD] " . $mensaje);
}

logDebug("Dashboard admin cargado dinámicamente");
logDebug("Usuario: " . $usuario['nombre'] . " " . $usuario['apellido']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Administrador - Tech Home Bolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Evitar cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Estilos base -->
    <link rel="stylesheet" href="../../publico/css/admin/admin.css">
</head>
<body>
    <!-- Incluir Sidebar Component -->
    <?php
    $sidebar_path = '../../vistas/layouts/sidebar.php';
    
    if (file_exists($sidebar_path)) {
        include_once $sidebar_path;
    } else {
        echo '<div class="sidebar-placeholder">
                <div style="text-align: center;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px;"></i><br>
                    <strong>Sidebar no encontrado</strong><br>
                    <small>Archivo: sidebar.php</small>
                </div>
              </div>';
    }
    ?>

    <!-- Incluir Header Component -->
    <div class="header-container">
        <?php
        $header_path = '../../vistas/layouts/header.php';
        
        if (file_exists($header_path)) {
            include_once $header_path;
        } else {
            echo '<div class="header-placeholder" style="background: rgba(220, 38, 38, 0.1); border-radius: 25px; padding: 20px; color: #dc2626; font-weight: bold; text-align: center;">Header no encontrado</div>';
        }
        ?>
    </div>

    <div style="height: 180px;"></div>

    <!-- Área de Contenido Principal -->
    <div class="main-content-area">
        <div class="dashboard-content">
            
            <!-- Sección 1: Acciones Rápidas -->
            <div class="section-card">
                <h2 class="section-title">
                    <i class="fas fa-bolt"></i>
                    Acciones Rápidas
                </h2>
                <p class="section-subtitle">Accede rápidamente a las funciones principales del sistema</p>
                
                <div class="quick-actions-grid">
                    <a href="../../vistas/usuarios/crear.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h3 class="action-title">Nuevo Usuario</h3>
                        <p class="action-description">Registrar un nuevo usuario en el sistema</p>
                    </a>

                    <a href="../../vistas/cursos/crear.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <h3 class="action-title">Nuevo Curso</h3>
                        <p class="action-description">Crear un nuevo curso en la plataforma</p>
                    </a>

                    <a href="../../vistas/componentes/crear.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <h3 class="action-title">Nuevo Componente</h3>
                        <p class="action-description">Agregar componente al inventario</p>
                    </a>

                    <a href="../../vistas/ventas/crear.php" class="action-card">
                        <div class="action-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3 class="action-title">Nueva Venta</h3>
                        <p class="action-description">Procesar una nueva orden de venta</p>
                    </a>

                    <a href="../../vistas/libros/crear.php" class="action-card">
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
                                <div class="metric-value"><?php echo $estadisticas['estudiantes_total']; ?></div>
                                <div class="metric-label">Estudiantes Registrados</div>
                            </div>
                        </div>
                        <div class="metric-footer">
                            <div class="metric-trend trend-positive">
                                <i class="fas fa-arrow-up"></i>
                                <span><?php echo $estadisticas['estudiantes_activos']; ?> activos</span>
                            </div>
                            <a href="../../vistas/usuarios/index.php?rol=estudiante" class="metric-action">
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
                                <div class="metric-value"><?php echo $estadisticas['docentes_total']; ?></div>
                                <div class="metric-label">Docentes Certificados</div>
                            </div>
                        </div>
                        <div class="metric-footer">
                            <div class="metric-trend trend-positive">
                                <i class="fas fa-check-circle"></i>
                                <span><?php echo $estadisticas['docentes_activos']; ?> activos</span>
                            </div>
                            <a href="../../vistas/usuarios/index.php?rol=docente" class="metric-action">
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
                                <div class="metric-value"><?php echo $estadisticas['reportes_generados']; ?></div>
                                <div class="metric-label">Reportes del Mes</div>
                            </div>
                        </div>
                        <div class="metric-footer">
                            <div class="metric-trend trend-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span><?php echo $estadisticas['reportes_pendientes']; ?> pendientes</span>
                            </div>
                            <a href="../../vistas/Reportes/index.php" class="metric-action">
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
                                <div class="metric-value"><?php echo $estadisticas['cursos_total']; ?></div>
                                <div class="metric-label">Cursos Disponibles</div>
                            </div>
                        </div>
                        <div class="metric-footer">
                            <div class="metric-trend trend-positive">
                                <i class="fas fa-check-circle"></i>
                                <span><?php echo $estadisticas['cursos_publicados']; ?> publicados</span>
                            </div>
                            <a href="../../vistas/cursos/index.php" class="metric-action">
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
                                <div class="metric-value"><?php echo $estadisticas['libros_total']; ?></div>
                                <div class="metric-label">Libros en Biblioteca</div>
                            </div>
                        </div>
                        <div class="metric-footer">
                            <div class="metric-trend trend-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span><?php echo $estadisticas['libros_stock_bajo']; ?> stock bajo</span>
                            </div>
                            <a href="../../vistas/libros/index.php" class="metric-action">
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
                                <div class="metric-value"><?php echo $estadisticas['componentes_total']; ?></div>
                                <div class="metric-label">Componentes Electrónicos</div>
                            </div>
                        </div>
                        <div class="metric-footer">
                            <div class="metric-trend trend-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span><?php echo $estadisticas['componentes_stock_bajo']; ?> stock bajo</span>
                            </div>
                            <a href="../../vistas/componentes/index.php" class="metric-action">
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
                        
                        <?php foreach ($actividades_recientes as $actividad): ?>
                        <div class="activity-item">
                            <div class="activity-icon" style="background: <?php echo $actividad['color']; ?>;">
                                <i class="fas fa-<?php echo $actividad['icono']; ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title"><?php echo $actividad['titulo']; ?></div>
                                <div class="activity-description"><?php echo $actividad['descripcion']; ?></div>
                            </div>
                            <div class="activity-time">Hace <?php echo $actividad['tiempo']; ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Widget de Sesiones Activas -->
                    <div class="widget">
                        <h3 class="widget-title">
                            <i class="fas fa-wifi"></i>
                            Sesiones Activas (<?php echo count($sesiones_activas); ?>)
                        </h3>
                        
                        <?php foreach ($sesiones_activas as $sesion): ?>
                        <div class="session-item">
                            <div class="session-user">
                                <div class="status-indicator"></div>
                                <div>
                                    <div class="session-name"><?php echo $sesion['usuario']; ?></div>
                                    <div class="session-role"><?php echo $sesion['rol']; ?></div>
                                </div>
                            </div>
                            <div class="session-info">
                                <div class="session-time"><?php echo $sesion['tiempo']; ?></div>
                                <div class="session-device"><?php echo $sesion['dispositivo']; ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
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
                            <a href="../../vistas/reportes/index.php" class="widget-action">Ver reportes</a>
                        </h3>
                        
                        <div class="summary-grid">
                            <div class="summary-item">
                                <div class="summary-icon" style="background: linear-gradient(135deg, #3b82f6, #1d4ed8);">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="summary-content">
                                    <div class="summary-label">Promedio por venta</div>
                                    <div class="summary-value">Bs. <?php echo AdminControlador::formatearNumero($resumen_sistema['promedio_venta'], 2); ?></div>
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
                                    <div class="summary-value"><?php echo $resumen_sistema['categorias_activas']; ?></div>
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
                                    <div class="summary-value"><?php echo $resumen_sistema['total_usuarios']; ?></div>
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
                                    <div class="summary-value">Bs. <?php echo AdminControlador::formatearNumero($resumen_sistema['valor_inventario'] / 1000, 0); ?>K</div>
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
                                    <div class="summary-value"><?php echo $resumen_sistema['tasa_conversion']; ?>%</div>
                                    <div class="summary-description">Visitantes que realizan compras</div>
                                </div>
                                <div class="summary-badge trend-positive"><?php echo $resumen_sistema['tasa_conversion']; ?>%</div>
                            </div>
                        </div>
                    </div>

                    <!-- Widget de Ventas Recientes -->
                    <div class="widget sales-widget">
                        <h3 class="widget-title">
                            <i class="fas fa-shopping-cart"></i>
                            Ventas Recientes
                            <a href="../../vistas/ventas/index.php" class="widget-action">Ver todas</a>
                        </h3>
                        
                        <div class="sales-scroll">
                            <?php foreach ($ventas_recientes as $venta): ?>
                            <div class="sale-item">
                                <div class="sale-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="sale-content">
                                    <div class="sale-customer"><?php echo htmlspecialchars($venta['cliente']); ?></div>
                                    <div class="sale-product"><?php echo htmlspecialchars($venta['producto']); ?></div>
                                    <div class="sale-date">Hace <?php echo $venta['fecha']; ?></div>
                                </div>
                                <div class="sale-details">
                                    <div class="sale-amount"><?php echo AdminControlador::formatearMoneda($venta['monto']); ?></div>
                                    <div class="sale-location"><?php echo htmlspecialchars($venta['ciudad']); ?></div>
                                    <div class="sale-status <?php echo AdminControlador::obtenerClaseEstado($venta['estado']); ?>">
                                        <?php echo $venta['estado']; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
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
                        <a href="../../vistas/libros/index.php" class="section-action-header">
                            <i class="fas fa-book-open"></i>
                            Ver toda la biblioteca
                        </a>
                    </div>
                </div>
                
                <div class="products-scroll">
                    <div class="products-grid">
                        <?php foreach ($libros_recientes as $libro): ?>
                        <div class="product-card book-card">
                            <div class="product-image">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="product-content">
                                <div class="product-category"><?php echo htmlspecialchars($libro['categoria']); ?></div>
                                <div class="product-title"><?php echo htmlspecialchars($libro['titulo']); ?></div>
                                <div class="product-author">Por: <?php echo htmlspecialchars($libro['autor']); ?></div>
                                <div class="product-price"><?php echo AdminControlador::formatearMoneda($libro['precio']); ?></div>
                                <div class="product-footer">
                                    <div class="product-stock">Stock: <?php echo $libro['stock']; ?> unidades</div>
                                    <div class="product-status <?php echo AdminControlador::obtenerClaseEstado($libro['estado']); ?>">
                                        <?php echo $libro['estado']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
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
                        <a href="../../vistas/componentes/index.php" class="section-action-header">
                            <i class="fas fa-warehouse"></i>
                            Ver inventario completo
                        </a>
                    </div>
                </div>
                
                <div class="products-scroll">
                    <div class="products-grid">
                        <?php foreach ($componentes_recientes as $componente): ?>
                        <div class="product-card component-card">
                            <div class="product-image">
                                <i class="fas fa-microchip"></i>
                            </div>
                            <div class="product-content">
                                <div class="product-category"><?php echo htmlspecialchars($componente['categoria']); ?></div>
                                <div class="product-title"><?php echo htmlspecialchars($componente['nombre']); ?></div>
                                <div class="product-code">Código: <?php echo htmlspecialchars($componente['codigo']); ?></div>
                                <div class="product-price"><?php echo AdminControlador::formatearMoneda($componente['precio']); ?></div>
                                <div class="product-footer">
                                    <div class="product-stock">Stock: <?php echo $componente['stock']; ?> unidades</div>
                                    <div class="product-status <?php echo AdminControlador::obtenerClaseEstado($componente['estado']); ?>">
                                        <?php echo $componente['estado']; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluir Footer Component -->
    <div class="footer-container">
        <?php
        $footer_path = '../../vistas/layouts/footer.php';
        
        if (file_exists($footer_path)) {
            include_once $footer_path;
        } else {
            echo '<div class="footer-placeholder" style="background: rgba(220, 38, 38, 0.1); border-radius: 25px; padding: 20px; color: #dc2626; font-weight: bold; text-align: center; margin: 20px;">Footer no encontrado</div>';
        }
        ?>
    </div>

    <!-- Scripts -->
    <script src="../../publico/js/admin.js"></script>
    <script>
        // Inicializar el dashboard cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Preparar datos del usuario para JavaScript
            const userData = {
                nombre: '<?php echo htmlspecialchars($usuario['nombre']); ?>',
                apellido: '<?php echo htmlspecialchars($usuario['apellido']); ?>',
                rol: '<?php echo htmlspecialchars($usuario['rol']); ?>',
                email: '<?php echo htmlspecialchars($usuario['email']); ?>',
                sessionId: '<?php echo session_id(); ?>'
            };
            
            // Inicializar el dashboard con los datos del usuario
            if (typeof initAdminDashboard === 'function') {
                initAdminDashboard(userData);
            }
            
            // Inicializar efectos adicionales
            setTimeout(() => {
                if (typeof initAdvancedEffects === 'function') {
                    initAdvancedEffects();
                }
            }, 500);
        });
    </script>
</body>
</html>