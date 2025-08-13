<?php
define('BASE_ROUTE', dirname(__DIR__) . DIRECTORY_SEPARATOR);
require_once BASE_ROUTE . 'autoload.php';
// Cargar funciones y configuraciones principales
require_once BASE_ROUTE . 'functions.php';
require_once 'helpers.php';

// Cargar todas las clases del directorio `App/Modelo`
foreach (glob(BASE_ROUTE . 'App' . DIRECTORY_SEPARATOR . 'Modelo' . DIRECTORY_SEPARATOR . '*.php') as $modelFile) {
    require_once $modelFile;
}

// Cargar todas las clases del directorio `App/Controller`
foreach (glob(BASE_ROUTE . 'App' . DIRECTORY_SEPARATOR . 'Controller' . DIRECTORY_SEPARATOR . '*.php') as $controllerFile) {
    require_once $controllerFile;
}
// Cargar todas las clases de directorio `App/Services`
foreach (glob(BASE_ROUTE . 'App' . DIRECTORY_SEPARATOR . 'Services' . DIRECTORY_SEPARATOR . '*.php') as $serviceFile) {
    require_once $serviceFile;
}
// Cargar clases del núcleo (Core)
require_once __DIR__ . DIRECTORY_SEPARATOR . 'Router.php';
require_once BASE_ROUTE . 'Core' . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'RouteNotFoundException.php';
