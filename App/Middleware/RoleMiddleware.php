<?php

namespace App\Middleware;

use App\Models\User;
use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\Session;

class RoleMiddleware implements Middleware
{
    /**
     * Roles permitidos para esta instancia del middleware
     *
     * @var array
     */
    protected $allowedRoles = [];
    protected $superAdminRole = 'administrador';
    /**
     * Constructor que recibe los roles permitidos
     *
     * @param array $allowedRoles
     */
    public function __construct($allowedRoles = [])
    {
        $this->allowedRoles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
    }

    /**
     * Maneja la solicitud entrante.
     *
     * @param Request $request
     * @param callable $next
     * @return Response
     */
    public function handle(Request $request, callable $next)
    {
        // Verificar si el usuario está autenticado
        if (!Session::has('user')) {
            Session::flash('error', 'Debes iniciar sesión para acceder a esta página.');
            return redirect(route('login'));
        }

        // Obtener el usuario actual
        $user = auth();
        if (!$user) {
            Session::flash('error', 'Usuario no válido.');
            return redirect(route('login'));
        }

        // Si no se especificaron roles, permitir acceso
        if (empty($this->allowedRoles)) {
            return $next($request);
        }

        // Verificar si el usuario tiene uno de los roles permitidos
        if (!$this->isSuperAdmin($user) && !$this->userHasRole($user, $this->allowedRoles)) {
            if (request()->isApiRequest()) {
                return Response::json([
                    'success' => false,
                    'message' => 'No tienes permisos para acceder a este recurso.',
                    'error' => 'insufficient_permissions',
                    'required_roles' => $this->allowedRoles,
                    'user_role' => $user->rol() ? $user->rol()->nombre : null
                ], 403);
            }
            // Para peticiones web, mostrar la página 403
            Session::flash('error', 'No tienes permisos para acceder a esta página.');
            return view(view: 'errors.403', statusCode: 403);
        }

        // Si el usuario tiene el rol correcto, continuar
        return $next($request);
    }


    /**
     * Verifica si el usuario tiene uno de los roles permitidos
     *
     * @param User $user
     * @param array $allowedRoles
     * @return bool
     */
    protected function userHasRole($user, $allowedRoles)
    {
        // Si el user no tiene rol, denegar acceso
        if (!$user->rol_id) {
            return false;
        }

        // Obtener el rol del usuario
        $userRole = $user->rol() ?? null;

        if (!$userRole) {
            return false;
        }

        // Verificar si el nombre del rol está en los roles permitidos
        return in_array(strtolower($userRole->nombre), array_map('strtolower', $allowedRoles));
    }


    protected function isSuperAdmin($user)
    {
        return strtolower($user->rol()->nombre) === strtolower($this->superAdminRole);
    }
}
