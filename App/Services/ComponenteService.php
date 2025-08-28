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
                0, // stock anterior
                $data['stock'], // stock nuevo
                'Stock inicial al crear componente',
                'ajuste_manual',
                null, // referencia_id
                null  // usuario_id
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
                    $stockAnterior,
                    $stockNuevo,
                    'Ajuste manual de stock',
                    'ajuste_manual',
                    null,
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
            $stockReservado = $componente->stock_reservado ?? 0;
            $nuevoStock = $stockActual;

            switch ($tipoMovimiento) {
                case 'entrada':
                    $nuevoStock += $cantidad;
                    break;
                case 'salida':
                    $stockDisponible = $stockActual - $stockReservado;
                    if ($stockDisponible < $cantidad) {
                        throw new Exception("Stock insuficiente. Stock disponible: $stockDisponible");
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
            $this->registrarMovimientoStock($componenteId, $tipoMovimiento, $cantidad, $stockActual, $nuevoStock, $motivo, 'ajuste_manual', null, $usuarioId);

            // Actualizar estado automático basado en stock
            $this->actualizarEstadoAutomatico($componenteId, $nuevoStock);

            DB::commit();

            return [
                'stock_anterior' => $stockActual,
                'nuevo_stock' => $nuevoStock,
                'stock_reservado' => $stockReservado,
                'stock_disponible' => max(0, $nuevoStock - $stockReservado),
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

    // =============== MÉTODOS ESPECÍFICOS PARA VENTAS ===============

    /**
     * Verificar disponibilidad para venta
     */
    public function verificarDisponibilidadVenta(int $componenteId, int $cantidad): array
    {
        try {
            $componente = $this->obtenerComponentePorId($componenteId);
            
            if (!$componente) {
                return [
                    'disponible' => false,
                    'mensaje' => 'Componente no encontrado',
                    'stock_disponible' => 0
                ];
            }

            if ($componente->estado === 'Descontinuado') {
                return [
                    'disponible' => false,
                    'mensaje' => 'Componente descontinuado',
                    'stock_disponible' => 0
                ];
            }

            $stockDisponible = $componente->stock - ($componente->stock_reservado ?? 0);

            // Si permite venta sin stock
            if ($componente->permite_venta_sin_stock ?? false) {
                return [
                    'disponible' => true,
                    'mensaje' => 'Disponible (pre-orden)',
                    'stock_disponible' => $stockDisponible,
                    'es_preorden' => true
                ];
            }

            if ($stockDisponible >= $cantidad) {
                return [
                    'disponible' => true,
                    'mensaje' => 'Stock suficiente',
                    'stock_disponible' => $stockDisponible
                ];
            }

            return [
                'disponible' => false,
                'mensaje' => "Stock insuficiente. Disponible: $stockDisponible",
                'stock_disponible' => $stockDisponible
            ];

        } catch (Exception $e) {
            return [
                'disponible' => false,
                'mensaje' => 'Error al verificar disponibilidad: ' . $e->getMessage(),
                'stock_disponible' => 0
            ];
        }
    }

    /**
     * Reservar stock para una venta
     */
    public function reservarStockVenta(int $componenteId, int $cantidad, int $ventaId, ?int $usuarioId = null): array
    {
        try {
            $verificacion = $this->verificarDisponibilidadVenta($componenteId, $cantidad);
            
            if (!$verificacion['disponible']) {
                return [
                    'exito' => false,
                    'mensaje' => $verificacion['mensaje']
                ];
            }

            $db = DB::getInstance();
            DB::beginTransaction();

            // Actualizar stock reservado
            $stmt = $db->query("
                UPDATE componentes 
                SET stock_reservado = IFNULL(stock_reservado, 0) + ? 
                WHERE id = ?
            ", [$cantidad, $componenteId]);

            // Registrar reserva
            $stmt = $db->query("
                INSERT INTO stock_reservado 
                (componente_id, cantidad, motivo, referencia_tipo, referencia_id, usuario_id, fecha_expiracion) 
                VALUES (?, ?, 'Reserva para venta', 'venta_proceso', ?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))
            ", [$componenteId, $cantidad, $ventaId, $usuarioId]);

            $reservaId = $db->getConnection()->lastInsertId();

            DB::commit();

            return [
                'exito' => true,
                'mensaje' => 'Stock reservado exitosamente',
                'reserva_id' => $reservaId
            ];

        } catch (Exception $e) {
            DB::rollBack();
            return [
                'exito' => false,
                'mensaje' => 'Error al reservar stock: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Confirmar venta y reducir stock definitivamente
     */
    public function confirmarVenta(int $componenteId, int $cantidad, int $ventaId, int $usuarioId): array
    {
        try {
            $db = DB::getInstance();
            DB::beginTransaction();

            $componente = $this->obtenerComponentePorId($componenteId);
            $stockAnterior = $componente->stock;
            $stockReservadoAnterior = $componente->stock_reservado ?? 0;

            // Reducir stock real y reservado
            $stmt = $db->query("
                UPDATE componentes 
                SET 
                    stock = GREATEST(0, stock - ?),
                    stock_reservado = GREATEST(0, IFNULL(stock_reservado, 0) - ?),
                    fecha_actualizacion = NOW()
                WHERE id = ?
            ", [$cantidad, $cantidad, $componenteId]);

            // Obtener nuevo stock
            $stmt = $db->query("SELECT stock, stock_reservado FROM componentes WHERE id = ?", [$componenteId]);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            $stockNuevo = $resultado['stock'];
            $stockReservadoNuevo = $resultado['stock_reservado'] ?? 0;

            // Registrar movimiento de stock
            $this->registrarMovimientoStock(
                $componenteId,
                'salida',
                $cantidad,
                $stockAnterior,
                $stockNuevo,
                "Venta confirmada #$ventaId",
                'venta',
                $ventaId,
                $usuarioId
            );

            // Marcar reserva como completada
            $stmt = $db->query("
                UPDATE stock_reservado 
                SET estado = 'completado' 
                WHERE componente_id = ? AND referencia_tipo = 'venta_proceso' AND referencia_id = ?
            ", [$componenteId, $ventaId]);

            // Actualizar estado automático
            $this->actualizarEstadoAutomatico($componenteId, $stockNuevo);

            DB::commit();

            return [
                'exito' => true,
                'mensaje' => 'Venta confirmada exitosamente',
                'stock_anterior' => $stockAnterior,
                'stock_nuevo' => $stockNuevo,
                'stock_disponible' => max(0, $stockNuevo - $stockReservadoNuevo)
            ];

        } catch (Exception $e) {
            DB::rollBack();
            return [
                'exito' => false,
                'mensaje' => 'Error al confirmar venta: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Liberar stock reservado (cancelar venta)
     */
    public function liberarStockReservado(int $componenteId, int $cantidad, int $ventaId): array
    {
        try {
            $db = DB::getInstance();
            DB::beginTransaction();

            // Reducir stock reservado
            $stmt = $db->query("
                UPDATE componentes 
                SET stock_reservado = GREATEST(0, IFNULL(stock_reservado, 0) - ?) 
                WHERE id = ?
            ", [$cantidad, $componenteId]);

            // Marcar reserva como liberada
            $stmt = $db->query("
                UPDATE stock_reservado 
                SET estado = 'liberado' 
                WHERE componente_id = ? AND referencia_tipo = 'venta_proceso' AND referencia_id = ? AND estado = 'activo'
            ", [$componenteId, $ventaId]);

            DB::commit();

            return [
                'exito' => true,
                'mensaje' => 'Stock liberado exitosamente'
            ];

        } catch (Exception $e) {
            DB::rollBack();
            return [
                'exito' => false,
                'mensaje' => 'Error al liberar stock: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Limpiar reservas expiradas
     */
    public function limpiarReservasExpiradas(): int
    {
        try {
            $db = DB::getInstance();
            DB::beginTransaction();

            // Obtener reservas expiradas
            $stmt = $db->query("
                SELECT sr.id, sr.componente_id, sr.cantidad 
                FROM stock_reservado sr 
                WHERE sr.estado = 'activo' 
                AND sr.fecha_expiracion < NOW()
            ");
            $reservasExpiradas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $totalLiberadas = 0;

            foreach ($reservasExpiradas as $reserva) {
                // Reducir stock reservado
                $stmt = $db->query("
                    UPDATE componentes 
                    SET stock_reservado = GREATEST(0, IFNULL(stock_reservado, 0) - ?) 
                    WHERE id = ?
                ", [$reserva['cantidad'], $reserva['componente_id']]);

                // Marcar como liberada
                $stmt = $db->query("
                    UPDATE stock_reservado 
                    SET estado = 'liberado' 
                    WHERE id = ?
                ", [$reserva['id']]);

                $totalLiberadas++;
            }

            DB::commit();
            return $totalLiberadas;

        } catch (Exception $e) {
            DB::rollBack();
            error_log("Error al limpiar reservas expiradas: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener componentes más vendidos
     */
    public function obtenerComponentesMasVendidos(int $limite = 10): array
    {
        try {
            $db = DB::getInstance();
            $stmt = $db->query("
                SELECT 
                    c.id,
                    c.nombre,
                    c.precio,
                    c.stock,
                    IFNULL(SUM(dv.cantidad), 0) as total_vendido,
                    IFNULL(SUM(dv.cantidad * dv.precio_unitario), 0) as ingresos_generados,
                    cat.nombre as categoria_nombre
                FROM componentes c
                LEFT JOIN detalle_ventas dv ON dv.producto_id = c.id AND dv.tipo_producto = 'componente'
                LEFT JOIN categorias cat ON c.categoria_id = cat.id
                WHERE c.estado != 'Descontinuado'
                GROUP BY c.id, c.nombre, c.precio, c.stock, cat.nombre
                ORDER BY total_vendido DESC, ingresos_generados DESC
                LIMIT ?
            ", [$limite]);
            
            return $stmt->fetchAll(PDO::FETCH_OBJ);

        } catch (Exception $e) {
            throw new Exception("Error al obtener componentes más vendidos: " . $e->getMessage());
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
    private function registrarMovimientoStock(int $componenteId, string $tipo, int $cantidad, int $stockAnterior, int $stockNuevo, string $motivo, string $referenciaType = 'ajuste_manual', ?int $referenciaId = null, ?int $usuarioId = null): void
    {
        try {
            $db = DB::getInstance();
            
            // Verificar si la tabla existe, si no existe la creamos
            $this->crearTablaMovimientosStock();

            $stmt = $db->query("
                INSERT INTO movimientos_stock 
                (componente_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, motivo, referencia_tipo, referencia_id, usuario_id, fecha_movimiento) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ", [$componenteId, $tipo, $cantidad, $stockAnterior, $stockNuevo, $motivo, $referenciaType, $referenciaId, $usuarioId]);

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
