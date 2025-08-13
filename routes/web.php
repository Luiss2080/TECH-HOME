<?php

use App\Controllers\AuthController;
use Core\Router;
use App\Controllers\HomeController;


Router::get('/', [HomeController::class, 'index'])->name('home');
Router::get('/login', [AuthController::class, 'login'])->name('login');
Router::post('/login', [AuthController::class, 'loginForm'])->name('login.loginForm');
