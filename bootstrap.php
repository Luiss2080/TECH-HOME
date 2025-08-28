<?php

// Definición de constantes
define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('BASE_URL', '/TECH-HOME');
define('API_PREFIX', '/api');
define('DEBUG_MODE', true);

// Configuracion de PHP
ini_set('display_errors', DEBUG_MODE);
error_reporting(E_ALL);
date_default_timezone_set('America/La_Paz');

// Cargar todas las clases del directorio Core `Core/`
foreach (glob(BASE_PATH  . 'Core' . DIRECTORY_SEPARATOR . '*.php') as $coreFile) {
    require_once $coreFile;
}

// Cargar variables de entorno del archivo .env
if (file_exists(BASE_PATH . '.env')) {
    $envVars = loadEnv(BASE_PATH . '.env');
    foreach ($envVars as $key => $value) {
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

// Cargar todas las clases del directorio Core `Core/`
foreach (glob(BASE_PATH  . 'Core' . DIRECTORY_SEPARATOR . '*.php') as $coreFile) {
    require_once $coreFile;
}
require BASE_PATH . 'resources' . DIRECTORY_SEPARATOR . 'PHPMailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Exception.php';
require BASE_PATH . 'resources' . DIRECTORY_SEPARATOR . 'PHPMailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'PHPMailer.php';
require BASE_PATH . 'resources' . DIRECTORY_SEPARATOR . 'PHPMailer' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'SMTP.php';
// Función de autocarga para clases con namespace
spl_autoload_register(function ($className) {
    // Convertir el namespace a ruta de archivo
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $filePath = BASE_PATH . $classPath . '.php';

    if (file_exists($filePath)) {
        require_once $filePath;
    }
});
