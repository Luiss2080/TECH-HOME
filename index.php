<?php
header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: X-Mi-Token, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, Cache-Control');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE');

// Respuesta para la solicitud OPTIONS (preflight)
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    exit();
}
require_once __DIR__ . '/Core/configuration.php';
require_once __DIR__ . '/Core/Router.php';
$_ENV = loadEnv(__DIR__ . DIRECTORY_SEPARATOR . '.env');

use Core\Router;
use Core\Request;
// Cargar rutas desde la carpeta /routes
$request = new Request();
Router::loadRoutes(BASE_ROUTE . 'routes');

// Obtener la ruta solicitada
$requestedRoute = strtok($_SERVER['REQUEST_URI'], '?'); // Elimina query params
// Despachar la ruta solicitada
$response = Router::dispatch($request);
$response->send();
