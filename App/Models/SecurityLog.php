<?php

namespace App\Models;

use Core\Model;
use Core\Logger;
use Core\Session;

class SecurityLog extends Model
{
    protected $table = 'security_log';
    protected $timestamps = false; // No usar timestamps automáticos
    
    protected $fillable = [
        'event_type',
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'details',
        'severity'
    ];

    /**
     * Registrar evento de login exitoso
     */
    public static function logLoginSuccess($userId, $email)
    {
        self::createSecurityLog('LOGIN_SUCCESS', $userId, $email, 'LOW', [
            'message' => 'Usuario autenticado exitosamente'
        ]);
    }

    /**
     * Registrar evento de login fallido
     */
    public static function logLoginFailed($email, $reason)
    {
        self::createSecurityLog('LOGIN_FAILED', null, $email, 'MEDIUM', [
            'reason' => $reason,
            'message' => 'Intento de login fallido'
        ]);
    }

    /**
     * Registrar logout
     */
    public static function logLogout($userId, $email)
    {
        self::createSecurityLog('LOGOUT', $userId, $email, 'LOW', [
            'message' => 'Usuario cerró sesión'
        ]);
    }

    /**
     * Registrar rate limiting
     */
    public static function logRateLimit($email, $action, $attempts)
    {
        self::createSecurityLog('RATE_LIMIT', null, $email, 'HIGH', [
            'action' => $action,
            'attempts' => $attempts,
            'message' => "Rate limit excedido para acción: $action"
        ]);
    }

    /**
     * Registrar acceso denegado
     */
    public static function logAccessDenied($resource, $reason = '')
    {
        $user = Session::get('user');
        self::createSecurityLog('ACCESS_DENIED', $user->id ?? null, $user->email ?? null, 'HIGH', [
            'resource' => $resource,
            'reason' => $reason,
            'message' => "Acceso denegado a recurso: $resource"
        ]);
    }

    /**
     * Registrar cambio de contraseña
     */
    public static function logPasswordChange($userId, $email)
    {
        self::createSecurityLog('PASSWORD_CHANGE', $userId, $email, 'MEDIUM', [
            'message' => 'Contraseña cambiada exitosamente'
        ]);
    }

    /**
     * Registrar actividad sospechosa
     */
    public static function logSuspiciousActivity($details)
    {
        self::createSecurityLog('SUSPICIOUS_ACTIVITY', null, null, 'CRITICAL', $details);
    }

    /**
     * Crear registro de seguridad
     */
    private static function createSecurityLog($eventType, $userId, $email, $severity, $details)
    {
        try {
            $log = new self([
                'event_type' => $eventType,
                'user_id' => $userId,
                'email' => $email,
                'ip_address' => self::getClientIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
                'details' => json_encode($details),
                'severity' => $severity
            ]);
            
            $log->save();
        } catch (\Exception $e) {
            error_log("Error guardando log de seguridad: " . $e->getMessage());
        }
    }

    /**
     * Obtener estadísticas de seguridad
     */
    public static function getSecurityStats($days = 7)
    {
        try {
            $db = \Core\DB::getInstance();
            $since = date('Y-m-d H:i:s', strtotime("-$days days"));
            
            $query = "SELECT 
                        event_type,
                        severity,
                        COUNT(*) as count,
                        COUNT(DISTINCT ip_address) as unique_ips,
                        MAX(created_at) as last_occurrence
                      FROM security_log
                      WHERE created_at >= ?
                      GROUP BY event_type, severity
                      ORDER BY count DESC";
            
            $result = $db->query($query, [$since]);
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error obteniendo stats de seguridad: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener IPs sospechosas
     */
    public static function getSuspiciousIPs($minEvents = 10, $days = 1)
    {
        try {
            $db = \Core\DB::getInstance();
            $since = date('Y-m-d H:i:s', strtotime("-$days days"));
            
            $query = "SELECT 
                        ip_address,
                        COUNT(*) as total_events,
                        COUNT(CASE WHEN severity IN ('HIGH', 'CRITICAL') THEN 1 END) as high_severity_events,
                        GROUP_CONCAT(DISTINCT event_type) as event_types,
                        MIN(created_at) as first_seen,
                        MAX(created_at) as last_seen
                      FROM security_log
                      WHERE created_at >= ?
                      GROUP BY ip_address
                      HAVING total_events >= ?
                      ORDER BY high_severity_events DESC, total_events DESC";
            
            $result = $db->query($query, [$since, $minEvents]);
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error obteniendo IPs sospechosas: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener IP del cliente
     */
    private static function getClientIp(): string
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}

/**
 * Modelo para Auditoría General
 */
class AuditLog extends Model
{
    protected $table = 'audit_log';
    protected $timestamps = false; // No usar timestamps automáticos
    
    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];

    /**
     * Registrar operación CRUD
     */
    public static function logCRUD($action, $tableName, $recordId, $oldValues = null, $newValues = null)
    {
        try {
            $user = Session::get('user');
            
            $audit = new self([
                'user_id' => $user->id ?? null,
                'action' => strtoupper($action),
                'table_name' => $tableName,
                'record_id' => (string)$recordId,
                'old_values' => $oldValues ? json_encode($oldValues) : null,
                'new_values' => $newValues ? json_encode($newValues) : null,
                'ip_address' => self::getClientIp(),
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
            ]);
            
            $audit->save();
        } catch (\Exception $e) {
            error_log("Error guardando audit log: " . $e->getMessage());
        }
    }

    /**
     * Obtener historial de un registro
     */
    public static function getRecordHistory($tableName, $recordId)
    {
        try {
            $db = \Core\DB::getInstance();
            
            $query = "SELECT a.*, u.nombre, u.apellido, u.email
                      FROM audit_log a
                      LEFT JOIN users u ON a.user_id = u.id
                      WHERE a.table_name = ? AND a.record_id = ?
                      ORDER BY a.created_at DESC";
            
            $result = $db->query($query, [$tableName, $recordId]);
            return $result->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error obteniendo historial de auditoría: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtener IP del cliente
     */
    private static function getClientIp(): string
    {
        $ipKeys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ips = explode(',', $_SERVER[$key]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }

        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}
