<?php

namespace App\Controllers;

use Core\Controller;

class LaboratorioController extends Controller
{
    public function laboratorios()
    {
        return view('laboratorios.index', [
            'title' => 'Laboratorios Virtuales',
            'ruta' => '/laboratorios'
        ]);
    }
}
