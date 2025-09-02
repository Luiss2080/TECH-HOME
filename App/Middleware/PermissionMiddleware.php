<?php

namespace App\Middleware;

use App\Models\User;
use App\Models\SecurityLog;
use Core\Middleware;
use Core\Request;
use Core\Response;
use Core\Session;

class PermissionMiddleware implements Middleware
{
    /**
     * Verifica si el usuario tiene los permisos necesarios
     *
     * @param Request $request
     * @param callable $next
     * @param string $permission Permiso requerido
     * @return Response
     */
    public function handle(Request $request, callable $next, string $permission = '')
    {
        // Verificar si el usuario está autenticado
        if (!Session::has('user')) {
            SecurityLog::logAccessDenied($request->uri(), 'Usuario no autenticado');
            
            if ($this->isApiRequest($request)) {
                return Response::json([
                    'success' => false,
                    'message' => 'No estás autenticado.',
                    'error' => 'unauthenticated'
                ], 401);
            }

            Session::flash('error', 'Debes iniciar sesión para acceder a esta página.');
            return redirect(route('login'));
        }

        $user = Session::get('user');
        $userModel = User::find($user->id);

        // Verificar si tiene el permiso específico
        if ($permission && !$this->hasPermission($userModel, $permission)) {
            SecurityLog::logAccessDenied($request->uri(), "Permiso '$permission' denegado para usuario {$user->email}");
            
            if ($this->isApiRequest($request)) {
                return Response::json([
                    'success' => false,
                    'message' => 'No tienes permisos para realizar esta acción.',
                    'error' => 'insufficient_permissions'
                ], 403);
            }

            Session::flash('error', 'No tienes permisos para acceder a esta sección.');
            return redirect(route('home'));
        }

        return $next($request);
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    private function hasPermission(User $user, string $permission): bool
    {
        // Los administradores tienen todos los permisos
        if ($user->hasRole('Administrador')) {
            return true;
        }

        // Verificar si el usuario tiene el permiso específico
        return $user->hasPermission($permission);
    }

    /**
     * Verificar si es una petición API
     */
    private function isApiRequest(Request $request): bool
    {
        $uri = $request->uri();
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        
        return strpos($uri, '/api/') === 0 || 
               strpos($contentType, 'application/json') !== false ||
               strpos($accept, 'application/json') !== false;
    }

    /**
     * Middleware factory para crear instancias con permisos específicos
     */
    public static function require(string $permission)
    {
        return function(Request $request, callable $next) use ($permission) {
            $middleware = new self();
            return $middleware->handle($request, $next, $permission);
        };
    }
}

/**
 * Middleware combinado para roles y permisos
 */
class RolePermissionMiddleware implements Middleware
{
    /**
     * Verificar múltiples roles o permisos
     */
    public function handle(Request $request, callable $next, string $requirement = '')
    {
        if (!Session::has('user')) {
            SecurityLog::logAccessDenied($request->uri(), 'Usuario no autenticado');
            
            Session::flash('error', 'Debes iniciar sesión para acceder a esta página.');
            return redirect(route('login'));
        }

        $user = Session::get('user');
        $userModel = User::find($user->id);

        // Parsear el requirement (puede ser role:Admin o permission:users.create o role:Admin|permission:users.view)
        if (!$this->checkRequirement($userModel, $requirement)) {
            SecurityLog::logAccessDenied($request->uri(), "Requirement '$requirement' denegado para usuario {$user->email}");
            
            Session::flash('error', 'No tienes permisos para acceder a esta sección.');
            return redirect(route('home'));
        }

        return $next($request);
    }

    /**
     * Verificar si cumple con los requisitos
     */
    private function checkRequirement(User $user, string $requirement): bool
    {
        if (empty($requirement)) {
            return true;
        }

        // Los administradores siempre pasan
        if ($user->hasRole('Administrador')) {
            return true;
        }

        // Parsear múltiples requisitos separados por |
        $requirements = explode('|', $requirement);
        
        foreach ($requirements as $req) {
            $req = trim($req);
            
            if (strpos($req, 'role:') === 0) {
                $roleName = substr($req, 5);
                if ($user->hasRole($roleName)) {
                    return true;
                }
            } elseif (strpos($req, 'permission:') === 0) {
                $permissionName = substr($req, 11);
                if ($user->hasPermission($permissionName)) {
                    return true;
                }
            }
        }

        return false;
    }
}
