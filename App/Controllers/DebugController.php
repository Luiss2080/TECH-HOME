<?php

namespace App\Controllers;

use Core\Controller;
use App\Services\TwoFactorDebugger;
use App\Models\CodigoOTP;
use Core\Session;

class DebugController extends Controller
{
    /**
     * Verificar si el modo debug está habilitado
     */
    private function isDebugModeEnabled()
    {
        // Verificar múltiples formas de activar debug
        return (defined('DEBUG_MODE') && constant('DEBUG_MODE')) || 
               (isset($_ENV['DEBUG']) && $_ENV['DEBUG'] === 'true') ||
               (isset($_SERVER['DEBUG']) && $_SERVER['DEBUG'] === 'true');
    }

    /**
     * Página de debug para el sistema 2FA
     * Solo disponible en modo debug
     */
    public function twoFactorDebug()
    {
        // Solo en modo desarrollo/debug
        if (!$this->isDebugModeEnabled()) {
            return redirect(route('home'));
        }

        $userId = $_GET['user_id'] ?? Session::get('2fa_user_id');
        $report = TwoFactorDebugger::generateReport($userId);
        $consistency = TwoFactorDebugger::isSystemConsistent();

        return view('debug.two-factor', [
            'title' => 'Debug Sistema 2FA',
            'report' => $report,
            'consistency' => $consistency,
            'userId' => $userId
        ], false);
    }

    /**
     * API para obtener estado del sistema 2FA en JSON
     */
    public function twoFactorStatus()
    {
        if (!$this->isDebugModeEnabled()) {
            return response()->json(['error' => 'Debug mode disabled'], 403);
        }

        $userId = $_GET['user_id'] ?? Session::get('2fa_user_id');
        $report = TwoFactorDebugger::generateReport($userId);
        
        return response()->json($report);
    }

    /**
     * Limpiar manualmente la sesión 2FA
     */
    public function clearTwoFactorSession()
    {
        if (!$this->isDebugModeEnabled()) {
            return response()->json(['error' => 'Debug mode disabled'], 403);
        }

        Session::remove('2fa_user_id');
        Session::remove('2fa_email');
        Session::remove('2fa_start_time');
        Session::remove('2fa_attempts');
        Session::remove('2fa_code_sent');

        return response()->json(['success' => true, 'message' => 'Sesión 2FA limpiada']);
    }

    /**
     * Ejecutar limpieza manual del sistema OTP
     */
    public function cleanupOtp()
    {
        if (!$this->isDebugModeEnabled()) {
            return response()->json(['error' => 'Debug mode disabled'], 403);
        }

        $result = CodigoOTP::cleanupExpiredCodes();
        return response()->json($result);
    }
}
