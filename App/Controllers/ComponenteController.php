<?php

namespace App\Controllers;

use Core\Controller;

class ComponenteController extends Controller
{
    public function componentes()
    {
        return view('componentes.index', [
            'title' => 'Gestión de Componentes',
            'ruta' => '/componentes'
        ]);
    }

    public function crearComponente()
    {
        return view('componentes.crear', [
            'title' => 'Crear Nuevo Componente',
            'ruta' => '/componentes/crear'
        ]);
    }
}
