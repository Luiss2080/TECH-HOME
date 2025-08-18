<?php

namespace App\Controllers;

use Core\Controller;
use Exception;

class EstudianteController extends Controller 
{
    public function estudiantes()
    {
        return view('estudiantes.dashboard', [
            'title' => 'Dashboard Estudiante',
            'ruta' => '/estudiantes/dashboard'
        ]);
    }
}
