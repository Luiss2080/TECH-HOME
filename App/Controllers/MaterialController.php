<?php

namespace App\Controllers;

use App\Services\MaterialService;
use Core\Controller;
use Core\Request;
use Core\Session;
use Core\Validation;
use Exception;

class MaterialController extends Controller
{
    private $materialService;

    public function __construct()
    {
        parent::__construct();
        $this->materialService = new MaterialService();
    }

    /**
     * Mostrar lista de materiales
     */
    public function index()
    {
        try {
            $materiales = $this->materialService->getAllMaterials();
            return view('admin.materiales.index', [
                'title' => 'Gestión de Materiales - Panel de Administración',
                'materiales' => $materiales
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar materiales: ' . $e->getMessage());
            return view('admin.materiales.index', [
                'title' => 'Gestión de Materiales - Panel de Administración',
                'materiales' => []
            ]);
        }
    }

    /**
     * Mostrar formulario de creación de material
     */
    public function crear()
    {
        try {
            $categorias = $this->materialService->getAllCategories();
            $docentes = $this->materialService->getAllDocentes();
            
            return view('admin.materiales.crear', [
                'title' => 'Crear Material - Panel de Administración',
                'categorias' => $categorias,
                'docentes' => $docentes
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar formulario: ' . $e->getMessage());
            return redirect(route('admin.materiales'));
        }
    }

    /**
     * Guardar nuevo material
     */
    public function guardar(Request $request)
    {
        try {
            // Validaciones
            $rules = [
                'titulo' => 'required|min:3|max:200',
                'descripcion' => 'nullable|max:1000',
                'tipo' => 'required|in:video,documento,presentacion,audio,enlace,otro',
                'categoria_id' => 'required|numeric',
                'docente_id' => 'required|numeric',
                'duracion' => 'nullable|numeric|min:0',
                'publico' => 'required|in:0,1',
                'estado' => 'required|in:0,1'
            ];

            // Validación condicional para archivo o enlace
            if ($request->input('tipo') === 'enlace' || !empty($request->input('enlace_externo'))) {
                $rules['enlace_externo'] = 'required|url|max:500';
            } else {
                $rules['archivo_upload'] = 'required';
            }

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->errors());
                Session::flash('old', $request->except(['archivo_upload']));
                return redirect(route('admin.materiales.crear'));
            }

            // Verificar que la categoría existe
            $categorias = $this->materialService->getAllCategories();
            $categoriaValida = false;
            foreach ($categorias as $categoria) {
                if ($categoria->id == $request->input('categoria_id')) {
                    $categoriaValida = true;
                    break;
                }
            }

            if (!$categoriaValida) {
                Session::flash('error', 'La categoría seleccionada no es válida.');
                Session::flash('old', $request->except(['archivo_upload']));
                return redirect(route('admin.materiales.crear'));
            }

            // Verificar que el docente existe
            $docentes = $this->materialService->getAllDocentes();
            $docenteValido = false;
            foreach ($docentes as $docente) {
                if ($docente->id == $request->input('docente_id')) {
                    $docenteValido = true;
                    break;
                }
            }

            if (!$docenteValido) {
                Session::flash('error', 'El docente seleccionado no es válido.');
                Session::flash('old', $request->except(['archivo_upload']));
                return redirect(route('admin.materiales.crear'));
            }

            // Preparar datos del material
            $materialData = [
                'titulo' => $request->input('titulo'),
                'descripcion' => $request->input('descripcion'),
                'tipo' => $request->input('tipo'),
                'enlace_externo' => $request->input('enlace_externo'),
                'duracion' => $request->input('duracion'),
                'categoria_id' => $request->input('categoria_id'),
                'docente_id' => $request->input('docente_id'),
                'imagen_preview' => $request->input('imagen_preview'),
                'publico' => $request->input('publico'),
                'estado' => $request->input('estado')
            ];

            // Agregar archivo si se subió
            if (isset($_FILES['archivo_upload']) && $_FILES['archivo_upload']['error'] === UPLOAD_ERR_OK) {
                $materialData['archivo_upload'] = $_FILES['archivo_upload'];
            }

            // Crear material
            $materialId = $this->materialService->createMaterial($materialData);

            Session::flash('success', 'Material creado exitosamente.');
            return redirect(route('admin.materiales'));
        } catch (Exception $e) {
            Session::flash('error', 'Error al crear material: ' . $e->getMessage());
            Session::flash('old', $request->except(['archivo_upload']));
            return redirect(route('admin.materiales.crear'));
        }
    }

    /**
     * Mostrar formulario de edición de material
     */
    public function editar(Request $request, $id)
    {
        try {
            $material = $this->materialService->getMaterialById($id);
            if (!$material) {
                Session::flash('error', 'Material no encontrado.');
                return redirect(route('admin.materiales'));
            }

            $categorias = $this->materialService->getAllCategories();
            $docentes = $this->materialService->getAllDocentes();

            return view('admin.materiales.editar', [
                'title' => 'Editar Material - Panel de Administración',
                'material' => $material,
                'categorias' => $categorias,
                'docentes' => $docentes
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar material: ' . $e->getMessage());
            return redirect(route('admin.materiales'));
        }
    }

    /**
     * Actualizar material existente
     */
    public function actualizar(Request $request, $id)
    {
        try {
            // Verificar que el material existe
            $material = $this->materialService->getMaterialById($id);
            if (!$material) {
                Session::flash('error', 'Material no encontrado.');
                return redirect(route('admin.materiales'));
            }

            // Validaciones
            $rules = [
                'titulo' => 'required|min:3|max:200',
                'descripcion' => 'nullable|max:1000',
                'tipo' => 'required|in:video,documento,presentacion,audio,enlace,otro',
                'categoria_id' => 'required|numeric',
                'docente_id' => 'required|numeric',
                'duracion' => 'nullable|numeric|min:0',
                'publico' => 'required|in:0,1',
                'estado' => 'required|in:0,1'
            ];

            // Validación condicional para enlace externo
            if ($request->input('tipo') === 'enlace' || !empty($request->input('enlace_externo'))) {
                $rules['enlace_externo'] = 'required|url|max:500';
            }

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->getErrors());
                Session::flash('old', $request->all());
                return redirect(route('admin.materiales.editar', ['id' => $id]));
            }

            // Verificar categoría y docente (similar al crear)
            $categorias = $this->materialService->getAllCategories();
            $categoriaValida = false;
            foreach ($categorias as $categoria) {
                if ($categoria->id == $request->input('categoria_id')) {
                    $categoriaValida = true;
                    break;
                }
            }

            if (!$categoriaValida) {
                Session::flash('error', 'La categoría seleccionada no es válida.');
                Session::flash('old', $request->except(['archivo_upload']));
                return redirect(route('admin.materiales.editar', ['id' => $id]));
            }

            $docentes = $this->materialService->getAllDocentes();
            $docenteValido = false;
            foreach ($docentes as $docente) {
                if ($docente->id == $request->input('docente_id')) {
                    $docenteValido = true;
                    break;
                }
            }

            if (!$docenteValido) {
                Session::flash('error', 'El docente seleccionado no es válido.');
                Session::flash('old', $request->except(['archivo_upload']));
                return redirect(route('admin.materiales.editar', ['id' => $id]));
            }

            // Preparar datos de actualización
            $materialData = [
                'titulo' => $request->input('titulo'),
                'descripcion' => $request->input('descripcion'),
                'tipo' => $request->input('tipo'),
                'enlace_externo' => $request->input('enlace_externo'),
                'duracion' => $request->input('duracion'),
                'categoria_id' => $request->input('categoria_id'),
                'docente_id' => $request->input('docente_id'),
                'imagen_preview' => $request->input('imagen_preview'),
                'publico' => $request->input('publico'),
                'estado' => $request->input('estado')
            ];

            // Agregar archivo si se subió uno nuevo
            if (isset($_FILES['archivo_upload']) && $_FILES['archivo_upload']['error'] === UPLOAD_ERR_OK) {
                $materialData['archivo_upload'] = $_FILES['archivo_upload'];
            }

            // Actualizar material
            $this->materialService->updateMaterial($id, $materialData);

            Session::flash('success', 'Material actualizado exitosamente.');
            return redirect(route('admin.materiales'));
        } catch (Exception $e) {
            Session::flash('error', 'Error al actualizar material: ' . $e->getMessage());
            Session::flash('old', $request->except(['archivo_upload']));
            return redirect(route('admin.materiales.editar', ['id' => $id]));
        }
    }

    /**
     * Eliminar material
     */
    public function eliminar(Request $request, $id)
    {
        try {
            // Verificar que el material existe
            $material = $this->materialService->getMaterialById($id);
            if (!$material) {
                Session::flash('error', 'Material no encontrado.');
                return redirect(route('admin.materiales'));
            }

            // Eliminar material
            $this->materialService->deleteMaterial($id);

            Session::flash('success', 'Material eliminado exitosamente.');
            return redirect(route('admin.materiales'));
        } catch (Exception $e) {
            Session::flash('error', 'Error al eliminar material: ' . $e->getMessage());
            return redirect(route('admin.materiales'));
        }
    }

    /**
     * Cambiar estado del material
     */
    public function cambiarEstado(Request $request, $id)
    {
        try {
            $material = $this->materialService->getMaterialById($id);
            if (!$material) {
                Session::flash('error', 'Material no encontrado.');
                return redirect(route('admin.materiales'));
            }

            $nuevoEstado = $request->input('estado');
            if (!in_array($nuevoEstado, ['0', '1'])) {
                Session::flash('error', 'Estado inválido.');
                return redirect(route('admin.materiales'));
            }

            $this->materialService->changeStatus($id, $nuevoEstado);

            $estadoTexto = $nuevoEstado == '1' ? 'activado' : 'desactivado';
            Session::flash('success', "Material {$estadoTexto} exitosamente.");
            return redirect(route('admin.materiales'));
        } catch (Exception $e) {
            Session::flash('error', 'Error al cambiar estado: ' . $e->getMessage());
            return redirect(route('admin.materiales'));
        }
    }

    /**
     * Cambiar visibilidad pública del material
     */
    public function cambiarVisibilidad(Request $request, $id)
    {
        try {
            $material = $this->materialService->getMaterialById($id);
            if (!$material) {
                Session::flash('error', 'Material no encontrado.');
                return redirect(route('admin.materiales'));
            }

            $nuevoEstado = $request->input('publico');
            if (!in_array($nuevoEstado, ['0', '1'])) {
                Session::flash('error', 'Visibilidad inválida.');
                return redirect(route('admin.materiales'));
            }

            $this->materialService->changePublicStatus($id, $nuevoEstado);

            $visibilidadTexto = $nuevoEstado == '1' ? 'público' : 'privado';
            Session::flash('success', "Material marcado como {$visibilidadTexto} exitosamente.");
            return redirect(route('admin.materiales'));
        } catch (Exception $e) {
            Session::flash('error', 'Error al cambiar visibilidad: ' . $e->getMessage());
            return redirect(route('admin.materiales'));
        }
    }

    /**
     * Buscar materiales con filtros
     */
    public function buscar(Request $request)
    {
        try {
            $filtros = [
                'buscar' => $request->input('buscar'),
                'tipo' => $request->input('tipo'),
                'categoria' => $request->input('categoria'),
                'docente' => $request->input('docente'),
                'estado' => $request->input('estado'),
                'publico' => $request->input('publico'),
                'orden' => $request->input('orden')
            ];

            $materiales = $this->materialService->searchMaterials($filtros);
            $categorias = $this->materialService->getAllCategories();
            $docentes = $this->materialService->getAllDocentes();

            return view('admin.materiales.buscar', [
                'title' => 'Buscar Materiales - Panel de Administración',
                'materiales' => $materiales,
                'categorias' => $categorias,
                'docentes' => $docentes,
                'filtros' => $filtros
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error en la búsqueda: ' . $e->getMessage());
            return redirect(route('admin.materiales'));
        }
    }

    /**
     * Ver detalles de un material
     */
    public function ver(Request $request, $id)
    {
        try {
            $material = $this->materialService->getMaterialById($id);
            if (!$material) {
                Session::flash('error', 'Material no encontrado.');
                return redirect(route('admin.materiales'));
            }

            // Obtener información adicional
            $docente = $material->docente();
            $categoria = $material->categoria();

            return view('admin.materiales.ver', [
                'title' => 'Detalles del Material - ' . $material->titulo,
                'material' => $material,
                'docente' => $docente,
                'categoria' => $categoria,
                'estadisticas' => $material->getEstadisticas()
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar material: ' . $e->getMessage());
            return redirect(route('admin.materiales'));
        }
    }

    /**
     * Descargar material
     */
    public function descargar(Request $request, $id)
    {
        try {
            $material = $this->materialService->getMaterialById($id);
            if (!$material) {
                Session::flash('error', 'Material no encontrado.');
                return redirect(route('admin.materiales'));
            }

            // Verificar permisos
            $userId = auth() ? auth()->id : null;
            if (!$this->materialService->canAccess($id, $userId)) {
                Session::flash('error', 'No tienes permisos para descargar este material.');
                return redirect(route('admin.materiales'));
            }

            // Si es enlace externo, redirigir
            if ($material->esEnlaceExterno()) {
                $this->materialService->registerDownload($id, $userId);
                return redirect($material->enlace_externo);
            }

            // Si es archivo local, servir archivo
            if ($material->esArchivoLocal()) {
                $rutaArchivo = BASE_PATH . 'public/materiales/' . ltrim($material->archivo, '/');
                
                if (!file_exists($rutaArchivo)) {
                    Session::flash('error', 'Archivo no encontrado en el servidor.');
                    return redirect(route('admin.materiales'));
                }

                // Registrar descarga
                $this->materialService->registerDownload($id, $userId);

                // Servir archivo
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($material->titulo . '.' . pathinfo($material->archivo, PATHINFO_EXTENSION)) . '"');
                header('Content-Length: ' . filesize($rutaArchivo));
                readfile($rutaArchivo);
                exit;
            }

            Session::flash('error', 'No hay archivo disponible para descargar.');
            return redirect(route('admin.materiales'));
        } catch (Exception $e) {
            Session::flash('error', 'Error al descargar material: ' . $e->getMessage());
            return redirect(route('admin.materiales'));
        }
    }

    /**
     * Duplicar material
     */
    public function duplicar(Request $request, $id)
    {
        try {
            $material = $this->materialService->getMaterialById($id);
            if (!$material) {
                Session::flash('error', 'Material no encontrado.');
                return redirect(route('admin.materiales'));
            }

            // Usar el docente actual por defecto
            $nuevoDocenteId = auth() ? auth()->id : $material->docente_id;
            
            $nuevoMaterialId = $this->materialService->duplicateMaterial($id, $nuevoDocenteId);

            Session::flash('success', 'Material duplicado exitosamente.');
            return redirect(route('admin.materiales.editar', ['id' => $nuevoMaterialId]));
        } catch (Exception $e) {
            Session::flash('error', 'Error al duplicar material: ' . $e->getMessage());
            return redirect(route('admin.materiales'));
        }
    }

    /**
     * Estadísticas generales de materiales (AJAX)
     */
    public function estadisticas(Request $request)
    {
        try {
            $stats = $this->materialService->getGeneralStats();

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener materiales por docente (AJAX)
     */
    public function porDocente(Request $request, $docenteId)
    {
        try {
            $materiales = $this->materialService->getMaterialsByDocente($docenteId);

            return response()->json([
                'success' => true,
                'data' => $materiales
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener materiales por categoría (AJAX)
     */
    public function porCategoria(Request $request, $categoriaId)
    {
        try {
            $materiales = $this->materialService->getMaterialsByCategoria($categoriaId);

            return response()->json([
                'success' => true,
                'data' => $materiales
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Método de compatibilidad con el sistema anterior
     */
    public function materiales()
    {
        return $this->index();
    }
}
