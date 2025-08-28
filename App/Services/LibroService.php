<?php

namespace App\Services;

use App\Models\Libro;
use App\Models\Categoria;
use App\Models\User;
use Core\DB;
use PDO;
use Exception;

class LibroService
{
    /**
     * Obtener libros con filtros y paginación
     */
    public function getLibrosFiltrados(array $filtros, int $page = 1, int $perPage = 12): array
    {
        try {
            $db = DB::getInstance();
            $where = ['estado = 1'];
            $bindings = [];

            // Aplicar filtros
            if (!empty($filtros['categoria'])) {
                $where[] = 'categoria_id = ?';
                $bindings[] = $filtros['categoria'];
            }

            if (!empty($filtros['autor'])) {
                $where[] = 'autor LIKE ?';
                $bindings[] = "%{$filtros['autor']}%";
            }

            if (!empty($filtros['editorial'])) {
                $where[] = 'editorial LIKE ?';
                $bindings[] = "%{$filtros['editorial']}%";
            }

            if (!empty($filtros['tipo'])) {
                if ($filtros['tipo'] === 'gratuito') {
                    $where[] = 'es_gratuito = 1';
                } elseif ($filtros['tipo'] === 'pago') {
                    $where[] = 'es_gratuito = 0';
                }
            }

            if (!empty($filtros['buscar'])) {
                $where[] = '(titulo LIKE ? OR autor LIKE ? OR descripcion LIKE ?)';
                $termino = "%{$filtros['buscar']}%";
                $bindings[] = $termino;
                $bindings[] = $termino;
                $bindings[] = $termino;
            }

            // Ordenar
            $orderBy = 'ORDER BY titulo ASC';
            switch ($filtros['orden'] ?? 'titulo') {
                case 'precio_asc':
                    $orderBy = 'ORDER BY precio ASC';
                    break;
                case 'precio_desc':
                    $orderBy = 'ORDER BY precio DESC';
                    break;
                case 'autor':
                    $orderBy = 'ORDER BY autor ASC';
                    break;
                case 'recientes':
                    $orderBy = 'ORDER BY created_at DESC';
                    break;
            }

            // Construir consulta para contar total
            $countSql = "SELECT COUNT(*) as total FROM libros WHERE " . implode(' AND ', $where);
            $totalResult = $db->query($countSql, $bindings);
            $total = $totalResult->fetch(PDO::FETCH_ASSOC)['total'];

            // Construir consulta con paginación
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM libros WHERE " . implode(' AND ', $where) . " {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
            
            $result = $db->query($sql, $bindings);
            $librosData = $result->fetchAll(PDO::FETCH_ASSOC);
            
            // Convertir a objetos Libro
            $libros = [];
            foreach ($librosData as $data) {
                $libro = new Libro();
                $libro->fill($data);
                $libros[] = $libro;
            }

            return [
                'libros' => $this->formatearLibrosParaVista($libros),
                'total' => $total
            ];
        } catch (Exception $e) {
            throw new Exception('Error al obtener libros: ' . $e->getMessage());
        }
    }

    /**
     * Obtener libros para panel administrativo
     */
    public function getLibrosAdmin(array $filtros, int $page = 1, int $perPage = 15): array
    {
        try {
            $db = DB::getInstance();
            $where = [];
            $bindings = [];

            // Aplicar filtros administrativos
            if ($filtros['estado'] !== null) {
                $where[] = 'estado = ?';
                $bindings[] = $filtros['estado'];
            }

            if (!empty($filtros['categoria'])) {
                $where[] = 'categoria_id = ?';
                $bindings[] = $filtros['categoria'];
            }

            if ($filtros['stock_bajo']) {
                $where[] = 'stock <= stock_minimo AND es_gratuito = 0';
            }

            if (!empty($filtros['buscar'])) {
                $where[] = '(titulo LIKE ? OR autor LIKE ? OR isbn LIKE ?)';
                $termino = "%{$filtros['buscar']}%";
                $bindings[] = $termino;
                $bindings[] = $termino;
                $bindings[] = $termino;
            }

            // Construir WHERE clause
            $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

            // Construir consulta para contar total
            $countSql = "SELECT COUNT(*) as total FROM libros {$whereClause}";
            $totalResult = $db->query($countSql, $bindings);
            $total = $totalResult->fetch(PDO::FETCH_ASSOC)['total'];

            // Construir consulta con paginación
            $offset = ($page - 1) * $perPage;
            $sql = "SELECT * FROM libros {$whereClause} ORDER BY created_at DESC LIMIT {$perPage} OFFSET {$offset}";
            
            $result = $db->query($sql, $bindings);
            $librosData = $result->fetchAll(PDO::FETCH_ASSOC);
            
            // Convertir a objetos Libro
            $libros = [];
            foreach ($librosData as $data) {
                $libro = new Libro();
                $libro->fill($data);
                $libros[] = $libro;
            }

            return [
                'libros' => $this->formatearLibrosParaAdmin($libros),
                'total' => $total
            ];
        } catch (Exception $e) {
            throw new Exception('Error al obtener libros para administración: ' . $e->getMessage());
        }
    }

    /**
     * Obtener libro por ID
     */
    public function obtenerLibro(int $id): ?array
    {
        try {
            $libro = Libro::find($id);
            if (!$libro) {
                return null;
            }

            return $this->formatearLibroCompleto($libro);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Crear nuevo libro
     */
    public function crearLibro(array $datos): array
    {
        try {
            // Validaciones básicas
            if (empty($datos['titulo'])) {
                return ['success' => false, 'message' => 'El título es obligatorio'];
            }

            if (empty($datos['autor'])) {
                return ['success' => false, 'message' => 'El autor es obligatorio'];
            }

            if (empty($datos['categoria_id'])) {
                return ['success' => false, 'message' => 'La categoría es obligatoria'];
            }

            // Verificar categoría existe
            $categoria = Categoria::find($datos['categoria_id']);
            if (!$categoria) {
                return ['success' => false, 'message' => 'La categoría seleccionada no existe'];
            }

            $libro = new Libro();
            $libro->fill($datos);

            if ($libro->save()) {
                return ['success' => true, 'message' => 'Libro creado exitosamente', 'id' => $libro->id];
            }

            return ['success' => false, 'message' => 'Error al crear el libro'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Actualizar libro
     */
    public function actualizarLibro(int $id, array $datos): array
    {
        try {
            $libro = Libro::find($id);
            if (!$libro) {
                return ['success' => false, 'message' => 'Libro no encontrado'];
            }

            // Validaciones básicas
            if (empty($datos['titulo'])) {
                return ['success' => false, 'message' => 'El título es obligatorio'];
            }

            if (empty($datos['autor'])) {
                return ['success' => false, 'message' => 'El autor es obligatorio'];
            }

            if (!empty($datos['categoria_id'])) {
                $categoria = Categoria::find($datos['categoria_id']);
                if (!$categoria) {
                    return ['success' => false, 'message' => 'La categoría seleccionada no existe'];
                }
            }

            $libro->fill($datos);

            if ($libro->save()) {
                return ['success' => true, 'message' => 'Libro actualizado exitosamente'];
            }

            return ['success' => false, 'message' => 'Error al actualizar el libro'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Eliminar libro
     */
    public function eliminarLibro(int $id): array
    {
        try {
            $libro = Libro::find($id);
            if (!$libro) {
                return ['success' => false, 'message' => 'Libro no encontrado'];
            }

            if (!$libro->puedeSerEliminado()) {
                return ['success' => false, 'message' => 'El libro no puede ser eliminado porque tiene registros asociados'];
            }

            if ($libro->delete()) {
                return ['success' => true, 'message' => 'Libro eliminado exitosamente'];
            }

            return ['success' => false, 'message' => 'Error al eliminar el libro'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Cambiar estado del libro
     */
    public function cambiarEstado(int $id): array
    {
        try {
            $libro = Libro::find($id);
            if (!$libro) {
                return ['success' => false, 'message' => 'Libro no encontrado'];
            }

            $libro->estado = $libro->estado == 1 ? 0 : 1;

            if ($libro->save()) {
                $estado = $libro->estado == 1 ? 'activado' : 'desactivado';
                return ['success' => true, 'message' => "Libro {$estado} exitosamente"];
            }

            return ['success' => false, 'message' => 'Error al cambiar el estado'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Actualizar stock
     */
    public function actualizarStock(int $id, int $nuevoStock): array
    {
        try {
            $libro = Libro::find($id);
            if (!$libro) {
                return ['success' => false, 'message' => 'Libro no encontrado'];
            }

            if ($libro->actualizarStock($nuevoStock)) {
                return ['success' => true, 'message' => 'Stock actualizado exitosamente'];
            }

            return ['success' => false, 'message' => 'Error al actualizar el stock'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener categorías activas
     */
    public function getCategorias(): array
    {
        try {
            $categorias = Categoria::where('estado', '=', 1)->orderBy('nombre')->get();
            
            // Si get() devuelve un array de modelos, convertir cada modelo a array
            if (is_array($categorias) && !empty($categorias)) {
                return array_map(function($categoria) {
                    return is_object($categoria) && method_exists($categoria, 'toArray') 
                        ? $categoria->toArray() 
                        : (array) $categoria;
                }, $categorias);
            }
            
            return $categorias ?: [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener libros relacionados
     */
    public function getLibrosRelacionados(int $libroId, int $categoriaId, int $limit = 4): array
    {
        try {
            $libros = Libro::porCategoria($categoriaId)
                          ->where('id', '!=', $libroId)
                          ->limit($limit)
                          ->get();

            return $this->formatearLibrosParaVista($libros);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Buscar libros
     */
    public function buscarLibros(string $termino, int $limit = 10): array
    {
        try {
            $libros = Libro::buscar($termino)->limit($limit)->get();
            return $this->formatearLibrosParaVista($libros);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener estadísticas básicas
     */
    public function getEstadisticasLibros(): array
    {
        try {
            return [
                'total_libros' => Libro::count(),
                'libros_activos' => Libro::where('estado', '=', 1)->count(),
                'libros_inactivos' => Libro::where('estado', '=', 0)->count(),
                'stock_bajo' => Libro::countStockBajo(),
                'libros_gratuitos' => Libro::gratuitos()->count(),
                'libros_pago' => Libro::dePago()->count()
            ];
        } catch (Exception $e) {
            return [
                'total_libros' => 0,
                'libros_activos' => 0,
                'libros_inactivos' => 0,
                'stock_bajo' => 0,
                'libros_gratuitos' => 0,
                'libros_pago' => 0
            ];
        }
    }

    // ==========================================
    // MÉTODOS PRIVADOS DE FORMATEO
    // ==========================================

    /**
     * Formatear libros para vista pública
     */
    private function formatearLibrosParaVista(array $libros): array
    {
        $librosFormateados = [];

        foreach ($libros as $libro) {
            $categoria = $libro->categoria();
            
            $librosFormateados[] = [
                'id' => $libro->id,
                'titulo' => $libro->titulo,
                'autor' => $libro->autor,
                'descripcion' => $libro->descripcion,
                'isbn' => $libro->isbn,
                'paginas' => $libro->paginas,
                'editorial' => $libro->editorial,
                'año_publicacion' => $libro->año_publicacion,
                'idioma' => $libro->idioma,
                'precio' => $libro->precio,
                'precio_formateado' => $libro->getPrecioFormateado(),
                'es_gratuito' => $libro->es_gratuito,
                'stock' => $libro->stock,
                'estado' => $libro->estado,
                'imagen_portada' => $libro->imagen_portada,
                'imagen_portada_url' => $libro->getImagenPortadaUrl(),
                'esta_disponible' => $libro->estaDisponible(),
                'tiene_stock_bajo' => $libro->tieneStockBajo(),
                'esta_agotado' => $libro->estaAgotado(),
                'estado_stock' => $libro->getEstadoStock(),
                'categoria' => $categoria ? [
                    'id' => $categoria->id,
                    'nombre' => $categoria->nombre,
                    'color' => $categoria->color ?? '#007bff',
                    'icono' => $categoria->icono ?? 'book'
                ] : null,
                'created_at' => $libro->created_at,
                'updated_at' => $libro->updated_at
            ];
        }

        return $librosFormateados;
    }

    /**
     * Formatear libros para panel administrativo
     */
    private function formatearLibrosParaAdmin(array $libros): array
    {
        $librosFormateados = [];

        foreach ($libros as $libro) {
            $categoria = $libro->categoria();
            
            $librosFormateados[] = [
                'id' => $libro->id,
                'titulo' => $libro->titulo,
                'autor' => $libro->autor,
                'isbn' => $libro->isbn,
                'categoria_id' => $libro->categoria_id,
                'precio' => $libro->precio,
                'precio_formateado' => $libro->getPrecioFormateado(),
                'es_gratuito' => $libro->es_gratuito,
                'stock' => $libro->stock,
                'stock_minimo' => $libro->stock_minimo,
                'estado' => $libro->estado,
                'estado_class' => $libro->getEstadoClass(),
                'imagen_portada' => $libro->imagen_portada,
                'imagen_portada_url' => $libro->getImagenPortadaUrl(),
                'esta_disponible' => $libro->estaDisponible(),
                'tiene_stock_bajo' => $libro->tieneStockBajo(),
                'esta_agotado' => $libro->estaAgotado(),
                'estado_stock' => $libro->getEstadoStock(),
                'puede_ser_eliminado' => $libro->puedeSerEliminado(),
                'categoria' => $categoria ? [
                    'id' => $categoria->id,
                    'nombre' => $categoria->nombre
                ] : null,
                'categoria_nombre' => $categoria ? $categoria->nombre : 'Sin categoría',
                'created_at' => $libro->created_at,
                'updated_at' => $libro->updated_at
            ];
        }

        return $librosFormateados;
    }

    /**
     * Formatear libro completo para vista detalle
     */
    private function formatearLibroCompleto(Libro $libro): array
    {
        $categoria = $libro->categoria();
        
        return [
            'id' => $libro->id,
            'titulo' => $libro->titulo,
            'autor' => $libro->autor,
            'descripcion' => $libro->descripcion,
            'categoria_id' => $libro->categoria_id,
            'isbn' => $libro->isbn,
            'paginas' => $libro->paginas,
            'editorial' => $libro->editorial,
            'año_publicacion' => $libro->año_publicacion,
            'idioma' => $libro->idioma,
            'precio' => $libro->precio,
            'precio_formateado' => $libro->getPrecioFormateado(),
            'es_gratuito' => $libro->es_gratuito,
            'stock' => $libro->stock,
            'stock_minimo' => $libro->stock_minimo,
            'estado' => $libro->estado,
            'imagen_portada' => $libro->imagen_portada,
            'imagen_portada_url' => $libro->getImagenPortadaUrl(),
            'esta_disponible' => $libro->estaDisponible(),
            'tiene_stock_bajo' => $libro->tieneStockBajo(),
            'esta_agotado' => $libro->estaAgotado(),
            'estado_stock' => $libro->getEstadoStock(),
            'puede_ser_eliminado' => $libro->puedeSerEliminado(),
            'categoria' => $categoria ? [
                'id' => $categoria->id,
                'nombre' => $categoria->nombre,
                'color' => $categoria->color ?? '#007bff',
                'icono' => $categoria->icono ?? 'book'
            ] : null,
            'created_at' => $libro->created_at,
            'updated_at' => $libro->updated_at
        ];
    }
}
