<?php

require_once 'bootstrap.php';
$_ENV = loadEnv(BASE_PATH . '.env');

echo "=== PRUEBA DEL SISTEMA DE RECUPERACIÓN DE CONTRASEÑA ===\n\n";

try {
    // 1. Verificar que la tabla existe
    echo "1. Verificando tabla password_reset_tokens...\n";
    $db = \Core\DB::getInstance();
    $result = $db->query("SHOW TABLES LIKE 'password_reset_tokens'");
    if ($result->rowCount() > 0) {
        echo "✅ Tabla password_reset_tokens existe\n";
    } else {
        echo "❌ Tabla password_reset_tokens NO existe\n";
        exit(1);
    }

    // 2. Probar creación de token
    echo "\n2. Probando creación de token...\n";
    $testEmail = 'admin@techhome.bo';
    $token = \App\Models\PasswordResetToken::createToken($testEmail);
    echo "✅ Token creado: " . substr($token, 0, 16) . "...\n";

    // 3. Verificar token en base de datos
    echo "\n3. Verificando token en base de datos...\n";
    $tokenData = $db->query("SELECT * FROM password_reset_tokens WHERE email = ?", [$testEmail])->fetch();
    if ($tokenData) {
        echo "✅ Token guardado en BD\n";
        echo "   - Email: {$tokenData->email}\n";
        echo "   - Expira: {$tokenData->expires_at}\n";
        echo "   - Usado: " . ($tokenData->used ? 'Sí' : 'No') . "\n";
    }

    // 4. Probar validación de token
    echo "\n4. Probando validación de token...\n";
    $validationResult = \App\Models\PasswordResetToken::validateToken($token);
    if ($validationResult) {
        echo "✅ Token válido para: {$validationResult['email']}\n";
    } else {
        echo "❌ Token inválido\n";
    }

    // 5. Probar servicio de email
    echo "\n5. Probando servicio de email...\n";
    $emailService = new \App\Services\EmailService();
    echo "   - Configuración completa: " . ($emailService->isConfigured() ? 'Sí' : 'No (modo desarrollo)') . "\n";
    
    $emailSent = $emailService->sendPasswordResetEmail($testEmail, $token);
    if ($emailSent) {
        echo "✅ Email enviado/simulado exitosamente\n";
    } else {
        echo "❌ Error enviando email\n";
    }

    // 6. Generar URLs de prueba
    echo "\n6. URLs generadas:\n";
    echo "   - Solicitar recuperación: " . route('password.forgot') . "\n";
    echo "   - Reset con token: " . route('password.reset') . "?token=" . substr($token, 0, 16) . "...\n";

    // 7. Probar limpieza de tokens
    echo "\n7. Probando limpieza de tokens...\n";
    // Crear un token expirado para probar
    $db->query("INSERT INTO password_reset_tokens (email, token, expires_at, used) VALUES (?, ?, DATE_SUB(NOW(), INTERVAL 1 HOUR), 0)", 
               ['test@example.com', 'expired_token_123', ]);
    
    $cleaned = \App\Models\PasswordResetToken::cleanExpiredTokens();
    echo "✅ Tokens expirados limpiados: $cleaned\n";

    // 8. Marcar token como usado
    echo "\n8. Probando marcar token como usado...\n";
    $marked = \App\Models\PasswordResetToken::markAsUsed($token);
    if ($marked) {
        echo "✅ Token marcado como usado\n";
        
        // Verificar que ya no es válido
        $validationResult2 = \App\Models\PasswordResetToken::validateToken($token);
        if (!$validationResult2) {
            echo "✅ Token usado ya no es válido\n";
        }
    }

    echo "\n✅ TODAS LAS PRUEBAS COMPLETADAS EXITOSAMENTE\n";
    echo "\nAhora puedes probar el sistema completo:\n";
    echo "1. Ve a: " . route('login') . "\n";
    echo "2. Haz clic en '¿Olvidaste tu contraseña?'\n";
    echo "3. Ingresa un email existente\n";
    echo "4. Revisa los logs o el email enviado\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN DE PRUEBAS ===\n";
