<?php
/**
 * Tech Home Bolivia - Bootstrap del sistema
 * Inicializa todas las configuraciones necesarias
 */

// Configurar zona horaria
date_default_timezone_set('America/La_Paz');

// Configurar el nivel de errores (desarrollo)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuraciones de seguridad
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en HTTPS
ini_set('session.use_strict_mode', 1);

// Configurar el autoloader
require_once __DIR__ . '/../autoload.php';

// Cargar archivos de configuración en orden
require_once __DIR__ . '/constantes.php';
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/sesion.php';
require_once __DIR__ . '/rutas.php';

// Cargar funciones principales
require_once __DIR__ . '/../includes/funciones.php';
require_once __DIR__ . '/../includes/funciones_auth.php';
require_once __DIR__ . '/../includes/funciones_validacion.php';

// Inicializar sesión de forma segura
Sesion::iniciar();

// Configurar headers de seguridad
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
?>