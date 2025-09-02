<?php

namespace App\Controllers;

use Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        // Si el usuario está autenticado, redirigir a su dashboard correspondiente
        $user = auth();
        if ($user) {
            $roles = $user->roles();
            if (!empty($roles)) {
                $firstRole = strtolower($roles[0]['nombre']);
                switch ($firstRole) {
                    case 'administrador':
                        return redirect(route('admin.dashboard'));
                    case 'estudiante':
                        return redirect(route('estudiante.dashboard'));
                    case 'docente':
                        return redirect(route('docente.dashboard'));
                }
            }
        }

        // Página de inicio pública
        return view('home.index', [
            'title' => 'Tech-Home - Inicio'
        ]);
    }
}
