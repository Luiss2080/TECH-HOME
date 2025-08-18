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
        'estado'
    ];
    protected $hidden = [];
    protected $timestamps = false;
    protected $softDeletes = false;

    public function permissions()
    {
        $permissions = RoleHasPermissions::getPermissionsForRole($this->id);
        return is_array($permissions) ? $permissions : [];
    }

    public function users()
    {
        try {
            $db = \Core\DB::getInstance();
            $query = "SELECT u.* FROM usuarios u 
                      INNER JOIN model_has_roles mhr ON u.id = mhr.model_id 
                      WHERE mhr.role_id = ? AND mhr.model_type = ?";

            $result = $db->query($query, [$this->id, 'App\\Models\\User']);
            $result = $result ? $result->fetchAll(\PDO::FETCH_ASSOC) : [];
            
            $users = [];
            foreach ($result as $userData) {
                $users[] = new User($userData);
            }
            return $users;
        } catch (\Exception $e) {
            return [];
        }
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
