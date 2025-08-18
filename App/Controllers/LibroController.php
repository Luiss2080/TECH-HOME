<?php

namespace App\Controllers;

use Core\Controller;

class LibroController extends Controller
{
    public function libros()
    {
        return view('libros.index', [
            'title' => 'Biblioteca - Gestión de Libros',
            'ruta' => '/libros'
        ]);
    }

    public function crearLibro()
    {
        return view('libros.crear', [
            'title' => 'Añadir Nuevo Libro',
            'ruta' => '/libros/crear'
        ]);
    }
}
