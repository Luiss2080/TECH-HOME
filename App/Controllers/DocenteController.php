<?php

namespace App\Controllers;

use Core\Controller;
use Exception;

class DocenteController extends Controller
{
    public function dashboard()
    {
        return view('docente.dashboard', ['title' => 'Dashboard Docente', 'ruta' => '/docente/dashboard']);
    }
}
