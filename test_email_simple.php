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
    echo "⏳ Enviando...\n\n";

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
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n========================================\n";
