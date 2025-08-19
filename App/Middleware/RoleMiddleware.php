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
     * Permisos permitidos para esta instancia del middleware
     *
     * @var array
     */
    protected $allowedPermissions = [];
    
    protected $superAdminRole = 'Administrador';
    
    /**
     * Constructor que recibe los roles y permisos permitidos
     *
     * @param array $allowedRoles
     * @param array $allowedPermissions
     */
    public function __construct($allowedRoles = [], $allowedPermissions = [])
    {
        $this->allowedRoles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
        $this->allowedPermissions = is_array($allowedPermissions) ? $allowedPermissions : [$allowedPermissions];
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

        // Si no se especificaron roles ni permisos, permitir acceso
        if (empty($this->allowedRoles) && empty($this->allowedPermissions)) {
            return $next($request);
        }

        // Verificar si el usuario tiene uno de los roles permitidos O uno de los permisos permitidos
        if (!$this->isSuperAdmin($user) && !$this->userHasAccess($user)) {
            if (request()->isApiRequest()) {
                return Response::json([
                    'success' => false,
                    'message' => 'No tienes permisos para acceder a este recurso.',
                    'error' => 'insufficient_permissions',
                    'required_roles' => $this->allowedRoles,
                    'required_permissions' => $this->allowedPermissions,
                    'user_roles' => array_column($user->roles(), 'nombre'),
                    'user_permissions' => array_column($user->permissions(), 'nombre')
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
     * Verifica si el usuario tiene acceso (por rol o por permiso)
     *
     * @param User $user
     * @return bool
     */
    protected function userHasAccess($user)
    {
        // Verificar roles
        if (!empty($this->allowedRoles) && $this->userHasRole($user, $this->allowedRoles)) {
            return true;
        }
        // Verificar permisos
        if (!empty($this->allowedPermissions) && $this->userHasPermission($user, $this->allowedPermissions)) {
            return true;
        }

        return false;
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
        // Usar el nuevo sistema de roles
        return $user->hasAnyRole($allowedRoles);
    }

    /**
     * Verifica si el usuario tiene uno de los permisos permitidos
     *
     * @param User $user
     * @param array $allowedPermissions
     * @return bool
     */
    protected function userHasPermission($user, $allowedPermissions)
    {
        foreach ($allowedPermissions as $permission) {
            if ($user->can($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verifica si el usuario es super admin
     *
     * @param User $user
     * @return bool
     */
    protected function isSuperAdmin($user)
    {
        return $user->hasRole($this->superAdminRole);
    }
}
