<?php

use Core\Router;
use App\Controllers\HomeController;


Router::get('/', [HomeController::class, 'index'])->name('home');