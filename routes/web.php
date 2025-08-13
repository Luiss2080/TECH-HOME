<?php

use App\Controllers\AuthController;
use Core\Router;
use App\Controllers\HomeController;



Router::get('/', [HomeController::class, 'index'])->name('home');
Router::get('/login', [AuthController::class, 'login'])->name('login');
Router::post('/login', [AuthController::class, 'loginForm'])->name('login.loginForm');

// Dashboard
Router::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
// Reportes
Router::get('/reportes', [HomeController::class, 'reportes'])->name('reportes');
// Configuración
Router::get('/configuracion', [HomeController::class, 'configuracion'])->name('configuracion');
// Estudiantes
Router::get('/estudiantes', [HomeController::class, 'estudiantes'])->name('estudiantes');
// Cursos
Router::get('/cursos', [HomeController::class, 'cursos'])->name('cursos');
// Usuarios
Router::get('/usuarios', [HomeController::class, 'usuarios'])->name('usuarios');
// Biblioteca
Router::get('/libros', [HomeController::class, 'libros'])->name('libros');
// Materiales
Router::get('/materiales', [HomeController::class, 'materiales'])->name('materiales');
// Laboratorios
Router::get('/laboratorios', [HomeController::class, 'laboratorios'])->name('laboratorios');
// Aula Virtual
Router::get('/aula-virtual', [HomeController::class, 'aulaVirtual'])->name('aulaVirtual');
// Evaluaciones
Router::get('/evaluaciones', [HomeController::class, 'evaluaciones'])->name('evaluaciones');
// Certificados
Router::get('/certificados', [HomeController::class, 'certificados'])->name('certificados');
// Componentes
Router::get('/componentes', [HomeController::class, 'componentes'])->name('componentes');
// Ventas
Router::get('/ventas', [HomeController::class, 'ventas'])->name('ventas');

// Crear usuario
Router::get('/usuarios/crear', [HomeController::class, 'crearUsuario'])->name('usuarios.crear');
// Crear curso
Router::get('/cursos/crear', [HomeController::class, 'crearCurso'])->name('cursos.crear');
// Crear componente
Router::get('/componentes/crear', [HomeController::class, 'crearComponente'])->name('componentes.crear');
// Crear venta
Router::get('/ventas/crear', [HomeController::class, 'crearVenta'])->name('ventas.crear');
// Crear libro
Router::get('/libros/crear', [HomeController::class, 'crearLibro'])->name('libros.crear');
