<?php

namespace App\Models;

use Core\Model;

class RoleHasPermissions extends Model
{
    protected $table = 'role_has_permissions';
    protected $primaryKey = null; // Esta tabla no tiene primary key único
    protected $fillable = [
        'role_id',
        'permission_id'
    ];
    protected $hidden = [];
    protected $timestamps = false;
    protected $softDeletes = false;

    // Relación con el rol
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    // Relación con el permiso
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    // Método estático para asignar permiso a un rol
    public static function assignPermissionToRole($roleId, $permissionId)
    {
        $db = \Core\DB::getInstance();
        
        // Verificar si ya existe la relación
        $existing = $db->query(
            "SELECT COUNT(*) as count FROM role_has_permissions WHERE role_id = ? AND permission_id = ?",
            [$roleId, $permissionId]
        );
        
        $row = $existing->fetch(\PDO::FETCH_ASSOC);
        if ($row['count'] > 0) {
            return false; // Ya existe
        }
        
        // Insertar la nueva relación
        $result = $db->query(
            "INSERT INTO role_has_permissions (role_id, permission_id) VALUES (?, ?)",
            [$roleId, $permissionId]
        );
        
        return $result ? true : false;
    }

    // Método estático para remover permiso de un rol
    public static function removePermissionFromRole($roleId, $permissionId)
    {
        $db = \Core\DB::getInstance();
        
        $result = $db->query(
            "DELETE FROM role_has_permissions WHERE role_id = ? AND permission_id = ?",
            [$roleId, $permissionId]
        );
        
        return $result ? true : false;
    }

    // Método estático para obtener permisos de un rol
    public static function getPermissionsForRole($roleId)
    {
        $db = \Core\DB::getInstance();
        
        $query = "SELECT p.* FROM permissions p 
                  INNER JOIN role_has_permissions rhp ON p.id = rhp.permission_id 
                  WHERE rhp.role_id = ?";
        
        $result = $db->query($query, [$roleId]);
        return $result ? $result->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    // Método estático para sincronizar permisos de un rol
    public static function syncPermissionsForRole($roleId, $permissionIds)
    {
        $db = \Core\DB::getInstance();
        
        // Primero eliminar todos los permisos actuales del rol
        $db->query(
            "DELETE FROM role_has_permissions WHERE role_id = ?",
            [$roleId]
        );
        
        // Luego insertar los nuevos permisos
        if (!empty($permissionIds)) {
            foreach ($permissionIds as $permissionId) {
                $db->query(
                    "INSERT INTO role_has_permissions (role_id, permission_id) VALUES (?, ?)",
                    [$roleId, $permissionId]
                );
            }
        }
        
        return true;
    }

    // Verificar si un rol tiene un permiso específico
    public static function roleHasPermission($roleId, $permissionId)
    {
        $db = \Core\DB::getInstance();
        
        $query = "SELECT COUNT(*) as count FROM role_has_permissions 
                  WHERE role_id = ? AND permission_id = ?";
        
        $result = $db->query($query, [$roleId, $permissionId]);
        $row = $result->fetch(\PDO::FETCH_ASSOC);
        
        return $row['count'] > 0;
    }
}
