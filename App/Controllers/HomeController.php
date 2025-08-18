<?php

namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        // Página de inicio pública
        return view('home.index', [
            'title' => 'Tech-Home - Inicio'
        ]);
    }
}
