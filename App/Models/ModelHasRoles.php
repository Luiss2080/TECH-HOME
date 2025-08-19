<?php

namespace App\Models;

use Core\Model;

class ModelHasRoles extends Model
{
    protected $table = 'model_has_roles';
    protected $primaryKey = null; // Esta tabla no tiene primary key único
    protected $fillable = [
        'role_id',
        'model_type',
        'model_id'
    ];
    protected $hidden = [];
    protected $timestamps = false;
    protected $softDeletes = false;

    // Relación con el rol
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    // Método estático para asignar rol a un modelo
    public static function assignRole($modelType, $modelId, $roleId)
    {
        $db = \Core\DB::getInstance();

        // Verificar si ya existe la relación
        $existing = $db->query(
            "SELECT COUNT(*) as count FROM model_has_roles WHERE role_id = ? AND model_type = ? AND model_id = ?",
            [$roleId, $modelType, $modelId]
        );

        $row = $existing->fetch(\PDO::FETCH_ASSOC);
        if ($row['count'] > 0) {
            return false; // Ya existe
        }

        // Insertar la nueva relación
        $result = $db->query(
            "INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, ?, ?)",
            [$roleId, $modelType, $modelId]
        );

        return $result ? true : false;
    }

    // Método estático para remover rol de un modelo
    public static function removeRole($modelType, $modelId, $roleId)
    {
        $db = \Core\DB::getInstance();

        $result = $db->query(
            "DELETE FROM model_has_roles WHERE role_id = ? AND model_type = ? AND model_id = ?",
            [$roleId, $modelType, $modelId]
        );

        return $result ? true : false;
    }

    // Método estático para obtener roles de un modelo
    public static function getRolesForModel($modelType, $modelId)
    {
        $db = \Core\DB::getInstance();

        $query = "SELECT r.* FROM roles r 
                  INNER JOIN model_has_roles mhr ON r.id = mhr.role_id 
                  WHERE mhr.model_type = ? AND mhr.model_id = ?";

        $result = $db->query($query, [$modelType, $modelId]);
        return $result ? $result->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    // Método estático para sincronizar roles de un modelo
    public static function syncRolesForModel($modelType, $modelId, $roleIds)
    {
        $db = \Core\DB::getInstance();

        // Primero eliminar todos los roles actuales
        $db->query(
            "DELETE FROM model_has_roles WHERE model_type = ? AND model_id = ?",
            [$modelType, $modelId]
        );

        // Luego insertar los nuevos roles
        if (!empty($roleIds)) {
            $query = "INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES ";
            $bindings = [];
            foreach ($roleIds as $roleId) {
                $query .= "(?, ?, ?),";
                $bindings[] = $roleId;
                $bindings[] = $modelType;
                $bindings[] = $modelId;
            }
            $query = rtrim($query, ',');
            $db->query($query, $bindings);
        }

        return true;
    }

    // Verificar si un modelo tiene un rol específico
    public static function modelHasRole($modelType, $modelId, $roleId)
    {
        $db = \Core\DB::getInstance();

        $query = "SELECT COUNT(*) as count FROM model_has_roles 
                  WHERE role_id = ? AND model_type = ? AND model_id = ?";

        $result = $db->query($query, [$roleId, $modelType, $modelId]);
        $row = $result->fetch(\PDO::FETCH_ASSOC);

        return $row['count'] > 0;
    }
}
