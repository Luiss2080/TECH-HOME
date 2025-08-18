<?php
require_once 'bootstrap.php';

$_ENV = loadEnv(__DIR__ . DIRECTORY_SEPARATOR . '.env');

use Core\Router;
use Core\Request;
// Cargar rutas desde la carpeta /routes
$request = new Request();
Router::loadRoutes(BASE_PATH . 'routes');

// Obtener la ruta solicitada
$requestedRoute = strtok($_SERVER['REQUEST_URI'], '?'); // Elimina query params
// Despachar la ruta solicitada
$response = Router::dispatch($request);
$response->send();
