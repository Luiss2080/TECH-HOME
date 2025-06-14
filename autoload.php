<?php
/**
 * Autoload para Tech Home Bolivia
 * Sistema mejorado de carga automática de clases
 */

// Prevenir ejecución directa
if (!defined('TECH_HOME_ROOT')) {
    define('TECH_HOME_ROOT', __DIR__);
}

/**
 * Función de autoload personalizada
 */
spl_autoload_register(function ($className) {
    // Mapeo de clases a directorios
    $classMap = [
        // Controladores
        'UsuarioControlador' => '/controladores/UsuarioControlador.php',
        'DashboardControlador' => '/controladores/DashboardControlador.php',
        'CursoControlador' => '/controladores/CursoControlador.php',
        'LibroControlador' => '/controladores/LibroControlador.php',
        'ComponenteControlador' => '/controladores/ComponenteControlador.php',
        'VentaControlador' => '/controladores/VentaControlador.php',
        'AdminControlador' => '/controladores/AdminControlador.php',
        'DocenteControlador' => '/controladores/DocenteControlador.php',
        'EstudianteControlador' => '/controladores/EstudianteControlador.php',
        'ReporteControlador' => '/controladores/ReporteControlador.php',
        'ErroresControlador' => '/controladores/ErroresControlador.php',
        'PasswordControlador' => '/controladores/PasswordControlador.php',
        
        // Modelos
        'UsuarioModelo' => '/modelos/UsuarioModelo.php',
        'DashboardModelo' => '/modelos/DashboardModelo.php',
        'CursoModelo' => '/modelos/CursoModelo.php',
        'LibroModelo' => '/modelos/LibroModelo.php',
        'ComponenteModelo' => '/modelos/ComponenteModelo.php',
        'VentaModelo' => '/modelos/VentaModelo.php',
        'AdminModelo' => '/modelos/AdminModelo.php',
        'DocenteModelo' => '/modelos/DocenteModelo.php',
        'EstudianteModelo' => '/modelos/EstudianteModelo.php',
        'ReporteModelo' => '/modelos/ReporteModelo.php',
        'ErroresModelo' => '/modelos/ErroresModelo.php',
        'PasswordModelo' => '/modelos/PasswordModelo.php',
        
        // Configuración
        'Database' => '/config/database.php'
    ];
    
    // Buscar en el mapeo específico primero
    if (isset($classMap[$className])) {
        $file = TECH_HOME_ROOT . $classMap[$className];
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // Buscar en directorios estándar
    $directories = [
        TECH_HOME_ROOT . '/controladores/',
        TECH_HOME_ROOT . '/modelos/',
        TECH_HOME_ROOT . '/config/',
        TECH_HOME_ROOT . '/helpers/',
        TECH_HOME_ROOT . '/libs/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // Log si no se encuentra la clase
    error_log("AUTOLOAD: No se pudo cargar la clase '$className'");
});

/**
 * Incluir archivos de configuración esenciales
 */
$configFiles = [
    TECH_HOME_ROOT . '/config/database.php',
    TECH_HOME_ROOT . '/config/constantes.php',
    TECH_HOME_ROOT . '/config/sesion.php'
];

foreach ($configFiles as $file) {
    if (file_exists($file)) {
        require_once $file;
    } else {
        error_log("AUTOLOAD: Archivo de configuración no encontrado: $file");
    }
}

/**
 * Función helper para verificar si una clase está disponible
 */
function verificarClase($className) {
    if (class_exists($className)) {
        return true;
    } else {
        error_log("SISTEMA: Clase '$className' no disponible");
        return false;
    }
}

/**
 * Función helper para mostrar errores de desarrollo
 */
function mostrarErrorDesarrollo($mensaje, $archivo = null, $linea = null) {
    if (defined('DESARROLLO') && DESARROLLO === true) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
        echo "<strong>Error de Desarrollo:</strong> $mensaje<br>";
        if ($archivo) echo "<strong>Archivo:</strong> $archivo<br>";
        if ($linea) echo "<strong>Línea:</strong> $linea<br>";
        echo "</div>";
    }
    error_log("ERROR DESARROLLO: $mensaje" . ($archivo ? " en $archivo" : "") . ($linea ? " línea $linea" : ""));
}

// Definir constantes si no existen
if (!defined('DESARROLLO')) {
    define('DESARROLLO', true); // Cambiar a false en producción
}

if (!defined('BASE_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = str_replace('/autoload.php', '', $_SERVER['REQUEST_URI'] ?? '/TECH-HOME');
    define('BASE_URL', $protocol . '://' . $host . $path);
}

// Mostrar información de debug si se solicita
if (isset($_GET['autoload_debug']) && DESARROLLO) {
    echo "<div style='background: #e7f3ff; padding: 15px; margin: 10px; border: 1px solid #b3d9ff; border-radius: 5px;'>";
    echo "<h3>🔧 Debug Autoload</h3>";
    echo "<strong>Directorio raíz:</strong> " . TECH_HOME_ROOT . "<br>";
    echo "<strong>Clases cargadas:</strong> " . implode(', ', get_declared_classes()) . "<br>";
    echo "<strong>Archivos incluidos:</strong> " . count(get_included_files()) . "<br>";
    echo "</div>";
}
?>