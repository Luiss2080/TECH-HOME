<?php

use App\Controllers\AuthController;
use Core\Router;
use App\Controllers\HomeController;

// ==================== RUTAS PÚBLICAS ====================
// Ruta de inicio (redirige al dashboard si está autenticado)
Router::get('/', [HomeController::class, 'index'])
    ->middleware('role:administrador')
    ->name('home');

Router::get('/login', [AuthController::class, 'login'])->name('login');
Router::post('/login', [AuthController::class, 'loginForm'])->name('login.loginForm');

Router::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ==================== RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN) ====================

Router::get('/dashboard', [HomeController::class, 'index'])
    ->middleware('role:administrador')
    ->name('dashboard');

Router::get('/reportes', [HomeController::class, 'reportes'])
    ->name('reportes')
    ->middleware('role:administrador');

Router::get('/configuracion', [HomeController::class, 'configuracion'])
    ->name('configuracion')
    ->middleware('role:administrador');

Router::get('/usuarios', [HomeController::class, 'usuarios'])
    ->name('usuarios')
    ->middleware('role:administrador');

Router::get('/usuarios/crear', [HomeController::class, 'crearUsuario'])
    ->name('usuarios.crear')
    ->middleware('role:administrador');

Router::get('/ventas', [HomeController::class, 'ventas'])
    ->name('ventas')
    ->middleware('role:administrador');

Router::get('/ventas/crear', [HomeController::class, 'crearVenta'])
    ->name('ventas.crear')
    ->middleware('role:administrador');

Router::get('/estudiantes', [HomeController::class, 'estudiantes'])
    ->name('estudiantes')
    ->middleware('role:administrador,docente');

Router::get('/cursos', [HomeController::class, 'cursos'])
    ->name('cursos')
    ->middleware('role:administrador,docente');

Router::get('/cursos/crear', [HomeController::class, 'crearCurso'])
    ->name('cursos.crear')
    ->middleware('role:administrador,docente');

Router::get('/libros', [HomeController::class, 'libros'])
    ->name('libros')
    ->middleware('role:administrador,docente');

Router::get('/libros/crear', [HomeController::class, 'crearLibro'])
    ->name('libros.crear')
    ->middleware('role:administrador,docente');

Router::get('/materiales', [HomeController::class, 'materiales'])
    ->name('materiales')
    ->middleware('role:administrador,docente');

Router::get('/laboratorios', [HomeController::class, 'laboratorios'])
    ->name('laboratorios')
    ->middleware('role:administrador,docente');

Router::get('/aula-virtual', [HomeController::class, 'aulaVirtual'])
    ->name('aulaVirtual')
    ->middleware('role:administrador,docente');

Router::get('/evaluaciones', [HomeController::class, 'evaluaciones'])
    ->name('evaluaciones')
    ->middleware('role:administrador,docente');

Router::get('/certificados', [HomeController::class, 'certificados'])
    ->name('certificados')
    ->middleware('role:administrador,docente');

Router::get('/componentes', [HomeController::class, 'componentes'])
    ->name('componentes')
    ->middleware('role:administrador,docente');

Router::get('/componentes/crear', [HomeController::class, 'crearComponente'])
    ->name('componentes.crear')
    ->middleware('role:administrador,docente');
