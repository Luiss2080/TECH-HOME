<?php
require_once 'bootstrap.php';

$_ENV = loadEnv(BASE_PATH . '.env');

use Core\Request;
use Core\Router;

$request = Request::getInstance();
Router::loadRoutes(BASE_PATH . 'routes');
// Despachar la solicitud
$response = Router::dispatch($request);
$response->send();
