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
            // Validaciones
            $rules = [
                'titulo' => 'required|min:5|max:200',
                'descripcion' => 'required|min:10',
                'docente_id' => 'required|numeric',
                'categoria_id' => 'required|numeric',
                'precio' => 'nullable|numeric',
                'duracion_horas' => 'nullable|numeric',
                'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
                'estado' => 'required|in:Borrador,Publicado,Archivado'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->errors());
                Session::flash('old', $request->all());
                return redirect(route('cursos.crear'));
            }

            // Preparar datos del curso
            $cursoData = [
                'titulo' => trim($request->input('titulo')),
                'descripcion' => trim($request->input('descripcion')),
                'contenido' => $request->input('contenido'),
                'docente_id' => (int)$request->input('docente_id'),
                'categoria_id' => (int)$request->input('categoria_id'),
                'imagen_portada' => $request->input('imagen_portada'),
                'precio' => $request->input('precio') ? (float)$request->input('precio') : 0.00,
                'duracion_horas' => $request->input('duracion_horas') ? (int)$request->input('duracion_horas') : 0,
                'nivel' => $request->input('nivel'),
                'requisitos' => $request->input('requisitos'),
                'objetivos' => $request->input('objetivos'),
                'estado' => $request->input('estado') ?: 'Borrador'
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

            // Validaciones
            $rules = [
                'titulo' => 'required|min:5|max:200',
                'descripcion' => 'required|min:10',
                'docente_id' => 'required|numeric',
                'categoria_id' => 'required|numeric',
                'precio' => 'nullable|numeric',
                'duracion_horas' => 'nullable|numeric',
                'nivel' => 'required|in:Principiante,Intermedio,Avanzado',
                'estado' => 'required|in:Borrador,Publicado,Archivado'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->errors());
                Session::flash('old', $request->all());
                return redirect(route('cursos.editar', ['id' => $id]));
            }

            // Preparar datos de actualización
            $cursoData = [
                'titulo' => trim($request->input('titulo')),
                'descripcion' => trim($request->input('descripcion')),
                'contenido' => $request->input('contenido'),
                'docente_id' => (int)$request->input('docente_id'),
                'categoria_id' => (int)$request->input('categoria_id'),
                'imagen_portada' => $request->input('imagen_portada'),
                'precio' => $request->input('precio') ? (float)$request->input('precio') : 0.00,
                'duracion_horas' => $request->input('duracion_horas') ? (int)$request->input('duracion_horas') : 0,
                'nivel' => $request->input('nivel'),
                'requisitos' => $request->input('requisitos'),
                'objetivos' => $request->input('objetivos'),
                'estado' => $request->input('estado')
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
            
            if ($user) {
                $puedeEditar = $user->hasRole('administrador') || 
                              ($user->hasRole('docente') && $curso['docente_id'] == $user->id);
            }

            return view('cursos.detalle', [
                'title' => $curso['titulo'],
                'curso' => $curso,
                'puedeEditar' => $puedeEditar
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

    // ==========================================
    // MÉTODOS PARA ESTUDIANTES
    // ==========================================

    /**
     * Inscribir estudiante a curso
     */
    public function inscribir(Request $request, $id)
    {
        try {
            $user = auth();
            if (!$user || !$user->hasRole('estudiante')) {
                Session::flash('error', 'Solo los estudiantes pueden inscribirse a cursos.');
                return redirect(route('cursos'));
            }

            $this->cursoService->inscribirEstudiante($id, $user->id);

            Session::flash('success', 'Te has inscrito exitosamente al curso.');
            return redirect(route('cursos.ver', ['id' => $id]));
        } catch (Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect(route('cursos.ver', ['id' => $id]));
        }
    }

    // ==========================================
    // MÉTODOS AJAX
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

            // Implementar lógica de búsqueda aquí
            // Por ahora retornar todos los cursos
            $cursos = $this->cursoService->getAllCursos();

            // Filtrar por término de búsqueda si se proporciona
            if (!empty($termino)) {
                $cursos = array_filter($cursos, function($curso) use ($termino) {
                    return stripos($curso['titulo'], $termino) !== false || 
                           stripos($curso['descripcion'], $termino) !== false;
                });
            }

            // Filtrar por categoría
            if (!empty($categoria)) {
                $cursos = array_filter($cursos, function($curso) use ($categoria) {
                    return $curso['categoria_id'] == $categoria;
                });
            }

            // Filtrar por nivel
            if (!empty($nivel)) {
                $cursos = array_filter($cursos, function($curso) use ($nivel) {
                    return $curso['nivel'] == $nivel;
                });
            }

            // Filtrar por estado
            if (!empty($estado)) {
                $cursos = array_filter($cursos, function($curso) use ($estado) {
                    return $curso['estado'] == $estado;
                });
            }

            echo json_encode([
                'success' => true,
                'data' => array_values($cursos)
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
    // MÉTODOS PARA FAVORITOS Y CALIFICACIONES
    // ==========================================

    /**
     * Toggle favorito para un curso
     */
    public function toggleFavorito(Request $request, int $id)
    {
        try {
            $user = auth();
            if (!$user) {
                return Response::json(['error' => 'Debes iniciar sesión'], 401);
            }

            $resultado = $this->cursoService->toggleFavorito($id, $user->id);
            
            return Response::json([
                'success' => true,
                'message' => $resultado['accion'] === 'agregado' ? 'Curso agregado a favoritos' : 'Curso eliminado de favoritos',
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Calificar un curso
     */
    public function calificar(Request $request, int $id)
    {
        try {
            $user = auth();
            if (!$user) {
                return Response::json(['error' => 'Debes iniciar sesión'], 401);
            }

            $calificacion = (int)$request->input('calificacion');
            $comentario = $request->input('comentario');

            if ($calificacion < 1 || $calificacion > 5) {
                return Response::json(['error' => 'La calificación debe estar entre 1 y 5'], 400);
            }

            $resultado = $this->cursoService->calificarCurso($id, $user->id, $calificacion, $comentario);
            
            return Response::json([
                'success' => true,
                'message' => 'Calificación guardada exitosamente',
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Obtener calificaciones de un curso
     */
    public function getCalificaciones(int $id)
    {
        try {
            $calificaciones = $this->cursoService->getCalificacionesCurso($id);
            return Response::json(['success' => true, 'data' => $calificaciones]);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar mis cursos favoritos
     */
    public function misFavoritos()
    {
        try {
            $user = auth();
            if (!$user) {
                return redirect(route('login'));
            }

            $favoritos = $this->cursoService->getFavoritosUsuario($user->id);
            
            return view('cursos.favoritos', [
                'cursos' => $favoritos,
                'title' => 'Mis Cursos Favoritos'
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar favoritos: ' . $e->getMessage());
            return redirect(route('cursos'));
        }
    }

    /**
     * Ver progreso de un curso
     */
    public function verProgreso(int $id)
    {
        try {
            $user = auth();
            if (!$user) {
                return redirect(route('login'));
            }

            $curso = $this->cursoService->getCurso($id);
            if (!$curso) {
                Session::flash('error', 'Curso no encontrado');
                return redirect(route('cursos'));
            }

            $progreso = $this->cursoService->getProgresoEstudiante($id, $user->id);
            
            if (empty($progreso)) {
                Session::flash('error', 'No estás inscrito en este curso');
                return redirect(route('curso.show', ['id' => $id]));
            }

            return view('cursos.progreso', [
                'curso' => $curso,
                'progreso' => $progreso,
                'title' => 'Progreso - ' . $curso['titulo']
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar progreso: ' . $e->getMessage());
            return redirect(route('cursos'));
        }
    }

    /**
     * Completar módulo de curso
     */
    public function completarModulo(Request $request, int $cursoId, int $moduloId)
    {
        try {
            $user = auth();
            if (!$user) {
                return Response::json(['error' => 'Debes iniciar sesión'], 401);
            }

            $exito = $this->cursoService->completarModulo($cursoId, $moduloId, $user->id);
            
            if ($exito) {
                return Response::json([
                    'success' => true,
                    'message' => 'Módulo completado exitosamente'
                ]);
            } else {
                return Response::json(['error' => 'No se pudo completar el módulo'], 500);
            }
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }
}
