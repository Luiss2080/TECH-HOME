<?php

namespace App\Controllers;

use Core\Controller;

class MaterialController extends Controller
{
    public function materiales()
    {
        return view('materiales.index', [
            'title' => 'Gestión de Materiales',
            'ruta' => '/materiales'
        ]);
    }
}
