<?php

namespace App\Models;

use Core\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'descripcion',
        'guard_name'
    ];
    protected $hidden = [];
    protected $timestamps = true;
    protected $softDeletes = false;

    // Relación: usuarios que tienen este rol (sistema antiguo - para compatibilidad)
    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id', 'id');
    }

    // ==========================================
    // MÉTODOS PARA EL NUEVO SISTEMA DE PERMISOS
    // ==========================================

    /**
     * Obtener todos los permisos asignados a este rol
     */
    public function permissions()
    {
        return RoleHasPermissions::getPermissionsForRole($this->id);
    }

    /**
     * Obtener todos los usuarios que tienen este rol (nuevo sistema)
     */
    public function users()
    {
        $db = \Core\DB::getInstance();
        $query = "SELECT u.* FROM usuarios u 
                  INNER JOIN model_has_roles mhr ON u.id = mhr.model_id 
                  WHERE mhr.role_id = ? AND mhr.model_type = ?";

        $result = $db->query($query, [$this->id, 'App\\Models\\User']);
        $result = $result ? $result->fetchAll(\PDO::FETCH_ASSOC) : [];
        foreach ($result as &$user) {
            $user = new User($user);
        }
        return $result;
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function hasPermissionTo($permission)
    {
        $permissionId = is_numeric($permission) ? $permission : $this->getPermissionIdByName($permission);
        if (!$permissionId) return false;

        return RoleHasPermissions::roleHasPermission($this->id, $permissionId);
    }

    /**
     * Asignar un permiso a este rol
     */
    public function givePermissionTo($permission)
    {
        $permissionId = is_numeric($permission) ? $permission : $this->getPermissionIdByName($permission);

        if (!$permissionId) {
            throw new \Exception("Permiso no encontrado: {$permission}");
        }

        RoleHasPermissions::assignPermissionToRole($this->id, $permissionId);
        return $this;
    }

    /**
     * Remover un permiso de este rol
     */
    public function revokePermissionTo($permission)
    {
        $permissionId = is_numeric($permission) ? $permission : $this->getPermissionIdByName($permission);

        if (!$permissionId) {
            return $this;
        }

        RoleHasPermissions::removePermissionFromRole($this->id, $permissionId);
        return $this;
    }

    /**
     * Sincronizar permisos (remover todos y asignar los nuevos)
     */
    public function syncPermissions($permissions)
    {
        if (!is_array($permissions)) {
            $permissions = [$permissions];
        }

        // Convertir nombres de permisos a IDs si es necesario
        $permissionIds = [];
        foreach ($permissions as $permission) {
            $permissionId = is_numeric($permission) ? $permission : $this->getPermissionIdByName($permission);
            if ($permissionId) {
                $permissionIds[] = $permissionId;
            }
        }

        RoleHasPermissions::syncPermissionsForRole($this->id, $permissionIds);
        return $this;
    }

    // ==========================================
    // MÉTODOS AUXILIARES PRIVADOS
    // ==========================================

    /**
     * Obtener ID del permiso por nombre
     */
    private function getPermissionIdByName($permissionName)
    {
        $db = \Core\DB::getInstance();
        $query = "SELECT id FROM permissions WHERE name = ? LIMIT 1";
        $result = $db->query($query, [$permissionName]);

        if ($result) {
            $row = $result->fetch(\PDO::FETCH_ASSOC);
            return $row ? $row['id'] : null;
        }

        return null;
    }

    // ==========================================
    // SCOPES Y MÉTODOS ESTÁTICOS
    // ==========================================

    /**
     * Obtener todos los roles ordenados por nombre
     */
    public static function getAll()
    {
        return self::orderBy('nombre')->get();
    }

    /**
     * Obtener rol por nombre
     */
    public static function findByName($name)
    {
        return self::where('nombre', '=', $name)->first();
    }

    /**
     * Obtener roles activos
     */
    public static function activos()
    {
        return self::where('estado', '=', 1)->get();
    }
}
