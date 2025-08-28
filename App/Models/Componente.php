<?php

namespace App\Models;

use Core\Model;
use Core\DB;
use PDO;

class Componente extends Model
{
    protected $table = 'componentes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'codigo_producto',
        'marca',
        'modelo',
        'especificaciones',
        'imagen_principal',
        'imagenes_adicionales',
        'precio',
        'stock',
        'stock_minimo',
        'stock_reservado',
        'alerta_stock_bajo',
        'permite_venta_sin_stock',
        'estado'
    ];
    protected $timestamps = true;

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }

    // Scopes
    public static function disponibles()
    {
        return self::where('estado', '!=', 'Descontinuado')->where('stock', '>', 0);
    }

    public static function stockBajo()
    {
        return self::whereRaw('stock <= stock_minimo')->where('estado', '!=', 'Descontinuado');
    }

    public static function countStockBajo()
    {
        return self::whereRaw('stock <= stock_minimo')->where('estado', '!=', 'Descontinuado')->count();
    }

    // Métodos para manejo de stock avanzado

    /**
     * Calcular stock disponible real (descontando reservas)
     */
    public function getStockDisponible(): int
    {
        return max(0, $this->stock - ($this->stock_reservado ?? 0));
    }

    /**
     * Verificar si tiene stock suficiente para venta
     */
    public function tieneStockDisponible(int $cantidad = 1): bool
    {
        if ($this->permite_venta_sin_stock) {
            return true;
        }
        return $this->getStockDisponible() >= $cantidad;
    }

    /**
     * Verificar si el stock está bajo
     */
    public function isStockBajo(): bool
    {
        return $this->getStockDisponible() <= $this->stock_minimo;
    }

    /**
     * Reservar stock temporalmente
     */
    public function reservarStock(int $cantidad, string $motivo, string $referenciaType = 'venta_proceso', ?int $referenciaId = null, ?int $usuarioId = null): bool
    {
        if (!$this->tieneStockDisponible($cantidad)) {
            return false;
        }

        try {
            $db = DB::getInstance();
            DB::beginTransaction();

            // Actualizar stock reservado
            $stmt = $db->query(
                "UPDATE componentes SET stock_reservado = IFNULL(stock_reservado, 0) + ? WHERE id = ?",
                [$cantidad, $this->id]
            );

            // Registrar en tabla de reservas
            $stmt = $db->query(
                "INSERT INTO stock_reservado 
                (componente_id, cantidad, motivo, referencia_tipo, referencia_id, usuario_id, fecha_expiracion) 
                VALUES (?, ?, ?, ?, ?, ?, DATE_ADD(NOW(), INTERVAL 30 MINUTE))",
                [$this->id, $cantidad, $motivo, $referenciaType, $referenciaId, $usuarioId]
            );

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Liberar stock reservado
     */
    public function liberarStock(int $cantidad, ?int $reservaId = null): bool
    {
        try {
            $db = DB::getInstance();
            DB::beginTransaction();

            // Actualizar stock reservado
            $stmt = $db->query(
                "UPDATE componentes SET stock_reservado = GREATEST(0, IFNULL(stock_reservado, 0) - ?) WHERE id = ?",
                [$cantidad, $this->id]
            );

            // Marcar reserva como liberada
            if ($reservaId) {
                $stmt = $db->query(
                    "UPDATE stock_reservado SET estado = 'liberado' WHERE id = ? AND componente_id = ?",
                    [$reservaId, $this->id]
                );
            } else {
                // Liberar la reserva más antigua activa
                $stmt = $db->query(
                    "UPDATE stock_reservado SET estado = 'liberado' 
                     WHERE componente_id = ? AND estado = 'activo' AND cantidad <= ?
                     ORDER BY fecha_reserva ASC LIMIT 1",
                    [$this->id, $cantidad]
                );
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Confirmar venta y reducir stock real
     */
    public function confirmarVenta(int $cantidad, int $ventaId, int $usuarioId): bool
    {
        try {
            $db = DB::getInstance();
            DB::beginTransaction();

            $stockAnterior = $this->stock;

            // Reducir stock real
            $stmt = $db->query(
                "UPDATE componentes SET 
                    stock = GREATEST(0, stock - ?),
                    stock_reservado = GREATEST(0, IFNULL(stock_reservado, 0) - ?),
                    fecha_actualizacion = NOW()
                 WHERE id = ?",
                [$cantidad, $cantidad, $this->id]
            );

            // Obtener stock nuevo
            $stmt = $db->query("SELECT stock FROM componentes WHERE id = ?", [$this->id]);
            $stockNuevo = $stmt->fetchColumn();

            // Registrar movimiento de stock
            $stmt = $db->query(
                "INSERT INTO movimientos_stock 
                (componente_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, motivo, referencia_tipo, referencia_id, usuario_id) 
                VALUES (?, 'salida', ?, ?, ?, 'Venta confirmada', 'venta', ?, ?)",
                [$this->id, $cantidad, $stockAnterior, $stockNuevo, $ventaId, $usuarioId]
            );

            // Marcar reserva como completada
            $stmt = $db->query(
                "UPDATE stock_reservado SET estado = 'completado' 
                 WHERE componente_id = ? AND referencia_tipo = 'venta_proceso' AND referencia_id = ?",
                [$this->id, $ventaId]
            );

            // Actualizar estado automático si se agotó
            if ($stockNuevo <= 0) {
                $stmt = $db->query(
                    "UPDATE componentes SET estado = 'Agotado' WHERE id = ? AND estado != 'Descontinuado'",
                    [$this->id]
                );
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Obtener estadísticas de stock
     */
    public static function getEstadisticasStock(): array
    {
        try {
            $db = DB::getInstance();
            
            $stmt = $db->query("
                SELECT 
                    COUNT(*) as total_componentes,
                    SUM(CASE WHEN stock <= 0 THEN 1 ELSE 0 END) as agotados,
                    SUM(CASE WHEN stock <= stock_minimo AND stock > 0 THEN 1 ELSE 0 END) as stock_bajo,
                    SUM(stock * precio) as valor_inventario,
                    SUM(IFNULL(stock_reservado, 0)) as total_reservado
                FROM componentes 
                WHERE estado != 'Descontinuado'
            ");
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        } catch (\Exception $e) {
            return [
                'total_componentes' => 0,
                'agotados' => 0,
                'stock_bajo' => 0,
                'valor_inventario' => 0,
                'total_reservado' => 0
            ];
        }
    }
}
