<?php

/**
 * Script simple para probar el email de recuperación de contraseña
 * TECH HOME BOLIVIA
 */

require_once 'bootstrap.php';
$_ENV = loadEnv(BASE_PATH . '.env');

echo "\n========================================\n";
echo "PRUEBA DE EMAIL DE RECUPERACIÓN\n";
echo "========================================\n";

try {
    $testEmail = 'jhoel0521@gmail.com';
    $resetToken = 'recovery-token-' . uniqid() . '-' . time();
    
    echo "📧 Destinatario: $testEmail\n";
    echo "🔐 Token: $resetToken\n";
    echo "🚀 Servicio: " . ($_ENV['MAIL_SERVICE_CLASS'] ?? 'SimpleMailService') . "\n";
    
    // Verificar configuración antes del test
    echo "\n🔧 CONFIGURACIÓN:\n";
    echo "   Host: " . ($_ENV['MAIL_HOST'] ?? 'NO CONFIGURADO') . "\n";
    echo "   Puerto: " . ($_ENV['MAIL_PORT'] ?? 'NO CONFIGURADO') . "\n";
    echo "   Usuario: " . ($_ENV['MAIL_USERNAME'] ?? 'NO CONFIGURADO') . "\n";
    echo "   Password: " . (!empty($_ENV['MAIL_PASSWORD']) ? '✅ Configurado' : '❌ VACÍO') . "\n";
    echo "   From: " . ($_ENV['MAIL_FROM_ADDRESS'] ?? 'NO CONFIGURADO') . "\n";
    
    if (empty($_ENV['MAIL_PASSWORD'])) {
        echo "\n❌ ERROR: MAIL_PASSWORD está vacío en el archivo .env\n";
        echo "   Por favor configura la contraseña del servidor SMTP\n";
        exit(1);
    }
    
    echo "\n⏳ Enviando...\n";

    $startTime = microtime(true);
    
    // Exactamente como querías
    $emailService = mailService();
    $sent = $emailService->sendPasswordResetEmail($testEmail, $resetToken);
    
    $endTime = microtime(true);
    $duration = round(($endTime - $startTime) * 1000, 2);

    if ($sent) {
        echo "✅ Email enviado exitosamente\n";
        echo "⏱️  Tiempo: {$duration}ms\n";
        echo "🔗 URL: " . $_ENV['APP_URL'] . "/reset-password?token=" . urlencode($resetToken) . "\n";
    } else {
        echo "❌ Error enviando email\n";
        echo "⏱️  Tiempo: {$duration}ms\n";
        echo "💡 Revisa los logs de PHP para más detalles\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "🔍 Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n========================================\n";
