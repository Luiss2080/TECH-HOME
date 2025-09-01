<?php

namespace App\Controllers;

use App\Services\ComponenteService;
use Core\Controller;
use Core\Request;
use Core\Session;
use Core\Validation;
use Exception;

class ComponenteController extends Controller
{
    private $componenteService;

    public function __construct()
    {
        parent::__construct();
        $this->componenteService = new ComponenteService();
    }

    /**
     * Listado principal de componentes
     */
    public function componentes()
    {
        try {
            // Datos de prueba para evitar problemas con el service
            $componentes = [];
            $categorias = [];
            $marcas = [];
            $estadisticas = [
                'total_componentes' => 0,
                'agotados' => 0,
                'stock_bajo' => 0,
                'valor_inventario' => 0
            ];
            
            $filtros = [
                'busqueda' => $_GET['busqueda'] ?? '',
                'categoria_id' => $_GET['categoria_id'] ?? '',
                'estado' => $_GET['estado'] ?? '',
                'marca' => $_GET['marca'] ?? '',
                'stock_bajo' => false
            ];
            
            // Intentar cargar datos reales si no hay problemas
            try {
                $filtrosCompletos = [
                    'busqueda' => $_GET['busqueda'] ?? '',
                    'categoria_id' => $_GET['categoria_id'] ?? '',
                    'estado' => $_GET['estado'] ?? '',
                    'marca' => $_GET['marca'] ?? '',
                    'stock_bajo' => isset($_GET['stock_bajo']) ? (bool)$_GET['stock_bajo'] : false,
                    'pagina' => (int)($_GET['pagina'] ?? 1),
                    'por_pagina' => (int)($_GET['por_pagina'] ?? 20)
                ];

                $data = $this->componenteService->listarComponentes($filtrosCompletos);
                
                $componentes = $data['componentes'] ?? [];
                $categorias = $data['categorias'] ?? [];
                $marcas = $data['marcas'] ?? [];
                $estadisticas = $data['estadisticas'] ?? $estadisticas;
                $filtros = $filtrosCompletos;
            } catch (Exception $serviceError) {
                Session::flash('error', 'Error en el servicio: ' . $serviceError->getMessage());
            }
            
            return view('componentes.index', [
                'title' => 'Gestión de Componentes',
                'componentes' => $componentes,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'filtros' => $filtros,
                'estadisticas' => $estadisticas
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error crítico al cargar componentes: ' . $e->getMessage());
            return view('componentes.index', [
                'title' => 'Gestión de Componentes',
                'componentes' => [],
                'categorias' => [],
                'marcas' => [],
                'filtros' => [],
                'estadisticas' => [
                    'total_componentes' => 0,
                    'agotados' => 0,
                    'stock_bajo' => 0,
                    'valor_inventario' => 0
                ]
            ]);
        }
    }

    /**
     * Formulario para crear nuevo componente
     */
    public function crearComponente()
    {
        try {
            $categorias = $this->componenteService->obtenerCategoriasComponentes();
            
            return view('componentes.crear', [
                'title' => 'Crear Componente',
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar formulario: ' . $e->getMessage());
            return redirect(route('componentes'));
        }
    }

    /**
     * Guardar nuevo componente
     */
    public function guardarComponente(Request $request)
    {
        try {
            // Validaciones
            $rules = [
                'nombre' => 'required|min:3|max:200',
                'descripcion' => 'nullable|max:1000',
                'categoria_id' => 'required|integer',
                'codigo_producto' => 'nullable|max:50',
                'marca' => 'nullable|max:100',
                'modelo' => 'nullable|max:100',
                'precio' => 'required|numeric|min:0.01',
                'stock' => 'required|integer|min:0',
                'stock_minimo' => 'required|integer|min:0',
                'proveedor' => 'nullable|max:150',
                'estado' => 'required|in:Disponible,Agotado,Descontinuado'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->errors());
                Session::flash('old', $request->all());
                return redirect(route('componentes.crear'));
            }

            // Verificar que la categoría existe y es de tipo componente
            if (!$this->componenteService->categoriaValida($request->input('categoria_id'))) {
                Session::flash('error', 'La categoría seleccionada no es válida para componentes.');
                Session::flash('old', $request->all());
                return redirect(route('componentes.crear'));
            }

            // Verificar código de producto único si se proporciona
            $codigoProducto = $request->input('codigo_producto');
            if ($codigoProducto && $this->componenteService->codigoProductoExiste($codigoProducto)) {
                Session::flash('error', 'El código de producto ya está en uso.');
                Session::flash('old', $request->all());
                return redirect(route('componentes.crear'));
            }

            // Preparar datos del componente
            $componenteData = [
                'nombre' => trim($request->input('nombre')),
                'descripcion' => $request->input('descripcion'),
                'categoria_id' => (int)$request->input('categoria_id'),
                'codigo_producto' => $codigoProducto,
                'marca' => $request->input('marca'),
                'modelo' => $request->input('modelo'),
                'precio' => (float)$request->input('precio'),
                'stock' => (int)$request->input('stock'),
                'stock_minimo' => (int)$request->input('stock_minimo'),
                'proveedor' => $request->input('proveedor'),
                'estado' => $request->input('estado')
            ];

            // Procesar especificaciones técnicas si están presentes
            $especificaciones = $this->procesarEspecificaciones($request);
            if (!empty($especificaciones)) {
                $componenteData['especificaciones'] = json_encode($especificaciones, JSON_UNESCAPED_UNICODE);
            }

            // Crear componente
            $componenteId = $this->componenteService->crearComponente($componenteData);

            Session::flash('success', 'Componente creado exitosamente.');
            return redirect(route('componentes.ver', ['id' => $componenteId]));

        } catch (Exception $e) {
            Session::flash('error', 'Error al crear componente: ' . $e->getMessage());
            Session::flash('old', $request->all());
            return redirect(route('componentes.crear'));
        }
    }

    /**
     * Ver detalles de un componente
     */
    public function verComponente(Request $request, $id)
    {
        try {
            $componente = $this->componenteService->obtenerComponentePorId($id);
            
            if (!$componente) {
                Session::flash('error', 'Componente no encontrado.');
                return redirect(route('componentes'));
            }

            // Obtener historial de ventas del componente
            $historialVentas = $this->componenteService->obtenerHistorialVentas($id);
            
            // Obtener movimientos de stock
            $movimientosStock = $this->componenteService->obtenerMovimientosStock($id);

            return view('componentes.ver', [
                'title' => 'Ver Componente - ' . $componente->nombre,
                'componente' => $componente,
                'historial_ventas' => $historialVentas,
                'movimientos_stock' => $movimientosStock
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar componente: ' . $e->getMessage());
            return redirect(route('componentes'));
        }
    }

    /**
     * Formulario para editar componente
     */
    public function editarComponente(Request $request, $id)
    {
        try {
            $componente = $this->componenteService->obtenerComponentePorId($id);
            
            if (!$componente) {
                Session::flash('error', 'Componente no encontrado.');
                return redirect(route('componentes'));
            }

            $categorias = $this->componenteService->obtenerCategoriasComponentes();

            return view('componentes.editar', [
                'title' => 'Editar Componente - ' . $componente->nombre,
                'componente' => $componente,
                'categorias' => $categorias
            ]);
        } catch (Exception $e) {
            Session::flash('error', 'Error al cargar componente: ' . $e->getMessage());
            return redirect(route('componentes'));
        }
    }

    /**
     * Actualizar componente
     */
    public function actualizarComponente(Request $request, $id)
    {
        try {
            $componente = $this->componenteService->obtenerComponentePorId($id);
            
            if (!$componente) {
                Session::flash('error', 'Componente no encontrado.');
                return redirect(route('componentes'));
            }

            // Validaciones
            $rules = [
                'nombre' => 'required|min:3|max:200',
                'descripcion' => 'nullable|max:1000',
                'categoria_id' => 'required|integer',
                'codigo_producto' => 'nullable|max:50',
                'marca' => 'nullable|max:100',
                'modelo' => 'nullable|max:100',
                'precio' => 'required|numeric|min:0.01',
                'stock' => 'required|integer|min:0',
                'stock_minimo' => 'required|integer|min:0',
                'proveedor' => 'nullable|max:150',
                'estado' => 'required|in:Disponible,Agotado,Descontinuado'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                Session::flash('errors', $validator->errors());
                Session::flash('old', $request->all());
                return redirect(route('componentes.editar', ['id' => $id]));
            }

            // Verificar categoría válida
            if (!$this->componenteService->categoriaValida($request->input('categoria_id'))) {
                Session::flash('error', 'La categoría seleccionada no es válida para componentes.');
                Session::flash('old', $request->all());
                return redirect(route('componentes.editar', ['id' => $id]));
            }

            // Verificar código de producto único si cambió
            $codigoProducto = $request->input('codigo_producto');
            if ($codigoProducto && $codigoProducto !== $componente->codigo_producto) {
                if ($this->componenteService->codigoProductoExiste($codigoProducto, $id)) {
                    Session::flash('error', 'El código de producto ya está en uso por otro componente.');
                    Session::flash('old', $request->all());
                    return redirect(route('componentes.editar', ['id' => $id]));
                }
            }

            // Preparar datos actualizados
            $componenteData = [
                'nombre' => trim($request->input('nombre')),
                'descripcion' => $request->input('descripcion'),
                'categoria_id' => (int)$request->input('categoria_id'),
                'codigo_producto' => $codigoProducto,
                'marca' => $request->input('marca'),
                'modelo' => $request->input('modelo'),
                'precio' => (float)$request->input('precio'),
                'stock' => (int)$request->input('stock'),
                'stock_minimo' => (int)$request->input('stock_minimo'),
                'proveedor' => $request->input('proveedor'),
                'estado' => $request->input('estado')
            ];

            // Procesar especificaciones técnicas
            $especificaciones = $this->procesarEspecificaciones($request);
            if (!empty($especificaciones)) {
                $componenteData['especificaciones'] = json_encode($especificaciones, JSON_UNESCAPED_UNICODE);
            }

            // Actualizar componente
            $this->componenteService->actualizarComponente($id, $componenteData);

            Session::flash('success', 'Componente actualizado exitosamente.');
            return redirect(route('componentes.ver', ['id' => $id]));

        } catch (Exception $e) {
            Session::flash('error', 'Error al actualizar componente: ' . $e->getMessage());
            Session::flash('old', $request->all());
            return redirect(route('componentes.editar', ['id' => $id]));
        }
    }

    /**
     * Eliminar componente (cambiar estado a Descontinuado)
     */
    public function eliminarComponente(Request $request, $id)
    {
        try {
            $componente = $this->componenteService->obtenerComponentePorId($id);
            
            if (!$componente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Componente no encontrado.'
                ], 404);
            }

            // Verificar si el componente tiene ventas asociadas
            if ($this->componenteService->tieneVentasAsociadas($id)) {
                // No eliminar físicamente, solo cambiar estado
                $this->componenteService->descontinuarComponente($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Componente marcado como descontinuado debido a ventas asociadas.'
                ]);
            } else {
                // Eliminar físicamente si no tiene ventas
                $this->componenteService->eliminarComponente($id);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Componente eliminado exitosamente.'
                ]);
            }

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar componente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Ajustar stock de componente
     */
    public function ajustarStock(Request $request, $id)
    {
        try {
            $componente = $this->componenteService->obtenerComponentePorId($id);
            
            if (!$componente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Componente no encontrado.'
                ], 404);
            }

            // Validar datos de ajuste de stock
            $rules = [
                'tipo_movimiento' => 'required|in:entrada,salida,ajuste',
                'cantidad' => 'required|integer|min:1',
                'motivo' => 'required|min:5|max:255'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de ajuste inválidos.',
                    'errors' => $validator->errors()
                ], 400);
            }

            // Realizar ajuste de stock
            $resultado = $this->componenteService->ajustarStock(
                $id,
                $request->input('tipo_movimiento'),
                (int)$request->input('cantidad'),
                $request->input('motivo'),
                auth()->id
            );

            return response()->json([
                'success' => true,
                'message' => 'Stock ajustado exitosamente.',
                'nuevo_stock' => $resultado['nuevo_stock']
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al ajustar stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener componentes con stock bajo (AJAX)
     */
    public function stockBajo()
    {
        try {
            header('Content-Type: application/json');

            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $componentes = $this->componenteService->obtenerComponentesStockBajo();

            return response()->json([
                'success' => true,
                'data' => $componentes
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Búsqueda de componentes (AJAX)
     */
    public function buscar()
    {
        try {
            header('Content-Type: application/json');

            if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') {
                throw new Exception('Solo se permiten peticiones AJAX');
            }

            $termino = $_GET['q'] ?? '';
            $limite = (int)($_GET['limite'] ?? 10);

            $componentes = $this->componenteService->buscarComponentes($termino, $limite);

            return response()->json([
                'success' => true,
                'data' => $componentes
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // =============== MÉTODOS PARA INTEGRACIÓN CON VENTAS ===============

    /**
     * Verificar disponibilidad para venta (AJAX)
     */
    public function verificarDisponibilidad(Request $request, $id)
    {
        try {
            header('Content-Type: application/json');

            $cantidad = (int)$request->input('cantidad', 1);

            if ($cantidad <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cantidad debe ser mayor a 0'
                ], 400);
            }

            $disponibilidad = $this->componenteService->verificarDisponibilidadVenta($id, $cantidad);

            return response()->json([
                'success' => true,
                'data' => $disponibilidad
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al verificar disponibilidad: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reservar stock para venta (AJAX)
     */
    public function reservarStock(Request $request, $id)
    {
        try {
            header('Content-Type: application/json');

            // Validar datos
            $rules = [
                'cantidad' => 'required|integer|min:1',
                'venta_id' => 'required|integer'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $cantidad = (int)$request->input('cantidad');
            $ventaId = (int)$request->input('venta_id');
            $usuarioId = auth()->id ?? null;

            $resultado = $this->componenteService->reservarStockVenta($id, $cantidad, $ventaId, $usuarioId);

            return response()->json([
                'success' => $resultado['exito'],
                'message' => $resultado['mensaje'],
                'data' => $resultado
            ], $resultado['exito'] ? 200 : 400);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al reservar stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Confirmar venta y reducir stock (AJAX)
     */
    public function confirmarVenta(Request $request, $id)
    {
        try {
            header('Content-Type: application/json');

            // Validar datos
            $rules = [
                'cantidad' => 'required|integer|min:1',
                'venta_id' => 'required|integer'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $cantidad = (int)$request->input('cantidad');
            $ventaId = (int)$request->input('venta_id');
            $usuarioId = auth()->id ?? 1; // Usuario requerido para auditoría

            $resultado = $this->componenteService->confirmarVenta($id, $cantidad, $ventaId, $usuarioId);

            return response()->json([
                'success' => $resultado['exito'],
                'message' => $resultado['mensaje'],
                'data' => $resultado
            ], $resultado['exito'] ? 200 : 400);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al confirmar venta: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancelar venta y liberar stock reservado (AJAX)
     */
    public function liberarStock(Request $request, $id)
    {
        try {
            header('Content-Type: application/json');

            // Validar datos
            $rules = [
                'cantidad' => 'required|integer|min:1',
                'venta_id' => 'required|integer'
            ];

            $validator = new Validation();
            if (!$validator->validate($request->all(), $rules)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validator->errors()
                ], 400);
            }

            $cantidad = (int)$request->input('cantidad');
            $ventaId = (int)$request->input('venta_id');

            $resultado = $this->componenteService->liberarStockReservado($id, $cantidad, $ventaId);

            return response()->json([
                'success' => $resultado['exito'],
                'message' => $resultado['mensaje']
            ], $resultado['exito'] ? 200 : 400);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al liberar stock: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reporte de stock y ventas
     */
    public function reporteStock()
    {
        try {
            $estadisticas = $this->componenteService->obtenerEstadisticasComponentes();
            $stockBajo = $this->componenteService->obtenerComponentesStockBajo();
            $masVendidos = $this->componenteService->obtenerComponentesMasVendidos();

            return view('componentes.reporte_stock', [
                'title' => 'Reporte de Stock y Ventas',
                'estadisticas' => $estadisticas,
                'stock_bajo' => $stockBajo,
                'mas_vendidos' => $masVendidos
            ]);

        } catch (Exception $e) {
            Session::flash('error', 'Error al generar reporte: ' . $e->getMessage());
            return redirect(route('componentes'));
        }
    }

    /**
     * Procesar especificaciones técnicas del formulario
     */
    private function procesarEspecificaciones(Request $request): array
    {
        $especificaciones = [];

        // Obtener especificaciones dinámicas del formulario
        $especKeys = $request->input('espec_keys', []);
        $especValues = $request->input('espec_values', []);

        if (is_array($especKeys) && is_array($especValues)) {
            for ($i = 0; $i < count($especKeys); $i++) {
                $key = trim($especKeys[$i] ?? '');
                $value = trim($especValues[$i] ?? '');
                
                if (!empty($key) && !empty($value)) {
                    $especificaciones[$key] = $value;
                }
            }
        }

        return $especificaciones;
    }
}
