<?php

/**
 * Herramientas de debug y monitoreo para el sistema 2FA
 */

namespace App\Services;

use Core\Session;
use App\Models\CodigoOTP;
use App\Models\User;

class TwoFactorDebugger
{
    /**
     * Obtener información completa del estado de sesión 2FA
     */
    public static function getSessionState()
    {
        return [
            'session_active' => Session::isActive(),
            'session_id' => session_id(),
            'has_2fa_user_id' => Session::has('2fa_user_id'),
            'has_2fa_email' => Session::has('2fa_email'),
            'has_2fa_start_time' => Session::has('2fa_start_time'),
            'has_2fa_attempts' => Session::has('2fa_attempts'),
            'has_2fa_code_sent' => Session::has('2fa_code_sent'),
            '2fa_user_id' => Session::get('2fa_user_id'),
            '2fa_email' => Session::get('2fa_email'),
            '2fa_start_time' => Session::get('2fa_start_time'),
            '2fa_attempts' => Session::get('2fa_attempts', 0),
            'session_age' => Session::has('2fa_start_time') ? (time() - Session::get('2fa_start_time')) : null,
            'time_remaining' => Session::has('2fa_start_time') ? max(0, 600 - (time() - Session::get('2fa_start_time'))) : null,
            'all_session_vars' => array_keys($_SESSION ?? []),
        ];
    }

    /**
     * Obtener información del último código OTP para un usuario
     */
    public static function getOTPState($userId)
    {
        if (!$userId) return null;

        $lastCode = CodigoOTP::getLastCodeForUser($userId);
        
        if (!$lastCode) {
            return ['has_code' => false];
        }

        return [
            'has_code' => true,
            'codigo' => $lastCode->codigo,
            'created_at' => $lastCode->creado_en,
            'expires_at' => $lastCode->expira_en,
            'is_used' => (bool)$lastCode->utilizado,
            'is_expired' => strtotime($lastCode->expira_en) < time(),
            'time_until_expiry' => max(0, strtotime($lastCode->expira_en) - time()),
            'age_seconds' => time() - strtotime($lastCode->creado_en),
        ];
    }

    /**
     * Obtener información del usuario
     */
    public static function getUserState($userId)
    {
        if (!$userId) return null;

        $user = User::find($userId);
        if (!$user) return null;

        return [
            'id' => $user->id,
            'email' => $user->email,
            'nombre' => $user->nombre,
            'estado' => $user->estado,
            'intentos_fallidos' => $user->intentos_fallidos ?? 0,
            'bloqueado_hasta' => $user->bloqueado_hasta,
            'is_blocked' => $user->bloqueado_hasta && strtotime($user->bloqueado_hasta) > time(),
            'block_time_remaining' => $user->bloqueado_hasta ? max(0, strtotime($user->bloqueado_hasta) - time()) : null,
        ];
    }

    /**
     * Verificar integridad del sistema 2FA
     */
    public static function checkSystemIntegrity()
    {
        $issues = [];

        // Verificar configuración de sesión
        if (ini_get('session.gc_maxlifetime') < 3600) {
            $issues[] = 'session.gc_maxlifetime muy bajo: ' . ini_get('session.gc_maxlifetime');
        }

        // Verificar directorio de sesiones
        $sessionPath = session_save_path();
        if (empty($sessionPath)) {
            $issues[] = 'session_save_path no configurado';
        } elseif (!is_writable($sessionPath)) {
            $issues[] = 'Directorio de sesiones no escribible: ' . $sessionPath;
        }

        // Verificar configuración de cookies
        if (ini_get('session.cookie_lifetime') == 0) {
            $issues[] = 'session.cookie_lifetime configurado como 0 (hasta cerrar navegador)';
        }

        return [
            'healthy' => empty($issues),
            'issues' => $issues,
            'php_version' => PHP_VERSION,
            'session_module' => ini_get('session.save_handler'),
            'session_path' => $sessionPath,
            'memory_limit' => ini_get('memory_limit'),
        ];
    }

    /**
     * Generar reporte completo del estado 2FA
     */
    public static function generateReport($userId = null)
    {
        $userId = $userId ?: Session::get('2fa_user_id');
        
        return [
            'timestamp' => date('Y-m-d H:i:s'),
            'session_state' => self::getSessionState(),
            'otp_state' => self::getOTPState($userId),
            'user_state' => self::getUserState($userId),
            'system_integrity' => self::checkSystemIntegrity(),
            'stats' => CodigoOTP::getStats($userId),
        ];
    }

    /**
     * Log de debug para troubleshooting
     */
    public static function logDebugInfo($context = 'general', $userId = null)
    {
        $report = self::generateReport($userId);
        $logMessage = "2FA_DEBUG_{$context}: " . json_encode($report, JSON_PRETTY_PRINT);
        error_log($logMessage);
        return $report;
    }

    /**
     * Verificar si el sistema 2FA está en estado consistente
     */
    public static function isSystemConsistent()
    {
        $sessionState = self::getSessionState();
        
        // Verificaciones básicas de consistencia
        $issues = [];

        if ($sessionState['has_2fa_user_id'] && !$sessionState['has_2fa_email']) {
            $issues[] = 'Tiene user_id pero no email en sesión 2FA';
        }

        if ($sessionState['has_2fa_email'] && !$sessionState['has_2fa_user_id']) {
            $issues[] = 'Tiene email pero no user_id en sesión 2FA';
        }

        if ($sessionState['has_2fa_start_time'] && $sessionState['session_age'] > 600) {
            $issues[] = 'Sesión 2FA expirada pero no limpiada';
        }

        if ($sessionState['has_2fa_user_id']) {
            $userId = $sessionState['2fa_user_id'];
            $otpState = self::getOTPState($userId);
            $userState = self::getUserState($userId);

            if (!$userState) {
                $issues[] = 'Usuario en sesión 2FA no existe en BD';
            } elseif ($userState['is_blocked']) {
                $issues[] = 'Usuario en sesión 2FA está bloqueado';
            }

            if (!$otpState || !$otpState['has_code']) {
                $issues[] = 'Sesión 2FA activa pero sin código OTP';
            }
        }

        return [
            'consistent' => empty($issues),
            'issues' => $issues,
            'checked_at' => date('Y-m-d H:i:s')
        ];
    }
}
