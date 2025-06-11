<?php
/**
 * ============================================================================
 * TECH HOME BOLIVIA - Gestión de Usuarios
 * Instituto: Tech Home Bolivia – Escuela de Robótica y Tecnología Avanzada
 * ============================================================================
 */

// Configuración de errores para desarrollo
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inicializar sesión si no está activa
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ============================================================================
// INICIALIZACIÓN DE VARIABLES (SIEMPRE DEFINIDAS)
// ============================================================================

// Inicializar TODAS las variables por defecto para evitar errores
$usuarios = [];
$roles = [];
$estadisticas = [
    'total_usuarios' => 0,
    'usuarios_activos' => 0,
    'administradores' => 0,
    'nuevos_mes' => 0
];
$paginacion = [
    'total_registros' => 0, 
    'total_paginas' => 1, 
    'pagina_actual' => 1,
    'tiene_anterior' => false,
    'tiene_siguiente' => false
];
$filtros = [
    'busqueda' => $_GET['busqueda'] ?? '',
    'rol' => $_GET['rol'] ?? '',
    'estado' => isset($_GET['estado']) ? ($_GET['estado'] === '1' ? 1 : ($_GET['estado'] === '0' ? 0 : null)) : null,
    'orden' => $_GET['orden'] ?? 'u.fecha_creacion DESC'
];

// Verificación de autenticación simplificada (sin redirecciones automáticas)
$usuario_autenticado = isset($_SESSION['usuario_id']) && isset($_SESSION['rol']);
$es_administrador = $usuario_autenticado && $_SESSION['rol'] === 'Administrador';

// Si no está autenticado, mostrar mensaje de error pero no redirigir
if (!$usuario_autenticado) {
    $error_autenticacion = "Debes iniciar sesión para acceder a esta página.";
} elseif (!$es_administrador) {
    $error_autenticacion = "No tienes permisos para acceder a esta página.";
}

// Solo incluir el controlador si está autenticado
if ($usuario_autenticado && $es_administrador) {
    // Incluir el controlador
    require_once __DIR__ . '/../../controladores/UsuarioControlador.php';
    
    // Verificar si la clase existe
    if (!class_exists('UsuarioControlador')) {
        die("ERROR: La clase UsuarioControlador no está disponible");
    }
    
    try {
        $controlador = new UsuarioControlador();
    } catch (Exception $e) {
        die("ERROR: No se pudo crear instancia del controlador: " . $e->getMessage());
    }
    
    // ============================================================================
    // PROCESAMIENTO DE DATOS
    // ============================================================================
    
    // Obtener número de página actual
    $pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
    
    try {
        // Obtener datos del controlador
        $resultado = $controlador->index($filtros, $pagina);
        
        $usuarios = $resultado['usuarios'];
        $roles = $resultado['roles'];
        $paginacion = $resultado['paginacion'];
        
        // Obtener estadísticas
        $datosEstadisticas = $controlador->obtenerEstadisticas();
        $estadisticas = $datosEstadisticas['estadisticas'];
        
    } catch (Exception $e) {
        $error_datos = "ERROR: No se pudieron obtener los datos: " . $e->getMessage();
        // Las variables ya están inicializadas al principio
    }
}

// ============================================================================
// FUNCIONES AUXILIARES
// ============================================================================

function calcularTiempoTranscurrido($fecha) {
    try {
        $fechaCreacion = new DateTime($fecha);
        $fechaActual = new DateTime();
        $diferencia = $fechaActual->diff($fechaCreacion);
        
        if ($diferencia->days > 0) {
            return 'Hace ' . $diferencia->days . ' días';
        } elseif ($diferencia->h > 0) {
            return 'Hace ' . $diferencia->h . ' horas';
        } else {
            return 'Hace ' . $diferencia->i . ' minutos';
        }
    } catch (Exception $e) {
        return 'Fecha inválida';
    }
}

function obtenerColorRol($rol) {
    $colores = [
        'Administrador' => '#dc3545',
        'Docente' => '#6f42c1', 
        'Estudiante' => '#007bff',
        'Invitado' => '#6c757d',
        'Vendedor' => '#17a2b8'
    ];
    return $colores[$rol] ?? '#6c757d';
}

function construirUrl($archivo, $parametros = []) {
    $url = $archivo;
    if (!empty($parametros)) {
        $url .= '?' . http_build_query($parametros);
    }
    return $url;
}

// Función para debug
function logDebug($mensaje) {
    error_log("[USUARIOS INDEX] " . $mensaje);
}

logDebug("Vista usuarios/index.php cargada");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- ============================================================================
         CONFIGURACIÓN DEL DOCUMENTO HTML
         ============================================================================ -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - Tech Home Bolivia</title>
    
    <!-- Hojas de estilo externas -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Estilos base del sistema -->
    <link rel="stylesheet" href="../../publico/css/admin/admin.css">
    <link rel="stylesheet" href="../../publico/css/dashboard.css">
</head>
<body>
    <!-- Incluir Sidebar Component -->
    <?php
    $sidebar_path = '../../vistas/layouts/sidebar.php';
    if (file_exists($sidebar_path)) {
        include_once $sidebar_path;
    }
    ?>

    <!-- Incluir Header Component -->
    <div class="header-container">
        <?php
        $header_path = '../../vistas/layouts/header.php';
        if (file_exists($header_path)) {
            include_once $header_path;
        }
        ?>
    </div>

    <div style="height: 180px;"></div>

    <!-- Fondo animado -->
    <div class="bg-animation">
        <div class="floating-shapes shape-1"></div>
        <div class="floating-shapes shape-2"></div> 
        <div class="floating-shapes shape-3"></div>
    </div>

    <div class="main-container">
        <!-- ============================================================================
             HEADER DEL MÓDULO DE USUARIOS
             ============================================================================ -->
        <div class="module-header">
            <div class="header-content">
                <div class="header-left">
                    <div class="header-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="header-text">
                        <h1>Gestión de Usuarios</h1>
                        <p>Administra los usuarios registrados en el sistema</p>
                    </div>
                </div>
                <div class="breadcrumb">
                    <a href="../dashboard/index.php">Dashboard</a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Usuarios</span>
                </div>
            </div>
        </div>

        <!-- ============================================================================
             TARJETAS DE ESTADÍSTICAS
             ============================================================================ -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number"><?= number_format($estadisticas['total_usuarios'] ?? 0) ?></div>
                    <div class="stat-label">Total Usuarios</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number"><?= number_format($estadisticas['usuarios_activos']) ?></div>
                    <div class="stat-label">Usuarios Activos</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number"><?= number_format($estadisticas['administradores']) ?></div>
                    <div class="stat-label">Administradores</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-shield"></i>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-number"><?= number_format($estadisticas['nuevos_mes']) ?></div>
                    <div class="stat-label">Nuevos este mes</div>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>

        <!-- ============================================================================
             FILTROS Y BÚSQUEDA
             ============================================================================ -->
        <div class="filters-section">
            <div class="section-header">
                <h3><i class="fas fa-filter"></i> Filtros y Búsqueda</h3>
                <div class="header-actions">
                    <button type="button" class="btn btn-success" onclick="exportarUsuarios()">
                        <i class="fas fa-download"></i>
                        Exportar
                    </button>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='crear.php'">
                        <i class="fas fa-plus"></i>
                        Nuevo Usuario
                    </button>
                </div>
            </div>

            <form method="GET" class="filters-form" id="filtersForm">
                <div class="filters-row">
                    <div class="filter-group">
                        <label class="filter-label">Buscar usuario</label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                class="form-input" 
                                name="busqueda" 
                                value="<?= htmlspecialchars($filtros['busqueda'] ?? '') ?>"
                                placeholder="Nombre, apellido o email..."
                            >
                            <i class="fas fa-search input-icon"></i>
                        </div>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Filtrar por rol</label>
                        <div class="input-wrapper">
                            <select class="form-select" name="rol">
                                <option value="">Todos los roles</option>
                                <option value="Administrador" <?= ($filtros['rol'] ?? '') == 'Administrador' ? 'selected' : '' ?>>
                                    Administrador
                                </option>
                                <option value="Docente" <?= ($filtros['rol'] ?? '') == 'Docente' ? 'selected' : '' ?>>
                                    Docente
                                </option>
                                <option value="Estudiante" <?= ($filtros['rol'] ?? '') == 'Estudiante' ? 'selected' : '' ?>>
                                    Estudiante
                                </option>
                                <option value="Vendedor" <?= ($filtros['rol'] ?? '') == 'Vendedor' ? 'selected' : '' ?>>
                                    Vendedor
                                </option>
                                <option value="Invitado" <?= ($filtros['rol'] ?? '') == 'Invitado' ? 'selected' : '' ?>>
                                    Invitado
                                </option>
                            </select>
                            <i class="fas fa-user-tag input-icon"></i>
                        </div>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Estado</label>
                        <div class="input-wrapper">
                            <select class="form-select" name="estado">
                                <option value="">Todos los estados</option>
                                <option value="1" <?= ($filtros['estado'] ?? '') == '1' ? 'selected' : '' ?>>
                                    Activos
                                </option>
                                <option value="0" <?= ($filtros['estado'] ?? '') == '0' ? 'selected' : '' ?>>
                                    Inactivos
                                </option>
                            </select>
                            <i class="fas fa-toggle-on input-icon"></i>
                        </div>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                            Filtrar
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="limpiarFiltros()">
                            <i class="fas fa-times"></i>
                            Limpiar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ============================================================================
             TABLA DE USUARIOS
             ============================================================================ -->
        <div class="table-section">
            <div class="section-header">
                <h3><i class="fas fa-list"></i> Lista de Usuarios Registrados</h3>
                <div class="results-info">
                    Mostrando: <?= count($usuarios) ?> de <?= number_format($paginacion['total_registros'] ?? 0) ?> usuarios
                </div>
            </div>

            <!-- Mostrar errores si existen -->
            <?php if (isset($error_mensaje)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error_mensaje) ?>
                </div>
            <?php endif; ?>

            <!-- Mostrar error de autenticación si existe -->
            <?php if (isset($error_autenticacion)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($error_autenticacion) ?>
                </div>
            <?php endif; ?>

            <!-- Mostrar mensajes de éxito si existen -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($_GET['success']) ?>
                </div>
            <?php endif; ?>

            <!-- Mostrar mensajes de error si existen -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <?php if (empty($usuarios)): ?>
                <!-- Estado vacío -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>No se encontraron usuarios</h3>
                    <p>No hay usuarios que coincidan con los filtros seleccionados.</p>
                    <button type="button" class="btn btn-primary" onclick="window.location.href='crear.php'">
                        <i class="fas fa-plus"></i>
                        Crear Primer Usuario
                    </button>
                </div>
            <?php else: ?>
                <!-- Tabla con usuarios -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag"></i> ID</th>
                                <th><i class="fas fa-user"></i> Usuario</th>
                                <th><i class="fas fa-envelope"></i> Email</th>
                                <th><i class="fas fa-user-tag"></i> Rol</th>
                                <th><i class="fas fa-toggle-on"></i> Estado</th>
                                <th><i class="fas fa-clock"></i> Última Actividad</th>
                                <th><i class="fas fa-calendar"></i> Registro</th>
                                <th><i class="fas fa-cogs"></i> Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $usuario): ?>
                                <tr>
                                    <!-- ID -->
                                    <td>
                                        <span class="id-badge">#<?= $usuario['id'] ?></span>
                                    </td>
                                    
                                    <!-- Usuario con avatar -->
                                    <td>
                                        <div class="product-info">
                                            <div class="product-avatar">
                                                <div class="avatar-fallback">
                                                    <span class="initials">
                                                        <?= strtoupper(substr($usuario['nombre'] ?? 'U', 0, 1) . substr($usuario['apellido'] ?? 'S', 0, 1)) ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="product-details">
                                                <div class="product-name">
                                                    <?= htmlspecialchars(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? '')) ?>
                                                </div>
                                                <div class="product-description">
                                                    <?= htmlspecialchars($usuario['telefono'] ?? 'Sin teléfono') ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Email -->
                                    <td>
                                        <div class="location-info">
                                            <div class="location-name"><?= htmlspecialchars($usuario['email'] ?? '') ?></div>
                                        </div>
                                    </td>
                                    
                                    <!-- Rol -->
                                    <td>
                                        <?php
                                        $rol = $usuario['rol_nombre'] ?? 'Sin rol';
                                        $rol_class = strtolower($rol);
                                        $rol_icons = [
                                            'administrador' => 'fas fa-user-shield',
                                            'docente' => 'fas fa-chalkboard-teacher',
                                            'estudiante' => 'fas fa-user-graduate',
                                            'vendedor' => 'fas fa-shopping-cart',
                                            'invitado' => 'fas fa-user-clock'
                                        ];
                                        $icon = $rol_icons[$rol_class] ?? 'fas fa-user';
                                        ?>
                                        <span class="category-badge <?= $rol_class ?>">
                                            <i class="<?= $icon ?>"></i> <?= htmlspecialchars($rol) ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Estado -->
                                    <td>
                                        <div class="stock-info">
                                            <span class="stock-amount <?= ($usuario['estado'] ?? 0) ? 'in-stock' : 'out-stock' ?>">
                                                <?= ($usuario['estado'] ?? 0) ? 'Activo' : 'Inactivo' ?>
                                            </span>
                                            <small class="stock-label">
                                                <?= ($usuario['estado'] ?? 0) ? 'Habilitado' : 'Deshabilitado' ?>
                                            </small>
                                        </div>
                                    </td>
                                    
                                    <!-- Última actividad -->
                                    <td>
                                        <div class="date-info">
                                            <?php if (!empty($usuario['fecha_actualizacion'])): ?>
                                                <span class="date"><?= date('d/m/Y', strtotime($usuario['fecha_actualizacion'])) ?></span>
                                                <small class="time">Hace <?= calcularTiempoTranscurrido($usuario['fecha_actualizacion']) ?></small>
                                            <?php else: ?>
                                                <span class="date">Nunca</span>
                                                <small class="time">Sin actividad</small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    
                                    <!-- Fecha de registro -->
                                    <td>
                                        <div class="date-info">
                                            <?php if (!empty($usuario['fecha_creacion'])): ?>
                                                <span class="date"><?= date('d/m/Y', strtotime($usuario['fecha_creacion'])) ?></span>
                                                <small class="time">Hace <?= calcularTiempoTranscurrido($usuario['fecha_creacion']) ?></small>
                                            <?php else: ?>
                                                <span class="date">-</span>
                                                <small class="time">-</small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    
                                    <!-- Botones de acción -->
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="btn-action btn-view" 
                                                    onclick="window.location.href='ver.php?id=<?= $usuario['id'] ?>'"
                                                    title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn-action btn-edit" 
                                                    onclick="window.location.href='editar.php?id=<?= $usuario['id'] ?>'"
                                                    title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn-action btn-delete" 
                                                    onclick="confirmarEliminacion(<?= $usuario['id'] ?>, '<?= htmlspecialchars(($usuario['nombre'] ?? '') . ' ' . ($usuario['apellido'] ?? '')) ?>')"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <?php if (($paginacion['total_paginas'] ?? 1) > 1): ?>
                    <div class="pagination-container">
                        <div class="pagination-info">
                            Página <?= $paginacion['pagina_actual'] ?? 1 ?> de <?= $paginacion['total_paginas'] ?? 1 ?>
                            (<?= number_format($paginacion['total_registros'] ?? 0) ?> usuarios en total)
                        </div>
                        
                        <div class="pagination">
                            <?php if ($paginacion['tiene_anterior'] ?? false): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => ($paginacion['pagina_actual'] ?? 1) - 1])) ?>" 
                                   class="pagination-btn">
                                    <i class="fas fa-chevron-left"></i>
                                    Anterior
                                </a>
                            <?php endif; ?>

                            <?php
                            $inicio = max(1, ($paginacion['pagina_actual'] ?? 1) - 2);
                            $fin = min(($paginacion['total_paginas'] ?? 1), ($paginacion['pagina_actual'] ?? 1) + 2);
                            
                            for ($i = $inicio; $i <= $fin; $i++):
                            ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => $i])) ?>" 
                                   class="pagination-btn <?= $i == ($paginacion['pagina_actual'] ?? 1) ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($paginacion['tiene_siguiente'] ?? false): ?>
                                <a href="?<?= http_build_query(array_merge($_GET, ['pagina' => ($paginacion['pagina_actual'] ?? 1) + 1])) ?>" 
                                   class="pagination-btn">
                                    Siguiente
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de eliminación -->
    <div id="deleteModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h3>
                <button type="button" class="modal-close" onclick="cerrarModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>¿Está seguro que desea eliminar el usuario <strong id="userName"></strong>?</p>
                <p class="warning-text">Esta acción desactivará el usuario y no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash"></i>
                    Eliminar Usuario
                </button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer-container">
        <?php
        $footer_path = '../../vistas/layouts/footer.php';
        if (file_exists($footer_path)) {
            include_once $footer_path;
        }
        ?>
    </div>

    <!-- JavaScript -->
    <script>
        let userIdToDelete = null;

        function limpiarFiltros() {
            window.location.href = 'index.php';
        }

        function confirmarEliminacion(userId, userName) {
            userIdToDelete = userId;
            document.getElementById('userName').textContent = userName;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('deleteModal').style.display = 'none';
            userIdToDelete = null;
        }

        function exportarUsuarios() {
            const params = new URLSearchParams(window.location.search);
            params.delete('pagina');
            params.delete('success');
            params.delete('error');
            params.append('export_time', new Date().getTime());
            
            const exportUrl = 'exportar.php?' + params.toString();
            window.open(exportUrl, '_blank');
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('confirmDelete').addEventListener('click', function() {
                if (userIdToDelete) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'eliminar.php';
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'user_id';
                    input.value = userIdToDelete;
                    
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    cerrarModal();
                }
            });

            const selects = document.querySelectorAll('.filters-form select');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                    document.getElementById('filtersForm').submit();
                });
            });
        });
    </script>
</body>
</html>