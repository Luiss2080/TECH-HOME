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

    public function reportes()
    {
        // TODO: Implementar lógica para reportes
        return view('reportes.index', ['title' => 'Reportes - En Desarrollo']);
    }

    public function configuracion()
    {
        // TODO: Implementar lógica para configuración
        return view('configuracion.index', ['title' => 'Configuración - En Desarrollo']);
    }

    public function estudiantes()
    {
        // TODO: Implementar lógica para estudiantes
        return view('estudiantes.index', ['title' => 'Estudiantes - En Desarrollo']);
    }

    public function cursos()
    {
        // TODO: Implementar lógica para cursos
        return view('cursos.index', ['title' => 'Cursos - En Desarrollo']);
    }

    public function usuarios()
    {
        // TODO: Implementar lógica para usuarios
        return view('usuarios.index', ['title' => 'Usuarios - En Desarrollo']);
    }

    public function libros()
    {
        // TODO: Implementar lógica para libros
        return view('libros.index', ['title' => 'Biblioteca - En Desarrollo']);
    }

    public function materiales()
    {
        // TODO: Implementar lógica para materiales
        return view('materiales.index', ['title' => 'Materiales - En Desarrollo']);
    }

    public function laboratorios()
    {
        // TODO: Implementar lógica para laboratorios
        return view('laboratorios.index', ['title' => 'Laboratorios - En Desarrollo']);
    }

    public function aulaVirtual()
    {
        // TODO: Implementar lógica para aula virtual
        return view('aula-virtual.index', ['title' => 'Aula Virtual - En Desarrollo']);
    }

    public function evaluaciones()
    {
        // TODO: Implementar lógica para evaluaciones
        return view('evaluaciones.index', ['title' => 'Evaluaciones - En Desarrollo']);
    }

    public function certificados()
    {
        // TODO: Implementar lógica para certificados
        return view('certificados.index', ['title' => 'Certificados - En Desarrollo']);
    }

    public function crearUsuario()
    {
        // TODO: Implementar lógica para crear usuario
        return view('usuarios.crear', ['title' => 'Crear Usuario - En Desarrollo']);
    }

    public function crearCurso()
    {
        // TODO: Implementar lógica para crear curso
        return view('cursos.crear', ['title' => 'Crear Curso - En Desarrollo']);
    }

    public function crearComponente()
    {
        // TODO: Implementar lógica para crear componente
        return view('componentes.crear', ['title' => 'Crear Componente - En Desarrollo']);
    }

    public function crearVenta()
    {
        // TODO: Implementar lógica para crear venta
        return view('ventas.crear', ['title' => 'Crear Venta - En Desarrollo']);
    }

    public function crearLibro()
    {
        // TODO: Implementar lógica para crear libro
        return view('libros.crear', ['title' => 'Crear Libro - En Desarrollo']);
    }

    // Métodos adicionales necesarios para los enlaces de la vista
    public function componentes()
    {
        // TODO: Implementar lógica para componentes
        return view('componentes.index', ['title' => 'Componentes - En Desarrollo']);
    }

    public function ventas()
    {
        // TODO: Implementar lógica para ventas
        return view('ventas.index', ['title' => 'Ventas - En Desarrollo']);
    }
}
