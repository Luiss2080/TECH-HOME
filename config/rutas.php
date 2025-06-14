<?php
/**
 * Sistema de Rutas - Tech Home Bolivia
 * Configuración y manejo de rutas del sistema
 */

// Prevenir acceso directo
if (!defined('TECH_HOME_INIT')) {
    die('Acceso directo no permitido');
}

// ============================================================================
// DEFINICIÓN DE RUTAS PRINCIPALES
// ============================================================================

/**
 * Rutas principales del sistema
 */
$rutas = [
    // ===== RUTAS DE AUTENTICACIÓN =====
    'login' => BASE_URL . '/login.php',
    'logout' => BASE_URL . '/logout.php',
    'verificar_sesion' => BASE_URL . '/verify_session.php',
    
    // ===== RUTAS DE DASHBOARD =====
    'dashboard' => BASE_URL . '/vistas/dashboard',
    'dashboard.admin' => BASE_URL . '/vistas/dashboard/admin.php',
    'dashboard.docente' => BASE_URL . '/vistas/dashboard/docente.php',
    'dashboard.estudiante' => BASE_URL . '/vistas/dashboard/estudiante.php',
    
    // ===== RUTAS DE USUARIOS =====
    'usuarios' => BASE_URL . '/vistas/usuarios',
    'usuarios.index' => BASE_URL . '/vistas/usuarios/index.php',
    'usuarios.crear' => BASE_URL . '/vistas/usuarios/crear.php',
    'usuarios.editar' => BASE_URL . '/vistas/usuarios/editar.php',
    'usuarios.ver' => BASE_URL . '/vistas/usuarios/ver.php',
    'usuarios.eliminar' => BASE_URL . '/vistas/usuarios/eliminar.php',
    
    // ===== RUTAS DE CURSOS =====
    'cursos' => BASE_URL . '/vistas/cursos',
    'cursos.index' => BASE_URL . '/vistas/cursos/index.php',
    'cursos.crear' => BASE_URL . '/vistas/cursos/crear.php',
    'cursos.editar' => BASE_URL . '/vistas/cursos/editar.php',
    'cursos.ver' => BASE_URL . '/vistas/cursos/ver.php',
    'cursos.eliminar' => BASE_URL . '/vistas/cursos/eliminar.php',
    
    // ===== RUTAS DE LIBROS =====
    'libros' => BASE_URL . '/vistas/libros',
    'libros.index' => BASE_URL . '/vistas/libros/index.php',
    'libros.crear' => BASE_URL . '/vistas/libros/crear.php',
    'libros.editar' => BASE_URL . '/vistas/libros/editar.php',
    'libros.ver' => BASE_URL . '/vistas/libros/ver.php',
    'libros.eliminar' => BASE_URL . '/vistas/libros/eliminar.php',
    'libros.actualizar_stock' => BASE_URL . '/vistas/libros/actualizar_stock.php',
    
    // ===== RUTAS DE COMPONENTES =====
    'componentes' => BASE_URL . '/vistas/componentes',
    'componentes.index' => BASE_URL . '/vistas/componentes/index.php',
    'componentes.crear' => BASE_URL . '/vistas/componentes/crear.php',
    'componentes.editar' => BASE_URL . '/vistas/componentes/editar.php',
    'componentes.ver' => BASE_URL . '/vistas/componentes/ver.php',
    'componentes.eliminar' => BASE_URL . '/vistas/componentes/eliminar.php',
    'componentes.actualizar_stock' => BASE_URL . '/vistas/componentes/actualizar_stock.php',
    
    // ===== RUTAS DE VENTAS =====
    'ventas' => BASE_URL . '/vistas/ventas',
    'ventas.index' => BASE_URL . '/vistas/ventas/index.php',
    'ventas.crear' => BASE_URL . '/vistas/ventas/crear.php',
    'ventas.editar' => BASE_URL . '/vistas/ventas/editar.php',
    'ventas.ver' => BASE_URL . '/vistas/ventas/ver.php',
    'ventas.eliminar' => BASE_URL . '/vistas/ventas/eliminar.php',
    'ventas.cambiar_estado' => BASE_URL . '/vistas/ventas/cambiar_estado.php',
    'ventas.catalogo' => BASE_URL . '/vistas/ventas/catalogo.php',
    
    // ===== RUTAS DE REPORTES =====
    'reportes' => BASE_URL . '/vistas/Reportes',
    'reportes.index' => BASE_URL . '/vistas/Reportes/index.php',
    'reportes.estudiantes' => BASE_URL . '/vistas/Reportes/estudiantes.php',
    'reportes.docentes' => BASE_URL . '/vistas/Reportes/docentes.php',
    'reportes.ventas_libros' => BASE_URL . '/vistas/Reportes/ventas_libros.php',
    'reportes.ventas_componentes' => BASE_URL . '/vistas/Reportes/ventas_componentes.php',
    'reportes.graficos' => BASE_URL . '/vistas/Reportes/graficos.php',
    
    // ===== RUTAS DE PERFIL =====
    'perfil.admin' => BASE_URL . '/vistas/admin/perfil.php',
    'perfil.docente' => BASE_URL . '/vistas/docentes/perfil.php',
    'perfil.estudiante' => BASE_URL . '/vistas/estudiantes/perfil.php',
    'estudiante.progreso' => BASE_URL . '/vistas/estudiantes/progreso.php',
    
    // ===== RUTAS DE ERRORES =====
    'error.403' => BASE_URL . '/vistas/errores/403.php',
    'error.404' => BASE_URL . '/vistas/errores/404.php',
    'error.500' => BASE_URL . '/vistas/errores/500.php',
    
    // ===== RUTAS DE AUTENTICACIÓN ADICIONAL =====
    'auth.password' => BASE_URL . '/vistas/auth/Password.php',
    
    // ===== RUTAS DE RECURSOS =====
    'assets' => ASSETS_URL,
    'css' => CSS_URL,
    'js' => JS_URL,
    'imagenes' => IMAGES_URL,
    'uploads' => UPLOADS_URL,
];

// ============================================================================
// FUNCIONES PARA MANEJO DE RUTAS
// ============================================================================

/**
 * Obtener URL de una ruta específica
 */
function ruta($nombre, $parametros = []) {
    global $rutas;
    
    if (!isset($rutas[$nombre])) {
        error_log("RUTAS: Ruta '$nombre' no encontrada");
        return BASE_URL; // Fallback a la página principal
    }
    
    $url = $rutas[$nombre];
    
    // Agregar parámetros GET si existen
    if (!empty($parametros)) {
        $query = http_build_query($parametros);
        $url .= (strpos($url, '?') !== false ? '&' : '?') . $query;
    }
    
    return $url;
}

/**
 * Verificar si una ruta existe
 */
function rutaExiste($nombre) {
    global $rutas;
    return isset($rutas[$nombre]);
}

/**
 * Obtener todas las rutas disponibles
 */
function obtenerRutas() {
    global $rutas;
    return $rutas;
}

/**
 * Obtener ruta actual basada en REQUEST_URI
 */
function rutaActual() {
    $rutaActual = $_SERVER['REQUEST_URI'] ?? '';
    
    // Remover query string
    if (strpos($rutaActual, '?') !== false) {
        $rutaActual = substr($rutaActual, 0, strpos($rutaActual, '?'));
    }
    
    // Remover base del proyecto
    $basePath = parse_url(BASE_URL, PHP_URL_PATH);
    if ($basePath && strpos($rutaActual, $basePath) === 0) {
        $rutaActual = substr($rutaActual, strlen($basePath));
    }
    
    return ltrim($rutaActual, '/');
}

/**
 * Verificar si estamos en una ruta específica
 */
function esRutaActual($nombre) {
    $rutaEsperada = ruta($nombre);
    $rutaActual = $_SERVER['REQUEST_URI'] ?? '';
    
    // Comparar solo la parte de la ruta sin query string
    $rutaActualLimpia = explode('?', $rutaActual)[0];
    $rutaEsperadaLimpia = explode('?', $rutaEsperada)[0];
    
    return $rutaActualLimpia === $rutaEsperadaLimpia;
}

// ============================================================================
// SISTEMA DE REDIRECCIÓN INTELIGENTE
// ============================================================================

/**
 * Redireccionar a una ruta del sistema
 */
function redirigirA($nombre, $parametros = [], $codigo = 302) {
    $url = ruta($nombre, $parametros);
    redirigir($url, $codigo);
}

/**
 * Redireccionar según el rol del usuario
 */
function redirigirPorRol($usuario_id = null) {
    if (!$usuario_id && isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
    }
    
    if (!$usuario_id) {
        redirigirA('login');
        return;
    }
    
    $rol = $_SESSION['rol'] ?? null;
    
    switch ($rol) {
        case 'Administrador':
            redirigirA('dashboard.admin');
            break;
        case 'Docente':
            redirigirA('dashboard.docente');
            break;
        case 'Estudiante':
        case 'Invitado':
            redirigirA('dashboard.estudiante');
            break;
        case 'Vendedor':
            redirigirA('ventas.index');
            break;
        default:
            redirigirA('login');
            break;
    }
}

/**
 * Redireccionar después de login exitoso
 */
function redirigirDespuesLogin() {
    // Verificar si hay una URL de retorno
    $returnUrl = $_SESSION['return_url'] ?? null;
    unset($_SESSION['return_url']);
    
    if ($returnUrl && filter_var($returnUrl, FILTER_VALIDATE_URL)) {
        // Verificar que sea una URL del mismo dominio
        $returnHost = parse_url($returnUrl, PHP_URL_HOST);
        $currentHost = $_SERVER['HTTP_HOST'] ?? '';
        
        if ($returnHost === $currentHost) {
            redirigir($returnUrl);
            return;
        }
    }
    
    // Redireccionar según el rol
    redirigirPorRol();
}

// ============================================================================
// VERIFICACIÓN DE PERMISOS POR RUTA
// ============================================================================

/**
 * Verificar si el usuario tiene acceso a una ruta
 */
function tieneAccesoRuta($nombre, $usuario_id = null) {
    if (!$usuario_id && isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
    }
    
    if (!$usuario_id) return false;
    
    $rol = $_SESSION['rol'] ?? null;
    
    // Administrador tiene acceso a todo
    if ($rol === 'Administrador') return true;
    
    // Mapeo de rutas a permisos requeridos
    $permisosRutas = [
        'usuarios' => ['Administrador'],
        'cursos' => ['Administrador', 'Docente', 'Estudiante', 'Invitado'],
        'libros' => ['Administrador', 'Vendedor', 'Estudiante', 'Invitado'],
        'componentes' => ['Administrador', 'Vendedor'],
        'ventas' => ['Administrador', 'Vendedor'],
        'reportes' => ['Administrador', 'Docente', 'Vendedor'],
        'dashboard' => ['Administrador', 'Docente', 'Estudiante', 'Invitado', 'Vendedor']
    ];
    
    // Extraer módulo de la ruta
    $modulo = explode('.', $nombre)[0];
    
    if (!isset($permisosRutas[$modulo])) {
        return true; // Permitir acceso si no hay restricciones específicas
    }
    
    return in_array($rol, $permisosRutas[$modulo]);
}

/**
 * Middleware para verificar acceso antes de cargar una página
 */
function verificarAccesoRuta($nombreRuta) {
    if (!tieneAccesoRuta($nombreRuta)) {
        // Registrar intento de acceso no autorizado
        error_log("SEGURIDAD: Acceso no autorizado a ruta '$nombreRuta' por usuario " . ($_SESSION['usuario_id'] ?? 'anónimo'));
        
        // Redireccionar a página de error
        redirigirA('error.403');
    }
}

// ============================================================================
// UTILIDADES PARA NAVEGACIÓN
// ============================================================================

/**
 * Generar breadcrumbs basado en la ruta actual
 */
function generarBreadcrumbs() {
    $rutaActual = rutaActual();
    $segmentos = explode('/', trim($rutaActual, '/'));
    
    $breadcrumbs = [
        ['nombre' => 'Inicio', 'url' => BASE_URL]
    ];
    
    $rutaAcumulada = '';
    foreach ($segmentos as $segmento) {
        if (empty($segmento)) continue;
        
        $rutaAcumulada .= '/' . $segmento;
        
        // Convertir segmento a nombre legible
        $nombre = ucfirst(str_replace(['-', '_'], ' ', $segmento));
        
        $breadcrumbs[] = [
            'nombre' => $nombre,
            'url' => BASE_URL . $rutaAcumulada
        ];
    }
    
    return $breadcrumbs;
}

/**
 * Generar menú de navegación principal
 */
function generarMenuNavegacion($rol = null) {
    if (!$rol && isset($_SESSION['rol'])) {
        $rol = $_SESSION['rol'];
    }
    
    $menu = [];
    
    // Dashboard siempre disponible para usuarios autenticados
    if ($rol) {
        $menu['Dashboard'] = ruta('dashboard.' . strtolower($rol));
    }
    
    // Menú según el rol
    switch ($rol) {
        case 'Administrador':
            $menu['Usuarios'] = ruta('usuarios.index');
            $menu['Cursos'] = ruta('cursos.index');
            $menu['Libros'] = ruta('libros.index');
            $menu['Componentes'] = ruta('componentes.index');
            $menu['Ventas'] = ruta('ventas.index');
            $menu['Reportes'] = ruta('reportes.index');
            break;
            
        case 'Docente':
            $menu['Cursos'] = ruta('cursos.index');
            $menu['Estudiantes'] = ruta('reportes.estudiantes');
            $menu['Reportes'] = ruta('reportes.index');
            break;
            
        case 'Vendedor':
            $menu['Catálogo'] = ruta('ventas.catalogo');
            $menu['Ventas'] = ruta('ventas.index');
            $menu['Libros'] = ruta('libros.index');
            $menu['Componentes'] = ruta('componentes.index');
            break;
            
        case 'Estudiante':
        case 'Invitado':
            $menu['Cursos'] = ruta('cursos.index');
            $menu['Biblioteca'] = ruta('libros.index');
            $menu['Mi Progreso'] = ruta('estudiante.progreso');
            break;
    }
    
    return $menu;
}

// ============================================================================
// DEFINIR CONSTANTES DE RUTAS CRÍTICAS
// ============================================================================

// Rutas que requieren autenticación
define('RUTAS_AUTENTICADAS', [
    'dashboard', 'usuarios', 'cursos', 'libros', 'componentes', 
    'ventas', 'reportes', 'perfil'
]);

// Rutas públicas (no requieren autenticación)
define('RUTAS_PUBLICAS', [
    'login', 'error'
]);

// Rutas de solo administrador
define('RUTAS_ADMIN_SOLO', [
    'usuarios'
]);
?>