<?php

namespace App\Middleware;

use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\Session;

class AuthMiddleware implements Middleware
{
    /**
     * Maneja la solicitud entrante.
     *
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function handle(Request $request, callable $next)
    {
        // Verifica si el usuario está autenticado
        if (!$this->isAuthenticated()) {
            Session::flash('error', 'Debes iniciar sesión para acceder a esta página.');
            // back
            Session::set('back', $request->uri());
            return redirect(route('login'))->with('error', 'Debes iniciar sesión para acceder a esta página.');
        }
        $request->setUser(auth());
        // Si el usuario está autenticado, continúa con la solicitud
        return $next($request);
    }

    /**
     * Verifica si el usuario está autenticado.
     *
     * @return bool
     */
    protected function isAuthenticated()
    {
        // Aquí verificas si el usuario está autenticado
        // Por ejemplo, puedes verificar si existe una sesión de usuario
        return Session::has('user');
    }
}
