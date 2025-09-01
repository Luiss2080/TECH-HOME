<?php

namespace App\Models;

use Core\Model;
use Core\DB;

class CodigoOTP extends Model
{
    protected $table = 'codigos_otp';
    protected $primaryKey = 'id';
    protected $fillable = [
        'usuario_id',
        'codigo',
        'expira_en',
        'utilizado',
        'creado_en'
    ];
    protected $timestamps = false;

    // Configuración OTP
    const EXPIRATION_MINUTES = 1; // 1 minuto = 60 segundos
    const MAX_ATTEMPTS = 3;
    const LOCKOUT_MINUTES = 5;
    const CODE_LENGTH = 6;

    /**
     * Generar un nuevo código OTP para un usuario
     */
    public static function generateOTP($usuarioId)
    {
        try {
            DB::beginTransaction();

            // Invalidar códigos anteriores del usuario
            self::invalidatePreviousCodes($usuarioId);

            // Generar código de 6 dígitos
            $codigo = self::generateSecureCode();

            // Calcular fecha de expiración (60 segundos)
            $expiraEn = date('Y-m-d H:i:s', time() + (self::EXPIRATION_MINUTES * 60));

            // Crear nuevo código
            $otp = new self([
                'usuario_id' => $usuarioId,
                'codigo' => $codigo,
                'expira_en' => $expiraEn,
                'utilizado' => 0,
                'creado_en' => date('Y-m-d H:i:s')
            ]);

            if ($otp->save()) {
                DB::commit();
                return [
                    'success' => true,
                    'codigo' => $codigo,
                    'expira_en' => $expiraEn,
                    'expira_en_timestamp' => time() + (self::EXPIRATION_MINUTES * 60)
                ];
            }

            DB::rollBack();
            return ['success' => false, 'error' => 'Error al guardar código OTP'];

        } catch (\Exception $e) {
            DB::rollBack();
            error_log('Error generando OTP: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Error interno generando código'];
        }
    }

    /**
     * Validar un código OTP
     */
    public static function validateOTP($usuarioId, $codigo)
    {
        try {
            // Verificar si el usuario está bloqueado
            $user = User::find($usuarioId);
            if (!$user) {
                return ['success' => false, 'error' => 'Usuario no encontrado'];
            }

            if ($user->bloqueado_hasta && strtotime($user->bloqueado_hasta) > time()) {
                $tiempoRestante = ceil((strtotime($user->bloqueado_hasta) - time()) / 60);
                return [
                    'success' => false, 
                    'error' => "Usuario bloqueado. Intenta en {$tiempoRestante} minutos.",
                    'locked' => true,
                    'unlock_time' => $user->bloqueado_hasta
                ];
            }

            // Buscar código OTP válido
            $otpRecord = self::where('usuario_id', '=', $usuarioId)
                            ->where('codigo', '=', $codigo)
                            ->where('utilizado', '=', 0)
                            ->orderBy('creado_en', 'DESC')
                            ->first();

            if (!$otpRecord) {
                self::handleFailedAttempt($usuarioId);
                return ['success' => false, 'error' => 'Código OTP inválido'];
            }

            // Verificar expiración
            if (strtotime($otpRecord->expira_en) < time()) {
                self::handleFailedAttempt($usuarioId);
                return ['success' => false, 'error' => 'Código OTP expirado'];
            }

            // Código válido - marcarlo como utilizado
            DB::beginTransaction();

            $otpRecord->utilizado = 1;
            $otpRecord->save();

            // Resetear intentos fallidos del usuario
            $user->intentos_fallidos = 0;
            $user->bloqueado_hasta = null;
            $user->save();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Código OTP válido',
                'tiempo_restante' => max(0, strtotime($otpRecord->expira_en) - time())
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            error_log('Error validando OTP: ' . $e->getMessage());
            return ['success' => false, 'error' => 'Error interno validando código'];
        }
    }

    /**
     * Manejar intento fallido
     */
    private static function handleFailedAttempt($usuarioId)
    {
        try {
            $user = User::find($usuarioId);
            if (!$user) return;

            $user->intentos_fallidos = ($user->intentos_fallidos ?? 0) + 1;

            // Bloquear después de MAX_ATTEMPTS intentos
            if ($user->intentos_fallidos >= self::MAX_ATTEMPTS) {
                $user->bloqueado_hasta = date('Y-m-d H:i:s', time() + (self::LOCKOUT_MINUTES * 60));
            }

            $user->save();

        } catch (\Exception $e) {
            error_log('Error manejando intento fallido: ' . $e->getMessage());
        }
    }

    /**
     * Generar código seguro de 6 dígitos
     */
    private static function generateSecureCode()
    {
        // Usar random_int para mayor seguridad criptográfica
        return str_pad(random_int(100000, 999999), self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }

    /**
     * Invalidar códigos anteriores del usuario
     */
    private static function invalidatePreviousCodes($usuarioId)
    {
        $db = DB::getInstance();
        $query = "UPDATE codigos_otp SET utilizado = 1 WHERE usuario_id = ? AND utilizado = 0";
        $db->query($query, [$usuarioId]);
    }

    /**
     * Limpiar códigos expirados (llamar periódicamente)
     */
    public static function cleanupExpiredCodes()
    {
        try {
            $db = DB::getInstance();
            $query = "DELETE FROM codigos_otp WHERE expira_en < NOW() OR utilizado = 1";
            $result = $db->query($query);
            
            $deletedCount = $db->lastInsertId(); // En este caso, cuenta de filas afectadas
            error_log("Limpieza OTP: {$deletedCount} códigos eliminados");
            
            return ['success' => true, 'deleted' => $deletedCount];
        } catch (\Exception $e) {
            error_log('Error en limpieza de códigos OTP: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Obtener estadísticas de códigos OTP
     */
    public static function getStats($usuarioId = null)
    {
        try {
            $db = DB::getInstance();
            
            if ($usuarioId) {
                // Stats específicas del usuario
                $query = "SELECT 
                    COUNT(*) as total_generados,
                    SUM(CASE WHEN utilizado = 1 THEN 1 ELSE 0 END) as utilizados,
                    SUM(CASE WHEN expira_en < NOW() AND utilizado = 0 THEN 1 ELSE 0 END) as expirados,
                    SUM(CASE WHEN expira_en >= NOW() AND utilizado = 0 THEN 1 ELSE 0 END) as activos
                    FROM codigos_otp WHERE usuario_id = ?";
                $result = $db->query($query, [$usuarioId]);
            } else {
                // Stats globales
                $query = "SELECT 
                    COUNT(*) as total_generados,
                    SUM(CASE WHEN utilizado = 1 THEN 1 ELSE 0 END) as utilizados,
                    SUM(CASE WHEN expira_en < NOW() AND utilizado = 0 THEN 1 ELSE 0 END) as expirados,
                    SUM(CASE WHEN expira_en >= NOW() AND utilizado = 0 THEN 1 ELSE 0 END) as activos
                    FROM codigos_otp";
                $result = $db->query($query);
            }

            return $result ? $result->fetch(\PDO::FETCH_ASSOC) : [];
        } catch (\Exception $e) {
            error_log('Error obteniendo stats OTP: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar si un usuario puede generar un nuevo código
     */
    public static function canGenerateNewCode($usuarioId)
    {
        try {
            // Verificar si hay código activo no utilizado
            $activeCode = self::where('usuario_id', '=', $usuarioId)
                            ->where('utilizado', '=', 0)
                            ->whereRaw('expira_en >= NOW()')
                            ->first();

            if ($activeCode) {
                $tiempoRestante = strtotime($activeCode->expira_en) - time();
                return [
                    'can_generate' => false,
                    'reason' => 'Ya existe un código activo',
                    'tiempo_restante' => max(0, $tiempoRestante),
                    'codigo_existente' => true
                ];
            }

            // Verificar si el usuario está bloqueado
            $user = User::find($usuarioId);
            if ($user && $user->bloqueado_hasta && strtotime($user->bloqueado_hasta) > time()) {
                $tiempoBloqueo = ceil((strtotime($user->bloqueado_hasta) - time()) / 60);
                return [
                    'can_generate' => false,
                    'reason' => "Usuario bloqueado por {$tiempoBloqueo} minutos",
                    'tiempo_bloqueo' => $tiempoBloqueo,
                    'bloqueado' => true
                ];
            }

            return ['can_generate' => true];
        } catch (\Exception $e) {
            error_log('Error verificando generación de código: ' . $e->getMessage());
            return ['can_generate' => false, 'reason' => 'Error interno'];
        }
    }

    /**
     * Reenviar código OTP (invalidar anterior y generar nuevo)
     */
    public static function resendOTP($usuarioId)
    {
        $canGenerate = self::canGenerateNewCode($usuarioId);
        
        if (!$canGenerate['can_generate'] && isset($canGenerate['bloqueado'])) {
            return $canGenerate;
        }

        // Permitir reenvío incluso si hay código activo
        return self::generateOTP($usuarioId);
    }

    /**
     * Obtener el último código generado para un usuario (para debugging)
     */
    public static function getLastCodeForUser($usuarioId)
    {
        return self::where('usuario_id', '=', $usuarioId)
                  ->orderBy('creado_en', 'DESC')
                  ->first();
    }
}