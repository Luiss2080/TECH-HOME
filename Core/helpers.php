<?php

/**
 * Helpers esenciales para el sistema TECH-HOME
 * Solo funciones que se usan activamente en el sistema
 */

// ==================== FUNCIONES DE RUTAS Y ASSETS ====================

function asset($path)
{
    $baseUrl = getBaseUrl() . BASE_URL . '/public/';
    $path = ltrim($path, '/');
    return $baseUrl . $path;
}

function url($path = '')
{
    return getBaseUrl() . BASE_URL . '/public/' . ltrim($path, '/');
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

function route(string $name, array $parameters = []): string
{
    return \Core\Router::route($name, $parameters);
}

// ==================== FUNCIONES DE SEGURIDAD ====================

function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function CSRF()
{
    echo '<input type="hidden" name="_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

// ==================== FUNCIONES DE AUTENTICACIÓN ====================

function auth(): \App\Models\User|null
{
    $user = \Core\Session::get('user');
    
    // Si no hay usuario en sesión, devolver null
    if ($user === null) {
        return null;
    }
    
    // Si el usuario es un objeto User válido, devolverlo
    if ($user instanceof \App\Models\User) {
        return $user;
    }
    
    // Si el usuario es un objeto incompleto o cualquier otra cosa, intentar recargarlo
    $userId = \Core\Session::get('user_id');
    if ($userId) {
        try {
            $freshUser = \App\Models\User::find($userId);
            if ($freshUser) {
                // Actualizar la sesión con el objeto completo
                \Core\Session::set('user', $freshUser);
                return $freshUser;
            }
        } catch (\Exception $e) {
            // Si hay error al cargar el usuario, limpiar la sesión
        }
    }
    
    // Si no se puede recuperar, limpiar la sesión
    \Core\Session::remove('user');
    \Core\Session::remove('user_id');
    return null;
}

function isAuth()
{
    return auth() !== null;
}

// ==================== FUNCIONES DE SESIÓN Y FLASH ====================

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

function clearFlash()
{
    unset($_SESSION['_flash']);
}

// ==================== FUNCIONES DE VISTAS Y RESPUESTAS ====================

function view($view, $data = [], $layout = 'layouts/app', $statusCode = 200): \Core\Response
{
    extract($data);

    $viewPath = str_replace('.', DIRECTORY_SEPARATOR, $view);
    $path = BASE_PATH . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $viewPath . '.view.php';

    if (!file_exists($path)) {
        throw new \Exception("Vista '{$view}' no encontrada en '{$path}'");
    }

    ob_start();
    $errors = flashGet('errors') ?? [];
    $old = flashGet('old') ?? [];
    require $path;
    $content = ob_get_clean();

    if ($layout === false) {
        clearFlash();
        return new \Core\Response($content, $statusCode);
    }

    $layoutPath = BASE_PATH . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $layout) . '.view.php';

    if (file_exists($layoutPath)) {
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

function response()
{
    return new class {
        public function json($data, $status = 200)
        {
            return \Core\Response::json($data, $status);
        }
    };
}

// ==================== FUNCIONES UTILITARIAS ====================

function request(): \Core\Request
{
    return \Core\Request::getInstance();
}

function Dashboard()
{
    return \App\Models\User::Dashboard();
}

function mailService()
{
    return \App\Services\MailServiceFactory::create();
}

function loadEnv($path)
{
    if (!file_exists($path)) {
        throw new Exception("El archivo .env no existe en la ruta: $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $env = [];

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Procesar valores con comillas
        if (!empty($value) && strlen($value) >= 2) {
            if (($value[0] === '"' && $value[strlen($value) - 1] === '"') || 
                ($value[0] === "'" && $value[strlen($value) - 1] === "'")) {
                $value = substr($value, 1, -1);
            }
        }
        
        $env[$key] = $value;
    }

    return $env;
}

if (!function_exists('class_basename')) {
    function class_basename($class)
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        return basename(str_replace('\\', '/', $class));
    }
}

// ==================== FUNCIONES DE FORMATO (USADAS EN VISTAS) ====================

function formatearNumero($numero, $decimales = 0): string
{
    return number_format($numero, $decimales, '.', ',');
}

function formatearMoneda($monto): string
{
    return 'Bs. ' . formatearNumero($monto, 2);
}

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

function formatearBytes($bytes, $precision = 2): string
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, $precision) . ' ' . $units[$i];
}

// ==================== FUNCIONES AUXILIARES ESPECÍFICAS ====================

if (!function_exists('colorEstado')) {
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

        foreach ($iconos as $key => $icono) {
            if (strpos($tipoLower, $key) !== false) {
                return $icono;
            }
        }

        return 'file-alt';
    }
}

if (!function_exists('calcularPorcentaje')) {
    function calcularPorcentaje($actual, $total)
    {
        if ($total == 0) return 0;
        return min(100, round(($actual / $total) * 100, 1));
    }
}

if (!function_exists('formatearTiempo')) {
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

if (!function_exists('generarCodigoProducto')) {
    function generarCodigoProducto($categoriaNombre, $numero = null)
    {
        $prefijo = strtoupper(substr($categoriaNombre, 0, 3));
        $numeroFinal = $numero ?? rand(1000, 9999);
        return $prefijo . '-' . str_pad($numeroFinal, 4, '0', STR_PAD_LEFT);
    }
}
