<?php

namespace App\Controllers;

use App\Services\DocenteService;
use Core\Controller;
use Core\Request;
use Core\Session;
use Core\Validation;
use Exception;

class DocenteController extends Controller
{
    private $docenteService;

    public function __construct()
    {
        parent::__construct();
        $this->docenteService = new DocenteService();
    }

    /**
     * Dashboard principal del docente
     */
    public function dashboard()
    {
        try {
            // Obtener ID del docente actual desde la sesión
            $docenteId = Session::get('user_id') ?? Session::get('auth_user_id') ?? 1; // ID por defecto para testing
            
            if (!$docenteId) {
                throw new Exception('Usuario no autenticado');
            }

            // Obtener datos del dashboard usando el servicio
            $data = $this->docenteService->getDashboardData($docenteId);
            return view('docente.dashboard', array_merge($data, [
                'title' => 'Dashboard Docente - Tech Home Bolivia'
            ]));
            
        } catch (Exception $e) {
            // En caso de error, mostrar vista con datos por defecto
            return view('docente.dashboard', [
                'title' => 'Dashboard Docente - Tech Home Bolivia',
                'metricas_docente' => [
                    'estudiantes_totales' => 0,
                    'estudiantes_activos' => 0,
                    'cursos_creados' => 0,
                    'cursos_activos' => 0,
                    'materiales_subidos' => 0,
                    'materiales_mes' => 0,
                    'tareas_pendientes' => 0,
                    'tareas_urgentes' => 0,
                    'evaluaciones_creadas' => 0,
                    'evaluaciones_activas' => 0,
                    'progreso_promedio' => 0,
                    'mejora_promedio' => 0
                ],
                'actividad_estudiantes' => [],
                'estudiantes_conectados' => [],
                'rendimiento_cursos' => [
                    'calificacion_promedio' => 0,
                    'tasa_finalizacion' => 0,
                    'visualizaciones' => 0,
                    'tiempo_promedio' => 0,
                    'certificados' => 0
                ],
                'comentarios_recientes' => [],
                'cursos_recientes' => [],
                'materiales_recientes' => [],
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX para obtener métricas actualizadas
     */
    public function ajaxMetricas()
    {
        try {
            header('Content-Type: application/json');

            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $docenteId = Session::get('user_id') ?? Session::get('auth_user_id');
            $tipo = $_GET['tipo'] ?? 'general';
            
            $data = $this->docenteService->getMetricasAjax($docenteId, $tipo);

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
     * Refrescar métricas del dashboard
     */
    public function refreshMetrics()
    {
        try {
            header('Content-Type: application/json');

            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $docenteId = Session::get('user_id') ?? Session::get('auth_user_id');
            $stats = $this->docenteService->getDashboardData($docenteId);

            echo json_encode([
                'success' => true,
                'metricas_docente' => $stats['metricas_docente'],
                'rendimiento_cursos' => $stats['rendimiento_cursos']
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

    // =========================================
    // GESTIÓN DE CURSOS
    // =========================================

    /**
     * Lista de cursos del docente
     */
    public function cursos()
    {
        try {
            $docenteId = Session::get('user_id');
            $cursos = $this->docenteService->getCursos($docenteId);
            
            return view('docente.cursos.index', [
                'title' => 'Mis Cursos - Panel Docente',
                'cursos' => $cursos
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar cursos: ' . $e->getMessage());
            return view('docente.cursos.index', [
                'title' => 'Mis Cursos - Panel Docente',
                'cursos' => []
            ]);
        }
    }

    /**
     * Formulario para crear nuevo curso
     */
    public function crearCurso()
    {
        try {
            $categorias = $this->docenteService->getCategoriasCursos();
            
            return view('docente.cursos.crear', [
                'title' => 'Crear Nuevo Curso - Panel Docente',
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar formulario: ' . $e->getMessage());
            return redirect(route('docente.cursos'));
        }
    }

    /**
     * Guardar nuevo curso
     */
    public function guardarCurso(Request $request)
    {
        try {
            // Validaciones
            $rules = [
                'titulo' => 'required|min:5|max:200',
                'descripcion' => 'required|min:20|max:1000',
                'categoria_id' => 'required|numeric',
                'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
                'duracion_estimada' => 'required|numeric|min:1',
                'precio' => 'numeric|min:0',
                'es_gratuito' => 'boolean'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->errors());
                Session::flash('old', $request->all());
                return redirect(route('docente.cursos.crear'));
            }

            $docenteId = Session::get('user_id');
            $cursoData = array_merge($request->all(), ['docente_id' => $docenteId]);
            
            $cursoId = $this->docenteService->crearCurso($cursoData);

            Session::flash('success', 'Curso creado exitosamente.');
            return redirect(route('docente.cursos'));
            
        } catch (Exception $e) {
            Session::flash('error', 'Error al crear curso: ' . $e->getMessage());
            Session::flash('old', $request->all());
            return redirect(route('docente.cursos.crear'));
        }
    }

    // =========================================
    // GESTIÓN DE ESTUDIANTES
    // =========================================

    /**
     * Lista de estudiantes en cursos del docente
     */
    public function estudiantes()
    {
        try {
            $docenteId = Session::get('user_id');
            $estudiantes = $this->docenteService->getEstudiantes($docenteId);
            
            return view('docente.estudiantes.index', [
                'title' => 'Mis Estudiantes - Panel Docente',
                'estudiantes' => $estudiantes
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar estudiantes: ' . $e->getMessage());
            return view('docente.estudiantes.index', [
                'title' => 'Mis Estudiantes - Panel Docente',
                'estudiantes' => []
            ]);
        }
    }

    // =========================================
    // GESTIÓN DE MATERIALES
    // =========================================

    /**
     * Lista de materiales del docente
     */
    public function materiales()
    {
        try {
            $docenteId = Session::get('user_id');
            $materiales = $this->docenteService->getMateriales($docenteId);
            
            return view('docente.materiales.index', [
                'title' => 'Mis Materiales - Panel Docente',
                'materiales' => $materiales
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar materiales: ' . $e->getMessage());
            return view('docente.materiales.index', [
                'title' => 'Mis Materiales - Panel Docente',
                'materiales' => []
            ]);
        }
    }

    /**
     * Subir nuevo material educativo
     */
    public function subirMaterial()
    {
        try {
            $docenteId = Session::get('user_id');
            $cursos = $this->docenteService->getCursos($docenteId);
            
            return view('docente.materiales.subir', [
                'title' => 'Subir Material - Panel Docente',
                'cursos' => $cursos
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar formulario: ' . $e->getMessage());
            return redirect(route('docente.materiales'));
        }
    }

    // =========================================
    // GESTIÓN DE TAREAS Y EVALUACIONES
    // =========================================

    /**
     * Tareas pendientes de revisión
     */
    public function tareasRevision()
    {
        try {
            $docenteId = Session::get('user_id');
            $tareas = $this->docenteService->getTareasPendientes($docenteId);
            
            return view('docente.tareas.revision', [
                'title' => 'Revisar Tareas - Panel Docente',
                'tareas' => $tareas
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar tareas: ' . $e->getMessage());
            return view('docente.tareas.revision', [
                'title' => 'Revisar Tareas - Panel Docente',
                'tareas' => []
            ]);
        }
    }

    /**
     * Evaluaciones del docente
     */
    public function evaluaciones()
    {
        try {
            $docenteId = Session::get('user_id');
            $evaluaciones = $this->docenteService->getEvaluaciones($docenteId);
            
            return view('docente.evaluaciones.index', [
                'title' => 'Mis Evaluaciones - Panel Docente',
                'evaluaciones' => $evaluaciones
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar evaluaciones: ' . $e->getMessage());
            return view('docente.evaluaciones.index', [
                'title' => 'Mis Evaluaciones - Panel Docente',
                'evaluaciones' => []
            ]);
        }
    }

    /**
     * Crear nueva evaluación
     */
    public function crearEvaluacion()
    {
        try {
            $docenteId = Session::get('user_id');
            $cursos = $this->docenteService->getCursos($docenteId);
            
            return view('docente.evaluaciones.crear', [
                'title' => 'Crear Evaluación - Panel Docente',
                'cursos' => $cursos
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar formulario: ' . $e->getMessage());
            return redirect(route('docente.evaluaciones'));
        }
    }

    // =========================================
    // COMENTARIOS Y COMUNICACIÓN
    // =========================================

    /**
     * Comentarios y preguntas de estudiantes
     */
    public function comentarios()
    {
        try {
            $docenteId = Session::get('user_id');
            $comentarios = $this->docenteService->getComentarios($docenteId);
            
            return view('docente.comunicacion.comentarios', [
                'title' => 'Comentarios y Preguntas - Panel Docente',
                'comentarios' => $comentarios
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar comentarios: ' . $e->getMessage());
            return view('docente.comunicacion.comentarios', [
                'title' => 'Comentarios y Preguntas - Panel Docente',
                'comentarios' => []
            ]);
        }
    }

    // =========================================
    // ESTADÍSTICAS Y REPORTES
    // =========================================

    /**
     * Estadísticas detalladas
     */
    public function estadisticas()
    {
        try {
            $docenteId = Session::get('user_id');
            $estadisticas = $this->docenteService->getEstadisticasCompletas($docenteId);
            
            return view('docente.reportes.estadisticas', [
                'title' => 'Estadísticas - Panel Docente',
                'estadisticas' => $estadisticas
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar estadísticas: ' . $e->getMessage());
            return view('docente.reportes.estadisticas', [
                'title' => 'Estadísticas - Panel Docente',
                'estadisticas' => []
            ]);
        }
    }

    /**
     * Progreso de estudiantes
     */
    public function progreso()
    {
        try {
            $docenteId = Session::get('user_id');
            $progreso = $this->docenteService->getProgresoEstudiantes($docenteId);
            
            return view('docente.reportes.progreso', [
                'title' => 'Progreso de Estudiantes - Panel Docente',
                'progreso' => $progreso
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar progreso: ' . $e->getMessage());
            return view('docente.reportes.progreso', [
                'title' => 'Progreso de Estudiantes - Panel Docente',
                'progreso' => []
            ]);
        }
    }
}