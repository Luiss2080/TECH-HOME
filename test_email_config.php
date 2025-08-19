<?php

/**
 * Script de prueba para verificar el envío de emails
 */

require_once 'bootstrap.php';
$_ENV = loadEnv(BASE_PATH . '.env');

use App\Services\EmailService;

echo "\n========================================\n";
echo "PRUEBA DE CONFIGURACIÓN DE EMAIL\n";
echo "========================================\n";

try {
    $emailService = new EmailService();

    // Verificar configuración
    echo "\n1. VERIFICANDO CONFIGURACIÓN...\n";
    echo "   --------------------------------\n";
    if ($emailService->isConfigured()) {
        echo "   ✅ Configuración SMTP completa\n";
    } else {
        echo "   ❌ Configuración SMTP incompleta\n";
        echo "   \n   Asegúrate de tener configuradas las siguientes variables:\n";
        echo "   - MAIL_HOST\n";
        echo "   - MAIL_PORT\n";
        echo "   - MAIL_USERNAME\n";
        echo "   - MAIL_PASSWORD\n";
        echo "   - MAIL_FROM_ADDRESS\n";
        exit(1);
    }

    // Probar conexión
    echo "\n2. PROBANDO CONEXIÓN SMTP...\n";
    echo "   ----------------------------\n";
    $connectionTest = $emailService->testConnection();

    if ($connectionTest['success']) {
        echo "   ✅ " . $connectionTest['message'] . "\n";
        echo "   📡 Servidor: " . $connectionTest['host'] . ":" . $connectionTest['port'] . "\n";
        if (!empty($connectionTest['server_response'])) {
            echo "   📝 Respuesta: " . trim($connectionTest['server_response']) . "\n";
        }
    } else {
        echo "   ❌ " . $connectionTest['message'] . "\n";
        exit(1);
    }

    // Enviar email de prueba
    echo "\n3. ENVIANDO EMAIL DE PRUEBA...\n";
    echo "   ------------------------------\n";
    $testEmail = 'jhoel0521@gmail.com';
    echo "   📧 Destinatario: $testEmail\n";
    echo "   ⏳ Enviando...\n";

    $startTime = microtime(true);
    $success = $emailService->sendPasswordResetEmail($testEmail, 'test-token-123');
    $endTime = microtime(true);
    $duration = round(($endTime - $startTime) * 1000, 2);

    if ($success) {
        echo "   ✅ Email enviado exitosamente\n";
        echo "   ⏱️  Tiempo de envío: {$duration}ms\n";
        echo "   📋 Revisa los logs para más detalles\n";
    } else {
        echo "   ❌ Error enviando email\n";
        echo "   ⏱️  Tiempo transcurrido: {$duration}ms\n";
        echo "   📋 Revisa los logs de error para más información\n";
    }
} catch (Exception $e) {
    echo "\n   ❌ ERROR EN PRUEBA: " . $e->getMessage() . "\n";
}

echo "\n========================================\n";
echo "CONFIGURACIÓN ACTUAL\n";
echo "========================================\n";
echo "SMTP Host:     " . ($_ENV['MAIL_HOST'] ?? '❌ No configurado') . "\n";
echo "SMTP Port:     " . ($_ENV['MAIL_PORT'] ?? '❌ No configurado') . "\n";
echo "SMTP User:     " . ($_ENV['MAIL_USERNAME'] ?? '❌ No configurado') . "\n";
echo "SMTP Pass:     " . (!empty($_ENV['MAIL_PASSWORD']) ? '✅ Configurado' : '❌ No configurado') . "\n";
echo "From Email:    " . ($_ENV['MAIL_FROM_ADDRESS'] ?? '❌ No configurado') . "\n";
echo "From Name:     " . ($_ENV['MAIL_FROM_NAME'] ?? '❌ No configurado') . "\n";

echo "\n========================================\n";
echo "PRUEBA COMPLETADA\n";
echo "========================================\n\n";
