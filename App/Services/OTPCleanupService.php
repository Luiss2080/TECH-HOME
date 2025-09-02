<?php

namespace App\Services;

use App\Models\CodigoOTP;
use App\Middleware\RateLimitMiddleware;
use Core\DB;

class OTPCleanupService
{
    /**
     * Ejecutar limpieza completa del sistema
     */
    public static function runFullCleanup(): array
    {
        $results = [
            'otp_cleanup' => self::cleanupOTPCodes(),
            'rate_limit_cleanup' => self::cleanupRateLimitAttempts(),
            'expired_sessions_cleanup' => self::cleanupExpiredSessions(),
            'failed_login_cleanup' => self::cleanupFailedLoginAttempts(),
            'total_cleaned' => 0,
            'execution_time' => 0
        ];

        $startTime = microtime(true);
        
        // Calcular total de registros limpiados
        $results['total_cleaned'] = 
            $results['otp_cleanup']['deleted'] + 
            $results['rate_limit_cleanup']['deleted'] + 
            $results['expired_sessions_cleanup']['deleted'] + 
            $results['failed_login_cleanup']['deleted'];

        $results['execution_time'] = round((microtime(true) - $startTime) * 1000, 2); // En milisegundos

        // Log del resultado
        error_log(sprintf(
            'OTP Cleanup Service executed: %d records cleaned in %sms',
            $results['total_cleaned'],
            $results['execution_time']
        ));

        return $results;
    }

    /**
     * Limpiar códigos OTP expirados y utilizados
     */
    public static function cleanupOTPCodes(): array
    {
        try {
            $db = DB::getInstance();
            
            // Eliminar códigos expirados o ya utilizados
            $query = "DELETE FROM codigos_otp WHERE expira_en < NOW() OR utilizado = 1";
            $result = $db->query($query);
            
            $deletedCount = $result ? $result->rowCount() : 0;
            
            // Obtener estadísticas actuales
            $statsQuery = "SELECT 
                COUNT(*) as total_active,
                COUNT(CASE WHEN expira_en >= NOW() AND utilizado = 0 THEN 1 END) as valid_codes,
                COUNT(CASE WHEN utilizado = 1 THEN 1 END) as used_codes,
                COUNT(CASE WHEN expira_en < NOW() AND utilizado = 0 THEN 1 END) as expired_codes
                FROM codigos_otp";
            
            $statsResult = $db->query($statsQuery);
            $stats = $statsResult ? $statsResult->fetch(\PDO::FETCH_ASSOC) : [];
            
            return [
                'success' => true,
                'deleted' => $deletedCount,
                'stats' => $stats,
                'message' => "Limpieza OTP: {$deletedCount} códigos eliminados"
            ];
            
        } catch (\Exception $e) {
            error_log('Error en limpieza OTP: ' . $e->getMessage());
            return [
                'success' => false,
                'deleted' => 0,
                'error' => $e->getMessage(),
                'message' => 'Error en limpieza de códigos OTP'
            ];
        }
    }

    /**
     * Limpiar intentos de rate limiting antiguos
     */
    public static function cleanupRateLimitAttempts(): array
    {
        try {
            $db = DB::getInstance();
            
            // Eliminar registros más antiguos que 24 horas
            $query = "DELETE FROM rate_limit_attempts WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            $result = $db->query($query);
            
            $deletedCount = $result ? $result->rowCount() : 0;
            
            // Estadísticas de rate limiting
            $statsQuery = "SELECT 
                COUNT(*) as total_attempts,
                COUNT(DISTINCT client_id) as unique_clients,
                COUNT(DISTINCT ip_address) as unique_ips,
                action,
                COUNT(*) as attempts_per_action
                FROM rate_limit_attempts 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
                GROUP BY action
                ORDER BY attempts_per_action DESC";
            
            $statsResult = $db->query($statsQuery);
            $actionStats = $statsResult ? $statsResult->fetchAll(\PDO::FETCH_ASSOC) : [];
            
            return [
                'success' => true,
                'deleted' => $deletedCount,
                'action_stats' => $actionStats,
                'message' => "Limpieza Rate Limit: {$deletedCount} registros eliminados"
            ];
            
        } catch (\Exception $e) {
            error_log('Error en limpieza Rate Limit: ' . $e->getMessage());
            return [
                'success' => false,
                'deleted' => 0,
                'error' => $e->getMessage(),
                'message' => 'Error en limpieza de rate limiting'
            ];
        }
    }

    /**
     * Limpiar sesiones activas expiradas
     */
    public static function cleanupExpiredSessions(): array
    {
        try {
            $db = DB::getInstance();
            
            // Eliminar sesiones inactivas por más de 24 horas
            $query = "DELETE FROM sesiones_activas 
                      WHERE fecha_actividad < DATE_SUB(NOW(), INTERVAL 24 HOUR) 
                      OR activa = 0";
            $result = $db->query($query);
            
            $deletedCount = $result ? $result->rowCount() : 0;
            
            // Estadísticas de sesiones activas
            $activeSessionsQuery = "SELECT 
                COUNT(*) as total_active_sessions,
                COUNT(DISTINCT usuario_id) as unique_users,
                COUNT(DISTINCT ip_address) as unique_ips
                FROM sesiones_activas 
                WHERE activa = 1 AND fecha_actividad >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
            
            $activeResult = $db->query($activeSessionsQuery);
            $activeStats = $activeResult ? $activeResult->fetch(\PDO::FETCH_ASSOC) : [];
            
            return [
                'success' => true,
                'deleted' => $deletedCount,
                'active_sessions' => $activeStats,
                'message' => "Limpieza Sesiones: {$deletedCount} sesiones eliminadas"
            ];
            
        } catch (\Exception $e) {
            error_log('Error en limpieza de sesiones: ' . $e->getMessage());
            return [
                'success' => false,
                'deleted' => 0,
                'error' => $e->getMessage(),
                'message' => 'Error en limpieza de sesiones'
            ];
        }
    }

    /**
     * Limpiar intentos de login fallidos antiguos
     */
    public static function cleanupFailedLoginAttempts(): array
    {
        try {
            $db = DB::getInstance();
            
            // Eliminar intentos de login más antiguos que 7 días
            $query = "DELETE FROM intentos_login WHERE fecha_intento < DATE_SUB(NOW(), INTERVAL 7 DAY)";
            $result = $db->query($query);
            
            $deletedCount = $result ? $result->rowCount() : 0;
            
            // Estadísticas de intentos fallidos recientes
            $recentFailsQuery = "SELECT 
                COUNT(*) as total_attempts,
                COUNT(CASE WHEN exito = 0 THEN 1 END) as failed_attempts,
                COUNT(CASE WHEN exito = 1 THEN 1 END) as successful_attempts,
                COUNT(DISTINCT email) as unique_emails,
                COUNT(DISTINCT ip_address) as unique_ips
                FROM intentos_login 
                WHERE fecha_intento >= DATE_SUB(NOW(), INTERVAL 24 HOUR)";
            
            $recentResult = $db->query($recentFailsQuery);
            $recentStats = $recentResult ? $recentResult->fetch(\PDO::FETCH_ASSOC) : [];
            
            return [
                'success' => true,
                'deleted' => $deletedCount,
                'recent_stats' => $recentStats,
                'message' => "Limpieza Login: {$deletedCount} intentos eliminados"
            ];
            
        } catch (\Exception $e) {
            error_log('Error en limpieza de intentos login: ' . $e->getMessage());
            return [
                'success' => false,
                'deleted' => 0,
                'error' => $e->getMessage(),
                'message' => 'Error en limpieza de intentos de login'
            ];
        }
    }

    /**
     * Obtener estadísticas generales del sistema
     */
    public static function getSystemStats(): array
    {
        try {
            $db = DB::getInstance();
            
            $stats = [];
            
            // Estadísticas de OTP
            $otpQuery = "SELECT 
                COUNT(*) as total_otp_codes,
                COUNT(CASE WHEN utilizado = 1 THEN 1 END) as used_codes,
                COUNT(CASE WHEN expira_en >= NOW() AND utilizado = 0 THEN 1 END) as active_codes,
                COUNT(CASE WHEN expira_en < NOW() AND utilizado = 0 THEN 1 END) as expired_codes
                FROM codigos_otp";
            $otpResult = $db->query($otpQuery);
            $stats['otp'] = $otpResult ? $otpResult->fetch(\PDO::FETCH_ASSOC) : [];

            // Estadísticas de Rate Limiting (última hora)
            $rateLimitQuery = "SELECT 
                COUNT(*) as total_attempts,
                COUNT(DISTINCT client_id) as unique_clients,
                COUNT(DISTINCT action) as unique_actions
                FROM rate_limit_attempts 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
            $rateLimitResult = $db->query($rateLimitQuery);
            $stats['rate_limit'] = $rateLimitResult ? $rateLimitResult->fetch(\PDO::FETCH_ASSOC) : [];

            // Estadísticas de usuarios bloqueados
            $blockedUsersQuery = "SELECT 
                COUNT(*) as blocked_users,
                AVG(intentos_fallidos) as avg_failed_attempts
                FROM usuarios 
                WHERE bloqueado_hasta IS NOT NULL AND bloqueado_hasta > NOW()";
            $blockedResult = $db->query($blockedUsersQuery);
            $stats['blocked_users'] = $blockedResult ? $blockedResult->fetch(\PDO::FETCH_ASSOC) : [];

            // Estadísticas de sesiones activas
            $sessionsQuery = "SELECT 
                COUNT(*) as active_sessions,
                COUNT(DISTINCT usuario_id) as unique_active_users
                FROM sesiones_activas 
                WHERE activa = 1 AND fecha_actividad >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
            $sessionsResult = $db->query($sessionsQuery);
            $stats['active_sessions'] = $sessionsResult ? $sessionsResult->fetch(\PDO::FETCH_ASSOC) : [];

            return [
                'success' => true,
                'stats' => $stats,
                'generated_at' => date('Y-m-d H:i:s')
            ];

        } catch (\Exception $e) {
            error_log('Error obteniendo estadísticas del sistema: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Resetear usuario bloqueado (uso administrativo)
     */
    public static function resetBlockedUser(int $userId): array
    {
        try {
            $db = DB::getInstance();
            
            // Resetear intentos fallidos y bloqueo
            $query = "UPDATE usuarios 
                      SET intentos_fallidos = 0, bloqueado_hasta = NULL 
                      WHERE id = ?";
            $result = $db->query($query, [$userId]);
            
            if ($result) {
                // Limpiar códigos OTP del usuario
                $otpCleanQuery = "UPDATE codigos_otp SET utilizado = 1 WHERE usuario_id = ?";
                $db->query($otpCleanQuery, [$userId]);
                
                error_log("Usuario ID {$userId} desbloqueado manualmente");
                
                return [
                    'success' => true,
                    'message' => 'Usuario desbloqueado exitosamente'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'No se pudo desbloquear el usuario'
                ];
            }

        } catch (\Exception $e) {
            error_log('Error desbloqueando usuario: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Optimizar tablas de la base de datos
     */
    public static function optimizeTables(): array
    {
        try {
            $db = DB::getInstance();
            
            $tables = [
                'codigos_otp',
                'rate_limit_attempts', 
                'intentos_login',
                'sesiones_activas'
            ];
            
            $results = [];
            
            foreach ($tables as $table) {
                try {
                    $query = "OPTIMIZE TABLE `{$table}`";
                    $result = $db->query($query);
                    $results[$table] = $result ? 'optimized' : 'failed';
                } catch (\Exception $e) {
                    $results[$table] = 'error: ' . $e->getMessage();
                }
            }
            
            return [
                'success' => true,
                'optimization_results' => $results,
                'message' => 'Optimización de tablas completada'
            ];

        } catch (\Exception $e) {
            error_log('Error optimizando tablas: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Configurar limpieza automática (llamar desde cron o task scheduler)
     */
    public static function scheduleCleanup(): void
    {
        // Verificar si debe ejecutarse limpieza (probabilidad)
        if (rand(1, 100) <= 5) { // 5% de probabilidad
            $results = self::runFullCleanup();
            
            if ($results['total_cleaned'] > 0) {
                error_log("Scheduled cleanup executed: {$results['total_cleaned']} records cleaned");
            }
        }
    }

    /**
     * Crear todas las tablas necesarias si no existen
     */
    public static function ensureTablesExist(): array
    {
        try {
            $results = [];
            
            // Crear tabla de rate limiting
            $results['rate_limit_table'] = RateLimitMiddleware::createTable();
            
            // Verificar otras tablas necesarias
            $db = DB::getInstance();
            
            // Verificar tabla codigos_otp
            $otpTableQuery = "SHOW TABLES LIKE 'codigos_otp'";
            $otpResult = $db->query($otpTableQuery);
            $results['otp_table_exists'] = $otpResult && $otpResult->rowCount() > 0;
            
            return [
                'success' => true,
                'results' => $results,
                'message' => 'Verificación de tablas completada'
            ];

        } catch (\Exception $e) {
            error_log('Error verificando tablas: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}