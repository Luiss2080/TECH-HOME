<?php

namespace App\Middleware;

use Core\Middleware;
use Core\Session;
use Core\Response;
use PDO;
use Exception;

/**
 * Middleware de permisos adaptado a la estructura existente de TECH-HOME
 */
class PermissionMiddlewareAdapted extends Middleware
{
    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO('mysql:host=localhost;port=3306;dbname=tech_home', 'root', '');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            error_log("Error conectando a la base de datos: " . $e->getMessage());
        }
    }

    /**
     * Verificar si el usuario tiene el permiso requerido
     */
    public function handle($permission = null)
    {
        // Si no se especifica permiso, permitir acceso
        if (!$permission) {
            return true;
        }

        // Verificar si el usuario está autenticado
        if (!Session::has('user_id')) {
            // Para usuarios no autenticados, solo permitir permisos públicos
            $publicPermissions = ['home.view', 'catalogo.view', 'login'];
            if (!in_array($permission, $publicPermissions)) {
                $this->redirectToLogin();
                return false;
            }
            return true;
        }

        $userId = Session::get('user_id');
        
        // Verificar si el usuario tiene el permiso
        if ($this->hasPermission($userId, $permission)) {
            return true;
        }

        // Log del intento de acceso no autorizado
        $this->logUnauthorizedAccess($userId, $permission);
        
        // Respuesta de acceso denegado
        $this->accessDenied($permission);
        return false;
    }

    /**
     * Verificar si un usuario tiene un permiso específico
     */
    private function hasPermission($userId, $permission)
    {
        try {
            $sql = "
                SELECT COUNT(*) as has_permission
                FROM usuarios u
                JOIN model_has_roles mhr ON u.id = mhr.model_id AND mhr.model_type = 'App\\\\Models\\\\User'
                JOIN role_has_permissions rhp ON mhr.role_id = rhp.role_id
                JOIN permissions p ON rhp.permission_id = p.id
                WHERE u.id = ? AND p.name = ?
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $permission]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['has_permission'] > 0;
            
        } catch (Exception $e) {
            error_log("Error verificando permisos: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener todos los permisos de un usuario
     */
    public function getUserPermissions($userId)
    {
        try {
            $sql = "
                SELECT DISTINCT p.name
                FROM usuarios u
                JOIN model_has_roles mhr ON u.id = mhr.model_id AND mhr.model_type = 'App\\\\Models\\\\User'
                JOIN role_has_permissions rhp ON mhr.role_id = rhp.role_id
                JOIN permissions p ON rhp.permission_id = p.id
                WHERE u.id = ?
                ORDER BY p.name
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId]);
            
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
            
        } catch (Exception $e) {
            error_log("Error obteniendo permisos del usuario: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($userId, $roleName)
    {
        try {
            $sql = "
                SELECT COUNT(*) as has_role
                FROM usuarios u
                JOIN model_has_roles mhr ON u.id = mhr.model_id AND mhr.model_type = 'App\\\\Models\\\\User'
                JOIN roles r ON mhr.role_id = r.id
                WHERE u.id = ? AND r.nombre = ?
            ";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$userId, $roleName]);
            
            return $stmt->fetchColumn() > 0;
            
        } catch (Exception $e) {
            error_log("Error verificando rol: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar múltiples permisos (el usuario debe tener AL MENOS UNO)
     */
    public function hasAnyPermission($userId, array $permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($userId, $permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verificar múltiples permisos (el usuario debe tener TODOS)
     */
    public function hasAllPermissions($userId, array $permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($userId, $permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Registrar intento de acceso no autorizado
     */
    private function logUnauthorizedAccess($userId, $permission)
    {
        try {
            // Verificar si existe la tabla security_log
            $tables = $this->pdo->query("SHOW TABLES LIKE 'security_log'")->fetchAll();
            
            if (count($tables) > 0) {
                $sql = "INSERT INTO security_log (user_id, event_type, event_details, ip_address, user_agent, created_at) 
                        VALUES (?, 'UNAUTHORIZED_ACCESS', ?, ?, ?, NOW())";
                
                $details = json_encode([
                    'permission_required' => $permission,
                    'url' => $_SERVER['REQUEST_URI'] ?? '',
                    'method' => $_SERVER['REQUEST_METHOD'] ?? ''
                ]);
                
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([
                    $userId,
                    $details,
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
                ]);
            } else {
                // Log simple si no existe la tabla de seguridad
                error_log("UNAUTHORIZED_ACCESS: User {$userId} tried to access permission '{$permission}'");
            }
            
        } catch (Exception $e) {
            error_log("Error registrando acceso no autorizado: " . $e->getMessage());
        }
    }

    /**
     * Redirigir a login
     */
    private function redirectToLogin()
    {
        if ($this->isAjaxRequest()) {
            Response::json([
                'success' => false,
                'message' => 'Debes iniciar sesión para acceder a esta función.',
                'error' => 'unauthenticated'
            ], 401)->send();
            exit;
        }

        Session::flash('error', 'Debes iniciar sesión para acceder a esta página.');
        redirect('/login');
        exit;
    }

    /**
     * Manejar acceso denegado
     */
    private function accessDenied($permission)
    {
        if ($this->isAjaxRequest()) {
            Response::json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción.',
                'permission_required' => $permission,
                'error' => 'access_denied'
            ], 403)->send();
            exit;
        }

        Session::flash('error', 'No tienes permisos para acceder a esta sección.');
        redirect('/');
        exit;
    }

    /**
     * Verificar si es una petición AJAX
     */
    private function isAjaxRequest()
    {
        return (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) || (
            !empty($_SERVER['CONTENT_TYPE']) && 
            strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false
        ) || (
            !empty($_SERVER['HTTP_ACCEPT']) && 
            strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false
        );
    }

    /**
     * Helper para verificar permisos en vistas
     */
    public static function can($permission)
    {
        if (!Session::has('user_id')) {
            return false;
        }

        $middleware = new self();
        return $middleware->hasPermission(Session::get('user_id'), $permission);
    }

    /**
     * Helper para verificar roles en vistas
     */
    public static function hasRoleStatic($roleName)
    {
        if (!Session::has('user_id')) {
            return false;
        }

        $middleware = new self();
        return $middleware->hasRole(Session::get('user_id'), $roleName);
    }

    /**
     * Middleware para rutas que requiere permisos específicos
     */
    public static function requirePermission($permission)
    {
        return function() use ($permission) {
            $middleware = new PermissionMiddlewareAdapted();
            return $middleware->handle($permission);
        };
    }

    /**
     * Middleware para rutas que requiere cualquiera de los permisos
     */
    public static function requireAnyPermission(array $permissions)
    {
        return function() use ($permissions) {
            if (!Session::has('user_id')) {
                redirect('/login');
                exit;
            }

            $middleware = new PermissionMiddlewareAdapted();
            $userId = Session::get('user_id');

            if (!$middleware->hasAnyPermission($userId, $permissions)) {
                Response::json([
                    'success' => false,
                    'message' => 'Acceso denegado. No tienes permisos suficientes.',
                    'permissions_required' => $permissions
                ], 403)->send();
                exit;
            }

            return true;
        };
    }

    /**
     * Middleware para rutas que requiere un rol específico
     */
    public static function requireRole($roleName)
    {
        return function() use ($roleName) {
            if (!Session::has('user_id')) {
                redirect('/login');
                exit;
            }

            $middleware = new PermissionMiddlewareAdapted();
            $userId = Session::get('user_id');

            if (!$middleware->hasRole($userId, $roleName)) {
                Session::flash('error', 'No tienes el rol necesario para acceder a esta sección.');
                redirect('/');
                exit;
            }

            return true;
        };
    }
}
