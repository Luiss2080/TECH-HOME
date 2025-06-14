<?php
/**
 * Constantes del Sistema Tech Home Bolivia
 * Configuración de constantes globales del instituto
 */

// Prevenir acceso directo
if (!defined('TECH_HOME_INIT')) {
    die('Acceso directo no permitido');
}

// ============================================================================
// INFORMACIÓN BÁSICA DEL INSTITUTO
// ============================================================================

// Información institucional
define('INSTITUTO_NOMBRE', 'Tech Home Bolivia');
define('INSTITUTO_NOMBRE_COMPLETO', 'Tech Home Bolivia – Escuela de Robótica y Tecnología Avanzada');
define('INSTITUTO_SLOGAN', 'Formando el futuro tecnológico de Bolivia');
define('INSTITUTO_EMAIL', 'contacto@techhome.bo');
define('INSTITUTO_TELEFONO', '+591 3 123 4567');
define('INSTITUTO_DIRECCION', 'Santa Cruz de la Sierra, Bolivia');
define('INSTITUTO_WEBSITE', 'https://techhome.bo');

// Información legal
define('INSTITUTO_NIT', '1234567890');
define('INSTITUTO_LICENCIA', 'Licencia Educativa #TH-2025-001');

// ============================================================================
// CONFIGURACIÓN DE URLS Y RUTAS
// ============================================================================

// Detectar protocolo y host automáticamente
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// URL base del proyecto
if (!defined('BASE_URL')) {
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

// URLs específicas
define('ASSETS_URL', BASE_URL . '/publico');
define('CSS_URL', ASSETS_URL . '/css');
define('JS_URL', ASSETS_URL . '/js');
define('IMAGES_URL', ASSETS_URL . '/imagenes');
define('UPLOADS_URL', BASE_URL . '/uploads');

// Rutas del sistema de archivos
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('ASSETS_PATH', ROOT_PATH . '/publico');
define('VIEWS_PATH', ROOT_PATH . '/vistas');

// ============================================================================
// CONFIGURACIÓN DE BASE DE DATOS
// ============================================================================

// Configuración de conexión (estas se pueden sobrescribir via variables de entorno)
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'tech_home');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', 'utf8mb4');

// Configuración adicional de BD
define('DB_TABLE_PREFIX', '');
define('DB_TIMEOUT', 30);

// ============================================================================
// CONFIGURACIÓN DE SESIONES Y SEGURIDAD
// ============================================================================

// Configuración de sesiones
define('SESSION_NAME', 'tech_home_session');
define('SESSION_LIFETIME', 3600); // 1 hora
define('SESSION_COOKIE_SECURE', !DESARROLLO); // Solo HTTPS en producción
define('SESSION_COOKIE_HTTPONLY', true);
define('SESSION_COOKIE_SAMESITE', 'Strict');

// Configuración de seguridad
define('PASSWORD_MIN_LENGTH', 6);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutos
define('CSRF_TOKEN_LENGTH', 32);

// Configuración específica para invitados
define('INVITADO_DIAS_ACCESO', 3);
define('INVITADO_NOTIFICACION_DIARIA', true);

// ============================================================================
// CONFIGURACIÓN DE ARCHIVOS Y UPLOADS
// ============================================================================

// Tamaños máximos de archivos (en bytes)
define('MAX_FILE_SIZE', 52428800); // 50MB
define('MAX_IMAGE_SIZE', 10485760); // 10MB
define('MAX_PDF_SIZE', 104857600); // 100MB

// Tipos de archivos permitidos
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_DOCUMENT_TYPES', ['pdf', 'doc', 'docx', 'txt']);
define('ALLOWED_VIDEO_TYPES', ['mp4', 'avi', 'mov', 'wmv']);

// Directorios de upload
define('UPLOAD_CURSOS', UPLOADS_PATH . '/cursos');
define('UPLOAD_LIBROS', UPLOADS_PATH . '/libros');
define('UPLOAD_COMPONENTES', UPLOADS_PATH . '/componentes');
define('UPLOAD_AVATARS', UPLOADS_PATH . '/avatars');
define('UPLOAD_DOCUMENTOS', UPLOADS_PATH . '/documentos');

// ============================================================================
// CONFIGURACIÓN ACADÉMICA
// ============================================================================

// Niveles de cursos
define('NIVELES_CURSO', [
    'Principiante' => 'Principiante',
    'Intermedio' => 'Intermedio', 
    'Avanzado' => 'Avanzado'
]);

// Estados de cursos
define('ESTADOS_CURSO', [
    'Borrador' => 'Borrador',
    'Publicado' => 'Publicado',
    'Archivado' => 'Archivado'
]);

// Tipos de contenido
define('TIPOS_CONTENIDO', [
    'video' => 'Video',
    'texto' => 'Texto',
    'pdf' => 'PDF',
    'enlace' => 'Enlace externo'
]);

// ============================================================================
// CONFIGURACIÓN DE VENTAS Y COMERCIO
// ============================================================================

// Moneda y formato
define('MONEDA_CODIGO', 'BOB');
define('MONEDA_SIMBOLO', 'Bs');
define('MONEDA_FORMATO', '%s %01.2f'); // Ejemplo: Bs 150.00

// Impuestos y descuentos
define('IVA_PORCENTAJE', 13.0);
define('DESCUENTO_MAXIMO', 20.0);
define('DESCUENTO_ESTUDIANTE', 10.0);

// Estados de ventas
define('ESTADOS_VENTA', [
    'Pendiente' => 'Pendiente',
    'Completada' => 'Completada',
    'Cancelada' => 'Cancelada',
    'Reembolsada' => 'Reembolsada'
]);

// Tipos de pago
define('TIPOS_PAGO', [
    'Efectivo' => 'Efectivo',
    'Transferencia' => 'Transferencia Bancaria',
    'Tarjeta' => 'Tarjeta de Crédito/Débito',
    'QR' => 'Código QR'
]);

// ============================================================================
// CONFIGURACIÓN DE ROLES Y PERMISOS
// ============================================================================

// Roles del sistema
define('ROLES_SISTEMA', [
    'Administrador' => 'Administrador',
    'Docente' => 'Docente',
    'Estudiante' => 'Estudiante',
    'Invitado' => 'Invitado',
    'Vendedor' => 'Vendedor'
]);

// Permisos por módulo
define('PERMISOS_MODULOS', [
    'usuarios' => ['Administrador'],
    'cursos' => ['Administrador', 'Docente'],
    'libros' => ['Administrador', 'Vendedor'],
    'componentes' => ['Administrador', 'Vendedor'],
    'ventas' => ['Administrador', 'Vendedor'],
    'reportes' => ['Administrador', 'Docente', 'Vendedor'],
    'dashboard' => ['Administrador', 'Docente', 'Vendedor', 'Estudiante']
]);

// ============================================================================
// CONFIGURACIÓN DE PAGINACIÓN Y LÍMITES
// ============================================================================

// Elementos por página
define('PAGINACION_USUARIOS', 12);
define('PAGINACION_CURSOS', 9);
define('PAGINACION_LIBROS', 12);
define('PAGINACION_COMPONENTES', 15);
define('PAGINACION_VENTAS', 20);
define('PAGINACION_REPORTES', 25);

// Límites de sistema
define('MAX_SESIONES_USUARIO', 3);
define('MAX_DESCARGAS_DIA', 50);
define('MAX_INTENTOS_LOGIN_IP', 10);

// ============================================================================
// CONFIGURACIÓN DE NOTIFICACIONES Y COMUNICACIÓN
// ============================================================================

// Configuración de email (para futuras implementaciones)
define('EMAIL_FROM', INSTITUTO_EMAIL);
define('EMAIL_FROM_NAME', INSTITUTO_NOMBRE);
define('EMAIL_ADMIN', 'admin@techhome.bo');

// Plantillas de notificación
define('PLANTILLAS_EMAIL', [
    'bienvenida' => 'Bienvenido a ' . INSTITUTO_NOMBRE,
    'vencimiento_invitado' => 'Tu acceso como invitado está por vencer',
    'nueva_venta' => 'Nueva venta registrada',
    'curso_completado' => 'Has completado un curso'
]);

// ============================================================================
// CONFIGURACIÓN DE LOGS Y DEBUGGING
// ============================================================================

// Configuración de logs
define('LOG_LEVEL', DESARROLLO ? 'DEBUG' : 'ERROR');
define('LOG_PATH', ROOT_PATH . '/logs');
define('LOG_MAX_SIZE', 10485760); // 10MB
define('LOG_MAX_FILES', 5);

// Tipos de log
define('LOG_TYPES', [
    'error' => 'error.log',
    'access' => 'access.log',
    'security' => 'security.log',
    'system' => 'system.log'
]);

// ============================================================================
// CONFIGURACIÓN DE CACHE Y RENDIMIENTO
// ============================================================================

// Configuración de cache
define('CACHE_ENABLED', !DESARROLLO);
define('CACHE_LIFETIME', 3600); // 1 hora
define('CACHE_PATH', ROOT_PATH . '/cache');

// Configuración de compresión
define('GZIP_ENABLED', true);
define('MINIFY_HTML', !DESARROLLO);
define('MINIFY_CSS', !DESARROLLO);
define('MINIFY_JS', !DESARROLLO);

// ============================================================================
// CONFIGURACIÓN DE API Y SERVICIOS EXTERNOS
// ============================================================================

// APIs externas (para futuras integraciones)
define('API_RATE_LIMIT', 100); // requests por hora
define('API_TIMEOUT', 30); // segundos

// Configuración de redes sociales
define('REDES_SOCIALES', [
    'facebook' => 'https://facebook.com/techhomebolivia',
    'instagram' => 'https://instagram.com/techhomebolivia',
    'tiktok' => 'https://tiktok.com/@techhomebolivia',
    'whatsapp' => 'https://wa.me/59173123456'
]);

// ============================================================================
// CONFIGURACIÓN DE VERSIÓN Y MANTENIMIENTO
// ============================================================================

// Información de versión
define('SISTEMA_VERSION', '2.0.0');
define('SISTEMA_BUILD', '20250611');
define('SISTEMA_NOMBRE', 'Tech Home Management System');

// Estado del sistema
define('MANTENIMIENTO_ACTIVO', false);
define('REGISTRO_PUBLICO_HABILITADO', true);
define('BIBLIOTECA_PUBLICA', true);

// ============================================================================
// VALIDACIÓN DE CONSTANTES CRÍTICAS
// ============================================================================

// Verificar que las constantes críticas estén definidas
$constantesCriticas = [
    'BASE_URL', 'ROOT_PATH', 'DB_NAME', 'SESSION_NAME'
];

foreach ($constantesCriticas as $constante) {
    if (!defined($constante)) {
        error_log("CONSTANTES: Constante crítica '$constante' no está definida");
        
        if (DESARROLLO) {
            die("Error crítico: Constante '$constante' no definida");
        }
    }
}

// ============================================================================
// FUNCIÓN HELPER PARA OBTENER CONSTANTES
// ============================================================================

/**
 * Obtener valor de constante con valor por defecto
 */
function obtenerConstante($nombre, $defecto = null) {
    return defined($nombre) ? constant($nombre) : $defecto;
}

/**
 * Verificar si una constante existe y no está vacía
 */
function constanteValida($nombre) {
    return defined($nombre) && !empty(constant($nombre));
}
?>