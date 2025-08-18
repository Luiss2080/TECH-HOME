<?php

namespace App\Models;

use Core\Model;

class Permission extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'guard_name'
    ];
    protected $hidden = [];
    protected $timestamps = true;
    protected $softDeletes = false;

    // Relación: roles que tienen este permiso
    public function roles()
    {
        // Esto retornará un array de roles que tienen este permiso
        $db = \Core\DB::getInstance();
        $query = "SELECT r.* FROM roles r 
                  INNER JOIN role_has_permissions rhp ON r.id = rhp.role_id 
                  WHERE rhp.permission_id = ?";
        
        $result = $db->query($query, [$this->id]);
        return $result ? $result->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    // Relación: usuarios que tienen este permiso directamente
    public function users()
    {
        $db = \Core\DB::getInstance();
        $query = "SELECT u.* FROM usuarios u 
                  INNER JOIN model_has_permissions mhp ON u.id = mhp.model_id 
                  WHERE mhp.permission_id = ? AND mhp.model_type = ?";
        
        $result = $db->query($query, [$this->id, 'App\\Models\\User']);
        return $result ? $result->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    // Método para verificar si el permiso está asignado a un rol
    public function isAssignedToRole($roleId)
    {
        $db = \Core\DB::getInstance();
        $query = "SELECT COUNT(*) as count FROM role_has_permissions WHERE role_id = ? AND permission_id = ?";
        $result = $db->query($query, [$roleId, $this->id]);
        $row = $result->fetch(\PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    // Método para verificar si el permiso está asignado directamente a un usuario
    public function isAssignedToUser($userId)
    {
        $db = \Core\DB::getInstance();
        $query = "SELECT COUNT(*) as count FROM model_has_permissions 
                  WHERE model_id = ? AND permission_id = ? AND model_type = ?";
        $result = $db->query($query, [$userId, $this->id, 'App\\Models\\User']);
        $row = $result->fetch(\PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    // Scopes estáticos
    public static function byGuard($guard = 'web')
    {
        return self::where('guard_name', '=', $guard);
    }

    public static function byName($name)
    {
        return self::where('name', '=', $name);
    }

    /**
     * Obtener todos los permisos ordenados por nombre
     */
    public static function getAll()
    {
        return self::orderBy('name')->get();
    }

    /**
     * Obtener permiso por nombre
     */
    public static function findByName($name)
    {
        return self::where('name', '=', $name)->first();
    }
}
