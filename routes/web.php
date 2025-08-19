<?php

use App\Controllers\AdminController;
use App\Controllers\AuthController;
use App\Controllers\EstudianteController;
use App\Controllers\CursoController;
use App\Controllers\LibroController;
use App\Controllers\MaterialController;
use App\Controllers\LaboratorioController;
use App\Controllers\ComponenteController;
use App\Controllers\DocenteController;
use Core\Router;
use App\Controllers\HomeController;

// ==================== RUTAS PÚBLICAS ====================
// Ruta de inicio (redirige al dashboard si está autenticado)
Router::get('/', [HomeController::class, 'index'])
    ->name('home');

Router::get('/login', [AuthController::class, 'login'])->name('login');
Router::post('/login', [AuthController::class, 'loginForm'])->name('login.loginForm');

Router::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// ==================== RUTAS PROTEGIDAS (REQUIEREN AUTENTICACIÓN) ====================

Router::get('/admin/dashboard', [AdminController::class, 'index'])
    ->middleware('role:administrador|has:admin.dashboard')
    ->name('admin.dashboard');

Router::get('/admin/reportes', [AdminController::class, 'reportes'])
    ->name('reportes')
    ->middleware('role:administrador|has:admin.reportes');

Router::get('/admin/configuracion', [AdminController::class, 'configuracion'])
    ->name('configuracion')
    ->middleware('role:administrador|has:admin.configuracion');

// Rutas para gestión de roles y permisos
Router::get('/admin/configuracion/roles', [AdminController::class, 'roles'])
    ->name('admin.roles')
    ->middleware('role:administrador|has:admin.configuracion');

Router::get('/admin/configuracion/roles/crear', [AdminController::class, 'crearRol'])
    ->name('admin.roles.crear')
    ->middleware('role:administrador|has:admin.configuracion');

Router::post('/admin/configuracion/roles', [AdminController::class, 'guardarRol'])
    ->name('admin.roles.store')
    ->middleware('role:administrador|has:admin.configuracion');

Router::get('/admin/configuracion/roles/{id}/editar', [AdminController::class, 'editarRol'])
    ->name('admin.roles.editar')
    ->middleware('role:administrador|has:admin.configuracion');

Router::put('/admin/configuracion/roles/{id}', [AdminController::class, 'actualizarRol'])
    ->name('admin.roles.update')
    ->middleware('role:administrador|has:admin.configuracion');

Router::delete('/admin/configuracion/roles/{id}', [AdminController::class, 'eliminarRol'])
    ->name('admin.roles.delete')
    ->middleware('role:administrador|has:admin.configuracion');

Router::get('/admin/configuracion/permisos', [AdminController::class, 'permisos'])
    ->name('admin.permisos')
    ->middleware('role:administrador|has:admin.configuracion');

Router::get('/admin/configuracion/roles/{id}/permisos', [AdminController::class, 'asignarPermisos'])
    ->name('admin.roles.permisos')
    ->middleware('role:administrador|has:admin.configuracion');

Router::post('/admin/configuracion/roles/{id}/permisos', [AdminController::class, 'guardarPermisosRol'])
    ->name('admin.roles.permisos.store')
    ->middleware('role:administrador|has:admin.configuracion');

Router::get('/admin/usuarios', [AdminController::class, 'usuarios'])
    ->name('usuarios')
    ->middleware('role:administrador|has:admin.usuarios.ver');

Router::get('/admin/usuarios/crear', [AdminController::class, 'crearUsuario'])
    ->name('usuarios.crear')
    ->middleware('role:administrador|has:admin.usuarios.crear');

Router::post('/admin/usuarios', [AdminController::class, 'guardarUsuario'])
    ->name('usuarios.store')
    ->middleware('role:administrador|has:admin.usuarios.crear');

Router::get('/admin/usuarios/{id}/editar', [AdminController::class, 'editarUsuario'])
    ->name('usuarios.editar')
    ->middleware('role:administrador|has:admin.usuarios.editar');

Router::put('/admin/usuarios/{id}', [AdminController::class, 'actualizarUsuario'])
    ->name('usuarios.update')
    ->middleware('role:administrador|has:admin.usuarios.editar');

Router::delete('/admin/usuarios/{id}', [AdminController::class, 'eliminarUsuario'])
    ->name('usuarios.delete')
    ->middleware('role:administrador|has:admin.usuarios.eliminar');

Router::post('/admin/usuarios/{id}/estado', [AdminController::class, 'cambiarEstadoUsuario'])
    ->name('usuarios.estado')
    ->middleware('role:administrador|has:admin.usuarios.editar');

Router::get('/admin/ventas', [AdminController::class, 'ventas'])
    ->name('ventas')
    ->middleware('role:administrador|has:admin.ventas.ver');

Router::get('/admin/ventas/crear', [AdminController::class, 'crearVenta'])
    ->name('ventas.crear')
    ->middleware('role:administrador|has:admin.ventas.crear');

Router::get('/estudiantes/dashboard', [EstudianteController::class, 'estudiantes'])
    ->name('estudiantes')
    ->middleware('role:estudiante|has:estudiantes.dashboard');

Router::get('/cursos', [CursoController::class, 'cursos'])
    ->name('cursos')
    ->middleware('role:docente,estudiante|has:cursos.ver');

Router::get('/cursos/crear', [CursoController::class, 'crearCurso'])
    ->name('cursos.crear')
    ->middleware('role:docente|has:cursos.crear');

Router::get('/libros', [LibroController::class, 'libros'])
    ->name('libros')
    ->middleware('role:docente,estudiante|has:libros.ver');

Router::get('/libros/crear', [LibroController::class, 'crearLibro'])
    ->name('libros.crear')
    ->middleware('role:docente|has:libros.crear');

Router::get('/materiales', [MaterialController::class, 'materiales'])
    ->name('materiales')
    ->middleware('role:docente,estudiante|has:materiales.ver');

Router::get('/laboratorios', [LaboratorioController::class, 'laboratorios'])
    ->name('laboratorios')
    ->middleware('role:docente,estudiante|has:laboratorios.ver');


Router::get('/componentes', [ComponenteController::class, 'componentes'])
    ->name('componentes')
    ->middleware('role:administrador,docente,estudiante,vendedor|has:componentes.ver');

Router::get('/componentes/crear', [ComponenteController::class, 'crearComponente'])
    ->name('componentes.crear')
    ->middleware('role:administrador,vendedor|has:componentes.crear');

Router::get('/docente/dashboard', [DocenteController::class, 'dashboard'])
    ->name('docente.dashboard')
    ->middleware('role:docente|has:docente.dashboard');

// Ejemplo de ruta que solo verifica permisos específicos
// Un estudiante monitor podría tener el permiso 'admin.reportes'
// sin necesidad de ser docente o administrador
Router::get('/reportes/basicos', [AdminController::class, 'reportesBasicos'])
    ->name('reportes.basicos')
    ->middleware('has:admin.reportes');
