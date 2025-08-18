<?php
require_once 'bootstrap.php';

// dd(password_hash('TechHome2025', PASSWORD_DEFAULT)); // tu token para nuestros PC
$_ENV = loadEnv(BASE_PATH . '.env');

use Core\Request;
use Core\Router;

$request = Request::getInstance();
Router::loadRoutes(BASE_PATH . 'routes');
// Despachar la solicitud
$response = Router::dispatch($request);
$response->send();

