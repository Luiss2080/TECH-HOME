<?php

namespace App\Controllers;

use Core\Controller;
use Core\Request;
use Core\Session;
use Core\Validation;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SecurityLog;
use App\Models\AuditLog;

class PermissionController extends Controller
{
    /**
     * Panel principal de gestión de permisos
     */
    public function index()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $permissionsByCategory = $this->groupPermissionsByCategory($permissions);
        
        return view('admin.permissions.index', [
            'title' => 'Gestión de Permisos',
            'roles' => $roles,
            'permissions' => $permissions,
            'permissionsByCategory' => $permissionsByCategory
        ]);
    }

    /**
     * Mostrar permisos de un rol específico
     */
    public function rolePermissions(Request $request)
    {
        $roleId = $request->input('role_id');
        $role = Role::find($roleId);
        
        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], 404);
        }

        $permissions = $role->permissions();
        $allPermissions = Permission::all();
        
        return response()->json([
            'success' => true,
            'role' => $role->toArray(),
            'permissions' => $permissions,
            'all_permissions' => $allPermissions
        ]);
    }

    /**
     * Asignar permisos a un rol
     */
    public function assignPermissions(Request $request)
    {
        $validator = new Validation();
        $rules = [
            'role_id' => 'required|numeric',
            'permissions' => 'required|array'
        ];

        if (!$validator->validate($request->all(), $rules)) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        $roleId = $request->input('role_id');
        $permissionIds = $request->input('permissions', []);

        try {
            $role = Role::find($roleId);
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rol no encontrado'
                ], 404);
            }

            // Obtener permisos actuales para auditoría
            $oldPermissions = $role->permissions();
            
            // Sincronizar permisos
            $role->syncPermissions($permissionIds);

            // Obtener nuevos permisos para auditoría
            $newPermissions = $role->permissions();

            // Log de auditoría
            AuditLog::logCRUD(
                'UPDATE_PERMISSIONS',
                'roles',
                $roleId,
                ['permissions' => array_column($oldPermissions, 'name')],
                ['permissions' => array_column($newPermissions, 'name')]
            );

            // Log de seguridad
            SecurityLog::logSuspiciousActivity([
                'action' => 'permissions_updated',
                'role_id' => $roleId,
                'role_name' => $role->nombre,
                'permissions_count' => count($permissionIds),
                'message' => 'Permisos actualizados para rol: ' . $role->nombre
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permisos actualizados exitosamente',
                'permissions_count' => count($permissionIds)
            ]);

        } catch (\Exception $e) {
            error_log('Error asignando permisos: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Ver permisos de un usuario específico
     */
    public function userPermissions(Request $request)
    {
        $userId = $request->input('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $userRoles = $user->roles();
        $userPermissions = [];
        
        // Recopilar todos los permisos del usuario a través de sus roles
        foreach ($userRoles as $role) {
            $roleObj = Role::find($role['id']);
            $permissions = $roleObj->permissions();
            foreach ($permissions as $permission) {
                $userPermissions[$permission['name']] = [
                    'name' => $permission['name'],
                    'via_role' => $role['nombre']
                ];
            }
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->nombre . ' ' . $user->apellido,
                'email' => $user->email
            ],
            'roles' => $userRoles,
            'permissions' => array_values($userPermissions),
            'permissions_count' => count($userPermissions)
        ]);
    }

    /**
     * Crear nuevo permiso
     */
    public function createPermission(Request $request)
    {
        $validator = new Validation();
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500'
        ];

        if (!$validator->validate($request->all(), $rules)) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $permission = new Permission([
                'name' => $request->input('name'),
                'guard_name' => 'web',
                'description' => $request->input('description', '')
            ]);
            
            $permission->save();

            // Log de auditoría
            AuditLog::logCRUD('CREATE', 'permissions', $permission->id, null, [
                'name' => $permission->name,
                'guard_name' => $permission->guard_name
            ]);

            // Log de seguridad
            SecurityLog::logSuspiciousActivity([
                'action' => 'permission_create',
                'permission_id' => $permission->id,
                'permission_name' => $permission->name,
                'message' => 'Nuevo permiso creado: ' . $permission->name
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Permiso creado exitosamente',
                'permission' => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'guard_name' => $permission->guard_name
                ]
            ]);

        } catch (\Exception $e) {
            error_log('Error creando permiso: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor'
            ], 500);
        }
    }

    /**
     * Matriz de permisos por rol
     */
    public function permissionMatrix()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $permissionsByCategory = $this->groupPermissionsByCategory($permissions);
        
        // Construir matriz de permisos
        $matrix = [];
        foreach ($roles as $role) {
            $roleObj = Role::find($role['id']);
            $rolePermissions = $roleObj->permissions();
            $rolePermissionNames = array_column($rolePermissions, 'name');
            
            $matrix[$role['nombre']] = [];
            foreach ($permissions as $permission) {
                $matrix[$role['nombre']][$permission['name']] = in_array($permission['name'], $rolePermissionNames);
            }
        }

        return view('admin.permissions.matrix', [
            'title' => 'Matriz de Permisos',
            'roles' => $roles,
            'permissions' => $permissions,
            'permissionsByCategory' => $permissionsByCategory,
            'matrix' => $matrix
        ]);
    }

    /**
     * Exportar configuración de permisos
     */
    public function exportPermissions()
    {
        $roles = Role::all();
        $permissions = Permission::all();
        $matrix = [];

        foreach ($roles as $role) {
            $roleObj = Role::find($role['id']);
            $rolePermissions = $roleObj->permissions();
            $matrix[$role['nombre']] = array_column($rolePermissions, 'name');
        }

        $export = [
            'exported_at' => date('Y-m-d H:i:s'),
            'roles' => $roles,
            'permissions' => $permissions,
            'role_permissions' => $matrix
        ];

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="permissions_export_' . date('Y-m-d') . '.json"');
        
        return json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Agrupar permisos por categoría
     */
    private function groupPermissionsByCategory($permissions)
    {
        $grouped = [];
        
        foreach ($permissions as $permission) {
            $parts = explode('.', $permission['name']);
            $category = $parts[0] ?? 'general';
            
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            
            $grouped[$category][] = $permission;
        }

        return $grouped;
    }

    /**
     * Validar si un usuario puede acceder a una ruta específica
     */
    public function checkAccess(Request $request)
    {
        $userId = $request->input('user_id');
        $permission = $request->input('permission');
        
        $user = User::find($userId);
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuario no encontrado'], 404);
        }

        $hasAccess = $user->hasPermission($permission);
        
        return response()->json([
            'success' => true,
            'has_access' => $hasAccess,
            'user_id' => $userId,
            'permission' => $permission
        ]);
    }
}
