<?php

/**
 * Script de mantenimiento para el sistema OTP
 * Debe ejecutarse periódicamente para limpiar códigos expirados y sesiones huérfanas
 */

// Cargar bootstrap para configurar constantes y autoload
require_once __DIR__ . '/../bootstrap.php';

// Cargar configuración del entorno
$_ENV = loadEnv(BASE_PATH . '.env');

use Core\DB;
use App\Models\CodigoOTP;

echo "🧹 INICIANDO LIMPIEZA DEL SISTEMA OTP\n";
echo "=====================================\n\n";

try {
    // 1. Limpiar códigos OTP expirados
    echo "📋 Limpiando códigos OTP expirados...\n";
    $result = CodigoOTP::cleanupExpiredCodes();
    
    if ($result['success']) {
        echo "✅ Códigos eliminados: " . ($result['deleted'] ?? 0) . "\n";
    } else {
        echo "❌ Error en limpieza: " . $result['error'] . "\n";
    }

    // 2. Limpiar sesiones OTP huérfanas (opcional, si tienes tabla de sesiones)
    echo "\n📋 Limpiando sesiones huérfanas...\n";
    cleanupOrphanedSessions();

    // 3. Resetear usuarios bloqueados que ya cumplieron su tiempo
    echo "\n📋 Liberando usuarios con bloqueo expirado...\n";
    unlockExpiredUsers();

    // 4. Mostrar estadísticas
    echo "\n📊 ESTADÍSTICAS ACTUALES:\n";
    showOTPStats();

    echo "\n✅ LIMPIEZA COMPLETADA EXITOSAMENTE\n";

} catch (\Exception $e) {
    echo "❌ ERROR DURANTE LA LIMPIEZA: " . $e->getMessage() . "\n";
    error_log("Error en limpieza OTP: " . $e->getMessage());
    exit(1);
}

/**
 * Limpiar sesiones PHP huérfanas relacionadas con 2FA
 */
function cleanupOrphanedSessions()
{
    $sessionPath = session_save_path();
    if (empty($sessionPath)) {
        $sessionPath = sys_get_temp_dir();
    }

    $cleaned = 0;
    $sessionFiles = glob($sessionPath . '/sess_*');
    
    if ($sessionFiles) {
        foreach ($sessionFiles as $file) {
            if (time() - filemtime($file) > 3600) { // Archivos de más de 1 hora
                $content = file_get_contents($file);
                if (strpos($content, '2fa_user_id') !== false && strpos($content, '2fa_start_time') !== false) {
                    // Es una sesión 2FA, verificar si está expirada
                    if (preg_match('/2fa_start_time.*?i:(\d+)/', $content, $matches)) {
                        $startTime = (int)$matches[1];
                        if (time() - $startTime > 600) { // Más de 10 minutos
                            if (unlink($file)) {
                                $cleaned++;
                            }
                        }
                    }
                }
            }
        }
    }

    echo "✅ Sesiones 2FA huérfanas eliminadas: $cleaned\n";
}

/**
 * Liberar usuarios con bloqueo expirado
 */
function unlockExpiredUsers()
{
    try {
        $db = DB::getInstance();
        $query = "UPDATE users SET intentos_fallidos = 0, bloqueado_hasta = NULL 
                  WHERE bloqueado_hasta IS NOT NULL AND bloqueado_hasta <= NOW()";
        
        $stmt = $db->query($query);
        $unlocked = $stmt->rowCount();
        
        echo "✅ Usuarios desbloqueados: $unlocked\n";
    } catch (\Exception $e) {
        echo "❌ Error desbloqueando usuarios: " . $e->getMessage() . "\n";
    }
}

/**
 * Mostrar estadísticas del sistema OTP
 */
function showOTPStats()
{
    $stats = CodigoOTP::getStats();
    
    if (!empty($stats)) {
        echo "  • Total códigos generados: " . ($stats['total_generados'] ?? 0) . "\n";
        echo "  • Códigos utilizados: " . ($stats['utilizados'] ?? 0) . "\n";
        echo "  • Códigos expirados: " . ($stats['expirados'] ?? 0) . "\n";
        echo "  • Códigos activos: " . ($stats['activos'] ?? 0) . "\n";
        
        if ($stats['total_generados'] > 0) {
            $tasa_exito = round(($stats['utilizados'] / $stats['total_generados']) * 100, 2);
            echo "  • Tasa de éxito: {$tasa_exito}%\n";
        }
    }

    // Estadísticas de usuarios bloqueados
    try {
        $db = DB::getInstance();
        $query = "SELECT COUNT(*) as bloqueados FROM users WHERE bloqueado_hasta IS NOT NULL AND bloqueado_hasta > NOW()";
        $result = $db->query($query);
        $row = $result->fetch(\PDO::FETCH_ASSOC);
        echo "  • Usuarios actualmente bloqueados: " . ($row['bloqueados'] ?? 0) . "\n";
    } catch (\Exception $e) {
        echo "  ❌ Error obteniendo stats de usuarios bloqueados\n";
    }
}

echo "\n🔄 Para ejecutar este script automáticamente, agrega a cron:\n";
echo "*/5 * * * * php " . __FILE__ . " > /dev/null 2>&1\n";
echo "(Ejecutar cada 5 minutos)\n\n";
