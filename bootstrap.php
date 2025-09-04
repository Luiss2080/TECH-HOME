<?php

// Constantes básicas del sistema
define('BASE_PATH', __DIR__ . '/');
define('BASE_URL', '/TECH-HOME');
define('API_PREFIX', '/api');

// Configuración de sesión
session_start();

// Autoload básico para las clases del sistema
spl_autoload_register(function ($class) {
    // Para PHPMailer
    if (strpos($class, 'PHPMailer\\PHPMailer\\') === 0) {
        $className = str_replace('PHPMailer\\PHPMailer\\', '', $class);
        $file = BASE_PATH . 'resources/PHPMailer/src/' . $className . '.php';
        
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
    
    // Para el resto de clases del sistema
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = BASE_PATH . $class . '.php';
    
    if (file_exists($file)) {
        require_once $file;
    }
});

// Cargar helpers
require_once BASE_PATH . 'Core/helpers.php';

// Pre-cargar clases críticas para evitar problemas de deserialización
require_once BASE_PATH . 'App/Models/User.php';
