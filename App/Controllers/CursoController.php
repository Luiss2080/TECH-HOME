<?php

namespace App\Controllers;

use App\Services\CursoService;
use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;
use Core\Validation;
use Exception;

class CursoController extends Controller
{
    private $cursoService;

    public function __construct()
    {
        parent::__construct();
        $this->cursoService = new CursoService();
    }

    // ==========================================
    // MÉTODOS PRINCIPALES DE GESTIÓN
    // ==========================================

    /**
     * Mostrar listado de cursos
     */
    public function cursos()
    {
        try {
            $user = auth();
            $isDocente = $user && $user->hasRole('docente');
            
            // Si es docente, mostrar solo sus cursos
            if ($isDocente) {
                $cursos = $this->cursoService->getCursosByDocente($user->id);
            } else {
                $cursos = $this->cursoService->getAllCursos();
            }
            
            $estadisticas = $this->cursoService->getEstadisticasCursos();
            
            return view('cursos.index', [
                'title' => 'Gestión de Cursos',
                'cursos' => $cursos,
                'estadisticas' => $estadisticas,
                'isDocente' => $isDocente
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar cursos: ' . $e->getMessage());
            return view('cursos.index', [
                'title' => 'Gestión de Cursos',
                'cursos' => [],
                'estadisticas' => [],
                'isDocente' => false
            ]);
        }
    }

    /**
     * Mostrar catálogo de cursos (vista pública)
     */
    public function catalogo()
    {
        try {
            $cursos = $this->cursoService->getAllCursos();
            $categorias = $this->cursoService->getAllCategoriasCursos();
            
            return view('cursos.catalogo', [
                'title' => 'Catálogo de Cursos',
                'cursos' => $cursos,
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar catálogo: ' . $e->getMessage());
            return view('cursos.catalogo', [
                'title' => 'Catálogo de Cursos',
                'cursos' => [],
                'categorias' => []
            ]);
        }
    }

    /**
     * Mostrar formulario de creación de curso
     */
    public function crearCurso()
    {
        try {
            $categorias = $this->cursoService->getAllCategoriasCursos();
            $docentes = $this->cursoService->getAllDocentes();
            
            return view('cursos.crear', [
                'title' => 'Crear Nuevo Curso',
                'categorias' => $categorias,
                'docentes' => $docentes
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar formulario: ' . $e->getMessage());
            return redirect(route('cursos'));
        }
    }

    /**
     * Procesar creación de curso
     */
    public function guardarCurso(Request $request)
    {
        try {
            // Validaciones simplificadas para cursos con videos de YouTube
            $rules = [
                'titulo' => 'required|min:5|max:200',
                'descripcion' => 'required|min:10',
                'video_url' => 'required|url',
                'docente_id' => 'required|numeric',
                'categoria_id' => 'required|numeric',
                'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
                'estado' => 'required|in:Borrador,Publicado,Archivado'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->errors());
                Session::flash('old', $request->all());
                return redirect(route('cursos.crear'));
            }

            // Verificar que sea una URL de YouTube válida
            if (!$this->isValidYoutubeUrl($request->input('video_url'))) {
                Session::flash('error', 'La URL debe ser un enlace válido de YouTube.');
                Session::flash('old', $request->all());
                return redirect(route('cursos.crear'));
            }

            // Preparar datos del curso
            $cursoData = [
                'titulo' => trim($request->input('titulo')),
                'descripcion' => trim($request->input('descripcion')),
                'video_url' => trim($request->input('video_url')),
                'docente_id' => (int)$request->input('docente_id'),
                'categoria_id' => (int)$request->input('categoria_id'),
                'imagen_portada' => $request->input('imagen_portada'),
                'nivel' => $request->input('nivel'),
                'estado' => $request->input('estado') ?: 'Borrador',
                'es_gratuito' => $request->input('es_gratuito') ? 1 : 0
            ];

            // Si es docente, solo puede crear cursos asignados a sí mismo
            $user = auth();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                $cursoData['docente_id'] = $user->id;
            }

            $cursoId = $this->cursoService->createCurso($cursoData);

            Session::flash('success', 'Curso creado exitosamente.');
            return redirect(route('cursos'));
        } catch (Exception $e) {
            Session::flash('error', 'Error al crear curso: ' . $e->getMessage());
            Session::flash('old', $request->all());
            return redirect(route('cursos.crear'));
        }
    }

    /**
     * Mostrar formulario de edición de curso
     */
    public function editarCurso(Request $request, $id)
    {
        try {
            $curso = $this->cursoService->getCursoById($id);
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado.');
                return redirect(route('cursos'));
            }

            // Verificar permisos si es docente
            $user = auth();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                if ($curso['docente_id'] != $user->id) {
                    Session::flash('error', 'No tienes permisos para editar este curso.');
                    return redirect(route('cursos'));
                }
            }

            $categorias = $this->cursoService->getAllCategoriasCursos();
            $docentes = $this->cursoService->getAllDocentes();

            return view('cursos.editar', [
                'title' => 'Editar Curso - ' . $curso['titulo'],
                'curso' => $curso,
                'categorias' => $categorias,
                'docentes' => $docentes
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar curso: ' . $e->getMessage());
            return redirect(route('cursos'));
        }
    }

    /**
     * Procesar actualización de curso
     */
    public function actualizarCurso(Request $request, $id)
    {
        try {
            // Verificar que el curso existe
            $curso = $this->cursoService->getCursoById($id);
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado.');
                return redirect(route('cursos'));
            }

            // Verificar permisos si es docente
            $user = auth();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                if ($curso['docente_id'] != $user->id) {
                    Session::flash('error', 'No tienes permisos para editar este curso.');
                    return redirect(route('cursos'));
                }
            }

            // Validaciones simplificadas
            $rules = [
                'titulo' => 'required|min:5|max:200',
                'descripcion' => 'required|min:10',
                'video_url' => 'required|url',
                'docente_id' => 'required|numeric',
                'categoria_id' => 'required|numeric',
                'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
                'estado' => 'required|in:Borrador,Publicado,Archivado'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->errors());
                Session::flash('old', $request->all());
                return redirect(route('cursos.editar', ['id' => $id]));
            }

            // Verificar que sea una URL de YouTube válida
            if (!$this->isValidYoutubeUrl($request->input('video_url'))) {
                Session::flash('error', 'La URL debe ser un enlace válido de YouTube.');
                Session::flash('old', $request->all());
                return redirect(route('cursos.editar', ['id' => $id]));
            }

            // Preparar datos de actualización
            $cursoData = [
                'titulo' => trim($request->input('titulo')),
                'descripcion' => trim($request->input('descripcion')),
                'video_url' => trim($request->input('video_url')),
                'docente_id' => (int)$request->input('docente_id'),
                'categoria_id' => (int)$request->input('categoria_id'),
                'imagen_portada' => $request->input('imagen_portada'),
                'nivel' => $request->input('nivel'),
                'estado' => $request->input('estado'),
                'es_gratuito' => $request->input('es_gratuito') ? 1 : 0
            ];

            // Si es docente, mantener la asignación a sí mismo
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                $cursoData['docente_id'] = $user->id;
            }

            $this->cursoService->updateCurso($id, $cursoData);

            Session::flash('success', 'Curso actualizado exitosamente.');
            return redirect(route('cursos'));
        } catch (Exception $e) {
            Session::flash('error', 'Error al actualizar curso: ' . $e->getMessage());
            Session::flash('old', $request->all());
            return redirect(route('cursos.editar', ['id' => $id]));
        }
    }

    /**
     * Eliminar curso
     */
    public function eliminarCurso(Request $request, $id)
    {
        try {
            // Verificar que el curso existe
            $curso = $this->cursoService->getCursoById($id);
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado.');
                return redirect(route('cursos'));
            }

            // Verificar permisos si es docente
            $user = auth();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                if ($curso['docente_id'] != $user->id) {
                    Session::flash('error', 'No tienes permisos para eliminar este curso.');
                    return redirect(route('cursos'));
                }
            }

            $this->cursoService->deleteCurso($id);

            Session::flash('success', 'Curso eliminado exitosamente.');
            return redirect(route('cursos'));
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('cursos'));
        }
    }

    /**
     * Ver detalles del curso
     */
    public function verCurso(Request $request, $id)
    {
        try {
            $curso = $this->cursoService->getCursoById($id);
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado.');
                return redirect(route('cursos'));
            }

            $user = auth();
            $puedeEditar = false;
            $estaInscrito = false;
            $puedeInscribirse = false;
            $progreso = null;
            
            if ($user) {
                $puedeEditar = $user->hasRole('administrador') || 
                              ($user->hasRole('docente') && $curso['docente_id'] == $user->id);
                
                // Verificar si es estudiante y está inscrito
                if ($user->hasRole('estudiante')) {
                    $estaInscrito = $this->cursoService->estaInscrito($user->id, $id);
                    
                    // Si es curso gratuito y no está inscrito, inscribirlo automáticamente
                    if (!$estaInscrito && $curso['es_gratuito'] == 1) {
                        $this->cursoService->inscribirEstudiante($user->id, $id);
                        $estaInscrito = true;
                        Session::flash('success', '¡Te has inscrito automáticamente a este curso gratuito!');
                    }
                    
                    // Si está inscrito, obtener su progreso
                    if ($estaInscrito) {
                        $progreso = $this->cursoService->getProgresoEstudiante($user->id, $id);
                    }
                    
                    // Puede inscribirse si no está inscrito y el curso está publicado
                    $puedeInscribirse = !$estaInscrito && $curso['estado'] === 'Publicado';
                }
            }

            return view('cursos.ver', [
                'title' => $curso['titulo'],
                'curso' => $curso,
                'puedeEditar' => $puedeEditar,
                'estaInscrito' => $estaInscrito,
                'puedeInscribirse' => $puedeInscribirse,
                'progreso' => $progreso
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar curso: ' . $e->getMessage());
            return redirect(route('cursos'));
        }
    }

    /**
     * Cambiar estado del curso
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $curso = $this->cursoService->getCursoById($id);
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado.');
                return redirect(route('cursos'));
            }

            // Verificar permisos si es docente
            $user = auth();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                if ($curso['docente_id'] != $user->id) {
                    Session::flash('error', 'No tienes permisos para cambiar el estado de este curso.');
                    return redirect(route('cursos'));
                }
            }

            $nuevoEstado = $request->input('estado');
            if (!in_array($nuevoEstado, ['Borrador', 'Publicado', 'Archivado'])) {
                Session::flash('error', 'Estado inválido.');
                return redirect(route('cursos'));
            }

            $this->cursoService->changeStatus($id, $nuevoEstado);

            Session::flash('success', "Estado del curso cambiado a '{$nuevoEstado}' exitosamente.");
            return redirect(route('cursos'));
        } catch (Exception $e) {
            Session::flash('error', 'Error al cambiar estado: ' . $e->getMessage());
            return redirect(route('cursos'));
        }
    }

    /**
     * Inscribir estudiante a un curso
     */
    public function inscribirCurso(Request $request, $id)
    {
        try {
            $user = auth();
            if (!$user || !$user->hasRole('estudiante')) {
                Session::flash('error', 'Solo los estudiantes pueden inscribirse a cursos.');
                return redirect(route('cursos.ver', ['id' => $id]));
            }

            $curso = $this->cursoService->getCursoById($id);
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado.');
                return redirect(route('cursos'));
            }

            if ($curso['estado'] !== 'Publicado') {
                Session::flash('error', 'El curso no está disponible para inscripción.');
                return redirect(route('cursos.ver', ['id' => $id]));
            }

            // Verificar si ya está inscrito
            if ($this->cursoService->estaInscrito($user->id, $id)) {
                Session::flash('info', 'Ya estás inscrito en este curso.');
                return redirect(route('cursos.ver', ['id' => $id]));
            }

            // Para cursos de pago, aquí se debería procesar el pago
            if ($curso['es_gratuito'] == 0) {
                // TODO: Implementar procesamiento de pago
                Session::flash('info', 'Los cursos de pago aún no están disponibles. Próximamente...');
                return redirect(route('cursos.ver', ['id' => $id]));
            }

            // Inscribir al estudiante
            $this->cursoService->inscribirEstudiante($user->id, $id);

            Session::flash('success', '¡Te has inscrito exitosamente al curso!');
            return redirect(route('cursos.cursando', ['id' => $id]));
        } catch (Exception $e) {
            Session::flash('error', 'Error al inscribirse: ' . $e->getMessage());
            return redirect(route('cursos.ver', ['id' => $id]));
        }
    }

    /**
     * Actualizar progreso del estudiante
     */
    public function actualizarProgreso(Request $request, $id)
    {
        try {
            $user = auth();
            if (!$user || !$user->hasRole('estudiante')) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Verificar que está inscrito
            if (!$this->cursoService->estaInscrito($user->id, $id)) {
                return response()->json(['error' => 'No estás inscrito en este curso'], 403);
            }

            // Si solo es actualización de acceso
            if ($request->input('solo_acceso')) {
                $this->cursoService->actualizarUltimoAcceso($user->id, $id);
                return response()->json(['success' => true]);
            }

            $porcentaje = (int)$request->input('progreso', 0);
            $tiempoAdicional = (int)$request->input('tiempo_adicional', 0);

            // Obtener progreso actual
            $progresoActual = $this->cursoService->getProgresoEstudiante($user->id, $id);
            $tiempoTotal = ($progresoActual['tiempo_estudiado'] ?? 0) + $tiempoAdicional;

            $this->cursoService->actualizarProgreso($user->id, $id, $porcentaje, $tiempoTotal);

            return response()->json([
                'success' => true,
                'message' => 'Progreso actualizado correctamente',
                'nuevo_progreso' => $porcentaje,
                'tiempo_total' => $tiempoTotal
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar página de confirmación para eliminar curso
     */
    public function confirmarEliminar(Request $request, $id)
    {
        try {
            $curso = $this->cursoService->getCursoById($id);
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado.');
                return redirect(route('cursos'));
            }

            // Verificar permisos
            $user = auth();
            if ($user && $user->hasRole('docente') && !$user->hasRole('administrador')) {
                if ($curso['docente_id'] != $user->id) {
                    Session::flash('error', 'No tienes permisos para eliminar este curso.');
                    return redirect(route('cursos'));
                }
            }

            return view('cursos.eliminar', [
                'title' => 'Eliminar Curso',
                'curso' => $curso
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar curso: ' . $e->getMessage());
            return redirect(route('cursos'));
        }
    }

    // ==========================================
    // MÉTODOS AJAX SIMPLIFICADOS
    // ==========================================

    /**
     * Obtener estadísticas para AJAX
     */
    public function ajaxEstadisticas()
    {
        try {
            header('Content-Type: application/json');

            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $estadisticas = $this->cursoService->getEstadisticasCursos();

            echo json_encode([
                'success' => true,
                'data' => $estadisticas
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
     * Buscar cursos (AJAX)
     */
    public function buscarCursos(Request $request)
    {
        try {
            header('Content-Type: application/json');

            $termino = $request->input('q', '');
            $categoria = $request->input('categoria', '');
            $nivel = $request->input('nivel', '');
            $estado = $request->input('estado', '');

            $cursos = $this->cursoService->buscarCursos($termino, $categoria, $nivel, $estado);

            echo json_encode([
                'success' => true,
                'data' => $cursos
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

    // ==========================================
    // MÉTODOS AUXILIARES
    // ==========================================

    /**
     * Validar si una URL es de YouTube válida
     */
    private function isValidYoutubeUrl(string $url): bool
    {
        $patterns = [
            '/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/',
            '/youtube\.com\/v\/([a-zA-Z0-9_-]{11})/'
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Vista de curso en progreso para estudiantes
     */
    public function cursando(Request $request, $id)
    {
        try {
            $user = auth();
            if (!$user || !$user->hasRole('estudiante')) {
                Session::flash('error', 'Solo los estudiantes pueden acceder a esta sección.');
                return redirect(route('cursos.ver', ['id' => $id]));
            }

            $curso = $this->cursoService->getCursoById((int)$id);
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado.');
                return redirect(route('cursos'));
            }

            // Verificar que el estudiante esté inscrito
            if (!$this->cursoService->estaInscrito($user->id, (int)$id)) {
                Session::flash('error', 'Debes estar inscrito en este curso para acceder.');
                return redirect(route('cursos.ver', ['id' => $id]));
            }

            // Obtener progreso del estudiante
            $progreso = $this->cursoService->getProgresoEstudiante($user->id, (int)$id);
            
            // Obtener otros cursos en los que está inscrito
            $otrosCursos = $this->cursoService->getCursosInscrito($user->id, (int)$id);
            
            // Estadísticas del estudiante en este curso
            $estadisticas = $this->cursoService->getEstadisticasEstudiante($user->id, (int)$id);

            return view('cursos.cursando', [
                'title' => 'Cursando: ' . $curso['titulo'],
                'curso' => $curso,
                'progreso' => $progreso,
                'otrosCursos' => $otrosCursos,
                'estadisticas' => $estadisticas,
                'user' => $user
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar el curso: ' . $e->getMessage());
            return redirect(route('cursos'));
        }
    }
}
