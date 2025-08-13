<?php

namespace App\Models;

use App\Models\User;
use App\Models\Role;
use App\Models\Curso;
use App\Models\Libro;
use App\Models\Componente;
use App\Models\Venta;
use App\Models\SesionActiva;
use Exception;

/**
 * AdminModelo - Métodos específicos para funcionalidades administrativas
 */
class AdminModelo
{
    /**
     * Obtiene estadísticas completas del dashboard
     */
    public static function getCompleteStats(): array
    {
        try {
            return [
                'usuarios' => self::getUserStats(),
                'contenido' => self::getContentStats(),
                'ventas' => self::getSalesStats(),
                'sistema' => self::getSystemStats()
            ];
        } catch (Exception $e) {
            throw new Exception("Error al obtener estadísticas completas: " . $e->getMessage());
        }
    }

    /**
     * Estadísticas detalladas de usuarios
     */
    private static function getUserStats(): array
    {
        return [
            'total' => User::count(),
            'activos' => User::activos()->count(),
            'por_rol' => [
                'administradores' => User::porRol(1)->count(),
                'docentes' => User::porRol(2)->count(),
                'estudiantes' => User::porRol(3)->count(),
                'vendedores' => User::porRol(4)->count()
            ],
            'nuevos_hoy' => User::registradosHoy()->count(),
            'nuevos_semana' => User::recientes(7)->count()
        ];
    }

    /**
     * Estadísticas de contenido
     */
    private static function getContentStats(): array
    {
        return [
            'cursos' => [
                'total' => Curso::count(),
                'publicados' => Curso::publicados()->count(),
                'recientes' => Curso::recientes(7)->count()
            ],
            'libros' => [
                'total' => Libro::where('estado', '=', 1)->count(),
                'stock_bajo' => Libro::countStockBajo(),
                'gratuitos' => Libro::gratuitos()->count()
            ],
            'componentes' => [
                'total' => Componente::where('estado', '!=', 'Descontinuado')->count(),
                'stock_bajo' => Componente::countStockBajo(),
                'disponibles' => Componente::disponibles()->count()
            ]
        ];
    }

    /**
     * Estadísticas de ventas
     */
    private static function getSalesStats(): array
    {
        $ventasMes = Venta::delMes()->get();
        $ventasCompletadas = array_filter($ventasMes, fn($v) => $v->estado === 'Completada');
        
        return [
            'mes_actual' => [
                'total' => count($ventasMes),
                'monto' => array_sum(array_column($ventasMes, 'total')),
                'completadas' => count($ventasCompletadas),
                'pendientes' => count($ventasMes) - count($ventasCompletadas)
            ],
            'recientes' => Venta::recientes(3)->limit(5)->get(),
            'crecimiento' => self::calculateSalesGrowth()
        ];
    }

    /**
     * Estadísticas del sistema
     */
    private static function getSystemStats(): array
    {
        return [
            'sesiones_activas' => SesionActiva::where('activa', '=', 1)->count(),
            'version' => '2.0.0',
            'uptime' => '15 días, 8 horas',
            'storage' => [
                'used' => '2.5 GB',
                'total' => '50 GB',
                'percentage' => 5
            ]
        ];
    }

    /**
     * Calcula crecimiento de ventas
     */
    private static function calculateSalesGrowth(): float
    {
        try {
            $mesActual = Venta::delMes()->get();
            $mesAnterior = Venta::delMes(date('m', strtotime('-1 month')), date('Y'))->get();
            
            $totalActual = array_sum(array_column($mesActual, 'total'));
            $totalAnterior = array_sum(array_column($mesAnterior, 'total'));
            
            if ($totalAnterior == 0) return 0;
            
            return round((($totalActual - $totalAnterior) / $totalAnterior) * 100, 2);
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtiene alertas del sistema
     */
    public static function getSystemAlerts(): array
    {
        $alerts = [];

        $librosStockBajo = Libro::countStockBajo();
        $componentesStockBajo = Componente::countStockBajo();
        
        if ($librosStockBajo > 0) {
            $alerts[] = [
                'tipo' => 'warning',
                'mensaje' => "{$librosStockBajo} libros con stock bajo",
                'accion' => 'Revisar inventario de libros'
            ];
        }

        if ($componentesStockBajo > 0) {
            $alerts[] = [
                'tipo' => 'warning', 
                'mensaje' => "{$componentesStockBajo} componentes con stock bajo",
                'accion' => 'Revisar inventario de componentes'
            ];
        }

        $sesionesActivas = SesionActiva::where('activa', '=', 1)->count();
        if ($sesionesActivas > 50) {
            $alerts[] = [
                'tipo' => 'info',
                'mensaje' => "Alta actividad: {$sesionesActivas} usuarios conectados",
                'accion' => 'Monitorear rendimiento del servidor'
            ];
        }

        return $alerts;
    }

    /**
     * Genera reporte de actividad
     */
    public static function getActivityReport(int $days = 30): array
    {
        return [
            'periodo' => "{$days} días",
            'usuarios_nuevos' => User::recientes($days)->count(),
            'cursos_publicados' => Curso::recientes($days)->count(),
            'ventas_realizadas' => Venta::recientes($days)->count(),
            'actividad_general' => 'Alta'
        ];
    }
}