<?php
define('BASE_URL', "/TECH-HOME");
define('API_PREFIX', '/api');

/**
 * @param mixed $value Valor a imprimir
 * Imprime el valor de la variable y termina la ejecución del script
 * @return void
 */
function dd(...$value)
{
    // código respuesta HTTP 400
    http_response_code(400);
    echo "<pre>", print_r($value, true), "</pre>";
    //echo json_encode($value);
    die();
}


function route(string $name, $parameters = []): string
{
    try {
        $path = \Core\Router::route($name, $parameters) ?? '/';
        foreach ($parameters as $key => $value) {
            $path = str_replace("{{$key}}", $value, $path);
        }
        return BASE_URL . $path;
    } catch (Exception $e) {
        return '#';
    }
}

function asset($path)
{
    $baseUrl = getBaseUrl() . BASE_URL . '/public/';
    // Elimina la barra inicial si existe en el path
    $path = ltrim($path, '/');

    // Retorna la ruta completa al recurso
    return $baseUrl . $path;
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
