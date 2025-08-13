<?php

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
 */
function view($view, $data = [], $layout = 'layouts/app', $statusCode = 200): \Core\Response
{
    // Extraer los datos para que estén disponibles en la vista
    extract($data);

    // Convertir la ruta de la vista a un path de archivo
    $viewPath = str_replace('.', DIRECTORY_SEPARATOR, $view);
    $path = BASE_ROUTE . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $viewPath . '.view.php';

    // Verificar si la vista existe
    if (!file_exists($path)) {
        throw new \Exception("Vista '{$view}' no encontrada en '{$path}'");
    }

    // Capturar el contenido de la vista en un buffer
    ob_start(); // Iniciar el buffer
    $errors = flashGet('errors') ?? [];
    $old = flashGet('old') ?? [];
    clearFlash();
    require $path;
    $content = ob_get_clean();

    // Si no se usa layout, devolver el contenido directamente
    if ($layout === false) {
        return new \Core\Response($content, $statusCode);
    }

    // Si se especifica un layout, usarlo
    $layoutPath = BASE_ROUTE . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . str_replace('.', DIRECTORY_SEPARATOR, $layout) . '.view.php';

    // Verificar si el layout existe
    if (file_exists($layoutPath)) {
        // Pasar el contenido de la vista al layout
        $layoutContent = $content;
        ob_start();
        require $layoutPath;
        $finalContent = ob_get_clean();
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
