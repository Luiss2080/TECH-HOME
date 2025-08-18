<?php

namespace App\Controllers;

use Core\Controller;

class CursoController extends Controller
{
    public function cursos()
    {
        return view('cursos.index', [
            'title' => 'Gestión de Cursos',
            'ruta' => '/cursos'
        ]);
    }

    public function crearCurso()
    {
        return view('cursos.crear', [
            'title' => 'Crear Nuevo Curso',
            'ruta' => '/cursos/crear'
        ]);
    }
}
