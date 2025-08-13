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
        $adminControlador = new AdminService();

        // Obtener todos los datos del dashboard
        $datosDashboard = $adminControlador->prepararDatosDashboard();

        // Extraer variables para usar en la vista
        $estadisticas = $datosDashboard['estadisticas'];
        $actividades_recientes = $datosDashboard['actividades_recientes'];
        $sesiones_activas = $datosDashboard['sesiones_activas'];
        $ventas_recientes = $datosDashboard['ventas_recientes'];
        $libros_recientes = $datosDashboard['libros_recientes'];
        $componentes_recientes = $datosDashboard['componentes_recientes'];
        $resumen_sistema = $datosDashboard['resumen_sistema'];
        $usuario = $datosDashboard['usuario'];

        return view('home.index', [
            'title' => 'Bienvenido',
            'estadisticas' => $estadisticas,
            'actividades_recientes' => $actividades_recientes,
            'sesiones_activas' => $sesiones_activas,
            'ventas_recientes' => $ventas_recientes,
            'libros_recientes' => $libros_recientes,
            'componentes_recientes' => $componentes_recientes,
            'resumen_sistema' => $resumen_sistema,
            'usuario' => $usuario
        ]);
    }
}
