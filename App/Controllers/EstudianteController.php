<?php

namespace App\Controllers;

use App\Services\EstudianteService;
use Core\Controller;
use Core\Request;
use Core\Session;
use Exception;

class EstudianteController extends Controller
{
    private $estudianteService;

    public function __construct()
    {
        parent::__construct();
        $this->estudianteService = new EstudianteService();
    }

    /**
     * Obtener ID del estudiante autenticado con validación
     */
    private function getEstudianteId()
    {
        $estudianteId = Session::get('user_id') ?? Session::get('auth_user_id');
        
        if (!$estudianteId) {
            throw new Exception('Usuario no autenticado o sesión expirada');
        }
        
        return $estudianteId;
    }

    /**
     * Dashboard principal del estudiante
     */
    public function estudiantes()
    {
        try {
            $estudianteId = $this->getEstudianteId();

            // Obtener datos del dashboard usando el servicio
            $data = $this->estudianteService->getDashboardData($estudianteId);
            return view('estudiantes.dashboard', array_merge($data, [
                'title' => 'Dashboard Estudiante - Tech Home Bolivia'
            ]));
            
        } catch (Exception $e) {
            // En caso de error, mostrar vista con datos por defecto
            return view('estudiantes.dashboard', [
                'title' => 'Dashboard Estudiante - Tech Home Bolivia',
                'metricas_estudiante' => [
                    'cursos_inscritos' => 0,
                    'progreso_general' => 0,
                    'certificados_obtenidos' => 0,
                    'tiempo_estudio_total' => 0
                ],
                'cursos_actuales' => [],
                'libros_disponibles' => [],
                'actividad_reciente' => [],
                'progreso_cursos' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Vista index para gestión de estudiantes (para administradores)
     */
    public function index()
    {
        try {
            // Para ahora, usar datos básicos - puedes implementar el método en el servicio después
            $estudiantes = []; // Implementar la lógica según necesites
            
            return view('estudiantes.index', [
                'title' => 'Gestión de Estudiantes - Tech Home Bolivia',
                'estudiantes' => $estudiantes
            ]);
            
        } catch (Exception $e) {
            return view('estudiantes.index', [
                'title' => 'Gestión de Estudiantes - Tech Home Bolivia',
                'estudiantes' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX - Métricas actualizadas
     */
    public function ajaxMetricas()
    {
        try {
            header('Content-Type: application/json');

            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') {
                throw new Exception('Solo se permiten peticiones AJAX');
            }
            
            $estudianteId = Session::get('user_id') ?? Session::get('auth_user_id');
            $tipo = $_GET['tipo'] ?? 'general';
            
            if (!$estudianteId) {
                throw new Exception('Usuario no autenticado');
            }
            
            $data = $this->estudianteService->getMetricasAjax($estudianteId, $tipo);
            
            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Lista de cursos inscritos
     */
    public function misCursos()
    {
        try {
            $estudianteId = Session::get('user_id') ?? Session::get('auth_user_id');
            
            if (!$estudianteId) {
                throw new Exception('Usuario no autenticado');
            }
            
            $cursos = $this->estudianteService->getCursosInscritos($estudianteId);
            
            return view('estudiantes.cursos', [
                'title' => 'Mis Cursos - Tech Home Bolivia',
                'cursos' => $cursos,
                'total_cursos' => count($cursos)
            ]);
            
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar cursos: ' . $e->getMessage());
            return view('estudiantes.cursos', [
                'title' => 'Mis Cursos - Tech Home Bolivia', 
                'cursos' => [],
                'total_cursos' => 0
            ]);
        }
    }

    /**
     * Ver detalles de un curso
     */
    public function verCurso($id)
    {
        try {
            $estudianteId = Session::get('user_id') ?? Session::get('auth_user_id');
            
            if (!$estudianteId) {
                throw new Exception('Usuario no autenticado');
            }
            
            // Validar acceso
            if (!$this->estudianteService->validarAccesoCurso($estudianteId, $id)) {
                Session::flash('error', 'No tienes acceso a este curso');
                return redirect(route('estudiante.cursos'));
            }
            
            // Obtener detalles del curso
            $curso = $this->getCursoDetalle($id, $estudianteId);
            
            return view('estudiantes.curso-detalle', [
                'title' => 'Ver Curso - ' . ($curso['titulo'] ?? 'Curso'),
                'curso' => $curso
            ]);
            
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar curso: ' . $e->getMessage());
            return redirect(route('estudiante.cursos'));
        }
    }

    /**
     * Actualizar progreso de curso (AJAX)
     */
    public function actualizarProgreso(Request $request, $id)
    {
        try {
            header('Content-Type: application/json');

            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') {
                throw new Exception('Solo se permiten peticiones AJAX');
            }
            
            $estudianteId = Session::get('user_id') ?? Session::get('auth_user_id');
            $progreso = (float) $request->input('progreso');
            
            if (!$estudianteId) {
                throw new Exception('Usuario no autenticado');
            }
            
            if ($progreso < 0 || $progreso > 100) {
                throw new Exception('Progreso inválido. Debe estar entre 0 y 100');
            }
            
            // Verificar acceso al curso
            if (!$this->estudianteService->validarAccesoCurso($estudianteId, $id)) {
                throw new Exception('No tienes acceso a este curso');
            }
            
            $resultado = $this->estudianteService->actualizarProgreso($estudianteId, $id, $progreso);
            
            echo json_encode([
                'success' => $resultado,
                'mensaje' => 'Progreso actualizado correctamente'
            ]);
            
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    /**
     * Biblioteca de libros
     */
    public function libros()
    {
        try {
            $libros = $this->estudianteService->getLibrosDisponibles();
            $esInvitado = $this->esUsuarioInvitado();
            $estudianteId = Session::get('user_id') ?? Session::get('auth_user_id');
            
            return view('estudiantes.libros', [
                'title' => 'Biblioteca - Tech Home Bolivia',
                'libros' => $libros,
                'es_invitado' => $esInvitado,
                'total_libros' => count($libros),
                'estudiante_id' => $estudianteId
            ]);
            
        } catch (Exception $e) {
            return view('estudiantes.libros', [
                'title' => 'Biblioteca - Tech Home Bolivia',
                'libros' => [],
                'es_invitado' => true,
                'total_libros' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Descargar libro
     */
    public function descargarLibro($id)
    {
        try {
            $estudianteId = Session::get('user_id') ?? Session::get('auth_user_id');
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            
            if (!$estudianteId) {
                throw new Exception('Usuario no autenticado');
            }
            
            // Registrar descarga
            $this->estudianteService->registrarDescarga($estudianteId, $id, $ip);
            
            // En implementación real: devolver archivo
            Session::flash('success', 'Descarga iniciada correctamente');
            return redirect(route('estudiante.libros'));
            
        } catch (Exception $e) {
            Session::flash('error', 'Error en descarga: ' . $e->getMessage());
            return redirect(route('estudiante.libros'));
        }
    }

    /**
     * Ver progreso detallado
     */
    public function miProgreso()
    {
        try {
            $estudianteId = Session::get('user_id') ?? Session::get('auth_user_id');
            
            if (!$estudianteId) {
                throw new Exception('Usuario no autenticado');
            }
            
            $progreso = $this->estudianteService->getProgresoDetallado($estudianteId);
            $metricas = $this->estudianteService->getMetricasAjax($estudianteId, 'progreso');
            
            return view('estudiantes.progreso', [
                'title' => 'Mi Progreso - Tech Home Bolivia',
                'progreso' => $progreso,
                'metricas' => $metricas,
                'total_cursos' => count($progreso)
            ]);
            
        } catch (Exception $e) {
            return view('estudiantes.progreso', [
                'title' => 'Mi Progreso - Tech Home Bolivia',
                'progreso' => [],
                'metricas' => [
                    'general' => 0,
                    'tiempo_total' => 0,
                    'certificados' => 0
                ],
                'total_cursos' => 0,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Ver/editar perfil
     */
    public function perfil()
    {
        try {
            $estudianteId = Session::get('user_id');
            $usuario = $this->obtenerDatosUsuario($estudianteId);
            
            return view('estudiantes.perfil', [
                'title' => 'Mi Perfil',
                'usuario' => $usuario
            ]);
            
        } catch (Exception $e) {
            return view('estudiantes.perfil', [
                'title' => 'Mi Perfil',
                'usuario' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Actualizar perfil
     */
    public function actualizarPerfil(Request $request)
    {
        try {
            $estudianteId = Session::get('user_id') ?? Session::get('auth_user_id');
            
            if (!$estudianteId) {
                throw new Exception('Usuario no autenticado');
            }
            
            $datos = $request->only(['nombre', 'apellido', 'telefono']);
            
            // Validaciones básicas
            if (empty($datos['nombre']) || empty($datos['apellido'])) {
                throw new Exception('Nombre y apellido son requeridos');
            }
            
            // En implementación real: actualizar BD
            Session::flash('success', 'Perfil actualizado correctamente');
            return redirect(route('estudiante.perfil'));
            
        } catch (Exception $e) {
            Session::flash('error', 'Error: ' . $e->getMessage());
            return redirect(route('estudiante.perfil'));
        }
    }

    /**
     * AJAX - Obtener estadísticas detalladas
     */
    public function ajaxEstadisticas()
    {
        try {
            header('Content-Type: application/json');

            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') {
                throw new Exception('Solo se permiten peticiones AJAX');
            }
            
            $estudianteId = Session::get('user_id') ?? Session::get('auth_user_id');
            
            if (!$estudianteId) {
                throw new Exception('Usuario no autenticado');
            }
            
            $estadisticas = $this->estudianteService->getEstadisticasResumen($estudianteId);
            $alertas = $this->estudianteService->getAlertasEstudiante($estudianteId);
            
            echo json_encode([
                'success' => true,
                'data' => $estadisticas,
                'alertas' => $alertas
            ]);
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }

    // Métodos auxiliares privados

    private function getCursoDetalle($cursoId, $estudianteId)
    {
        // En implementación real: consultar BD
        return [
            'id' => $cursoId,
            'titulo' => 'Curso de Ejemplo',
            'descripcion' => 'Descripción del curso',
            'progreso_actual' => 0
        ];
    }

    private function esUsuarioInvitado()
    {
        $userId = Session::get('user_id');
        // En implementación real: consultar tabla acceso_invitados
        return false;
    }

    private function obtenerDatosUsuario($userId)
    {
        // En implementación real: consultar tabla users
        return [
            'nombre' => 'Usuario',
            'apellido' => 'Ejemplo',
            'email' => 'usuario@ejemplo.com'
        ];
    }
}