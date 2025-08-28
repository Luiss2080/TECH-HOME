<?php

namespace App\Services;

use App\Models\Componente;
use Core\DB;
use PDO;
use Exception;

class StockCleanupService
{
    private $componenteService;

    public function __construct()
    {
        $this->componenteService = new ComponenteService();
    }

    /**
     * Limpiar reservas expiradas (ejecutar por cron)
     */
    public function limpiarReservasExpiradas(): array
    {
        try {
            $reservasLiberadas = $this->componenteService->limpiarReservasExpiradas();
            
            return [
                'exito' => true,
                'mensaje' => "Se liberaron $reservasLiberadas reservas expiradas",
                'reservas_liberadas' => $reservasLiberadas
            ];

        } catch (Exception $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al limpiar reservas: ' . $e->getMessage(),
                'reservas_liberadas' => 0
            ];
        }
    }

    /**
     * Actualizar estados automáticos basados en stock
     */
    public function actualizarEstadosAutomaticos(): array
    {
        try {
            $db = DB::getInstance();
            
            // Marcar como agotados los que tienen stock 0
            $stmt = $db->query("
                UPDATE componentes 
                SET estado = 'Agotado' 
                WHERE (stock - IFNULL(stock_reservado, 0)) <= 0 
                AND estado NOT IN ('Descontinuado', 'Agotado')
            ");
            $agotados = $stmt->rowCount();

            // Marcar como disponibles los que tienen stock suficiente
            $stmt = $db->query("
                UPDATE componentes 
                SET estado = 'Disponible' 
                WHERE (stock - IFNULL(stock_reservado, 0)) > stock_minimo 
                AND estado = 'Agotado'
            ");
            $disponibles = $stmt->rowCount();

            return [
                'exito' => true,
                'mensaje' => "Actualizados: $agotados agotados, $disponibles disponibles",
                'agotados' => $agotados,
                'disponibles' => $disponibles
            ];

        } catch (Exception $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al actualizar estados: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Generar alertas de stock bajo
     */
    public function generarAlertasStockBajo(): array
    {
        try {
            $db = DB::getInstance();
            
            // Obtener componentes con stock bajo que requieren alerta
            $stmt = $db->query("
                SELECT c.id, c.nombre, c.stock, c.stock_minimo, c.stock_reservado,
                       (c.stock - IFNULL(c.stock_reservado, 0)) as stock_disponible
                FROM componentes c
                WHERE c.alerta_stock_bajo = 1
                AND (c.stock - IFNULL(c.stock_reservado, 0)) <= c.stock_minimo
                AND c.estado != 'Descontinuado'
                ORDER BY stock_disponible ASC
            ");
            
            $alertas = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            return [
                'exito' => true,
                'alertas' => $alertas,
                'total_alertas' => count($alertas)
            ];

        } catch (Exception $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al generar alertas: ' . $e->getMessage(),
                'alertas' => []
            ];
        }
    }

    /**
     * Ejecutar limpieza completa (para ejecutar diariamente)
     */
    public function ejecutarLimpiezaDiaria(): array
    {
        $resultados = [];
        
        // Limpiar reservas expiradas
        $resultados['reservas'] = $this->limpiarReservasExpiradas();
        
        // Actualizar estados automáticos
        $resultados['estados'] = $this->actualizarEstadosAutomaticos();
        
        // Generar alertas
        $resultados['alertas'] = $this->generarAlertasStockBajo();
        
        return [
            'exito' => true,
            'mensaje' => 'Limpieza diaria ejecutada',
            'detalles' => $resultados,
            'fecha_ejecucion' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Obtener reporte de inconsistencias en stock
     */
    public function detectarInconsistenciasStock(): array
    {
        try {
            $db = DB::getInstance();
            
            $inconsistencias = [];
            
            // Stock negativo
            $stmt = $db->query("
                SELECT id, nombre, stock, stock_reservado 
                FROM componentes 
                WHERE stock < 0 OR stock_reservado < 0
            ");
            $inconsistencias['stock_negativo'] = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            // Stock reservado mayor que stock total
            $stmt = $db->query("
                SELECT id, nombre, stock, stock_reservado 
                FROM componentes 
                WHERE IFNULL(stock_reservado, 0) > stock
            ");
            $inconsistencias['reservado_excesivo'] = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            // Estados incorrectos
            $stmt = $db->query("
                SELECT id, nombre, stock, stock_reservado, estado 
                FROM componentes 
                WHERE (
                    (stock > 0 AND estado = 'Agotado') OR
                    (stock <= 0 AND estado = 'Disponible')
                )
                AND estado != 'Descontinuado'
            ");
            $inconsistencias['estados_incorrectos'] = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            return [
                'exito' => true,
                'inconsistencias' => $inconsistencias,
                'total_problemas' => array_sum(array_map('count', $inconsistencias))
            ];

        } catch (Exception $e) {
            return [
                'exito' => false,
                'mensaje' => 'Error al detectar inconsistencias: ' . $e->getMessage(),
                'inconsistencias' => []
            ];
        }
    }
}
