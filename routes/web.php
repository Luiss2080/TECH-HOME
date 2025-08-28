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
Router::post('/login', [AuthController::class, 'loginForm'])
    ->name('login.loginForm')
    ->middleware('rateLimit:login,5,15'); // Máximo 5 intentos cada 15 minutos

// Rutas de registro
Router::get('/register', [AuthController::class, 'register'])->name('register');
Router::post('/register', [AuthController::class, 'registerForm'])->name('register.store');

// Rutas para recuperación de contraseña
Router::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.forgot');
Router::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Router::get('/reset-password', [AuthController::class, 'resetPassword'])->name('password.reset');
Router::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');

// Ruta para activación de cuenta
Router::get('/account/activation', [AuthController::class, 'activateAccount'])->name('account.activation');

// ==================== RUTAS PARA 2FA (AUTENTICACIÓN DE DOS FACTORES) ====================

// Vista de verificación OTP
Router::get('/auth/otp-verify', [AuthController::class, 'showOTPVerification'])
    ->name('auth.otp.verify');

// Verificar código OTP
Router::post('/auth/otp-verify', [AuthController::class, 'verifyOTP'])
    ->name('auth.verify.otp')
    ->middleware('rateLimit:otp,3,5'); // Máximo 3 intentos cada 5 minutos

// Reenviar código OTP
Router::post('/auth/otp-resend', [AuthController::class, 'resendOTP'])
    ->name('auth.resend.otp')
    ->middleware('rateLimit:otp,2,1'); // Máximo 2 reenvíos por minuto

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

// Ruta para editar roles de usuarios
Router::get('/admin/usuarios/{id}/roles', [AdminController::class, 'editarRolesUsuario'])
    ->name('usuarios.roles')
    ->middleware('role:administrador|has:admin.usuarios.roles');

Router::put('/admin/usuarios/{id}/roles', [AdminController::class, 'actualizarRolesUsuario'])
    ->name('usuarios.roles.update')
    ->middleware('role:administrador|has:admin.usuarios.roles');

// Rutas para editar permisos de usuarios
Router::get('/admin/usuarios/{id}/permisos', [AdminController::class, 'editarPermisosUsuario'])
    ->name('usuarios.permisos')
    ->middleware('role:administrador|has:admin.usuarios.permisos');

Router::put('/admin/usuarios/{id}/permisos', [AdminController::class, 'actualizarPermisosUsuario'])
    ->name('usuarios.permisos.update')
    ->middleware('role:administrador|has:admin.usuarios.permisos');

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














    // ==================== RUTAS ADICIONALES PARA DOCENTE ====================
// Agregar estas rutas al final del archivo routes.php existente

// AJAX para métricas del docente
Router::get('/docente/ajax/metricas', [DocenteController::class, 'ajaxMetricas'])
    ->name('docente.ajax.metricas')
    ->middleware('role:docente|has:docente.dashboard');

Router::post('/docente/ajax/refresh-metrics', [DocenteController::class, 'refreshMetrics'])
    ->name('docente.ajax.refresh')
    ->middleware('role:docente|has:docente.dashboard');

// Gestión de cursos del docente
Router::get('/docente/cursos', [DocenteController::class, 'cursos'])
    ->name('docente.cursos')
    ->middleware('role:docente|has:docente.cursos');

Router::get('/docente/cursos/crear', [DocenteController::class, 'crearCurso'])
    ->name('docente.cursos.crear')
    ->middleware('role:docente|has:docente.cursos.crear');

Router::post('/docente/cursos', [DocenteController::class, 'guardarCurso'])
    ->name('docente.cursos.guardar')
    ->middleware('role:docente|has:docente.cursos.crear');

// Gestión de estudiantes
Router::get('/docente/estudiantes', [DocenteController::class, 'estudiantes'])
    ->name('docente.estudiantes')
    ->middleware('role:docente|has:docente.estudiantes');

Router::get('/docente/estudiantes/progreso', [DocenteController::class, 'progreso'])
    ->name('docente.progreso')
    ->middleware('role:docente|has:docente.estudiantes');

// Gestión de materiales
Router::get('/docente/materiales', [DocenteController::class, 'materiales'])
    ->name('docente.materiales')
    ->middleware('role:docente|has:docente.materiales');

Router::get('/docente/materiales/subir', [DocenteController::class, 'subirMaterial'])
    ->name('docente.materiales.subir')
    ->middleware('role:docente|has:docente.materiales.crear');

// Tareas y evaluaciones
Router::get('/docente/tareas/revision', [DocenteController::class, 'tareasRevision'])
    ->name('docente.tareas.revision')
    ->middleware('role:docente|has:docente.tareas');

Router::get('/docente/evaluaciones', [DocenteController::class, 'evaluaciones'])
    ->name('docente.evaluaciones')
    ->middleware('role:docente|has:docente.evaluaciones');

Router::get('/docente/evaluaciones/crear', [DocenteController::class, 'crearEvaluacion'])
    ->name('docente.evaluaciones.crear')
    ->middleware('role:docente|has:docente.evaluaciones.crear');

// Comentarios y comunicación
Router::get('/docente/comentarios', [DocenteController::class, 'comentarios'])
    ->name('docente.comentarios')
    ->middleware('role:docente|has:docente.comunicacion');

// Estadísticas
Router::get('/docente/estadisticas', [DocenteController::class, 'estadisticas'])
    ->name('docente.estadisticas')
    ->middleware('role:docente|has:docente.estadisticas');














    


// ==================== RUTAS ESENCIALES PARA ESTUDIANTE ====================

// Dashboard principal
Router::get('/estudiante/dashboard', [EstudianteController::class, 'estudiantes'])
    ->name('estudiante.dashboard')
    ->middleware('role:estudiante');

// AJAX para métricas
Router::get('/estudiante/ajax/metricas', [EstudianteController::class, 'ajaxMetricas'])
    ->name('estudiante.ajax.metricas')
    ->middleware('role:estudiante');

// =========================================
// GESTIÓN DE CURSOS
// =========================================

// Mis cursos inscritos
Router::get('/estudiante/cursos', [EstudianteController::class, 'misCursos'])
    ->name('estudiante.cursos')
    ->middleware('role:estudiante');

// Ver curso específico
Router::get('/estudiante/cursos/{id}', [EstudianteController::class, 'verCurso'])
    ->name('estudiante.curso.ver')
    ->middleware('role:estudiante');

// Actualizar progreso de curso (AJAX)
Router::post('/estudiante/cursos/{id}/progreso', [EstudianteController::class, 'actualizarProgreso'])
    ->name('estudiante.curso.progreso')
    ->middleware('role:estudiante');

// =========================================
// BIBLIOTECA DE LIBROS
// =========================================

// Ver libros disponibles
Router::get('/estudiante/libros', [EstudianteController::class, 'libros'])
    ->name('estudiante.libros')
    ->middleware('role:estudiante');

// Descargar libro
Router::get('/estudiante/libros/{id}/descargar', [EstudianteController::class, 'descargarLibro'])
    ->name('estudiante.libro.descargar')
    ->middleware('role:estudiante');

// =========================================
// PERFIL Y PROGRESO
// =========================================

// Ver mi progreso
Router::get('/estudiante/progreso', [EstudianteController::class, 'miProgreso'])
    ->name('estudiante.progreso')
    ->middleware('role:estudiante');

// Ver/editar perfil
Router::get('/estudiante/perfil', [EstudianteController::class, 'perfil'])
    ->name('estudiante.perfil')
    ->middleware('role:estudiante');

Router::post('/estudiante/perfil', [EstudianteController::class, 'actualizarPerfil'])
    ->name('estudiante.perfil.actualizar')
    ->middleware('role:estudiante');