<?php


use Core\Router;
use App\Controllers\AuthController;
// se debe usar la constante API_PREFIX para definir la ruta de la api
// example
// Router::post(API_PREFIX . '/login', [AuthApiController::class, 'login'])->name('api.auth.login');

// Ruta para verificación de sesión (AJAX)
Router::get(API_PREFIX . '/verify-session', [AuthController::class, 'verify_session'])->name('verify_session');
