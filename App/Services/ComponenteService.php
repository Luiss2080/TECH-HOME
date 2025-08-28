<?php

namespace App\Services;

use App\Models\Componente;
use App\Models\Categoria;
use App\Models\DetalleVenta;
use Core\DB;
use PDO;
use Exception;

class ComponenteService
{
    /**
     * Listar componentes con filtros y paginación
     */
    public function listarComponentes(array $filtros = []): array
    {
        try {
            $db = DB::getInstance();
            
            $query = "
                SELECT 
                    c.*,
                    cat.nombre as categoria_nombre,
                    cat.color as categoria_color,
                    CASE 
                        WHEN c.stock <= c.stock_minimo THEN 1 
                        ELSE 0 
                    END as stock_bajo
                FROM componentes c 
                LEFT JOIN categorias cat ON c.categoria_id = cat.id 
                WHERE 1=1
            ";
            
            $params = [];
            $conditions = [];

            // Filtro por búsqueda
            if (!empty($filtros['busqueda'])) {
                $conditions[] = "(c.nombre LIKE ? OR c.descripcion LIKE ? OR c.marca LIKE ? OR c.modelo LIKE ? OR c.codigo_producto LIKE ?)";
                $searchTerm = '%' . $filtros['busqueda'] . '%';
                $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            }

            // Filtro por categoría
            if (!empty($filtros['categoria_id'])) {
                $conditions[] = "c.categoria_id = ?";
                $params[] = $filtros['categoria_id'];
            }

            // Filtro por estado
            if (!empty($filtros['estado'])) {
                $conditions[] = "c.estado = ?";
                $params[] = $filtros['estado'];
            }

            // Filtro por marca
            if (!empty($filtros['marca'])) {
                $conditions[] = "c.marca = ?";
                $params[] = $filtros['marca'];
            }

            // Filtro por stock bajo
            if ($filtros['stock_bajo']) {
                $conditions[] = "c.stock <= c.stock_minimo";
            }

            // Agregar condiciones a la consulta
            if (!empty($conditions)) {
                $query .= " AND " . implode(" AND ", $conditions);
            }

            // Contar total de registros para paginación
            $countQuery = str_replace("SELECT c.*, cat.nombre as categoria_nombre, cat.color as categoria_color, CASE WHEN c.stock <= c.stock_minimo THEN 1 ELSE 0 END as stock_bajo", "SELECT COUNT(*)", $query);
            
                        $db = DB::getInstance();
                        $db = DB::getInstance();
            $stmt = $db->query($query, $params);
            $totalRegistros = $stmt->fetchColumn();

            // Calcular paginación
            $porPagina = $filtros['por_pagina'] ?? 20;
            $paginaActual = $filtros['pagina'] ?? 1;
            $totalPaginas = ceil($totalRegistros / $porPagina);
            $offset = ($paginaActual - 1) * $porPagina;

            // Agregar ordenación y límites
            $query .= " ORDER BY c.nombre ASC LIMIT ? OFFSET ?";
            $params[] = $porPagina;
            $params[] = $offset;

            $stmt = $db->query($query, $params);
            $componentes = $stmt->fetchAll(PDO::FETCH_OBJ);

            // Obtener categorías para filtros
            $categorias = $this->obtenerCategoriasComponentes();

            // Obtener marcas únicas para filtros
            $marcas = $this->obtenerMarcasUnicas();

            // Obtener estadísticas
            $estadisticas = $this->obtenerEstadisticasComponentes();

            return [
                'componentes' => $componentes,
                'categorias' => $categorias,
                'marcas' => $marcas,
                'paginacion' => [
                    'total_registros' => $totalRegistros,
                    'por_pagina' => $porPagina,
                    'pagina_actual' => $paginaActual,
                    'total_paginas' => $totalPaginas,
                    'tiene_anterior' => $paginaActual > 1,
                    'tiene_siguiente' => $paginaActual < $totalPaginas
                ],
                'estadisticas' => $estadisticas
            ];

        } catch (Exception $e) {
            throw new Exception("Error al listar componentes: " . $e->getMessage());
        }
    }

    /**
     * Obtener componente por ID
     */
    public function obtenerComponentePorId(int $id): ?object
    {
        try {
            $db = DB::getInstance();
            
            $query = "
                SELECT 
                    c.*,
                    cat.nombre as categoria_nombre,
                    cat.color as categoria_color,
                    CASE 
                        WHEN c.stock <= c.stock_minimo THEN 1 
                        ELSE 0 
                    END as stock_bajo
                FROM componentes c 
                LEFT JOIN categorias cat ON c.categoria_id = cat.id 
                WHERE c.id = ?
            ";
            
            $db = DB::getInstance();
            
            $stmt = $db->query($query, [$id]);
            
            return $stmt->fetch(PDO::FETCH_OBJ) ?: null;

        } catch (Exception $e) {
            throw new Exception("Error al obtener componente: " . $e->getMessage());
        }
    }

    /**
     * Crear nuevo componente
     */
    public function crearComponente(array $data): int
    {
        try {
            $db = DB::getInstance();
            DB::beginTransaction();

            // Generar código de producto automático si no se proporciona
            if (empty($data['codigo_producto'])) {
                $data['codigo_producto'] = $this->generarCodigoProducto($data['categoria_id']);
            }

            $query = "
                INSERT INTO componentes 
                (nombre, descripcion, categoria_id, codigo_producto, marca, modelo, 
                 especificaciones, precio, stock, stock_minimo, proveedor, estado, 
                 fecha_creacion, fecha_actualizacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ";

            $stmt = $db->query($query, [
                $data['nombre'],
                $data['descripcion'],
                $data['categoria_id'],
                $data['codigo_producto'],
                $data['marca'],
                $data['modelo'],
                $data['especificaciones'] ?? null,
                $data['precio'],
                $data['stock'],
                $data['stock_minimo'],
                $data['proveedor'],
                $data['estado']
            ]);

            $componenteId = $db->getConnection()->lastInsertId();

            // Registrar movimiento de stock inicial
            $this->registrarMovimientoStock(
                $componenteId,
                'entrada',
                $data['stock'],
                'Stock inicial al crear componente',
                null
            );

            DB::commit();

            return $componenteId;

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al crear componente: " . $e->getMessage());
        }
    }

    /**
     * Actualizar componente
     */
    public function actualizarComponente(int $id, array $data): bool
    {
        try {
            $db = DB::getInstance();
            DB::beginTransaction();

            // Obtener stock actual para detectar cambios
            $componenteActual = $this->obtenerComponentePorId($id);
            $stockAnterior = $componenteActual->stock;

            $query = "
                UPDATE componentes 
                SET nombre = ?, descripcion = ?, categoria_id = ?, codigo_producto = ?, 
                    marca = ?, modelo = ?, especificaciones = ?, precio = ?, stock = ?, 
                    stock_minimo = ?, proveedor = ?, estado = ?, fecha_actualizacion = NOW()
                WHERE id = ?
            ";

            $stmt = $db->query($query, [
                $data['nombre'],
                $data['descripcion'],
                $data['categoria_id'],
                $data['codigo_producto'],
                $data['marca'],
                $data['modelo'],
                $data['especificaciones'] ?? null,
                $data['precio'],
                $data['stock'],
                $data['stock_minimo'],
                $data['proveedor'],
                $data['estado'],
                $id
            ]);

            // Registrar cambio de stock si hay diferencia
            $stockNuevo = $data['stock'];
            if ($stockAnterior != $stockNuevo) {
                $diferencia = $stockNuevo - $stockAnterior;
                $tipoMovimiento = $diferencia > 0 ? 'entrada' : 'salida';
                $cantidad = abs($diferencia);
                
                $this->registrarMovimientoStock(
                    $id,
                    $tipoMovimiento,
                    $cantidad,
                    'Ajuste manual de stock',
                    auth()->id ?? null
                );
            }

            DB::commit();

            return $stmt->rowCount() > 0;

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al actualizar componente: " . $e->getMessage());
        }
    }

    /**
     * Eliminar componente físicamente
     */
    public function eliminarComponente(int $id): bool
    {
        try {
            $db = DB::getInstance();
            DB::beginTransaction();

            // Eliminar movimientos de stock asociados
            $stmt = $db->query("DELETE FROM movimientos_stock WHERE componente_id = ?", [$id]);

            // Eliminar componente
            $stmt = $db->query("DELETE FROM componentes WHERE id = ?", [$id]);
            $resultado = $stmt->rowCount() > 0;

            DB::commit();

            return $resultado;

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al eliminar componente: " . $e->getMessage());
        }
    }

    /**
     * Marcar componente como descontinuado
     */
    public function descontinuarComponente(int $id): bool
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->query("UPDATE componentes SET estado = 'Descontinuado', fecha_actualizacion = NOW() WHERE id = ?", [$id]);
            return $stmt->rowCount() > 0;

        } catch (Exception $e) {
            throw new Exception("Error al descontinuar componente: " . $e->getMessage());
        }
    }

    /**
     * Ajustar stock de componente
     */
    public function ajustarStock(int $componenteId, string $tipoMovimiento, int $cantidad, string $motivo, ?int $usuarioId = null): array
    {
        try {
            $db = DB::getInstance();
            DB::beginTransaction();

            $componente = $this->obtenerComponentePorId($componenteId);
            if (!$componente) {
                throw new Exception("Componente no encontrado");
            }

            $stockActual = $componente->stock;
            $nuevoStock = $stockActual;

            switch ($tipoMovimiento) {
                case 'entrada':
                    $nuevoStock += $cantidad;
                    break;
                case 'salida':
                    if ($stockActual < $cantidad) {
                        throw new Exception("Stock insuficiente. Stock actual: $stockActual");
                    }
                    $nuevoStock -= $cantidad;
                    break;
                case 'ajuste':
                    $nuevoStock = $cantidad;
                    break;
                default:
                    throw new Exception("Tipo de movimiento no válido");
            }

            // Actualizar stock en la tabla componentes
            $stmt = $db->query("UPDATE componentes SET stock = ?, fecha_actualizacion = NOW() WHERE id = ?", [$nuevoStock, $componenteId]);

            // Registrar movimiento
            $this->registrarMovimientoStock($componenteId, $tipoMovimiento, $cantidad, $motivo, $usuarioId);

            // Actualizar estado automático basado en stock
            $this->actualizarEstadoAutomatico($componenteId, $nuevoStock);

            DB::commit();

            return [
                'stock_anterior' => $stockActual,
                'nuevo_stock' => $nuevoStock,
                'diferencia' => $nuevoStock - $stockActual
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Error al ajustar stock: " . $e->getMessage());
        }
    }

    /**
     * Verificar si una categoría es válida para componentes
     */
    public function categoriaValida(int $categoriaId): bool
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->query("SELECT COUNT(*) FROM categorias WHERE id = ? AND tipo = 'componente' AND estado = 1", [$categoriaId]);
            return $stmt->fetchColumn() > 0;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Verificar si un código de producto ya existe
     */
    public function codigoProductoExiste(string $codigo, ?int $excludeId = null): bool
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT COUNT(*) FROM componentes WHERE codigo_producto = ?";
            $params = [$codigo];

            if ($excludeId) {
                $query .= " AND id != ?";
                $params[] = $excludeId;
            }

            $stmt = $db->query($query, $params);
            return $stmt->fetchColumn() > 0;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtener categorías de componentes
     */
    public function obtenerCategoriasComponentes(): array
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->query("
                SELECT * FROM categorias 
                WHERE tipo = 'componente' AND estado = 1 
                ORDER BY nombre ASC
            ");
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);

        } catch (Exception $e) {
            throw new Exception("Error al obtener categorías: " . $e->getMessage());
        }
    }

    /**
     * Obtener marcas únicas
     */
    public function obtenerMarcasUnicas(): array
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->query("
                SELECT DISTINCT marca 
                FROM componentes 
                WHERE marca IS NOT NULL AND marca != '' 
                ORDER BY marca ASC
            ");
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);

        } catch (Exception $e) {
            throw new Exception("Error al obtener marcas: " . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas de componentes
     */
    public function obtenerEstadisticasComponentes(): array
    {
        try {
            $db = DB::getInstance();
            $stats = [];

            // Total de componentes
            $stmt = $db->query("SELECT COUNT(*) FROM componentes WHERE estado != 'Descontinuado'");
            $stats['total'] = $stmt->fetchColumn();

            // Por estado
            $stmt = $db->query("
                SELECT estado, COUNT(*) as cantidad 
                FROM componentes 
                GROUP BY estado
            ");
            $estadoStats = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            $stats['por_estado'] = $estadoStats;

            // Stock bajo
            $stmt = $db->query("SELECT COUNT(*) FROM componentes WHERE stock <= stock_minimo AND estado != 'Descontinuado'");
            $stats['stock_bajo'] = $stmt->fetchColumn();

            // Valor total del inventario
            $stmt = $db->query("SELECT SUM(precio * stock) FROM componentes WHERE estado != 'Descontinuado'");
            $stats['valor_inventario'] = $stmt->fetchColumn() ?? 0;

            return $stats;

        } catch (Exception $e) {
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }

    /**
     * Obtener componentes con stock bajo
     */
    public function obtenerComponentesStockBajo(int $limite = 20): array
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->query("
                SELECT c.*, cat.nombre as categoria_nombre 
                FROM componentes c 
                LEFT JOIN categorias cat ON c.categoria_id = cat.id 
                WHERE c.stock <= c.stock_minimo AND c.estado != 'Descontinuado'
                ORDER BY (c.stock / c.stock_minimo) ASC, c.nombre ASC 
                LIMIT ?
            ", [$limite]);
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);

        } catch (Exception $e) {
            throw new Exception("Error al obtener componentes con stock bajo: " . $e->getMessage());
        }
    }

    /**
     * Buscar componentes
     */
    public function buscarComponentes(string $termino, int $limite = 10): array
    {
        try {
            $db = DB::getInstance();
            $termino = '%' . $termino . '%';
            
            $stmt = $db->query("
                SELECT c.id, c.nombre, c.codigo_producto, c.marca, c.modelo, c.precio, c.stock, 
                       cat.nombre as categoria_nombre
                FROM componentes c 
                LEFT JOIN categorias cat ON c.categoria_id = cat.id 
                WHERE c.estado != 'Descontinuado' 
                  AND (c.nombre LIKE ? OR c.codigo_producto LIKE ? OR c.marca LIKE ? OR c.modelo LIKE ?)
                ORDER BY c.nombre ASC 
                LIMIT ?
            ", [$termino, $termino, $termino, $termino, $limite]);
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);

        } catch (Exception $e) {
            throw new Exception("Error al buscar componentes: " . $e->getMessage());
        }
    }

    /**
     * Verificar si componente tiene ventas asociadas
     */
    public function tieneVentasAsociadas(int $componenteId): bool
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->query("
                SELECT COUNT(*) 
                FROM detalle_ventas 
                WHERE tipo_producto = 'componente' AND producto_id = ?
            ", [$componenteId]);
            
            return $stmt->fetchColumn() > 0;

        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtener historial de ventas de un componente
     */
    public function obtenerHistorialVentas(int $componenteId, int $limite = 20): array
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->query("
                SELECT dv.*, v.numero_venta, v.fecha_venta, v.estado as estado_venta,
                       u.nombre as cliente_nombre, u.apellido as cliente_apellido
                FROM detalle_ventas dv
                JOIN ventas v ON dv.venta_id = v.id
                LEFT JOIN users u ON v.cliente_id = u.id
                WHERE dv.tipo_producto = 'componente' AND dv.producto_id = ?
                ORDER BY v.fecha_venta DESC
                LIMIT ?
            ", [$componenteId, $limite]);
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);

        } catch (Exception $e) {
            throw new Exception("Error al obtener historial de ventas: " . $e->getMessage());
        }
    }

    /**
     * Obtener movimientos de stock
     */
    public function obtenerMovimientosStock(int $componenteId, int $limite = 50): array
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->query("
                SELECT ms.*, u.nombre as usuario_nombre, u.apellido as usuario_apellido
                FROM movimientos_stock ms
                LEFT JOIN users u ON ms.usuario_id = u.id
                WHERE ms.componente_id = ?
                ORDER BY ms.fecha_movimiento DESC
                LIMIT ?
            ", [$componenteId, $limite]);
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);

        } catch (Exception $e) {
            throw new Exception("Error al obtener movimientos de stock: " . $e->getMessage());
        }
    }

    /**
     * Generar código de producto automático
     */
    private function generarCodigoProducto(int $categoriaId): string
    {
        try {
            $db = DB::getInstance();
            
            // Obtener prefijo de categoría
            $stmt = $db->query("SELECT nombre FROM categorias WHERE id = ?", [$categoriaId]);
            $categoriaNombre = $stmt->fetchColumn();
            
            $prefijo = strtoupper(substr($categoriaNombre, 0, 3));
            
            // Obtener último número
            $stmt = $db->query("
                SELECT codigo_producto 
                FROM componentes 
                WHERE codigo_producto LIKE ? 
                ORDER BY id DESC 
                LIMIT 1
            ", [$prefijo . '%']);
            $ultimoCodigo = $stmt->fetchColumn();
            
            if ($ultimoCodigo) {
                $numero = (int)substr($ultimoCodigo, -4) + 1;
            } else {
                $numero = 1;
            }
            
            return $prefijo . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);

        } catch (Exception $e) {
            // Fallback genérico
            return 'COMP-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        }
    }

    /**
     * Registrar movimiento de stock
     */
    private function registrarMovimientoStock(int $componenteId, string $tipo, int $cantidad, string $motivo, ?int $usuarioId): void
    {
        try {
            $db = DB::getInstance();
            
            // Verificar si la tabla existe, si no existe la creamos
            $this->crearTablaMovimientosStock();

            $stmt = $db->query("
                INSERT INTO movimientos_stock 
                (componente_id, tipo_movimiento, cantidad, motivo, usuario_id, fecha_movimiento) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ", [$componenteId, $tipo, $cantidad, $motivo, $usuarioId]);

        } catch (Exception $e) {
            // Log del error pero no fallar la operación principal
            error_log("Error al registrar movimiento de stock: " . $e->getMessage());
        }
    }

    /**
     * Actualizar estado automático basado en stock
     */
    private function actualizarEstadoAutomatico(int $componenteId, int $nuevoStock): void
    {
        try {
            $db = DB::getInstance();
            $nuevoEstado = 'Disponible';
            
            if ($nuevoStock <= 0) {
                $nuevoEstado = 'Agotado';
            }

            $stmt = $db->query("
                UPDATE componentes 
                SET estado = ? 
                WHERE id = ? AND estado != 'Descontinuado'
            ", [$nuevoEstado, $componenteId]);

        } catch (Exception $e) {
            // Log del error pero no fallar la operación principal
            error_log("Error al actualizar estado automático: " . $e->getMessage());
        }
    }

    /**
     * Crear tabla movimientos_stock si no existe
     */
    private function crearTablaMovimientosStock(): void
    {
        try {
            $db = DB::getInstance();
            $createTable = "
                CREATE TABLE IF NOT EXISTS movimientos_stock (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    componente_id INT NOT NULL,
                    tipo_movimiento ENUM('entrada', 'salida', 'ajuste') NOT NULL,
                    cantidad INT NOT NULL,
                    motivo VARCHAR(255) NOT NULL,
                    usuario_id INT NULL,
                    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    INDEX idx_componente (componente_id),
                    INDEX idx_fecha (fecha_movimiento),
                    FOREIGN KEY (componente_id) REFERENCES componentes(id) ON DELETE CASCADE,
                    FOREIGN KEY (usuario_id) REFERENCES users(id) ON DELETE SET NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
            ";
            
            $db->getConnection()->exec($createTable);

        } catch (Exception $e) {
            error_log("Error al crear tabla movimientos_stock: " . $e->getMessage());
        }
    }
}
