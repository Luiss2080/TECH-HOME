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
        if (!$this->userHasRole($user, $this->allowedRoles)) {
            Session::flash('error', 'No tienes permisos para acceder a esta página.');
            
            // Redirigir al dashboard o página principal según el rol del usuario
            return $this->redirectBasedOnRole($user);
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
        $userRole = $user->rol();
        
        if (!$userRole) {
            return false;
        }

        // Verificar si el nombre del rol está en los roles permitidos
        return in_array(strtolower($userRole->nombre), array_map('strtolower', $allowedRoles));
    }

    /**
     * Redirige basado en el rol del usuario
     *
     * @param User $user
     * @return Response
     */
    protected function redirectBasedOnRole($user)
    {
        $userRole = $user->rol();
        
        if (!$userRole) {
            return redirect('/');
        }

        // Redirigir según el rol
        switch (strtolower($userRole->nombre)) {
            case 'administrador':
                return redirect(route('dashboard'));
            case 'docente':
                return redirect(route('dashboard')); // O ruta específica para docentes
            case 'estudiante':
                return redirect(route('dashboard')); // O ruta específica para estudiantes
            default:
                return redirect('/');
        }
    }

    /**
     * Método estático para crear instancias con roles específicos
     *
     * @param string|array $roles
     * @return static
     */
    public static function roles($roles)
    {
        return new static($roles);
    }
}
