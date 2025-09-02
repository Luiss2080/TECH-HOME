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
     * Obtener ID del docente autenticado con validación
     */
    private function getDocenteId()
    {
        $docenteId = Session::get('user_id') ?? Session::get('auth_user_id');
        
        if (!$docenteId) {
            throw new Exception('Usuario no autenticado o sesión expirada');
        }
        
        return $docenteId;
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
     * Vista index para gestión de docentes (para administradores)
     */
    public function index()
    {
        try {
            // Para ahora, usar datos básicos - puedes implementar la lógica según necesites
            $docentes = []; // Implementar la lógica según necesites
            
            return view('docente.index', [
                'title' => 'Gestión de Docentes - Tech Home Bolivia',
                'docentes' => $docentes
            ]);
            
        } catch (Exception $e) {
            return view('docente.index', [
                'title' => 'Gestión de Docentes - Tech Home Bolivia',
                'docentes' => [],
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
            $docenteId = $this->getDocenteId();
            $cursos = $this->docenteService->getCursos($docenteId);
            
            return view('docente.index', [
                'title' => 'Mis Cursos - Panel Docente',
                'cursos' => $cursos
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar cursos: ' . $e->getMessage());
            return view('docente.index', [
                'title' => 'Mis Cursos - Panel Docente',
                'cursos' => [],
                'error' => $e->getMessage()
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

            $docenteId = $this->getDocenteId();
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

    /**
     * Ver detalles de un curso específico del docente
     */
    public function verCurso($id)
    {
        try {
            $docenteId = $this->getDocenteId();
            $curso = $this->docenteService->getCursoById($id, $docenteId);
            
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado o no tienes permisos para verlo.');
                return redirect(route('docente.cursos'));
            }
            
            return view('docente.cursos.ver', [
                'title' => 'Ver Curso: ' . $curso['titulo'],
                'curso' => $curso
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar curso: ' . $e->getMessage());
            return redirect(route('docente.cursos'));
        }
    }

    /**
     * Mostrar formulario de edición de curso
     */
    public function editarCurso($id)
    {
        try {
            $docenteId = $this->getDocenteId();
            $curso = $this->docenteService->getCursoById($id, $docenteId);
            
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado o no tienes permisos para editarlo.');
                return redirect(route('docente.cursos'));
            }
            
            $categorias = $this->docenteService->getCategoriasCursos();
            
            return view('docente.cursos.editar', [
                'title' => 'Editar Curso: ' . $curso['titulo'],
                'curso' => $curso,
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar curso: ' . $e->getMessage());
            return redirect(route('docente.cursos'));
        }
    }

    /**
     * Actualizar curso del docente
     */
    public function actualizarCurso(Request $request, $id)
    {
        try {
            $docenteId = $this->getDocenteId();
            
            // Verificar que el curso pertenece al docente
            $curso = $this->docenteService->getCursoById($id, $docenteId);
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado o no tienes permisos para editarlo.');
                return redirect(route('docente.cursos'));
            }
            
            // Validaciones
            $rules = [
                'titulo' => 'required|min:5|max:200',
                'descripcion' => 'required|min:20|max:1000',
                'categoria_id' => 'required|numeric',
                'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
                'estado' => 'required|in:Borrador,Publicado,Archivado'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->errors());
                Session::flash('old', $request->all());
                return redirect(route('docente.cursos.editar', $id));
            }

            $cursoData = $request->all();
            $resultado = $this->docenteService->actualizarCurso($id, $cursoData);
            
            if ($resultado) {
                Session::flash('success', 'Curso actualizado exitosamente.');
                return redirect(route('docente.cursos.ver', $id));
            } else {
                Session::flash('error', 'Error al actualizar el curso.');
                return redirect(route('docente.cursos.editar', $id));
            }
            
        } catch (Exception $e) {
            Session::flash('error', 'Error al actualizar curso: ' . $e->getMessage());
            Session::flash('old', $request->all());
            return redirect(route('docente.cursos.editar', $id));
        }
    }

    /**
     * Eliminar curso del docente
     */
    public function eliminarCurso($id)
    {
        try {
            $docenteId = $this->getDocenteId();
            
            // Verificar que el curso pertenece al docente
            $curso = $this->docenteService->getCursoById($id, $docenteId);
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado o no tienes permisos para eliminarlo.');
                return redirect(route('docente.cursos'));
            }
            
            $resultado = $this->docenteService->eliminarCurso($id);
            
            if ($resultado) {
                Session::flash('success', 'Curso eliminado exitosamente.');
            } else {
                Session::flash('error', 'Error al eliminar el curso.');
            }
            
            return redirect(route('docente.cursos'));
            
        } catch (Exception $e) {
            Session::flash('error', 'Error al eliminar curso: ' . $e->getMessage());
            return redirect(route('docente.cursos'));
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
            $docenteId = $this->getDocenteId();
            $estudiantes = $this->docenteService->getEstudiantes($docenteId);
            
            return view('estudiantes.index', [
                'title' => 'Mis Estudiantes - Panel Docente',
                'estudiantes' => $estudiantes
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar estudiantes: ' . $e->getMessage());
            return view('estudiantes.index', [
                'title' => 'Mis Estudiantes - Panel Docente',
                'estudiantes' => [],
                'error' => $e->getMessage()
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
            $categorias = $this->docenteService->getCategoriasCursos();
            
            return view('docente.materiales.subir', [
                'title' => 'Subir Material - Panel Docente',
                'cursos' => $cursos,
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar formulario: ' . $e->getMessage());
            return redirect(route('docente.materiales'));
        }
    }

    /**
     * Guardar nuevo material
     */
    public function guardarMaterial(Request $request)
    {
        try {
            // Validaciones
            $rules = [
                'titulo' => 'required|min:5|max:200',
                'descripcion' => 'required|min:10|max:500',
                'tipo' => 'required|in:pdf,video,codigo,guia,dataset',
                'categoria_id' => 'required|numeric',
                'archivo' => 'file|max:10240'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->errors());
                Session::flash('old', $request->all());
                return redirect(route('docente.materiales.subir'));
            }

            $docenteId = $this->getDocenteId();
            
            // Manejar archivo si existe
            $archivo = '';
            $tamaño = 0;
            if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['archivo'];
                $archivo = '/materiales/' . uniqid() . '_' . $file['name'];
                $tamaño = $file['size'];
                
                // Mover el archivo a la carpeta de destino
                $uploadDir = BASE_PATH . 'public/materiales/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $destinationPath = $uploadDir . basename($archivo);
                if (!move_uploaded_file($file['tmp_name'], $destinationPath)) {
                    throw new Exception('Error al subir el archivo');
                }
            }
            
            $materialData = array_merge($request->all(), [
                'docente_id' => $docenteId,
                'archivo' => $archivo,
                'tamaño_archivo' => $tamaño
            ]);
            
            $materialId = $this->docenteService->crearMaterial($materialData);

            Session::flash('success', 'Material subido exitosamente.');
            return redirect(route('docente.materiales'));
            
        } catch (Exception $e) {
            Session::flash('error', 'Error al subir material: ' . $e->getMessage());
            Session::flash('old', $request->all());
            return redirect(route('docente.materiales.subir'));
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

    // =========================================
    // CALIFICACIONES
    // =========================================

    /**
     * Ver calificaciones
     */
    public function calificaciones()
    {
        try {
            $docenteId = $this->getDocenteId();
            $cursos = $this->docenteService->getCursos($docenteId);
            
            return view('docente.calificaciones.index', [
                'title' => 'Calificaciones - Panel Docente',
                'cursos' => $cursos
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar calificaciones: ' . $e->getMessage());
            return view('docente.calificaciones.index', [
                'title' => 'Calificaciones - Panel Docente',
                'cursos' => []
            ]);
        }
    }

    /**
     * Calificar estudiante
     */
    public function calificarEstudiante(Request $request)
    {
        try {
            $rules = [
                'estudiante_id' => 'required|numeric',
                'curso_id' => 'required|numeric',
                'nota' => 'required|numeric|min:0|max:100'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->errors());
                return redirect(back());
            }

            $result = $this->docenteService->calificarEstudiante(
                $request->input('estudiante_id'),
                $request->input('curso_id'),
                $request->input('nota')
            );

            if ($result) {
                Session::flash('success', 'Estudiante calificado exitosamente.');
            } else {
                Session::flash('error', 'Error al calificar estudiante.');
            }

            return redirect(back());
            
        } catch (Exception $e) {
            Session::flash('error', 'Error al calificar: ' . $e->getMessage());
            return redirect(back());
        }
    }

    /**
     * Ver notas de un curso específico
     */
    public function notasCurso(int $cursoId)
    {
        try {
            $notas = $this->docenteService->getNotasCurso($cursoId);
            
            return view('docente.calificaciones.notas', [
                'title' => 'Notas del Curso - Panel Docente',
                'notas' => $notas,
                'curso_id' => $cursoId
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar notas: ' . $e->getMessage());
            return view('docente.calificaciones.notas', [
                'title' => 'Notas del Curso - Panel Docente',
                'notas' => [],
                'curso_id' => $cursoId
            ]);
        }
    }
}