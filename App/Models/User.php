<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'telefono',
        'fecha_nacimiento',
        'avatar',
        'estado',
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    protected $hidden = [
        'password'
    ];
    protected $timestamps = false; // No usamos timestamps automáticos
    protected $softDeletes = false;

    // ==========================================
    // MÉTODOS PARA ROLES Y PERMISOS (HasRoles)
    // ==========================================

    /**
     * Obtener todos los roles del usuario
     */
    public function roles()
    {
        return ModelHasRoles::getRolesForModel('App\\Models\\User', $this->id);
    }

    /**
     * Obtener todos los permisos del usuario (directos + a través de roles)
     */
    public function permissions()
    {
        // Permisos directos del usuario
        $directPermissions = ModelHasPermissions::getPermissionsForModel('App\\Models\\User', $this->id);

        // Permisos a través de roles
        $rolePermissions = $this->getPermissionsViaRoles();

        // Combinar y eliminar duplicados
        $allPermissions = array_merge($directPermissions, $rolePermissions);
        $uniquePermissions = [];
        $seen = [];

        foreach ($allPermissions as $permission) {
            if (!in_array($permission['id'], $seen)) {
                $uniquePermissions[] = $permission;
                $seen[] = $permission['id'];
            }
        }

        return $uniquePermissions;
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($role)
    {
        $roleId = is_numeric($role) ? $role : $this->getRoleIdByName($role);
        if (!$roleId) return false;

        return ModelHasRoles::modelHasRole('App\\Models\\User', $this->id, $roleId);
    }

    /**
     * Verificar si el usuario tiene alguno de los roles especificados
     */
    public function hasAnyRole($roles)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar si el usuario tiene todos los roles especificados
     */
    public function hasAllRoles($roles)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verificar si el usuario tiene un permiso específico
     */
    public function hasPermissionTo($permission)
    {
        $permissionId = is_numeric($permission) ? $permission : $this->getPermissionIdByName($permission);
        if (!$permissionId) return false;
        // Verificar permiso directo
        if (ModelHasPermissions::modelHasPermission('App\\Models\\User', $this->id, $permissionId)) {
            return true;
        }

        // Verificar permiso a través de roles
        $userRoles = $this->roles();
        foreach ($userRoles as $role) {
            if (RoleHasPermissions::roleHasPermission($role['id'], $permissionId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verificar si el usuario puede realizar una acción (alias de hasPermissionTo)
     */
    public function can($permission)
    {
        return $this->hasPermissionTo($permission);
    }

    /**
     * Verificar si el usuario NO puede realizar una acción
     */
    public function cannot($permission)
    {
        return !$this->can($permission);
    }

    /**
     * Asignar un rol al usuario
     */
    public function assignRole($role)
    {
        $roleId = is_numeric($role) ? $role : $this->getRoleIdByName($role);

        if (!$roleId) {
            throw new \Exception("Rol no encontrado: {$role}");
        }

        ModelHasRoles::assignRole('App\\Models\\User', $this->id, $roleId);
        return $this;
    }

    /**
     * Remover un rol del usuario
     */
    public function removeRole($role)
    {
        $roleId = is_numeric($role) ? $role : $this->getRoleIdByName($role);

        if (!$roleId) {
            return $this;
        }

        ModelHasRoles::removeRole('App\\Models\\User', $this->id, $roleId);
        return $this;
    }

    /**
     * Sincronizar roles (remover todos y asignar los nuevos)
     */
    public function syncRoles($roles)
    {
        if (!is_array($roles)) {
            $roles = [$roles];
        }

        // Convertir nombres de roles a IDs si es necesario
        $roleIds = [];
        foreach ($roles as $role) {
            $roleId = is_numeric($role) ? $role : $this->getRoleIdByName($role);
            if ($roleId) {
                $roleIds[] = $roleId;
            }
        }
        ModelHasRoles::syncRolesForModel('App\\Models\\User', $this->id, $roleIds);
        return $this;
    }

    /**
     * Dar permiso directo al usuario
     */
    public function givePermissionTo($permission)
    {
        $permissionId = is_numeric($permission) ? $permission : $this->getPermissionIdByName($permission);

        if (!$permissionId) {
            throw new \Exception("Permiso no encontrado: {$permission}");
        }

        ModelHasPermissions::assignPermission('App\\Models\\User', $this->id, $permissionId);
        return $this;
    }

    /**
     * Remover permiso directo del usuario
     */
    public function revokePermissionTo($permission)
    {
        $permissionId = is_numeric($permission) ? $permission : $this->getPermissionIdByName($permission);

        if (!$permissionId) {
            return $this;
        }

        ModelHasPermissions::removePermission('App\\Models\\User', $this->id, $permissionId);
        return $this;
    }

    /**
     * Obtener el primer rol del usuario (para compatibilidad con el sistema actual)
     */
    public function getFirstRole()
    {
        $roles = $this->roles();
        return !empty($roles) ? $roles[0] : null;
    }

    // ==========================================
    // MÉTODOS AUXILIARES PRIVADOS
    // ==========================================

    /**
     * Obtener permisos a través de roles
     */
    private function getPermissionsViaRoles()
    {
        $db = \Core\DB::getInstance();
        $query = "SELECT DISTINCT p.* FROM permissions p 
                  INNER JOIN role_has_permissions rhp ON p.id = rhp.permission_id 
                  INNER JOIN model_has_roles mhr ON rhp.role_id = mhr.role_id 
                  WHERE mhr.model_type = ? AND mhr.model_id = ?";

        $result = $db->query($query, ['App\\Models\\User', $this->id]);
        return $result ? $result->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    /**
     * Obtener ID del rol por nombre
     */
    private function getRoleIdByName($roleName)
    {
        $db = \Core\DB::getInstance();
        $query = "SELECT id FROM roles WHERE nombre = ? LIMIT 1";
        $result = $db->query($query, [$roleName]);

        if ($result) {
            $row = $result->fetch(\PDO::FETCH_ASSOC);
            return $row ? $row['id'] : null;
        }

        return null;
    }

    /**
     * Obtener ID del permiso por nombre
     */
    private function getPermissionIdByName($permissionName)
    {
        $permission = Permission::where('name', '=', $permissionName)->first();
        return $permission ? $permission->id : null;
    }

    /**
     * Intenta autenticar un usuario por email y password
     * @param string $email
     * @param string $password
     * @return User|false
     */
    public static function attempt($email, $password)
    {
        $user = self::where('email', '=', $email)->first();
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    // Scopes
    public static function activos()
    {
        return self::where('estado', '=', 1);
    }

    public static function porRol($rolId)
    {
        return self::where('rol_id', '=', $rolId);
    }

    public static function registradosHoy()
    {
        return self::whereRaw('DATE(fecha_creacion) = CURRENT_DATE');
    }

    public static function recientes(int $dias = 7)
    {
        return self::whereRaw('fecha_creacion >= DATE_SUB(NOW(), INTERVAL ? DAY)', [$dias])
            ->orderBy('fecha_creacion', 'desc');
    }

    public static function Dashboard()
    {
        $user = auth();
        if (!$user) {
            return 'home';
        }
        // Usar el nuevo sistema de roles
        $roles = $user->roles();
        if (empty($roles)) {
            return 'home';
        }

        $firstRole = strtolower($roles[0]['nombre']);

        switch ($firstRole) {
            case 'administrador':
                return 'admin.dashboard';
            case 'estudiante':
                return 'estudiante.dashboard';
            case 'docente':
                return 'docente.dashboard';
            default:
                return 'home';
        }
    }
}
