<?php

namespace App\Services;

use App\Models\DashboardStats;
use App\Models\User;
use Exception;

class AdminService
{
    public function showDashboard(): array
    {
        return [
            'estadisticas' => DashboardStats::getGeneralStats(),
            'actividades_recientes' => DashboardStats::getRecentActivities(5),
            'sesiones_activas' => DashboardStats::getActiveSessions(5),
            'ventas_recientes' => DashboardStats::getRecentSales(5),
            'libros_recientes' => DashboardStats::getRecentBooks(5),
            'componentes_recientes' => DashboardStats::getRecentComponents(5),
            'resumen_sistema' => DashboardStats::getSystemSummary(),
            'usuario' => $this->getCurrentUserData()
        ];
    }

    public function getStatsForAjax(string $type = 'general'): array
    {

        switch ($type) {
            case 'general':
                return DashboardStats::getGeneralStats();
            case 'ventas':
                return DashboardStats::getRecentSales(10);
            case 'actividades':
                return DashboardStats::getRecentActivities(10);
            case 'sesiones':
                return DashboardStats::getActiveSessions(10);
            case 'libros':
                return DashboardStats::getRecentBooks(10);
            case 'componentes':
                return DashboardStats::getRecentComponents(10);
            default:
                throw new Exception("Tipo de estadística no válido: $type");
        }
    }

    public function updateMetrics(): array
    {

        if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') !== 'xmlhttprequest') {
            throw new Exception('Solo se permiten peticiones AJAX');
        }

        return [
            'estadisticas' => DashboardStats::getGeneralStats(),
            'resumen_sistema' => DashboardStats::getSystemSummary()
        ];
    }


    private function redirectByRole(string $role): void
    {
        $routes = [
            'docente' => 'docente.dashboard',
            'estudiante' => 'estudiante.dashboard',
            'vendedor' => 'vendedor.dashboard',
            'invitado' => 'home'
        ];
        $routeName = $routes[$role] ?? 'home';
        redirect(route($routeName));
    }

    private function getCurrentUserData(): array
    {
        $user = auth();
        $roles = $user->roles();

        return [
            'id' => $user->id,
            'nombre' => $user->nombre,
            'apellido' => $user->apellido,
            'email' => $user->email,
            'roles' => $roles ? array_column($roles, 'nombre') : ['Sin rol']
        ];
    }

    public static function formatNumber(float $number, int $decimals = 2): string
    {
        return number_format($number, $decimals, '.', ',');
    }

    public static function formatCurrency(float $amount): string
    {
        return 'Bs. ' . self::formatNumber($amount, 2);
    }

    public static function getStatusClass(string $status): string
    {
        $classes = [
            'Activo' => 'success',
            'Inactivo' => 'secondary',
            'Pendiente' => 'warning',
            'Completada' => 'success',
            'Cancelada' => 'danger',
            'Publicado' => 'success',
            'Borrador' => 'secondary',
            'Archivado' => 'warning'
        ];

        return $classes[$status] ?? 'secondary';
    }
}
