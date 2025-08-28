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
     * Obtener todos los libros con información adicional
     */
    public function getAllLibros(): array
    {
        $libros = Libro::all();
        $librosData = [];

        foreach ($libros as $libro) {
            $libroData = $libro->getAttributes();
            
            // Obtener información de la categoría
            $categoria = Categoria::find($libro->categoria_id);
            $libroData['categoria_nombre'] = $categoria ? $categoria->nombre : 'Sin categoría';
            $libroData['categoria_color'] = $categoria ? $categoria->color : '#6c757d';
            
            // Calcular estadísticas del libro
            $libroData['total_descargas'] = $this->getTotalDescargas($libro->id);
            $libroData['descargas_mes'] = $this->getDescargasMes($libro->id);
            $libroData['stock_status'] = $this->getStockStatus($libro);
            $libroData['precio_formateado'] = $this->formatPrice($libro->precio, $libro->es_gratuito);
            
            $librosData[] = $libroData;
        }

        return $librosData;
    }

    /**
     * Obtener libros disponibles para usuarios
     */
    public function getLibrosDisponibles(): array
    {
        $libros = Libro::where('estado', '=', 1)->get();
        $librosData = [];

        foreach ($libros as $libro) {
            $libroData = $libro->getAttributes();
            
            // Obtener información de la categoría
            $categoria = Categoria::find($libro->categoria_id);
            $libroData['categoria'] = $categoria ? [
                'nombre' => $categoria->nombre,
                'color' => $categoria->color,
                'icono' => $categoria->icono
            ] : null;
            
            $libroData['total_descargas'] = $this->getTotalDescargas($libro->id);
            $libroData['puede_descargar'] = $this->puedeDescargar($libro);
            $libroData['precio_formateado'] = $this->formatPrice($libro->precio, $libro->es_gratuito);
            $libroData['tamaño_formateado'] = $this->formatFileSize($libro->tamaño_archivo);
            
            $librosData[] = $libroData;
        }

        return $librosData;
    }

    /**
     * Obtener libro por ID con información completa
     */
    public function getLibroById(int $id)
    {
        $libro = Libro::find($id);
        if (!$libro) {
            return null;
        }

        $libroData = $libro->getAttributes();
        
        // Obtener información de la categoría
        $categoria = Categoria::find($libro->categoria_id);
        $libroData['categoria'] = $categoria ? [
            'id' => $categoria->id,
            'nombre' => $categoria->nombre,
            'color' => $categoria->color,
            'icono' => $categoria->icono
        ] : null;
        
        // Estadísticas del libro
        $libroData['total_descargas'] = $this->getTotalDescargas($libro->id);
        $libroData['descargas_mes'] = $this->getDescargasMes($libro->id);
        $libroData['descargas_semana'] = $this->getDescargasSemana($libro->id);
        $libroData['usuarios_unicos'] = $this->getUsuariosUnicos($libro->id);
        
        // Estado y disponibilidad
        $libroData['stock_status'] = $this->getStockStatus($libro);
        $libroData['puede_descargar'] = $this->puedeDescargar($libro);
        $libroData['precio_formateado'] = $this->formatPrice($libro->precio, $libro->es_gratuito);
        $libroData['tamaño_formateado'] = $this->formatFileSize($libro->tamaño_archivo);
        
        // Obtener descargas recientes
        $libroData['descargas_recientes'] = $this->getDescargasRecientes($libro->id, 10);

        return $libroData;
    }

    /**
     * Crear nuevo libro
     */
    public function createLibro(array $libroData): int
    {
        try {
            // Validar que la categoría existe
            if (isset($libroData['categoria_id'])) {
                $categoria = Categoria::find($libroData['categoria_id']);
                if (!$categoria) {
                    throw new Exception('La categoría especificada no existe');
                }
            }

            // Procesar datos del libro
            $libro = new Libro([
                'titulo' => $libroData['titulo'],
                'autor' => $libroData['autor'],
                'descripcion' => $libroData['descripcion'] ?? null,
                'categoria_id' => $libroData['categoria_id'],
                'isbn' => $libroData['isbn'] ?? null,
                'paginas' => $libroData['paginas'] ?? 0,
                'editorial' => $libroData['editorial'] ?? null,
                'año_publicacion' => $libroData['año_publicacion'] ?? null,
                'imagen_portada' => $libroData['imagen_portada'] ?? null,
                'archivo_pdf' => $libroData['archivo_pdf'] ?? null,
                'enlace_externo' => $libroData['enlace_externo'] ?? null,
                'tamaño_archivo' => $libroData['tamaño_archivo'] ?? 0,
                'stock' => $libroData['stock'] ?? 0,
                'stock_minimo' => $libroData['stock_minimo'] ?? 5,
                'precio' => $libroData['precio'] ?? 0.00,
                'es_gratuito' => $libroData['es_gratuito'] ?? 1,
                'estado' => $libroData['estado'] ?? 1
            ]);

            $libro->save();
            return $libro->getKey();
        } catch (Exception $e) {
            throw new Exception('Error al crear libro: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar libro
     */
    public function updateLibro(int $id, array $libroData): bool
    {
        try {
            $libro = Libro::find($id);
            if (!$libro) {
                throw new Exception('Libro no encontrado');
            }

            // Validar categoría si se proporciona
            if (isset($libroData['categoria_id'])) {
                $categoria = Categoria::find($libroData['categoria_id']);
                if (!$categoria) {
                    throw new Exception('La categoría especificada no existe');
                }
            }

            // Actualizar campos
            foreach ($libroData as $field => $value) {
                if ($value !== null && in_array($field, $libro->getFillable())) {
                    $libro->$field = $value;
                }
            }

            $libro->save();
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al actualizar libro: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar libro con verificación de dependencias
     */
    public function deleteLibro(int $id): bool
    {
        try {
            $libro = Libro::find($id);
            if (!$libro) {
                throw new Exception('Libro no encontrado');
            }

            // Verificar dependencias
            $dependencies = $this->checkLibroDependencies($id);
            if (!empty($dependencies)) {
                $message = "No se puede eliminar el libro '{$libro->titulo}' porque:\n";
                foreach ($dependencies as $dependency) {
                    $message .= "• " . $dependency . "\n";
                }
                $message .= "\nPrimero debe resolver estas dependencias.";
                throw new Exception($message);
            }

            $libro->delete();
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al eliminar libro: ' . $e->getMessage());
        }
    }

    /**
     * Procesar descarga de libro
     */
    public function procesarDescarga(int $libroId, int $usuarioId, string $ipAddress, ?string $userAgent = null): bool
    {
        try {
            $libro = Libro::find($libroId);
            if (!$libro) {
                throw new Exception('Libro no encontrado');
            }

            // Verificar disponibilidad
            if (!$this->puedeDescargar($libro)) {
                throw new Exception('Libro no disponible para descarga');
            }

            // Registrar descarga
            $db = DB::getInstance();
            $db->query(
                "INSERT INTO descargas_libros (usuario_id, libro_id, ip_address, user_agent) VALUES (?, ?, ?, ?)",
                [$usuarioId, $libroId, $ipAddress, $userAgent]
            );

            // Actualizar stock si no es gratuito
            if (!$libro->es_gratuito && $libro->stock > 0) {
                $libro->stock -= 1;
                $libro->save();
            }

            return true;
        } catch (Exception $e) {
            throw new Exception('Error al procesar descarga: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar stock de libro
     */
    public function updateStock(int $id, int $nuevoStock): bool
    {
        try {
            $libro = Libro::find($id);
            if (!$libro) {
                throw new Exception('Libro no encontrado');
            }

            $libro->stock = max(0, $nuevoStock);
            $libro->save();

            return true;
        } catch (Exception $e) {
            throw new Exception('Error al actualizar stock: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del libro
     */
    public function changeStatus(int $id, int $status): bool
    {
        try {
            $libro = Libro::find($id);
            if (!$libro) {
                throw new Exception('Libro no encontrado');
            }

            $libro->estado = $status;
            $libro->save();

            return true;
        } catch (Exception $e) {
            throw new Exception('Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * Obtener todas las categorías de libros
     */
    public function getAllCategoriasLibros(): array
    {
        return Categoria::where('tipo', '=', 'libro')->where('estado', '=', 1)->get();
    }

    /**
     * Obtener libros con stock bajo
     */
    public function getLibrosStockBajo(): array
    {
        return Libro::whereRaw('stock <= stock_minimo')->where('estado', '=', 1)->get();
    }

    /**
     * Obtener estadísticas generales de libros
     */
    public function getEstadisticasLibros(): array
    {
        try {
            return [
                'total' => Libro::where('estado', '=', 1)->count(),
                'disponibles' => Libro::where('estado', '=', 1)->where('stock', '>', 0)->count(),
                'gratuitos' => Libro::where('es_gratuito', '=', 1)->where('estado', '=', 1)->count(),
                'de_pago' => Libro::where('es_gratuito', '=', 0)->where('estado', '=', 1)->count(),
                'stock_bajo' => Libro::whereRaw('stock <= stock_minimo')->where('estado', '=', 1)->count(),
                'agotados' => Libro::where('stock', '=', 0)->where('es_gratuito', '=', 0)->where('estado', '=', 1)->count(),
                'descargas_hoy' => $this->getDescargasHoy(),
                'descargas_mes' => $this->getDescargasMesTotal(),
                'libro_mas_descargado' => $this->getLibroMasDescargado(),
                'promedio_precio' => $this->getPromedioPrecio()
            ];
        } catch (Exception $e) {
            return [
                'total' => 0, 'disponibles' => 0, 'gratuitos' => 0, 'de_pago' => 0,
                'stock_bajo' => 0, 'agotados' => 0, 'descargas_hoy' => 0,
                'descargas_mes' => 0, 'libro_mas_descargado' => null, 'promedio_precio' => 0
            ];
        }
    }

    /**
     * Buscar libros
     */
    public function buscarLibros(string $termino, array $filtros = []): array
    {
        try {
            $db = DB::getInstance();
            $where = ["l.estado = 1"];
            $params = [];

            // Búsqueda por término
            if (!empty($termino)) {
                $where[] = "(l.titulo LIKE ? OR l.autor LIKE ? OR l.descripcion LIKE ?)";
                $params[] = "%{$termino}%";
                $params[] = "%{$termino}%";
                $params[] = "%{$termino}%";
            }

            // Filtro por categoría
            if (!empty($filtros['categoria_id'])) {
                $where[] = "l.categoria_id = ?";
                $params[] = $filtros['categoria_id'];
            }

            // Filtro por tipo (gratuito/pago)
            if (isset($filtros['es_gratuito'])) {
                $where[] = "l.es_gratuito = ?";
                $params[] = $filtros['es_gratuito'];
            }

            // Filtro por disponibilidad
            if (!empty($filtros['disponible'])) {
                $where[] = "l.stock > 0";
            }

            $whereClause = implode(" AND ", $where);
            
            $query = "SELECT l.*, c.nombre as categoria_nombre, c.color as categoria_color
                      FROM libros l
                      LEFT JOIN categorias c ON l.categoria_id = c.id
                      WHERE {$whereClause}
                      ORDER BY l.titulo";

            $result = $db->query($query, $params);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            return [];
        }
    }

    // ==========================================
    // MÉTODOS PRIVADOS
    // ==========================================

    /**
     * Verificar dependencias del libro antes de eliminar
     */
    private function checkLibroDependencies(int $libroId): array
    {
        $dependencies = [];
        
        try {
            $db = DB::getInstance();
            
            // Verificar descargas
            $descargas = $db->query("SELECT COUNT(*) as count FROM descargas_libros WHERE libro_id = ?", [$libroId])->fetch();
            if ($descargas->count > 0) {
                $dependencies[] = "Tiene {$descargas->count} descarga(s) registrada(s)";
            }
            
            // Verificar si está en ventas
            $ventas = $db->query("SELECT COUNT(*) as count FROM detalle_ventas WHERE tipo_producto = 'libro' AND producto_id = ?", [$libroId])->fetch();
            if ($ventas->count > 0) {
                $dependencies[] = "Está incluido en {$ventas->count} venta(s)";
            }
            
        } catch (Exception $e) {
            $dependencies[] = "Error verificando dependencias: " . $e->getMessage();
        }
        
        return $dependencies;
    }

    /**
     * Obtener total de descargas de un libro
     */
    private function getTotalDescargas(int $libroId): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT COUNT(*) as count FROM descargas_libros WHERE libro_id = ?", [$libroId]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener descargas del mes actual
     */
    private function getDescargasMes(int $libroId): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query(
                "SELECT COUNT(*) as count FROM descargas_libros 
                 WHERE libro_id = ? AND MONTH(fecha_descarga) = MONTH(CURRENT_DATE()) 
                 AND YEAR(fecha_descarga) = YEAR(CURRENT_DATE())",
                [$libroId]
            );
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener descargas de la semana actual
     */
    private function getDescargasSemana(int $libroId): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query(
                "SELECT COUNT(*) as count FROM descargas_libros 
                 WHERE libro_id = ? AND fecha_descarga >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)",
                [$libroId]
            );
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener usuarios únicos que descargaron el libro
     */
    private function getUsuariosUnicos(int $libroId): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT COUNT(DISTINCT usuario_id) as count FROM descargas_libros WHERE libro_id = ?", [$libroId]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener estado del stock
     */
    private function getStockStatus($libro): array
    {
        if ($libro->es_gratuito) {
            return ['status' => 'unlimited', 'class' => 'success', 'text' => 'Ilimitado'];
        }

        if ($libro->stock <= 0) {
            return ['status' => 'out', 'class' => 'danger', 'text' => 'Agotado'];
        }

        if ($libro->stock <= $libro->stock_minimo) {
            return ['status' => 'low', 'class' => 'warning', 'text' => 'Stock Bajo'];
        }

        return ['status' => 'available', 'class' => 'success', 'text' => 'Disponible'];
    }

    /**
     * Verificar si el libro puede ser descargado
     */
    private function puedeDescargar($libro): bool
    {
        return $libro->estado == 1 && ($libro->es_gratuito || $libro->stock > 0);
    }

    /**
     * Formatear precio
     */
    private function formatPrice(float $precio, int $esGratuito): string
    {
        if ($esGratuito) {
            return 'Gratuito';
        }
        
        if ($precio <= 0) {
            return 'Gratuito';
        }
        
        return 'Bs. ' . number_format($precio, 2);
    }

    /**
     * Formatear tamaño de archivo
     */
    private function formatFileSize(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Obtener descargas recientes del libro
     */
    private function getDescargasRecientes(int $libroId, int $limit): array
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT dl.*, u.nombre, u.apellido, u.email
                      FROM descargas_libros dl
                      INNER JOIN usuarios u ON dl.usuario_id = u.id
                      WHERE dl.libro_id = ?
                      ORDER BY dl.fecha_descarga DESC
                      LIMIT ?";
            
            $result = $db->query($query, [$libroId, $limit]);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener descargas de hoy
     */
    private function getDescargasHoy(): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT COUNT(*) as count FROM descargas_libros WHERE DATE(fecha_descarga) = CURRENT_DATE()");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener descargas del mes total
     */
    private function getDescargasMesTotal(): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query(
                "SELECT COUNT(*) as count FROM descargas_libros 
                 WHERE MONTH(fecha_descarga) = MONTH(CURRENT_DATE()) 
                 AND YEAR(fecha_descarga) = YEAR(CURRENT_DATE())"
            );
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener libro más descargado
     */
    private function getLibroMasDescargado(): ?array
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT l.id, l.titulo, l.autor, COUNT(dl.id) as total_descargas
                      FROM libros l
                      INNER JOIN descargas_libros dl ON l.id = dl.libro_id
                      WHERE l.estado = 1
                      GROUP BY l.id
                      ORDER BY total_descargas DESC
                      LIMIT 1";
            
            $result = $db->query($query);
            return $result ? $result->fetch(PDO::FETCH_ASSOC) : null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Obtener promedio de precio
     */
    private function getPromedioPrecio(): float
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT AVG(precio) as promedio FROM libros WHERE estado = 1 AND es_gratuito = 0 AND precio > 0");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return round($row['promedio'] ?? 0, 2);
        } catch (Exception $e) {
            return 0;
        }
    }

    // ==========================================
    // MÉTODOS PARA NUEVAS FUNCIONALIDADES
    // ==========================================

    /**
     * Toggle favorito para un usuario
     */
    public function toggleFavorito(int $libroId, int $userId): array
    {
        $libro = Libro::find($libroId);
        if (!$libro) {
            throw new Exception('Libro no encontrado');
        }

        return $libro->toggleFavorito($userId);
    }

    /**
     * Calificar un libro
     */
    public function calificarLibro(int $libroId, int $userId, int $calificacion, ?string $comentario = null): array
    {
        $libro = Libro::find($libroId);
        if (!$libro) {
            throw new Exception('Libro no encontrado');
        }

        // Verificar si el usuario puede calificar (ha descargado el libro)
        if (!$libro->puedeSerCalificadoPor($userId)) {
            throw new Exception('Solo puedes calificar libros que hayas descargado');
        }

        return $libro->calificar($userId, $calificacion, $comentario);
    }

    /**
     * Registrar descarga de un libro
     */
    public function registrarDescarga(int $libroId, int $userId): void
    {
        $libro = Libro::find($libroId);
        if (!$libro) {
            throw new Exception('Libro no encontrado');
        }

        $libro->registrarDescarga($userId);
    }

    /**
     * Obtener calificaciones de un libro
     */
    public function getCalificacionesLibro(int $libroId): array
    {
        $libro = Libro::find($libroId);
        if (!$libro) {
            throw new Exception('Libro no encontrado');
        }

        return [
            'estadisticas' => $libro->getEstadisticasCalificacionesDetalladas(),
            'calificaciones' => $libro->getCalificacionesConUsuarios(20)
        ];
    }

    /**
     * Obtener libros favoritos de un usuario
     */
    public function getFavoritosUsuario(int $userId): array
    {
        try {
            $db = DB::getInstance();
            $query = "
                SELECT l.*, c.nombre as categoria_nombre, c.color as categoria_color
                FROM libros l
                INNER JOIN favoritos_libros f ON l.id = f.libro_id
                LEFT JOIN categorias c ON l.categoria_id = c.id
                WHERE f.usuario_id = ? AND l.estado = 1
                ORDER BY f.fecha_agregado DESC
            ";
            
            $result = $db->query($query, [$userId]);
            $favoritos = $result->fetchAll(PDO::FETCH_ASSOC);

            // Enriquecer datos
            foreach ($favoritos as &$libro) {
                $libro['precio_formateado'] = $this->formatPrice($libro['precio'], $libro['es_gratuito']);
                $libro['total_descargas'] = $this->getTotalDescargas($libro['id']);
                $libro['total_favoritos'] = $this->getTotalFavoritos($libro['id']);
            }

            return $favoritos;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener total de favoritos de un libro
     */
    public function getTotalFavoritos(int $libroId): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT COUNT(*) as total FROM favoritos_libros WHERE libro_id = ?", [$libroId]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener un libro por ID con información completa
     */
    public function obtenerLibro(int $id): ?array
    {
        $libro = Libro::find($id);
        if (!$libro) {
            return null;
        }

        $libroData = $libro->getInformacionCompleta();
        
        return $libroData['basica'];
    }

    /**
     * Verificar si un usuario puede acceder a un libro
     */
    public function puedeAccederUsuario(int $libroId, int $userId): bool
    {
        $libro = Libro::find($libroId);
        if (!$libro || !$libro->estaDisponible()) {
            return false;
        }

        // Si es gratuito, siempre puede acceder
        if ($libro->es_gratuito) {
            return true;
        }

        // Si es de pago, verificar si lo ha descargado
        try {
            $db = DB::getInstance();
            $result = $db->query(
                "SELECT COUNT(*) as count FROM descargas_libros WHERE libro_id = ? AND usuario_id = ?",
                [$libroId, $userId]
            );
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    // ==========================================
    // MÉTODOS FALTANTES PARA CONTROLADOR
    // ==========================================

    /**
     * Obtener libros filtrados para vista pública
     */
    public function getLibrosFiltrados(array $filtros, int $page = 1, int $perPage = 12): array
    {
        try {
            $db = DB::getInstance();
            
            $conditions = ['l.estado = 1'];
            $params = [];
            
            if (!empty($filtros['categoria'])) {
                $conditions[] = 'l.categoria_id = ?';
                $params[] = $filtros['categoria'];
            }
            
            if (!empty($filtros['autor'])) {
                $conditions[] = 'l.autor LIKE ?';
                $params[] = '%' . $filtros['autor'] . '%';
            }
            
            if (!empty($filtros['editorial'])) {
                $conditions[] = 'l.editorial LIKE ?';
                $params[] = '%' . $filtros['editorial'] . '%';
            }
            
            if (!empty($filtros['tipo'])) {
                if ($filtros['tipo'] === 'gratuito') {
                    $conditions[] = 'l.es_gratuito = 1';
                } elseif ($filtros['tipo'] === 'pago') {
                    $conditions[] = 'l.es_gratuito = 0';
                }
            }
            
            if (!empty($filtros['buscar'])) {
                $conditions[] = '(l.titulo LIKE ? OR l.autor LIKE ? OR l.descripcion LIKE ?)';
                $params[] = '%' . $filtros['buscar'] . '%';
                $params[] = '%' . $filtros['buscar'] . '%';
                $params[] = '%' . $filtros['buscar'] . '%';
            }
            
            $whereClause = implode(' AND ', $conditions);
            
            // Contar total
            $totalQuery = "SELECT COUNT(*) as total FROM libros l WHERE $whereClause";
            $totalResult = $db->query($totalQuery, $params);
            $total = $totalResult->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Obtener libros
            $orden = $filtros['orden'] ?? 'titulo';
            $offset = ($page - 1) * $perPage;
            
            $query = "
                SELECT l.*, c.nombre as categoria_nombre, c.color as categoria_color
                FROM libros l
                LEFT JOIN categorias c ON l.categoria_id = c.id
                WHERE $whereClause
                ORDER BY l.$orden ASC
                LIMIT $perPage OFFSET $offset
            ";
            
            $result = $db->query($query, $params);
            $libros = $result->fetchAll(PDO::FETCH_ASSOC);
            
            // Enriquecer datos
            foreach ($libros as &$libro) {
                $libro['precio_formateado'] = $this->formatPrice($libro['precio'], $libro['es_gratuito']);
                $libro['total_descargas'] = $this->getTotalDescargas($libro['id']);
            }
            
            return [
                'libros' => $libros,
                'total' => $total
            ];
        } catch (Exception $e) {
            return ['libros' => [], 'total' => 0];
        }
    }

    /**
     * Obtener categorías
     */
    public function getCategorias(): array
    {
        try {
            $categorias = Categoria::all();
            
            // Si es un array de modelos, convertir cada modelo a array
            if (is_array($categorias)) {
                return array_map(function($categoria) {
                    return $categoria->getAttributes();
                }, $categorias);
            }
            
            return [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener libros relacionados
     */
    public function getLibrosRelacionados(int $libroId, int $categoriaId, int $limit = 6): array
    {
        try {
            $db = DB::getInstance();
            $query = "
                SELECT l.*, c.nombre as categoria_nombre
                FROM libros l
                LEFT JOIN categorias c ON l.categoria_id = c.id
                WHERE l.categoria_id = ? AND l.id != ? AND l.estado = 1
                ORDER BY l.calificacion_promedio DESC, l.descargas_totales DESC
                LIMIT ?
            ";
            
            $result = $db->query($query, [$categoriaId, $libroId, $limit]);
            $libros = $result->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($libros as &$libro) {
                $libro['precio_formateado'] = $this->formatPrice($libro['precio'], $libro['es_gratuito']);
            }
            
            return $libros;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener libros para administración
     */
    public function getLibrosAdmin(array $filtros, int $page = 1, int $perPage = 20): array
    {
        try {
            $db = DB::getInstance();
            
            $conditions = [];
            $params = [];
            
            if (!empty($filtros['categoria'])) {
                $conditions[] = 'l.categoria_id = ?';
                $params[] = $filtros['categoria'];
            }
            
            if (!empty($filtros['estado'])) {
                $conditions[] = 'l.estado = ?';
                $params[] = $filtros['estado'];
            }
            
            if (!empty($filtros['buscar'])) {
                $conditions[] = '(l.titulo LIKE ? OR l.autor LIKE ?)';
                $params[] = '%' . $filtros['buscar'] . '%';
                $params[] = '%' . $filtros['buscar'] . '%';
            }
            
            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
            
            // Contar total
            $totalQuery = "SELECT COUNT(*) as total FROM libros l $whereClause";
            $totalResult = $db->query($totalQuery, $params);
            $total = $totalResult->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Obtener libros
            $offset = ($page - 1) * $perPage;
            
            $query = "
                SELECT l.*, c.nombre as categoria_nombre
                FROM libros l
                LEFT JOIN categorias c ON l.categoria_id = c.id
                $whereClause
                ORDER BY l.fecha_creacion DESC
                LIMIT $perPage OFFSET $offset
            ";
            
            $result = $db->query($query, $params);
            $libros = $result->fetchAll(PDO::FETCH_ASSOC);
            
            // Enriquecer datos
            foreach ($libros as &$libro) {
                $libro['precio_formateado'] = $this->formatPrice($libro['precio'], $libro['es_gratuito']);
                $libro['total_descargas'] = $this->getTotalDescargas($libro['id']);
                $libro['stock_status'] = $this->getStockStatusString($libro);
            }
            
            return [
                'libros' => $libros,
                'total' => $total
            ];
        } catch (Exception $e) {
            return ['libros' => [], 'total' => 0];
        }
    }

    /**
     * Crear libro
     */
    public function crearLibro(array $datos): array
    {
        try {
            $libro = new Libro();
            $libro->fill($datos);
            
            if ($libro->save()) {
                return ['success' => true, 'mensaje' => 'Libro creado exitosamente', 'id' => $libro->id];
            }
            
            return ['success' => false, 'mensaje' => 'Error al crear el libro'];
        } catch (Exception $e) {
            return ['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()];
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
                return ['success' => false, 'mensaje' => 'Libro no encontrado'];
            }
            
            $libro->fill($datos);
            
            if ($libro->save()) {
                return ['success' => true, 'mensaje' => 'Libro actualizado exitosamente'];
            }
            
            return ['success' => false, 'mensaje' => 'Error al actualizar el libro'];
        } catch (Exception $e) {
            return ['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()];
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
                return ['success' => false, 'mensaje' => 'Libro no encontrado'];
            }
            
            // Verificar si puede ser eliminado
            if (!$libro->puedeSerEliminado()) {
                return ['success' => false, 'mensaje' => 'No se puede eliminar un libro con descargas'];
            }
            
            if ($libro->delete()) {
                return ['success' => true, 'mensaje' => 'Libro eliminado exitosamente'];
            }
            
            return ['success' => false, 'mensaje' => 'Error al eliminar el libro'];
        } catch (Exception $e) {
            return ['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()];
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
                return ['success' => false, 'mensaje' => 'Libro no encontrado'];
            }
            
            $libro->estado = $libro->estado == 1 ? 0 : 1;
            
            if ($libro->save()) {
                $estado = $libro->estado == 1 ? 'activado' : 'desactivado';
                return ['success' => true, 'mensaje' => "Libro $estado exitosamente"];
            }
            
            return ['success' => false, 'mensaje' => 'Error al cambiar el estado'];
        } catch (Exception $e) {
            return ['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()];
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
                return ['success' => false, 'mensaje' => 'Libro no encontrado'];
            }
            
            if ($libro->actualizarStock($nuevoStock)) {
                return ['success' => true, 'mensaje' => 'Stock actualizado exitosamente'];
            }
            
            return ['success' => false, 'mensaje' => 'Error al actualizar el stock'];
        } catch (Exception $e) {
            return ['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Obtener descargas de un libro
     */
    public function getDescargasLibro(int $id, int $page = 1, int $perPage = 20): array
    {
        try {
            $db = DB::getInstance();
            
            // Contar total
            $totalQuery = "SELECT COUNT(*) as total FROM descargas_libros WHERE libro_id = ?";
            $totalResult = $db->query($totalQuery, [$id]);
            $total = $totalResult->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Obtener descargas
            $offset = ($page - 1) * $perPage;
            $query = "
                SELECT d.*, u.nombre_completo, u.email
                FROM descargas_libros d
                INNER JOIN usuarios u ON d.usuario_id = u.id
                WHERE d.libro_id = ?
                ORDER BY d.fecha_descarga DESC
                LIMIT $perPage OFFSET $offset
            ";
            
            $result = $db->query($query, [$id]);
            $descargas = $result->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'descargas' => $descargas,
                'total' => $total
            ];
        } catch (Exception $e) {
            return ['descargas' => [], 'total' => 0];
        }
    }

    /**
     * Verificar disponibilidad
     */
    public function verificarDisponibilidad(int $id): array
    {
        try {
            $libro = Libro::find($id);
            if (!$libro) {
                return ['disponible' => false, 'mensaje' => 'Libro no encontrado'];
            }
            
            $disponible = $libro->estaDisponible();
            $mensaje = $disponible ? 'Libro disponible' : 'Libro no disponible';
            
            return [
                'disponible' => $disponible,
                'mensaje' => $mensaje,
                'stock' => $libro->stock,
                'es_gratuito' => $libro->es_gratuito
            ];
        } catch (Exception $e) {
            return ['disponible' => false, 'mensaje' => 'Error al verificar disponibilidad'];
        }
    }

    /**
     * Métodos de reportes
     */
    public function getReporteDescargas(array $filtros): array
    {
        // Implementar lógica de reporte de descargas
        return ['datos' => [], 'total' => 0];
    }

    public function getReporteStock(array $filtros): array
    {
        // Implementar lógica de reporte de stock
        return ['datos' => [], 'total' => 0];
    }

    public function getReporteCategorias(array $filtros): array
    {
        // Implementar lógica de reporte de categorías
        return ['datos' => [], 'total' => 0];
    }

    public function getReporteGeneral(array $filtros): array
    {
        // Implementar lógica de reporte general
        return ['datos' => [], 'total' => 0];
    }

    /**
     * Obtener estado de stock como string
     */
    private function getStockStatusString(array $libro): string
    {
        if ($libro['es_gratuito']) {
            return 'Gratuito';
        }
        
        if ($libro['stock'] <= 0) {
            return 'Sin stock';
        }
        
        if ($libro['stock'] <= 5) {
            return 'Stock bajo';
        }
        
        return 'Disponible';
    }
}
