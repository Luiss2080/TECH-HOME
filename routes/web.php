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

// Nueva ruta para la vista index de estudiantes
Router::get('/estudiantes', [EstudianteController::class, 'index'])
    ->name('estudiantes.index')
    ->middleware('role:administrador|has:admin.usuarios.ver');

    
Router::get('/cursos', [CursoController::class, 'cursos'])
    ->name('cursos')
    ->middleware('role:administrador,docente,estudiante|has:cursos.ver');

Router::get('/cursos/crear', [CursoController::class, 'crearCurso'])
    ->name('cursos.crear')
    ->middleware('role:administrador,docente|has:cursos.crear');

Router::post('/cursos', [CursoController::class, 'guardarCurso'])
    ->name('cursos.store')
    ->middleware('role:administrador,docente|has:cursos.crear');

Router::get('/cursos/{id}', [CursoController::class, 'verCurso'])
    ->name('cursos.ver')
    ->middleware('role:administrador,docente,estudiante|has:cursos.ver');

Router::get('/cursos/{id}/editar', [CursoController::class, 'editarCurso'])
    ->name('cursos.editar')
    ->middleware('role:administrador,docente|has:cursos.editar');

Router::put('/cursos/{id}', [CursoController::class, 'actualizarCurso'])
    ->name('cursos.update')
    ->middleware('role:administrador,docente|has:cursos.editar');

Router::delete('/cursos/{id}', [CursoController::class, 'eliminarCurso'])
    ->name('cursos.delete')
    ->middleware('role:administrador,docente|has:cursos.eliminar');

Router::post('/cursos/{id}/estado', [CursoController::class, 'cambiarEstado'])
    ->name('cursos.estado')
    ->middleware('role:administrador,docente|has:cursos.editar');

Router::post('/cursos/{id}/inscribir', [CursoController::class, 'inscribir'])
    ->name('cursos.inscribir')
    ->middleware('role:estudiante');

// Rutas AJAX para cursos
Router::get('/ajax/cursos/estadisticas', [CursoController::class, 'ajaxEstadisticas'])
    ->name('cursos.ajax.estadisticas')
    ->middleware('role:administrador,docente|has:cursos.ver');

Router::get('/ajax/cursos/buscar', [CursoController::class, 'buscarCursos'])
    ->name('cursos.ajax.buscar')
    ->middleware('role:administrador,docente,estudiante|has:cursos.ver');







// ==================== RUTAS PARA MÓDULO DE LIBROS ====================

// Rutas públicas de libros (requieren autenticación)
Router::get('/libros', [LibroController::class, 'index'])
    ->name('libros.index')
    ->middleware('auth');

Router::get('/libros/{id}', [LibroController::class, 'show'])
    ->name('libros.show')
    ->middleware('auth');

Router::get('/libros/{id}/descargar', [LibroController::class, 'descargar'])
    ->name('libros.descargar')
    ->middleware('auth');

// Alias para compatibilidad
Router::get('/libros', [LibroController::class, 'libros'])
    ->name('libros')
    ->middleware('role:docente,estudiante|has:libros.ver');

// Rutas administrativas de libros
Router::get('/admin/libros', [LibroController::class, 'admin'])
    ->name('admin.libros')
    ->middleware('role:administrador|has:admin.libros');

Router::get('/admin/libros/crear', [LibroController::class, 'create'])
    ->name('admin.libros.crear')
    ->middleware('role:administrador|has:admin.libros.crear');

// Alias para compatibilidad
Router::get('/libros/crear', [LibroController::class, 'crearLibro'])
    ->name('libros.crear')
    ->middleware('role:docente|has:libros.crear');

Router::post('/admin/libros', [LibroController::class, 'store'])
    ->name('admin.libros.store')
    ->middleware('role:administrador|has:admin.libros.crear');

Router::get('/admin/libros/{id}/editar', [LibroController::class, 'edit'])
    ->name('admin.libros.editar')
    ->middleware('role:administrador|has:admin.libros.editar');

Router::put('/admin/libros/{id}', [LibroController::class, 'update'])
    ->name('admin.libros.update')
    ->middleware('role:administrador|has:admin.libros.editar');

Router::delete('/admin/libros/{id}', [LibroController::class, 'destroy'])
    ->name('admin.libros.delete')
    ->middleware('role:administrador|has:admin.libros.eliminar');

// Acciones AJAX/API para libros
Router::post('/admin/libros/{id}/estado', [LibroController::class, 'toggleEstado'])
    ->name('admin.libros.estado')
    ->middleware('role:administrador|has:admin.libros.editar');

Router::put('/admin/libros/{id}/stock', [LibroController::class, 'actualizarStock'])
    ->name('admin.libros.stock')
    ->middleware('role:administrador|has:admin.libros.editar');

Router::get('/admin/libros/{id}/descargas', [LibroController::class, 'verDescargas'])
    ->name('admin.libros.descargas')
    ->middleware('role:administrador|has:admin.libros.ver');

// Reportes de libros
Router::get('/admin/libros/reportes', [LibroController::class, 'reporte'])
    ->name('admin.libros.reportes')
    ->middleware('role:administrador|has:admin.reportes');

// APIs AJAX para búsqueda y funcionalidades
Router::get('/ajax/libros/buscar', [LibroController::class, 'buscar'])
    ->name('ajax.libros.buscar')
    ->middleware('auth');

Router::get('/ajax/libros/{id}/info', [LibroController::class, 'info'])
    ->name('ajax.libros.info')
    ->middleware('auth');

Router::get('/ajax/libros/{id}/disponibilidad', [LibroController::class, 'verificarDisponibilidad'])
    ->name('ajax.libros.disponibilidad')
    ->middleware('auth');









// ==================== RUTAS PARA FAVORITOS Y CALIFICACIONES ====================

// Favoritos - Libros
Router::post('/libros/{id}/favorito', [LibroController::class, 'toggleFavorito'])
    ->name('libros.favorito')
    ->middleware('auth');

Router::get('/mis-favoritos-libros', [LibroController::class, 'misFavoritos'])
    ->name('libros.mis-favoritos')
    ->middleware('auth');

// Calificaciones - Libros
Router::post('/libros/{id}/calificar', [LibroController::class, 'calificar'])
    ->name('libros.calificar')
    ->middleware('auth');

Router::get('/libros/{id}/calificaciones', [LibroController::class, 'getCalificaciones'])
    ->name('libros.calificaciones')
    ->middleware('auth');

// Favoritos - Cursos
Router::post('/cursos/{id}/favorito', [CursoController::class, 'toggleFavorito'])
    ->name('cursos.favorito')
    ->middleware('auth');

Router::get('/mis-favoritos-cursos', [CursoController::class, 'misFavoritos'])
    ->name('cursos.mis-favoritos')
    ->middleware('auth');

// Calificaciones - Cursos
Router::post('/cursos/{id}/calificar', [CursoController::class, 'calificar'])
    ->name('cursos.calificar')
    ->middleware('auth');

Router::get('/cursos/{id}/calificaciones', [CursoController::class, 'getCalificaciones'])
    ->name('cursos.calificaciones')
    ->middleware('auth');

// Progreso - Cursos
Router::get('/cursos/{id}/progreso', [CursoController::class, 'verProgreso'])
    ->name('cursos.progreso')
    ->middleware('auth');

Router::post('/cursos/{cursoId}/modulos/{moduloId}/completar', [CursoController::class, 'completarModulo'])
    ->name('cursos.modulo.completar')
    ->middleware('auth');






// ==================== RUTAS PARA MÓDULO DE MATERIALES ====================

// Rutas públicas de materiales (requieren autenticación)
Router::get('/materiales', [MaterialController::class, 'index'])
    ->name('materiales.index')
    ->middleware('auth');

Router::get('/materiales/{id}', [MaterialController::class, 'ver'])
    ->name('materiales.show')
    ->middleware('auth');

Router::get('/materiales/{id}/descargar', [MaterialController::class, 'descargar'])
    ->name('materiales.descargar')
    ->middleware('auth');

// Alias para compatibilidad
Router::get('/materiales', [MaterialController::class, 'materiales'])
    ->name('materiales')
    ->middleware('role:docente,estudiante|has:materiales.ver');

// Rutas administrativas de materiales
Router::get('/admin/materiales', [MaterialController::class, 'index'])
    ->name('admin.materiales')
    ->middleware('role:administrador|has:admin.materiales');

Router::get('/admin/materiales/crear', [MaterialController::class, 'crear'])
    ->name('admin.materiales.crear')
    ->middleware('role:administrador,docente|has:admin.materiales.crear');

Router::post('/admin/materiales', [MaterialController::class, 'guardar'])
    ->name('admin.materiales.store')
    ->middleware('role:administrador,docente|has:admin.materiales.crear');

Router::get('/admin/materiales/{id}/editar', [MaterialController::class, 'editar'])
    ->name('admin.materiales.editar')
    ->middleware('role:administrador,docente|has:admin.materiales.editar');

Router::put('/admin/materiales/{id}', [MaterialController::class, 'actualizar'])
    ->name('admin.materiales.update')
    ->middleware('role:administrador,docente|has:admin.materiales.editar');

Router::delete('/admin/materiales/{id}', [MaterialController::class, 'eliminar'])
    ->name('admin.materiales.delete')
    ->middleware('role:administrador|has:admin.materiales.eliminar');

Router::get('/admin/materiales/{id}', [MaterialController::class, 'ver'])
    ->name('admin.materiales.show')
    ->middleware('role:administrador,docente|has:admin.materiales.ver');

// Acciones específicas para materiales
Router::post('/admin/materiales/{id}/estado', [MaterialController::class, 'cambiarEstado'])
    ->name('admin.materiales.estado')
    ->middleware('role:administrador,docente|has:admin.materiales.editar');

Router::post('/admin/materiales/{id}/visibilidad', [MaterialController::class, 'cambiarVisibilidad'])
    ->name('admin.materiales.visibilidad')
    ->middleware('role:administrador,docente|has:admin.materiales.editar');

Router::post('/admin/materiales/{id}/duplicar', [MaterialController::class, 'duplicar'])
    ->name('admin.materiales.duplicar')
    ->middleware('role:administrador,docente|has:admin.materiales.crear');

// Búsqueda y filtros de materiales
Router::get('/admin/materiales/buscar', [MaterialController::class, 'buscar'])
    ->name('admin.materiales.buscar')
    ->middleware('role:administrador,docente|has:admin.materiales.ver');

// Rutas AJAX para materiales
Router::get('/ajax/materiales/estadisticas', [MaterialController::class, 'estadisticas'])
    ->name('ajax.materiales.estadisticas')
    ->middleware('role:administrador,docente|has:admin.materiales.ver');

Router::get('/ajax/materiales/docente/{docenteId}', [MaterialController::class, 'porDocente'])
    ->name('ajax.materiales.docente')
    ->middleware('role:administrador,docente|has:admin.materiales.ver');

Router::get('/ajax/materiales/categoria/{categoriaId}', [MaterialController::class, 'porCategoria'])
    ->name('ajax.materiales.categoria')
    ->middleware('role:administrador,docente,estudiante|has:materiales.ver');






// ==================== RUTAS PARA MÓDULO DE LABORATORIOS ====================

// Página principal de laboratorios (vista pública)
Router::get('/laboratorios', [LaboratorioController::class, 'laboratorios'])
    ->name('laboratorios')
    ->middleware('role:docente,estudiante|has:laboratorios.ver');

// Rutas administrativas para laboratorios
Router::get('/admin/laboratorios', [LaboratorioController::class, 'index'])
    ->name('admin.laboratorios')
    ->middleware('role:administrador|has:admin.laboratorios');

Router::get('/admin/laboratorios/crear', [LaboratorioController::class, 'create'])
    ->name('admin.laboratorios.crear')
    ->middleware('role:administrador,docente|has:admin.laboratorios.crear');

Router::post('/admin/laboratorios', [LaboratorioController::class, 'store'])
    ->name('admin.laboratorios.store')
    ->middleware('role:administrador,docente|has:admin.laboratorios.crear');

Router::get('/admin/laboratorios/{id}', [LaboratorioController::class, 'show'])
    ->name('admin.laboratorios.show')
    ->middleware('role:administrador,docente|has:admin.laboratorios.ver');

Router::get('/admin/laboratorios/{id}/editar', [LaboratorioController::class, 'edit'])
    ->name('admin.laboratorios.editar')
    ->middleware('role:administrador,docente|has:admin.laboratorios.editar');

Router::put('/admin/laboratorios/{id}', [LaboratorioController::class, 'update'])
    ->name('admin.laboratorios.update')
    ->middleware('role:administrador,docente|has:admin.laboratorios.editar');

Router::delete('/admin/laboratorios/{id}', [LaboratorioController::class, 'destroy'])
    ->name('admin.laboratorios.delete')
    ->middleware('role:administrador|has:admin.laboratorios.eliminar');

// Acciones específicas para laboratorios
Router::post('/admin/laboratorios/{id}/estado', [LaboratorioController::class, 'changeStatus'])
    ->name('admin.laboratorios.estado')
    ->middleware('role:administrador,docente|has:admin.laboratorios.editar');

Router::post('/admin/laboratorios/{id}/publico', [LaboratorioController::class, 'changePublicStatus'])
    ->name('admin.laboratorios.publico')
    ->middleware('role:administrador,docente|has:admin.laboratorios.editar');

Router::post('/admin/laboratorios/{id}/destacado', [LaboratorioController::class, 'changeDestacadoStatus'])
    ->name('admin.laboratorios.destacado')
    ->middleware('role:administrador,docente|has:admin.laboratorios.editar');

Router::post('/admin/laboratorios/{id}/duplicar', [LaboratorioController::class, 'duplicate'])
    ->name('admin.laboratorios.duplicar')
    ->middleware('role:administrador,docente|has:admin.laboratorios.crear');

Router::get('/admin/laboratorios/{id}/exportar', [LaboratorioController::class, 'export'])
    ->name('admin.laboratorios.exportar')
    ->middleware('role:administrador,docente|has:admin.laboratorios.ver');

// Gestión de participantes
Router::post('/admin/laboratorios/{id}/participantes', [LaboratorioController::class, 'addParticipante'])
    ->name('admin.laboratorios.participante.agregar')
    ->middleware('role:administrador,docente|has:admin.laboratorios.editar');

Router::delete('/admin/laboratorios/{id}/participantes', [LaboratorioController::class, 'removeParticipante'])
    ->name('admin.laboratorios.participante.remover')
    ->middleware('role:administrador,docente|has:admin.laboratorios.editar');

// Actualización de fechas
Router::put('/admin/laboratorios/{id}/fechas', [LaboratorioController::class, 'updateFechas'])
    ->name('admin.laboratorios.fechas')
    ->middleware('role:administrador,docente|has:admin.laboratorios.editar');

// Búsqueda y filtros de laboratorios
Router::get('/admin/laboratorios/buscar', [LaboratorioController::class, 'search'])
    ->name('admin.laboratorios.buscar')
    ->middleware('role:administrador,docente|has:admin.laboratorios.ver');

// Dashboard específico para docentes
Router::get('/docente/laboratorios/dashboard', [LaboratorioController::class, 'dashboard'])
    ->name('docente.laboratorios.dashboard')
    ->middleware('role:docente|has:docente.laboratorios');


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

// Nueva ruta para la vista index de docentes
Router::get('/docentes', [DocenteController::class, 'index'])
    ->name('docentes.index')
    ->middleware('role:administrador|has:admin.usuarios.ver');

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