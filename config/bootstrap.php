<?php
/**
 * Bootstrap del Sistema Tech Home Bolivia
 * Inicialización y configuración general del sistema
 * 
 * Este archivo debe ser incluido en todas las páginas principales
 */

// Prevenir acceso directo
if (!defined('TECH_HOME_INIT')) {
    define('TECH_HOME_INIT', true);
}

// ============================================================================
// 1. CONFIGURACIÓN DE ERRORES Y DESARROLLO
// ============================================================================

// Detectar si estamos en modo desarrollo
$isDevelopment = (
    $_SERVER['HTTP_HOST'] === 'localhost' || 
    $_SERVER['HTTP_HOST'] === '127.0.0.1' ||
    strpos($_SERVER['HTTP_HOST'], 'localhost:') === 0
);

if ($isDevelopment) {
    // Modo desarrollo: mostrar todos los errores
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    if (!defined('DESARROLLO')) define('DESARROLLO', true);
} else {
    // Modo producción: ocultar errores
    error_reporting(E_ALL);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    if (!defined('DESARROLLO')) define('DESARROLLO', false);
}

// ============================================================================
// 2. DEFINIR RUTAS Y CONSTANTES BÁSICAS
// ============================================================================

// Directorio raíz del proyecto
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

// Directorio de configuración
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', ROOT_PATH . '/config');
}

// URL base del proyecto (definir antes de cargar otros archivos)
if (!defined('BASE_URL')) {
    // Detectar protocolo y host automáticamente
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    
    // Detectar automáticamente la ruta del proyecto
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $projectPath = '';
    
    if (strpos($scriptName, '/TECH-HOME/') !== false) {
        $projectPath = '/TECH-HOME';
    } elseif (strpos($scriptName, '/tech-home/') !== false) {
        $projectPath = '/tech-home';
    }
    
    define('BASE_URL', $protocol . '://' . $host . $projectPath);
}

// Zona horaria
date_default_timezone_set('America/La_Paz');

// ============================================================================
// 3. INCLUIR ARCHIVOS DE CONFIGURACIÓN ESENCIALES
// ============================================================================

// Orden específico de carga
$configFiles = [
    CONFIG_PATH . '/constantes.php',    // Constantes del sistema
    CONFIG_PATH . '/database.php',      // Configuración de base de datos
    CONFIG_PATH . '/sesion.php',        // Configuración de sesiones
    CONFIG_PATH . '/rutas.php'          // Configuración de rutas
];

foreach ($configFiles as $file) {
    if (file_exists($file)) {
        require_once $file;
    } else {
        error_log("BOOTSTRAP: Archivo de configuración faltante: " . basename($file));
        
        if (DESARROLLO) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border-radius: 5px;'>";
            echo "<strong>⚠️ Error de configuración:</strong> No se encontró " . basename($file);
            echo "</div>";
        }
    }
}

// ============================================================================
// 4. CARGAR AUTOLOAD
// ============================================================================

$autoloadFile = ROOT_PATH . '/autoload.php';
if (file_exists($autoloadFile)) {
    require_once $autoloadFile;
} else {
    die("Error crítico: No se encontró autoload.php");
}

// ============================================================================
// 5. CONFIGURACIÓN DE SEGURIDAD
// ============================================================================

// Headers de seguridad básicos
if (!headers_sent()) {
    // Prevenir ataques XSS
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    
    // Solo en producción, habilitar HTTPS
    if (!DESARROLLO) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}

// ============================================================================
// 6. FUNCIONES HELPER GLOBALES
// ============================================================================

/**
 * Función para redireccionar de forma segura
 */
function redirigir($url, $codigo = 302) {
    if (!headers_sent()) {
        // Si es una URL relativa, agregar base
        if (strpos($url, 'http') !== 0 && defined('BASE_URL')) {
            $url = BASE_URL . '/' . ltrim($url, '/');
        }
        
        header("Location: $url", true, $codigo);
        exit();
    }
}

/**
 * Función para sanitizar datos de entrada
 */
function limpiar($data) {
    if (is_array($data)) {
        return array_map('limpiar', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

/**
 * Función para mostrar mensajes flash
 */
function mostrarMensaje($tipo = 'info', $mensaje = '') {
    $clases = [
        'success' => 'alert-success',
        'error' => 'alert-danger', 
        'warning' => 'alert-warning',
        'info' => 'alert-info'
    ];
    
    $clase = $clases[$tipo] ?? 'alert-info';
    
    return "<div class='alert $clase alert-dismissible fade show' role='alert'>
                $mensaje
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
}

/**
 * Función para debug en desarrollo
 */
function debug($data, $exit = false) {
    if (DESARROLLO) {
        echo "<pre style='background: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; margin: 10px;'>";
        echo "<strong>🐛 DEBUG:</strong>\n";
        print_r($data);
        echo "</pre>";
        
        if ($exit) exit();
    }
}

/**
 * Función para obtener configuración del sistema
 */
function obtenerConfiguracion($clave = null, $valorDefecto = null) {
    static $configuraciones = null;
    
    if ($configuraciones === null) {
        try {
            if (class_exists('Database')) {
                $db = new Database();
                $pdo = $db->getConnection();
                $stmt = $pdo->query("SELECT clave, valor, tipo FROM configuraciones");
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                $configuraciones = [];
                foreach ($results as $config) {
                    // Convertir según el tipo
                    switch ($config['tipo']) {
                        case 'booleano':
                            $configuraciones[$config['clave']] = filter_var($config['valor'], FILTER_VALIDATE_BOOLEAN);
                            break;
                        case 'numero':
                            $configuraciones[$config['clave']] = is_numeric($config['valor']) ? +$config['valor'] : $config['valor'];
                            break;
                        case 'json':
                            $configuraciones[$config['clave']] = json_decode($config['valor'], true);
                            break;
                        default:
                            $configuraciones[$config['clave']] = $config['valor'];
                    }
                }
            } else {
                $configuraciones = [];
            }
        } catch (Exception $e) {
            error_log("Error cargando configuraciones: " . $e->getMessage());
            $configuraciones = [];
        }
    }
    
    if ($clave === null) {
        return $configuraciones;
    }
    
    return $configuraciones[$clave] ?? $valorDefecto;
}

/**
 * Función para verificar permisos de usuario
 */
function tienePermiso($permiso, $usuario_id = null) {
    if ($usuario_id === null && isset($_SESSION['usuario_id'])) {
        $usuario_id = $_SESSION['usuario_id'];
    }
    
    if (!$usuario_id) return false;
    
    // Lógica básica de permisos por rol
    $rol = $_SESSION['rol'] ?? null;
    
    $permisos = [
        'Administrador' => ['todo'],
        'Docente' => ['cursos', 'estudiantes', 'reportes'],
        'Vendedor' => ['ventas', 'componentes', 'libros'],
        'Estudiante' => ['cursos', 'perfil'],
        'Invitado' => ['cursos', 'libros']
    ];
    
    if (!isset($permisos[$rol])) return false;
    
    return in_array('todo', $permisos[$rol]) || in_array($permiso, $permisos[$rol]);
}

/**
 * Función para formatear moneda boliviana
 */
function formatearMoneda($cantidad) {
    $simbolo = obtenerConfiguracion('moneda', 'Bs');
    return $simbolo . ' ' . number_format($cantidad, 2, '.', ',');
}

/**
 * Función para formatear fechas en español
 */
function formatearFecha($fecha, $formato = 'd/m/Y') {
    if (empty($fecha)) return '-';
    
    try {
        $dt = new DateTime($fecha);
        return $dt->format($formato);
    } catch (Exception $e) {
        return $fecha;
    }
}

// ============================================================================
// 7. VERIFICAR ESTADO DEL SISTEMA
// ============================================================================

// Variable para controlar si la BD está disponible
$dbDisponible = false;

// Verificar conexión a base de datos
try {
    if (class_exists('Database')) {
        $db = new Database();
        $pdo = $db->getConnection();
        $dbDisponible = true;
    }
} catch (Exception $e) {
    $dbDisponible = false;
    error_log("BOOTSTRAP: Error de conexión a BD: " . $e->getMessage());
    
    if (DESARROLLO) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 10px; border-radius: 5px;'>";
        echo "<strong>❌ Error de Base de Datos:</strong> " . $e->getMessage();
        echo "<br><strong>Solución:</strong> Verifica que MySQL esté ejecutándose y que la BD 'tech_home' exista.";
        echo "</div>";
    }
}

// Definir constante de BD disponible (solo una vez)
if (!defined('DB_AVAILABLE')) {
    define('DB_AVAILABLE', $dbDisponible);
}

// ============================================================================
// 8. CONFIGURACIÓN COMPLETADA
// ============================================================================

// Marcar que el bootstrap se completó
if (!defined('BOOTSTRAP_LOADED')) {
    define('BOOTSTRAP_LOADED', true);
}

// Log de inicialización (solo en desarrollo)
if (DESARROLLO) {
    error_log("BOOTSTRAP: Sistema inicializado correctamente - " . date('Y-m-d H:i:s'));
}

/**
 * Función para verificar si el sistema está listo
 */
function sistemaListo() {
    return defined('BOOTSTRAP_LOADED') && 
           defined('DB_AVAILABLE') && 
           DB_AVAILABLE && 
           class_exists('Database');
}

/**
 * Función para mostrar información del sistema (debug)
 */
function infoSistema() {
    if (!DESARROLLO) return;
    
    echo "<div style='background: #e7f3ff; padding: 10px; margin: 10px; border: 1px solid #b3d9ff; border-radius: 5px;'>";
    echo "<strong>📊 Info del Sistema:</strong><br>";
    echo "Bootstrap: " . (defined('BOOTSTRAP_LOADED') ? '✅' : '❌') . "<br>";
    echo "Base de Datos: " . (DB_AVAILABLE ? '✅' : '❌') . "<br>";
    echo "Desarrollo: " . (DESARROLLO ? '✅' : '❌') . "<br>";
    echo "Base URL: " . BASE_URL . "<br>";
    echo "Sesión: " . (session_status() === PHP_SESSION_ACTIVE ? '✅' : '❌') . "<br>";
    echo "</div>";
}
?>