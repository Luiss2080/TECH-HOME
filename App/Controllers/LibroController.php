<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Session;
use App\Models\Libro;
use App\Models\Categoria;
use App\Models\User;
use App\Services\LibroService;
use Exception;

class LibroController extends Controller
{
    private LibroService $libroService;

    public function __construct()
    {
        $this->libroService = new LibroService();
    }

    // ==========================================
    // MÉTODOS PÚBLICOS - VISUALIZACIÓN
    // ==========================================

    /**
     * Mostrar listado público de libros
     */
    public function index()
    {
        try {
            $filtros = [
                'categoria' => $_GET['categoria'] ?? null,
                'autor' => $_GET['autor'] ?? null,
                'editorial' => $_GET['editorial'] ?? null,
                'tipo' => $_GET['tipo'] ?? null, // gratuito|pago
                'buscar' => $_GET['buscar'] ?? null,
                'orden' => $_GET['orden'] ?? 'titulo'
            ];

            $page = max(1, intval($_GET['page'] ?? 1));
            $perPage = 12;

            $resultado = $this->libroService->getLibrosFiltrados($filtros, $page, $perPage);
            $categorias = $this->libroService->getCategorias();
            
            return view('libros/index', [
                'libros' => $resultado['libros'],
                'total' => $resultado['total'],
                'page' => $page,
                'perPage' => $perPage,
                'filtros' => $filtros,
                'categorias' => $categorias,
                'title' => 'Catálogo de Libros'
            ]);
        } catch (Exception $e) {
            return view('errors/500', [
                'error' => 'Error al cargar los libros: ' . $e->getMessage(),
                'title' => 'Error'
            ]);
        }
    }

    /**
     * Mostrar listado de libros
     */
    public function libros()
    {
        return $this->index();
    }

    /**
     * Mostrar detalles de un libro
     */
    public function show(int $id)
    {
        try {
            $libro = $this->libroService->obtenerLibro($id);
            
            if (!$libro || $libro['estado'] != 1) {
                return view('errors/404', [
                    'message' => 'El libro solicitado no existe o no está disponible.',
                    'title' => 'Libro no encontrado'
                ]);
            }

            // Libros relacionados (misma categoría)
            $librosRelacionados = $this->libroService->getLibrosRelacionados($id, $libro['categoria_id']);
            
            return view('libros/show', [
                'libro' => $libro,
                'librosRelacionados' => $librosRelacionados,
                'title' => $libro['titulo']
            ]);
        } catch (Exception $e) {
            return view('errors/500', [
                'error' => 'Error al cargar el libro: ' . $e->getMessage(),
                'title' => 'Error'
            ]);
        }
    }

    // ==========================================
    // MÉTODOS ADMINISTRATIVOS
    // ==========================================
    // ==========================================

    /**
     * Panel de administración de libros
     */
    public function admin()
    {
        try {
            $this->verificarRolAdmin();

            $filtros = [
                'estado' => $_GET['estado'] ?? null,
                'categoria' => $_GET['categoria'] ?? null,
                'stock_bajo' => isset($_GET['stock_bajo']),
                'buscar' => $_GET['buscar'] ?? null
            ];

            $page = max(1, intval($_GET['page'] ?? 1));
            $perPage = 15;

            $resultado = $this->libroService->getLibrosAdmin($filtros, $page, $perPage);
            $categorias = $this->libroService->getCategorias();
            $estadisticas = $this->libroService->getEstadisticasLibros();

            return view('admin/libros/index', [
                'libros' => $resultado['libros'],
                'total' => $resultado['total'],
                'page' => $page,
                'perPage' => $perPage,
                'filtros' => $filtros,
                'categorias' => $categorias,
                'estadisticas' => $estadisticas,
                'title' => 'Administrar Libros'
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar el panel de administración: ' . $e->getMessage());
            return redirect('/admin');
        }
    }

    /**
     * Formulario para crear libro
     */
    public function create()
    {
        try {
            $this->verificarRolAdmin();
            $categorias = $this->libroService->getCategorias();

            return view('admin/libros/create', [
                'categorias' => $categorias,
                'title' => 'Crear Nuevo Libro'
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar el formulario: ' . $e->getMessage());
            return redirect('/admin/libros');
        }
    }

    /**
     * Vista para crear libro
     */
    public function crearLibro()
    {
        return $this->create();
    }

    /**
     * Procesar creación de libro
     */
    public function store(Request $request)
    {
        try {
            $this->verificarRolAdmin();
            $this->verificarCsrfToken();

            $datos = $request->all();
            $resultado = $this->libroService->crearLibro($datos);

            if ($resultado['success']) {
                Session::flash('success', 'Libro creado exitosamente.');
                return redirect('/admin/libros');
            } else {
                Session::flash('error', $resultado['message']);
                return redirect('/admin/libros/crear');
            }
        } catch (Exception $e) {
            Session::flash('error', 'Error al crear el libro: ' . $e->getMessage());
            return redirect('/admin/libros/crear');
        }
    }

    /**
     * Formulario para editar libro
     */
    public function edit(int $id)
    {
        try {
            $this->verificarRolAdmin();
            
            $libro = $this->libroService->obtenerLibro($id);
            if (!$libro) {
                Session::flash('error', 'El libro especificado no existe.');
                return redirect('/admin/libros');
            }

            $categorias = $this->libroService->getCategorias();

            return view('admin/libros/edit', [
                'libro' => $libro,
                'categorias' => $categorias,
                'title' => 'Editar Libro'
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar el libro: ' . $e->getMessage());
            return redirect('/admin/libros');
        }
    }

    /**
     * Procesar actualización de libro
     */
    public function update(Request $request, int $id)
    {
        try {
            $this->verificarRolAdmin();
            $this->verificarCsrfToken();

            $datos = $request->all();
            $resultado = $this->libroService->actualizarLibro($id, $datos);

            if ($resultado['success']) {
                Session::flash('success', 'Libro actualizado exitosamente.');
                return redirect('/admin/libros');
            } else {
                Session::flash('error', $resultado['message']);
                return redirect('/admin/libros/' . $id . '/editar');
            }
        } catch (Exception $e) {
            Session::flash('error', 'Error al actualizar el libro: ' . $e->getMessage());
            return redirect('/admin/libros/' . $id . '/editar');
        }
    }

    /**
     * Eliminar libro
     */
    public function destroy(int $id)
    {
        try {
            $this->verificarRolAdmin();
            $this->verificarCsrfToken();

            $resultado = $this->libroService->eliminarLibro($id);

            if ($resultado['success']) {
                Session::flash('success', 'Libro eliminado exitosamente.');
            } else {
                Session::flash('error', $resultado['message']);
            }

            return redirect('/admin/libros');
        } catch (Exception $e) {
            Session::flash('error', 'Error al eliminar el libro: ' . $e->getMessage());
            return redirect('/admin/libros');
        }
    }

    /**
     * Cambiar estado del libro
     */
    public function toggleEstado(int $id)
    {
        try {
            $this->verificarRolAdmin();
            $this->verificarCsrfToken();

            $resultado = $this->libroService->cambiarEstado($id);

            return Response::json($resultado);
        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Actualizar stock
     */
    public function actualizarStock(Request $request, int $id)
    {
        try {
            $this->verificarRolAdmin();
            $this->verificarCsrfToken();

            $nuevoStock = intval($request->input('stock'));
            $resultado = $this->libroService->actualizarStock($id, $nuevoStock);

            return Response::json($resultado);
        } catch (Exception $e) {
            return Response::json([
                'success' => false,
                'message' => 'Error al actualizar el stock: ' . $e->getMessage()
            ]);
        }
    }

    // ==========================================
    // REPORTES Y ESTADÍSTICAS
    // ==========================================

    /**
     * Ver descargas del libro
     */
    public function verDescargas(int $id)
    {
        try {
            $this->verificarRolAdmin();

            $libro = $this->libroService->obtenerLibro($id);
            if (!$libro) {
                Session::flash('error', 'El libro especificado no existe.');
                return redirect('/admin/libros');
            }

            $page = max(1, intval($_GET['page'] ?? 1));
            $perPage = 20;
            
            $resultado = $this->libroService->getDescargasLibro($id, $page, $perPage);

            return view('admin/libros/descargas', [
                'libro' => $libro,
                'descargas' => $resultado['descargas'],
                'total' => $resultado['total'],
                'page' => $page,
                'perPage' => $perPage,
                'title' => 'Descargas de ' . $libro['titulo']
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar las descargas: ' . $e->getMessage());
            return redirect('/admin/libros');
        }
    }

    /**
     * Reporte de libros
     */
    public function reporte()
    {
        try {
            $this->verificarRolAdmin();

            $tipo = $_GET['tipo'] ?? 'general';
            $formato = $_GET['formato'] ?? 'web';

            switch ($tipo) {
                case 'descargas':
                    $datos = $this->libroService->getReporteDescargas($_GET);
                    break;
                case 'stock':
                    $datos = $this->libroService->getReporteStock($_GET);
                    break;
                case 'categorias':
                    $datos = $this->libroService->getReporteCategorias($_GET);
                    break;
                default:
                    $datos = $this->libroService->getReporteGeneral($_GET);
            }

            if ($formato === 'excel') {
                return $this->exportarExcel($datos, $tipo);
            }

            return view('admin/libros/reporte', [
                'datos' => $datos,
                'tipo' => $tipo,
                'filtros' => $_GET,
                'title' => 'Reporte de Libros'
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al generar el reporte: ' . $e->getMessage());
            return redirect('/admin/libros');
        }
    }

    // ==========================================
    // MÉTODOS API AJAX
    // ==========================================

    /**
     * Buscar libros por AJAX
     */
    public function buscar(Request $request)
    {
        try {
            $termino = $request->input('q', '');
            $limit = min(20, intval($request->input('limit', 10)));

            if (strlen($termino) < 2) {
                return Response::json(['libros' => []]);
            }

            $libros = $this->libroService->buscarLibros($termino, $limit);

            return Response::json(['libros' => $libros]);
        } catch (Exception $e) {
            return Response::json([
                'error' => 'Error en la búsqueda: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener información del libro por AJAX
     */
    public function info(int $id)
    {
        try {
            $libro = $this->libroService->obtenerLibro($id);
            
            if (!$libro) {
                return Response::json(['error' => 'Libro no encontrado'], 404);
            }

            return Response::json(['libro' => $libro]);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Verificar disponibilidad de descarga
     */
    public function verificarDisponibilidad(int $id)
    {
        try {
            if (!Session::has('user_id')) {
                return Response::json([
                    'disponible' => false,
                    'razon' => 'Debes iniciar sesión'
                ]);
            }

            $resultado = $this->libroService->verificarDisponibilidad($id);
            return Response::json($resultado);
        } catch (Exception $e) {
            return Response::json([
                'disponible' => false,
                'razon' => 'Error del servidor'
            ]);
        }
    }

    // ==========================================
    // MÉTODOS PRIVADOS
    // ==========================================

    /**
     * Verificar que el usuario tenga rol de administrador
     */
    private function verificarRolAdmin()
    {
        if (!Session::has('user_id')) {
            throw new Exception('Acceso no autorizado');
        }

        $user = User::find(Session::get('user_id'));
        if (!$user || !$user->hasRole('admin')) {
            throw new Exception('Permisos insuficientes');
        }
    }

    /**
     * Verificar token CSRF
     */
    private function verificarCsrfToken()
    {
        $token = $_POST['_token'] ?? $_GET['_token'] ?? null;
        if (!$token || !hash_equals(Session::get('csrf_token'), $token)) {
            throw new Exception('Token de seguridad inválido');
        }
    }

    /**
     * Servir archivo para descarga
     */
    private function servirArchivo(string $rutaArchivo)
    {
        if (!file_exists($rutaArchivo)) {
            throw new Exception('El archivo no existe en el servidor');
        }

        $nombreArchivo = basename($rutaArchivo);
        $mimeType = mime_content_type($rutaArchivo);
        $tamaño = filesize($rutaArchivo);

        // Headers para descarga
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        header('Content-Length: ' . $tamaño);
        header('Cache-Control: no-cache, must-revalidate');
        header('Pragma: no-cache');

        // Leer y enviar archivo
        readfile($rutaArchivo);
        exit;
    }

    // ==========================================
    // MÉTODOS DE INTERACCIÓN - FAVORITOS Y CALIFICACIONES
    // ==========================================

    /**
     * Toggle favorito para un libro
     */
    public function toggleFavorito(Request $request, int $id)
    {
        try {
            $user = auth();
            if (!$user) {
                return Response::json(['error' => 'Debes iniciar sesión'], 401);
            }

            $resultado = $this->libroService->toggleFavorito($id, $user->id);
            
            return Response::json([
                'success' => true,
                'message' => $resultado['accion'] === 'agregado' ? 'Libro agregado a favoritos' : 'Libro eliminado de favoritos',
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Calificar un libro
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

            $resultado = $this->libroService->calificarLibro($id, $user->id, $calificacion, $comentario);
            
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
     * Descargar un libro
     */
    public function descargar(Request $request, int $id)
    {
        try {
            $user = auth();
            if (!$user) {
                Session::flash('error', 'Debes iniciar sesión para descargar libros');
                return redirect(route('libro.show', ['id' => $id]));
            }

            $libro = $this->libroService->obtenerLibro($id);
            if (!$libro) {
                Session::flash('error', 'Libro no encontrado');
                return redirect(route('libros'));
            }

            // Verificar disponibilidad
            if (!$libro['esta_disponible']) {
                Session::flash('error', 'Este libro no está disponible para descarga');
                return redirect(route('libro.show', ['id' => $id]));
            }

            // Registrar la descarga
            $this->libroService->registrarDescarga($id, $user->id);
            
            // Obtener la ruta del archivo
            $rutaArchivo = $_SERVER['DOCUMENT_ROOT'] . '/files/libros/' . $libro['archivo_pdf'];
            
            if (!file_exists($rutaArchivo)) {
                Session::flash('error', 'El archivo PDF no está disponible');
                return redirect('/libros/' . $id);
            }

            // Iniciar descarga
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $libro['titulo'] . '.pdf"');
            header('Content-Length: ' . filesize($rutaArchivo));
            readfile($rutaArchivo);
            exit;
            
        } catch (Exception $e) {
            Session::flash('error', 'Error al descargar: ' . $e->getMessage());
            return redirect('/libros/' . $id);
        }
    }

    /**
     * Obtener calificaciones de un libro
     */
    public function getCalificaciones(int $id)
    {
        try {
            $calificaciones = $this->libroService->getCalificacionesLibro($id);
            return Response::json(['success' => true, 'data' => $calificaciones]);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar página de favoritos del usuario
     */
    public function misFavoritos()
    {
        try {
            $user = auth();
            if (!$user) {
                return redirect(route('login'));
            }

            $favoritos = $this->libroService->getFavoritosUsuario($user->id);
            
            return view('libros/favoritos', [
                'libros' => $favoritos,
                'title' => 'Mis Libros Favoritos'
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar favoritos: ' . $e->getMessage());
            return redirect(route('libros'));
        }
    }

    /**
     * Exportar datos a Excel
     */
    private function exportarExcel(array $datos, string $tipo)
    {
        // Implementar exportación a Excel si es necesario
        // Por ahora, devolver CSV simple
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="reporte_libros_' . $tipo . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Escribir CSV basado en el tipo de reporte
        if (!empty($datos)) {
            fputcsv($output, array_keys($datos[0]));
            foreach ($datos as $fila) {
                fputcsv($output, $fila);
            }
        }
        
        fclose($output);
        exit;
    }
}
