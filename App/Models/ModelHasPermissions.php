<?php

namespace App\Models;

use Core\Model;

class ModelHasPermissions extends Model
{
    protected $table = 'model_has_permissions';
    protected $primaryKey = null; // Esta tabla no tiene primary key único
    protected $fillable = [
        'permission_id',
        'model_type',
        'model_id'
    ];
    protected $hidden = [];
    protected $timestamps = false;
    protected $softDeletes = false;

    // Relación con el permiso
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }

    // Método estático para asignar permiso a un modelo
    public static function assignPermission($modelType, $modelId, $permissionId)
    {
        $db = \Core\DB::getInstance();
        
        // Verificar si ya existe la relación
        $existing = $db->query(
            "SELECT COUNT(*) as count FROM model_has_permissions WHERE permission_id = ? AND model_type = ? AND model_id = ?",
            [$permissionId, $modelType, $modelId]
        );
        
        $row = $existing->fetch(\PDO::FETCH_ASSOC);
        if ($row['count'] > 0) {
            return false; // Ya existe
        }
        
        // Insertar la nueva relación
        $result = $db->query(
            "INSERT INTO model_has_permissions (permission_id, model_type, model_id) VALUES (?, ?, ?)",
            [$permissionId, $modelType, $modelId]
        );
        
        return $result ? true : false;
    }

    // Método estático para remover permiso de un modelo
    public static function removePermission($modelType, $modelId, $permissionId)
    {
        $db = \Core\DB::getInstance();
        
        $result = $db->query(
            "DELETE FROM model_has_permissions WHERE permission_id = ? AND model_type = ? AND model_id = ?",
            [$permissionId, $modelType, $modelId]
        );
        
        return $result ? true : false;
    }

    // Método estático para obtener permisos directos de un modelo
    public static function getPermissionsForModel($modelType, $modelId)
    {
        $db = \Core\DB::getInstance();
        
        $query = "SELECT p.* FROM permissions p 
                  INNER JOIN model_has_permissions mhp ON p.id = mhp.permission_id 
                  WHERE mhp.model_type = ? AND mhp.model_id = ?";
        
        $result = $db->query($query, [$modelType, $modelId]);
        return $result ? $result->fetchAll(\PDO::FETCH_ASSOC) : [];
    }

    // Verificar si un modelo tiene un permiso específico
    public static function modelHasPermission($modelType, $modelId, $permissionId)
    {
        $db = \Core\DB::getInstance();
        
        $query = "SELECT COUNT(*) as count FROM model_has_permissions 
                  WHERE permission_id = ? AND model_type = ? AND model_id = ?";
        
        $result = $db->query($query, [$permissionId, $modelType, $modelId]);
        $row = $result->fetch(\PDO::FETCH_ASSOC);
        
        return $row['count'] > 0;
    }

    // Método estático para sincronizar permisos de un modelo (elimina los anteriores y asigna los nuevos)
    public static function syncPermissionsForModel($modelType, $modelId, array $permissionIds)
    {
        $db = \Core\DB::getInstance();
        $connection = $db->getConnection();
        
        try {
            // Iniciar transacción
            $connection->beginTransaction();
            
            // Eliminar todos los permisos actuales del modelo
            $db->query(
                "DELETE FROM model_has_permissions WHERE model_type = ? AND model_id = ?",
                [$modelType, $modelId]
            );
            
            // Insertar los nuevos permisos
            if (!empty($permissionIds)) {
                foreach ($permissionIds as $permissionId) {
                    $db->query(
                        "INSERT INTO model_has_permissions (permission_id, model_type, model_id) VALUES (?, ?, ?)",
                        [$permissionId, $modelType, $modelId]
                    );
                }
            }
            
            // Confirmar la transacción
            $connection->commit();
            return true;
            
        } catch (\Exception $e) {
            // Revertir la transacción en caso de error
            $connection->rollback();
            throw $e;
        }
    }
}
