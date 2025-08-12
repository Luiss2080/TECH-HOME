<?php

spl_autoload_register(function ($class) {
    $classPath = BASE_ROUTE . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    if (file_exists($classPath)) {
        require_once $classPath;
    }
});
