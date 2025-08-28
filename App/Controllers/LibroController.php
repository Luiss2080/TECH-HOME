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
}
