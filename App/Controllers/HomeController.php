<?php

namespace App\Controllers;

use App\Services\AdminService;

class HomeController extends Controller
{
    public function index()
    {
        if (!auth()) {
            return redirect(route('login'));
        }
        // Crear instancia del servicio
        $adminService = new AdminService();

        // Obtener todos los datos del dashboard usando el método refactorizado
        $datosDashboard = $adminService->showDashboard();
        return view('home.index', [
            'title' => 'Bienvenido',
            'estadisticas' => $datosDashboard['estadisticas'],
            'actividades_recientes' => $datosDashboard['actividades_recientes'],
            'sesiones_activas' => $datosDashboard['sesiones_activas'],
            'ventas_recientes' => $datosDashboard['ventas_recientes'],
            'libros_recientes' => $datosDashboard['libros_recientes'],
            'componentes_recientes' => $datosDashboard['componentes_recientes'],
            'resumen_sistema' => $datosDashboard['resumen_sistema'],
            'usuario' => $datosDashboard['usuario']
        ]);
    }
}
