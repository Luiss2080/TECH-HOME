<?php


function asset($path)
{
    $baseUrl = getBaseUrl() . BASE_URL . '/public/';
    // Elimina la barra inicial si existe en el path
    $path = ltrim($path, '/');

    // Retorna la ruta completa al recurso
    return $baseUrl . $path;
}
function url($path = '')
{
    return getBaseUrl() . BASE_URL . '/public/' . ltrim($path, '/');
}

function loadEnv($path)
{
    // Verificar si el archivo .env existe
    if (!file_exists($path)) {
        throw new Exception("El archivo .env no existe en la ruta: $path");
    }

    // Leer el archivo línea por línea
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    // Array para almacenar las variables de entorno
    $env = [];

    foreach ($lines as $line) {
        // Ignorar comentarios (líneas que comienzan con #)
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Separar la clave y el valor
        list($key, $value) = explode('=', $line, 2);

        // Limpiar la clave y el valor
        $key = trim($key);
        $value = trim($value);

        // Almacenar en el array de entorno
        $env[$key] = $value;
    }

    return $env;
}
function currentUrl(): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];
    return $protocol . $host . $uri;
}
function getBaseUrl(): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . $host;
}

// Generar CSRF Token
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verificar CSRF Token
function csrf_verify($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function CSRF()
{
    echo '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

// auth()
function auth(): \App\Models\User|null
{
    return \Core\Session::get('user');
}
function isAuth()
{
    return auth() !== null;
}

function flashGet($key)
{
    if (!\Core\Session::hasFlash($key)) {
        return null;
    }
    return \Core\Session::flashGet($key);
}

function old($key, $default = '')
{
    $oldData = flashGet('old') ?? [];
    return $oldData[$key] ?? $default;
}

function session($key = null, $default = null)
{
    if ($key === null) {
        return $_SESSION;
    }

    // Si es una clave flash
    if (\Core\Session::hasFlash($key)) {
        return \Core\Session::flashGet($key);
    }

    return $_SESSION[$key] ?? $default;
}

function clearFlash()
{
    unset($_SESSION['_flash']);
}
if (!function_exists('class_basename')) {
    function class_basename($class)
    {
        // Si el parámetro es un objeto, obtén su clase
        if (is_object($class)) {
            $class = get_class($class);
        }

        // Devuelve el nombre de la clase sin el namespace
        return basename(str_replace('\\', '/', $class));
    }
}
function back(): string
{
    return $_SERVER['HTTP_REFERER'] ?? BASE_URL;
}

/**
 * Retorna una vista
 * 
 */
function view($view,  $data = [], $layout = 'layouts/app',  $statusCode = 200): \Core\Response
{
    // Extraer los datos para que estén disponibles en la vista
    extract($data);

    // Convertir la ruta de la vista a un path de archivo
    $viewPath = str_replace('.', DIRECTORY_SEPARATOR, $view);
    $path = BASE_PATH . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $viewPath . '.view.php';

    // Verificar si la vista existe
    if (!file_exists($path)) {
        throw new \Exception("Vista '{$view}' no encontrada en '{$path}'");
    }
    // Capturar el contenido de la vista en un buffer
    ob_start(); // Iniciar el buffer
    $errors = flashGet('errors') ?? [];
    $old = flashGet('old') ?? [];
    require $path;
    $content = ob_get_clean();

    // Si no se usa layout, devolver el contenido directamente
    if ($layout === false) {
        clearFlash();
        return new \Core\Response($content, $statusCode);
    }

    // Si se especifica un layout, usarlo
    $layoutPath = BASE_PATH . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $layout) . '.view.php';

    // Verificar si el layout existe
    if (file_exists($layoutPath)) {
        // Pasar el contenido de la vista al layout
        $layoutContent = $content;
        ob_start();
        require $layoutPath;
        $finalContent = ob_get_clean();
        clearFlash();
        return new \Core\Response($finalContent, $statusCode);
    }
    throw new \Exception("Layout '{$layout}' no encontrado");
}

function redirect($url): \Core\Response
{
    return (new \Core\Response())->redirect($url);
}

/**
 * Formatear número con separadores de miles
 */
function formatearNumero($numero, $decimales = 0): string
{
    return number_format($numero, $decimales, '.', ',');
}

/**
 * Formatear moneda boliviana
 */
function formatearMoneda($monto): string
{
    return 'Bs. ' . formatearNumero($monto, 2);
}

/**
 * Formatear tiempo transcurrido en formato humano
 */
function tiempoTranscurrido($fecha): string
{
    $ahora = new DateTime();
    $tiempo = new DateTime($fecha);
    $diff = $ahora->diff($tiempo);

    if ($diff->y > 0) return $diff->y . ' años';
    if ($diff->m > 0) return $diff->m . ' meses';
    if ($diff->d > 0) return $diff->d . ' días';
    if ($diff->h > 0) return $diff->h . ' horas';
    if ($diff->i > 0) return $diff->i . ' minutos';
    return 'ahora';
}

/**
 * Formatear bytes en formato humano
 */
function formatearBytes($bytes, $precision = 2): string
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

/**
 * Crear una respuesta HTTP (helper para JSON)
 */
function response()
{
    return new class {
        public function json($data, $status = 200)
        {
            return \Core\Response::json($data, $status);
        }
    };
}
/**
 * Retorna la ruta de string
 */
function route(string $name, array $parameters = []): string
{
    return \Core\Router::route($name, $parameters);
}
/**
 * Dump y Die 
 * @param mixed $value
 * @return never
 */
function dd(...$value)
{
    // código respuesta HTTP 400
    http_response_code(400);
    echo "<pre>", print_r($value, true), "</pre>";
    //echo json_encode($value);
    die();
}
function request(): \Core\Request
{
    return \Core\Request::getInstance();
}
function Dashboard()
{
    return \App\Models\User::Dashboard();
}

/**
 * Helper para crear instancia del servicio de email
 */
function mailService()
{
    return \App\Services\MailServiceFactory::create();
}


















// ==================== FUNCIONES AUXILIARES ADICIONALES ====================
// Agregar estas funciones al final del archivo helpers.php existente

if (!function_exists('obtenerIniciales')) {
    /**
     * Obtiene las iniciales de un nombre completo
     */
    function obtenerIniciales($nombre)
    {
        if (empty($nombre)) {
            return 'NN';
        }

        $palabras = explode(' ', trim($nombre));
        $iniciales = '';

        foreach ($palabras as $palabra) {
            if (!empty($palabra)) {
                $iniciales .= strtoupper(substr($palabra, 0, 1));
            }
            if (strlen($iniciales) >= 2) {
                break;
            }
        }

        return $iniciales ?: 'NN';
    }
}

if (!function_exists('colorEstado')) {
    /**
     * Retorna color CSS según el estado
     */
    function colorEstado($estado)
    {
        $colores = [
            'activo' => '#10b981',
            'inactivo' => '#6b7280',
            'pendiente' => '#f59e0b',
            'completado' => '#10b981',
            'publicado' => '#10b981',
            'borrador' => '#6b7280',
            'disponible' => '#10b981',
            'stock_bajo' => '#f59e0b',
            'agotado' => '#ef4444'
        ];

        return $colores[strtolower($estado)] ?? '#6b7280';
    }
}

if (!function_exists('tipoMaterialIcono')) {
    /**
     * Retorna icono Font Awesome según el tipo de material
     */
    function tipoMaterialIcono($tipo)
    {
        $iconos = [
            'pdf' => 'file-pdf',
            'documento' => 'file-alt',
            'video' => 'file-video',
            'imagen' => 'file-image',
            'codigo' => 'file-code',
            'arduino' => 'file-code',
            'python' => 'file-code',
            'zip' => 'file-archive',
            'link' => 'link'
        ];

        $tipoLower = strtolower($tipo);

        // Buscar por coincidencia parcial
        foreach ($iconos as $key => $icono) {
            if (strpos($tipoLower, $key) !== false) {
                return $icono;
            }
        }

        return 'file-alt'; // Icono por defecto
    }
}

if (!function_exists('porcentajeProgreso')) {
    /**
     * Calcula porcentaje con dos valores
     */
    function porcentajeProgreso($actual, $total)
    {
        if ($total == 0) {
            return 0;
        }

        return min(100, round(($actual / $total) * 100, 1));
    }
}











// ==================== FUNCIONES AUXILIARES ESENCIALES PARA ESTUDIANTE ====================

if (!function_exists('calcularPorcentaje')) {
    /**
     * Calcula porcentaje entre dos valores
     */
    function calcularPorcentaje($actual, $total)
    {
        if ($total == 0) return 0;
        return min(100, round(($actual / $total) * 100, 1));
    }
}

if (!function_exists('formatearTiempo')) {
    /**
     * Convierte minutos a formato legible
     */
    function formatearTiempo($minutos)
    {
        if (!is_numeric($minutos) || $minutos <= 0) return '0min';

        $horas = floor($minutos / 60);
        $minutosRestantes = $minutos % 60;

        if ($horas > 0) {
            return $minutosRestantes > 0 ? "{$horas}h {$minutosRestantes}min" : "{$horas}h";
        }
        return "{$minutosRestantes}min";
    }
}

if (!function_exists('estadoCurso')) {
    /**
     * Determina estado del curso (simplificado para videos de YouTube)
     */
    function estadoCurso($estado = 'Borrador')
    {
        switch ($estado) {
            case 'Publicado':
                return [
                    'texto' => 'Publicado',
                    'color' => '#10b981',
                    'clase' => 'publicado'
                ];
            case 'Archivado':
                return [
                    'texto' => 'Archivado',
                    'color' => '#f59e0b',
                    'clase' => 'archivado'
                ];
            default:
                return [
                    'texto' => 'Borrador',
                    'color' => '#6b7280',
                    'clase' => 'borrador'
                ];
        }
    }
}

if (!function_exists('colorProgreso')) {
    /**
     * Color según porcentaje de progreso
     */
    function colorProgreso($porcentaje)
    {
        if ($porcentaje >= 80) return '#10b981';
        if ($porcentaje >= 60) return '#3b82f6';
        if ($porcentaje >= 40) return '#f59e0b';
        return '#ef4444';
    }
}

if (!function_exists('validarAccesoLibro')) {
    /**
     * Valida si el usuario puede acceder a un libro
     */
    function validarAccesoLibro($libro, $esInvitado = false)
    {
        // Libros gratuitos: acceso libre
        if ($libro['es_gratuito']) {
            return ['acceso' => true, 'razon' => null];
        }

        // Usuarios invitados: solo libros gratuitos
        if ($esInvitado) {
            return ['acceso' => false, 'razon' => 'Requiere registro completo'];
        }

        // Sin stock
        if ($libro['stock'] <= 0) {
            return ['acceso' => false, 'razon' => 'Sin stock disponible'];
        }

        return ['acceso' => true, 'razon' => null];
    }
}

// ==================== HELPERS ESPECÍFICOS PARA COMPONENTES ====================

if (!function_exists('formatearPrecioComponente')) {
    /**
     * Formatea precio de componente con moneda
     */
    function formatearPrecioComponente($precio)
    {
        return 'Bs. ' . number_format($precio, 2, '.', ',');
    }
}

if (!function_exists('estadoStockComponente')) {
    /**
     * Determina el estado del stock de un componente
     */
    function estadoStockComponente($stock, $stockMinimo, $stockReservado = 0)
    {
        $stockDisponible = max(0, $stock - $stockReservado);
        
        if ($stockDisponible <= 0) {
            return [
                'estado' => 'Agotado',
                'color' => '#ef4444',
                'icono' => 'exclamation-triangle',
                'clase' => 'agotado'
            ];
        }
        
        if ($stockDisponible <= $stockMinimo) {
            return [
                'estado' => 'Stock Bajo',
                'color' => '#f59e0b',
                'icono' => 'exclamation-circle',
                'clase' => 'stock-bajo'
            ];
        }
        
        return [
            'estado' => 'Disponible',
            'color' => '#10b981',
            'icono' => 'check-circle',
            'clase' => 'disponible'
        ];
    }
}

if (!function_exists('calcularStockDisponible')) {
    /**
     * Calcula el stock disponible descontando reservas
     */
    function calcularStockDisponible($stockTotal, $stockReservado = 0)
    {
        return max(0, $stockTotal - $stockReservado);
    }
}

if (!function_exists('validarDisponibilidadVenta')) {
    /**
     * Valida si se puede vender una cantidad específica de un componente
     */
    function validarDisponibilidadVenta($componente, $cantidad)
    {
        // Si permite venta sin stock
        if ($componente->permite_venta_sin_stock ?? false) {
            return [
                'valido' => true,
                'mensaje' => 'Disponible (pre-orden)',
                'es_preorden' => true
            ];
        }

        $stockDisponible = calcularStockDisponible($componente->stock, $componente->stock_reservado ?? 0);

        if ($stockDisponible >= $cantidad) {
            return [
                'valido' => true,
                'mensaje' => 'Stock disponible',
                'es_preorden' => false
            ];
        }

        return [
            'valido' => false,
            'mensaje' => "Stock insuficiente. Disponible: $stockDisponible",
            'stock_disponible' => $stockDisponible
        ];
    }
}

if (!function_exists('generarCodigoProducto')) {
    /**
     * Genera un código de producto automático
     */
    function generarCodigoProducto($categoriaNombre, $numero = null)
    {
        $prefijo = strtoupper(substr($categoriaNombre, 0, 3));
        $numeroFinal = $numero ?? rand(1000, 9999);
        return $prefijo . '-' . str_pad($numeroFinal, 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('calcularValorInventario')) {
    /**
     * Calcula el valor total de inventario
     */
    function calcularValorInventario($stock, $precio)
    {
        return $stock * $precio;
    }
}

if (!function_exists('alertaStockBajo')) {
    /**
     * Determina si debe mostrar alerta de stock bajo
     */
    function alertaStockBajo($stock, $stockMinimo, $alertaActiva = true)
    {
        return $alertaActiva && $stock <= $stockMinimo;
    }
}

if (!function_exists('formatearMovimientoStock')) {
    /**
     * Formatea un movimiento de stock para mostrar
     */
    function formatearMovimientoStock($movimiento)
    {
        $tipos = [
            'entrada' => ['texto' => 'Entrada', 'color' => '#10b981', 'icono' => 'arrow-up'],
            'salida' => ['texto' => 'Salida', 'color' => '#ef4444', 'icono' => 'arrow-down'],
            'ajuste' => ['texto' => 'Ajuste', 'color' => '#3b82f6', 'icono' => 'edit'],
            'reserva' => ['texto' => 'Reserva', 'color' => '#f59e0b', 'icono' => 'clock'],
            'liberacion' => ['texto' => 'Liberación', 'color' => '#6b7280', 'icono' => 'unlock']
        ];

        return $tipos[$movimiento] ?? ['texto' => 'Desconocido', 'color' => '#6b7280', 'icono' => 'question'];
    }
}

if (!function_exists('tiempoExpiracionReserva')) {
    /**
     * Calcula tiempo restante para expiración de reserva
     */
    function tiempoExpiracionReserva($fechaExpiracion)
    {
        $now = new DateTime();
        $expiracion = new DateTime($fechaExpiracion);
        
        if ($now >= $expiracion) {
            return ['expirado' => true, 'tiempo' => '0min'];
        }
        
        $diff = $now->diff($expiracion);
        
        if ($diff->h > 0) {
            return ['expirado' => false, 'tiempo' => $diff->h . 'h ' . $diff->i . 'min'];
        }
        
        return ['expirado' => false, 'tiempo' => $diff->i . 'min'];
    }
}

// Incluir helpers de permisos adaptados a la estructura existente
require_once __DIR__ . '/permission_helpers_adapted.php';
