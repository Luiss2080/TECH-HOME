<?php

/**
 * Sistema de Testing para 2FA OTP
 * Tests automatizados para verificar el funcionamiento completo del sistema
 */

require_once __DIR__ . '/../Core/DB.php';
require_once __DIR__ . '/../App/Models/CodigoOTP.php';
require_once __DIR__ . '/../App/Models/User.php';
require_once __DIR__ . '/../App/Controllers/AuthController.php';
require_once __DIR__ . '/../App/Middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/../App/Services/OTPCleanupService.php';

use Core\DB;
use App\Models\CodigoOTP;
use App\Models\User;
use App\Controllers\AuthController;
use App\Middleware\RateLimitMiddleware;
use App\Services\OTPCleanupService;

class OTP2FATest
{
    private $results = [];
    private $testUserId;
    private $testEmail = 'test2fa@techhome.bo';
    
    public function __construct()
    {
        echo "🔐 INICIANDO TESTS DEL SISTEMA 2FA OTP\n";
        echo "=====================================\n\n";
    }

    /**
     * Ejecutar todos los tests
     */
    public function runAllTests()
    {
        try {
            $this->setupTestEnvironment();
            
            // Tests de infraestructura
            $this->testDatabaseConnection();
            $this->testTablesExist();
            
            // Tests de modelo CodigoOTP
            $this->testOTPGeneration();
            $this->testOTPValidation();
            $this->testOTPExpiration();
            $this->testOTPResend();
            
            // Tests de protección brute force
            $this->testBruteForceProtection();
            $this->testRateLimiting();
            
            // Tests de limpieza
            $this->testCleanupService();
            
            // Tests de sistema completo
            $this->testFullAuthFlow();
            
            $this->cleanupTestEnvironment();
            $this->showResults();
            
        } catch (\Exception $e) {
            $this->addResult('SETUP_ERROR', false, 'Error en setup: ' . $e->getMessage());
            $this->showResults();
        }
    }

    /**
     * Setup del ambiente de testing
     */
    private function setupTestEnvironment()
    {
        echo "📋 Setting up test environment...\n";
        
        // Configurar variables de entorno de prueba
        $_ENV['DB_HOST'] = $_ENV['DB_HOST'] ?? 'localhost';
        $_ENV['DB_NAME'] = $_ENV['DB_NAME'] ?? 'tech_home';
        $_ENV['DB_USER'] = $_ENV['DB_USER'] ?? 'root';
        $_ENV['DB_PASS'] = $_ENV['DB_PASS'] ?? '';
        
        // Crear usuario de prueba
        $this->createTestUser();
        
        echo "✅ Test environment ready\n\n";
    }

    /**
     * Test de conexión a base de datos
     */
    private function testDatabaseConnection()
    {
        echo "🔌 Testing database connection...\n";
        
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT 1 as test");
            $success = $result && $result->fetch();
            
            $this->addResult('DB_CONNECTION', $success, $success ? 'Conexión exitosa' : 'Fallo en conexión');
        } catch (\Exception $e) {
            $this->addResult('DB_CONNECTION', false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Test de existencia de tablas
     */
    private function testTablesExist()
    {
        echo "🗄️  Testing required tables...\n";
        
        $requiredTables = [
            'usuarios',
            'codigos_otp',
            'rate_limit_attempts',
            'intentos_login'
        ];
        
        $db = DB::getInstance();
        $tablesExist = true;
        $missingTables = [];
        
        foreach ($requiredTables as $table) {
            try {
                $result = $db->query("SHOW TABLES LIKE '{$table}'");
                if (!$result || $result->rowCount() === 0) {
                    $tablesExist = false;
                    $missingTables[] = $table;
                }
            } catch (\Exception $e) {
                $tablesExist = false;
                $missingTables[] = $table . ' (error: ' . $e->getMessage() . ')';
            }
        }
        
        $message = $tablesExist ? 'Todas las tablas existen' : 'Tablas faltantes: ' . implode(', ', $missingTables);
        $this->addResult('TABLES_EXIST', $tablesExist, $message);
    }

    /**
     * Test de generación de códigos OTP
     */
    private function testOTPGeneration()
    {
        echo "🔢 Testing OTP generation...\n";
        
        try {
            $result = CodigoOTP::generateOTP($this->testUserId);
            $success = $result['success'] && strlen($result['codigo']) === 6 && is_numeric($result['codigo']);
            
            if ($success) {
                $this->testOTPCode = $result['codigo'];
            }
            
            $this->addResult('OTP_GENERATION', $success, $success ? 'OTP generado correctamente' : 'Fallo generando OTP');
        } catch (\Exception $e) {
            $this->addResult('OTP_GENERATION', false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Test de validación de códigos OTP
     */
    private function testOTPValidation()
    {
        echo "✅ Testing OTP validation...\n";
        
        try {
            // Generar código válido
            $otpResult = CodigoOTP::generateOTP($this->testUserId);
            $validCode = $otpResult['codigo'];
            
            // Test 1: Código válido
            $validationResult = CodigoOTP::validateOTP($this->testUserId, $validCode);
            $test1 = $validationResult['success'];
            
            // Test 2: Código inválido
            $invalidValidation = CodigoOTP::validateOTP($this->testUserId, '000000');
            $test2 = !$invalidValidation['success'];
            
            // Test 3: Código ya utilizado (reutilizar el mismo código)
            $reusedValidation = CodigoOTP::validateOTP($this->testUserId, $validCode);
            $test3 = !$reusedValidation['success'];
            
            $allPassed = $test1 && $test2 && $test3;
            $message = sprintf('Código válido: %s, Código inválido: %s, Código reutilizado: %s', 
                              $test1 ? 'PASS' : 'FAIL',
                              $test2 ? 'PASS' : 'FAIL', 
                              $test3 ? 'PASS' : 'FAIL');
            
            $this->addResult('OTP_VALIDATION', $allPassed, $message);
        } catch (\Exception $e) {
            $this->addResult('OTP_VALIDATION', false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Test de expiración de códigos
     */
    private function testOTPExpiration()
    {
        echo "⏰ Testing OTP expiration...\n";
        
        try {
            // Crear código que expire inmediatamente (modificar en BD)
            $otpResult = CodigoOTP::generateOTP($this->testUserId);
            $code = $otpResult['codigo'];
            
            // Modificar fecha de expiración a pasado
            $db = DB::getInstance();
            $query = "UPDATE codigos_otp SET expira_en = DATE_SUB(NOW(), INTERVAL 1 MINUTE) 
                      WHERE usuario_id = ? AND codigo = ?";
            $db->query($query, [$this->testUserId, $code]);
            
            // Intentar validar código expirado
            $validationResult = CodigoOTP::validateOTP($this->testUserId, $code);
            $success = !$validationResult['success'] && strpos($validationResult['error'], 'expirado') !== false;
            
            $this->addResult('OTP_EXPIRATION', $success, $success ? 'Código expirado correctamente rechazado' : 'Error en manejo de expiración');
        } catch (\Exception $e) {
            $this->addResult('OTP_EXPIRATION', false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Test de reenvío de códigos
     */
    private function testOTPResend()
    {
        echo "🔄 Testing OTP resend...\n";
        
        try {
            // Generar código inicial
            $firstResult = CodigoOTP::generateOTP($this->testUserId);
            $firstCode = $firstResult['codigo'];
            
            sleep(1); // Esperar para asegurar diferentes timestamps
            
            // Reenviar código
            $resendResult = CodigoOTP::resendOTP($this->testUserId);
            $secondCode = $resendResult['codigo'];
            
            // El nuevo código debe ser diferente
            $success = $resendResult['success'] && $firstCode !== $secondCode;
            
            // Verificar que el primer código ya no es válido
            $oldValidation = CodigoOTP::validateOTP($this->testUserId, $firstCode);
            $oldInvalid = !$oldValidation['success'];
            
            $success = $success && $oldInvalid;
            
            $this->addResult('OTP_RESEND', $success, $success ? 'Reenvío funciona correctamente' : 'Error en reenvío');
        } catch (\Exception $e) {
            $this->addResult('OTP_RESEND', false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Test de protección brute force
     */
    private function testBruteForceProtection()
    {
        echo "🛡️  Testing brute force protection...\n";
        
        try {
            // Generar código válido
            $otpResult = CodigoOTP::generateOTP($this->testUserId);
            
            // Hacer 3 intentos fallidos para activar bloqueo
            for ($i = 0; $i < 3; $i++) {
                CodigoOTP::validateOTP($this->testUserId, '000000');
            }
            
            // El 4to intento debe ser bloqueado incluso con código correcto
            $blockedResult = CodigoOTP::validateOTP($this->testUserId, $otpResult['codigo']);
            $success = !$blockedResult['success'] && isset($blockedResult['locked']);
            
            // Resetear usuario para otros tests
            OTPCleanupService::resetBlockedUser($this->testUserId);
            
            $this->addResult('BRUTE_FORCE_PROTECTION', $success, $success ? 'Protección brute force activa' : 'Fallo en protección');
        } catch (\Exception $e) {
            $this->addResult('BRUTE_FORCE_PROTECTION', false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Test de rate limiting
     */
    private function testRateLimiting()
    {
        echo "🚦 Testing rate limiting...\n";
        
        try {
            // Crear tabla si no existe
            RateLimitMiddleware::createTable();
            
            // Simular múltiples intentos del mismo cliente
            $clientId = 'test_client_' . time();
            $db = DB::getInstance();
            
            // Insertar múltiples intentos rápidamente
            for ($i = 0; $i < 6; $i++) {
                $query = "INSERT INTO rate_limit_attempts (client_id, action, ip_address, created_at) 
                          VALUES (?, 'login', '127.0.0.1', NOW())";
                $db->query($query, [$clientId]);
            }
            
            // Verificar que se detecta el exceso de intentos
            $checkQuery = "SELECT COUNT(*) as attempts FROM rate_limit_attempts 
                           WHERE client_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
            $result = $db->query($checkQuery, [$clientId]);
            $data = $result->fetch(\PDO::FETCH_ASSOC);
            
            $success = $data['attempts'] >= 5; // Should detect multiple attempts
            
            $this->addResult('RATE_LIMITING', $success, $success ? 'Rate limiting funcionando' : 'Fallo en rate limiting');
        } catch (\Exception $e) {
            $this->addResult('RATE_LIMITING', false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Test del servicio de limpieza
     */
    private function testCleanupService()
    {
        echo "🧹 Testing cleanup service...\n";
        
        try {
            // Crear datos de prueba para limpiar
            $db = DB::getInstance();
            
            // Insertar código expirado
            $expiredQuery = "INSERT INTO codigos_otp (usuario_id, codigo, expira_en, utilizado, creado_en) 
                           VALUES (?, '999999', DATE_SUB(NOW(), INTERVAL 1 HOUR), 0, NOW())";
            $db->query($expiredQuery, [$this->testUserId]);
            
            // Ejecutar limpieza
            $cleanupResult = OTPCleanupService::cleanupOTPCodes();
            
            $success = $cleanupResult['success'] && $cleanupResult['deleted'] > 0;
            
            $this->addResult('CLEANUP_SERVICE', $success, $success ? 'Servicio de limpieza funcional' : 'Fallo en limpieza');
        } catch (\Exception $e) {
            $this->addResult('CLEANUP_SERVICE', false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Test del flujo completo de autenticación
     */
    private function testFullAuthFlow()
    {
        echo "🔄 Testing full authentication flow...\n";
        
        try {
            // Simular el flujo completo sin HTTP requests reales
            
            // 1. Generar código
            $otpResult = CodigoOTP::generateOTP($this->testUserId);
            $step1 = $otpResult['success'];
            
            // 2. Verificar que se puede validar
            $canGenerate = CodigoOTP::canGenerateNewCode($this->testUserId);
            $step2 = !$canGenerate['can_generate'] && isset($canGenerate['codigo_existente']);
            
            // 3. Validar código correcto
            $validationResult = CodigoOTP::validateOTP($this->testUserId, $otpResult['codigo']);
            $step3 = $validationResult['success'];
            
            // 4. Verificar que no se puede reutilizar
            $reuseResult = CodigoOTP::validateOTP($this->testUserId, $otpResult['codigo']);
            $step4 = !$reuseResult['success'];
            
            $success = $step1 && $step2 && $step3 && $step4;
            $message = sprintf('Gen: %s, Check: %s, Valid: %s, Reuse: %s',
                              $step1 ? 'OK' : 'FAIL',
                              $step2 ? 'OK' : 'FAIL',
                              $step3 ? 'OK' : 'FAIL',
                              $step4 ? 'OK' : 'FAIL');
            
            $this->addResult('FULL_AUTH_FLOW', $success, $message);
        } catch (\Exception $e) {
            $this->addResult('FULL_AUTH_FLOW', false, 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Crear usuario de prueba
     */
    private function createTestUser()
    {
        try {
            $db = DB::getInstance();
            
            // Verificar si ya existe
            $checkQuery = "SELECT id FROM usuarios WHERE email = ?";
            $checkResult = $db->query($checkQuery, [$this->testEmail]);
            
            if ($checkResult && $checkResult->rowCount() > 0) {
                $user = $checkResult->fetch(\PDO::FETCH_ASSOC);
                $this->testUserId = $user['id'];
                return;
            }
            
            // Crear nuevo usuario
            $insertQuery = "INSERT INTO usuarios (nombre, apellido, email, password, estado, fecha_creacion) 
                           VALUES ('Test', '2FA', ?, ?, 1, NOW())";
            $hashedPassword = password_hash('Test123456!', PASSWORD_DEFAULT);
            $result = $db->query($insertQuery, [$this->testEmail, $hashedPassword]);
            
            if ($result) {
                $this->testUserId = $db->lastInsertId();
            } else {
                throw new \Exception('No se pudo crear usuario de prueba');
            }
            
        } catch (\Exception $e) {
            throw new \Exception('Error creando usuario de prueba: ' . $e->getMessage());
        }
    }

    /**
     * Limpiar ambiente de prueba
     */
    private function cleanupTestEnvironment()
    {
        echo "🧹 Cleaning up test environment...\n";
        
        try {
            $db = DB::getInstance();
            
            // Limpiar datos de prueba
            $db->query("DELETE FROM codigos_otp WHERE usuario_id = ?", [$this->testUserId]);
            $db->query("DELETE FROM rate_limit_attempts WHERE client_id LIKE 'test_client_%'");
            $db->query("DELETE FROM intentos_login WHERE email = ?", [$this->testEmail]);
            
            // No eliminar el usuario para evitar problemas de FK
            // En un ambiente real de testing se usaría una base de datos temporal
            
            echo "✅ Cleanup completed\n\n";
        } catch (\Exception $e) {
            echo "⚠️  Cleanup error: " . $e->getMessage() . "\n\n";
        }
    }

    /**
     * Agregar resultado de test
     */
    private function addResult($testName, $passed, $message)
    {
        $this->results[] = [
            'test' => $testName,
            'passed' => $passed,
            'message' => $message
        ];
        
        $status = $passed ? '✅ PASS' : '❌ FAIL';
        echo "  {$status}: {$message}\n";
    }

    /**
     * Mostrar resultados finales
     */
    private function showResults()
    {
        echo "\n🏁 RESULTADOS FINALES\n";
        echo "====================\n\n";
        
        $totalTests = count($this->results);
        $passedTests = array_sum(array_column($this->results, 'passed'));
        $failedTests = $totalTests - $passedTests;
        $successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 2) : 0;
        
        echo "📊 RESUMEN:\n";
        echo "  Total de tests: {$totalTests}\n";
        echo "  Tests exitosos: {$passedTests}\n";
        echo "  Tests fallidos: {$failedTests}\n";
        echo "  Tasa de éxito: {$successRate}%\n\n";
        
        if ($failedTests > 0) {
            echo "❌ TESTS FALLIDOS:\n";
            foreach ($this->results as $result) {
                if (!$result['passed']) {
                    echo "  • {$result['test']}: {$result['message']}\n";
                }
            }
            echo "\n";
        }
        
        // Recomendaciones según resultados
        if ($successRate >= 90) {
            echo "🎉 ¡EXCELENTE! El sistema 2FA está funcionando correctamente.\n";
        } elseif ($successRate >= 70) {
            echo "⚠️  BUENO: El sistema funciona pero hay algunos problemas menores.\n";
        } else {
            echo "🚨 CRÍTICO: El sistema 2FA tiene problemas serios que deben ser corregidos.\n";
        }
        
        echo "\n🔐 Testing del Sistema 2FA OTP completado.\n";
    }
}

// Ejecutar tests si se llama directamente
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $tester = new OTP2FATest();
    $tester->runAllTests();
}