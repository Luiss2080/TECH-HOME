<?php

/**
 * Testing REAL del Sistema 2FA OTP
 * Usando credenciales reales del usuario
 */

require_once __DIR__ . '/Core/DB.php';
require_once __DIR__ . '/Core/helpers.php';
require_once __DIR__ . '/App/Models/CodigoOTP.php';
require_once __DIR__ . '/App/Models/User.php';
require_once __DIR__ . '/App/Services/MailServiceFactory.php';
require_once __DIR__ . '/App/Services/Email/PHPMailerService.php';
require_once __DIR__ . '/App/Services/Email/BaseEmailService.php';
require_once __DIR__ . '/App/Middleware/RateLimitMiddleware.php';
require_once __DIR__ . '/App/Services/OTPCleanupService.php';

use Core\DB;
use App\Models\CodigoOTP;
use App\Models\User;
use App\Services\MailServiceFactory;
use App\Middleware\RateLimitMiddleware;
use App\Services\OTPCleanupService;

// Configurar variables de entorno
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value, '"\'');
        }
    }
}

class Real2FATest
{
    private $testEmail = 'naxelf666@gmail.com';
    private $testPassword = '12345678';
    private $testUser = null;
    private $results = [];

    public function __construct()
    {
        echo "🔐 TESTING REAL DEL SISTEMA 2FA - TECH HOME BOLIVIA\n";
        echo "==================================================\n";
        echo "📧 Email de prueba: {$this->testEmail}\n";
        echo "🔑 Password: " . str_repeat('*', strlen($this->testPassword)) . "\n\n";
    }

    public function runRealTests()
    {
        try {
            $this->testDatabaseConnection();
            $this->ensureTablesExist();
            $this->findTestUser();
            $this->testOTPGeneration();
            $this->testEmailSending();
            $this->testOTPValidation();
            $this->testFullFlow();
            $this->testCleanup();
            
            $this->showResults();
        } catch (\Exception $e) {
            echo "❌ ERROR CRÍTICO: " . $e->getMessage() . "\n";
            echo "📍 Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
        }
    }

    private function testDatabaseConnection()
    {
        echo "🔌 Probando conexión a base de datos...\n";
        
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT 1 as test");
            
            if ($result && $result->fetch()) {
                $this->addResult('DATABASE_CONNECTION', true, 'Conexión exitosa a MySQL');
                
                // Mostrar info de la base de datos
                $dbInfo = $db->query("SELECT DATABASE() as db_name, USER() as user_name, VERSION() as version");
                $info = $dbInfo->fetch(\PDO::FETCH_ASSOC);
                echo "  📊 Base de datos: {$info['db_name']}\n";
                echo "  👤 Usuario: {$info['user_name']}\n";
                echo "  🗄️  Versión MySQL: {$info['version']}\n";
            } else {
                throw new \Exception('No se pudo ejecutar query de prueba');
            }
        } catch (\Exception $e) {
            $this->addResult('DATABASE_CONNECTION', false, 'Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function ensureTablesExist()
    {
        echo "\n🗄️  Verificando y creando tablas necesarias...\n";
        
        try {
            $db = DB::getInstance();
            
            // Verificar tabla codigos_otp
            $otpTableExists = $this->tableExists('codigos_otp');
            echo "  📋 Tabla codigos_otp: " . ($otpTableExists ? "✅ Existe" : "❌ No existe") . "\n";
            
            if (!$otpTableExists) {
                echo "  🔧 Creando tabla codigos_otp...\n";
                $this->createOTPTable();
            }
            
            // Verificar tabla rate_limit_attempts
            $rateLimitExists = $this->tableExists('rate_limit_attempts');
            echo "  📋 Tabla rate_limit_attempts: " . ($rateLimitExists ? "✅ Existe" : "❌ No existe") . "\n";
            
            if (!$rateLimitExists) {
                echo "  🔧 Creando tabla rate_limit_attempts...\n";
                RateLimitMiddleware::createTable();
            }
            
            // Verificar campos en usuarios
            $this->ensureUserFields();
            
            $this->addResult('TABLES_SETUP', true, 'Todas las tablas verificadas/creadas');
            
        } catch (\Exception $e) {
            $this->addResult('TABLES_SETUP', false, 'Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createOTPTable()
    {
        $db = DB::getInstance();
        $sql = "
        CREATE TABLE IF NOT EXISTS `codigos_otp` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `usuario_id` int(11) NOT NULL,
          `codigo` varchar(6) NOT NULL,
          `expira_en` datetime NOT NULL,
          `utilizado` tinyint(1) NOT NULL DEFAULT 0,
          `creado_en` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_usuario_id` (`usuario_id`),
          KEY `idx_codigo` (`codigo`),
          KEY `idx_expira_en` (`expira_en`),
          KEY `idx_utilizado` (`utilizado`),
          CONSTRAINT `fk_codigos_otp_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $db->query($sql);
    }

    private function ensureUserFields()
    {
        try {
            $db = DB::getInstance();
            
            // Verificar si los campos existen
            $columns = $db->query("SHOW COLUMNS FROM usuarios LIKE 'intentos_fallidos'");
            if (!$columns || $columns->rowCount() == 0) {
                echo "  🔧 Añadiendo campos de seguridad a tabla usuarios...\n";
                $db->query("ALTER TABLE `usuarios` ADD COLUMN `intentos_fallidos` int(11) NOT NULL DEFAULT 0");
                $db->query("ALTER TABLE `usuarios` ADD COLUMN `bloqueado_hasta` datetime NULL DEFAULT NULL");
                $db->query("ALTER TABLE `usuarios` ADD INDEX `idx_intentos_fallidos` (`intentos_fallidos`)");
                $db->query("ALTER TABLE `usuarios` ADD INDEX `idx_bloqueado_hasta` (`bloqueado_hasta`)");
                echo "  ✅ Campos añadidos exitosamente\n";
            }
        } catch (\Exception $e) {
            echo "  ⚠️  Error añadiendo campos (pueden ya existir): " . $e->getMessage() . "\n";
        }
    }

    private function tableExists($tableName)
    {
        $db = DB::getInstance();
        $result = $db->query("SHOW TABLES LIKE '{$tableName}'");
        return $result && $result->rowCount() > 0;
    }

    private function findTestUser()
    {
        echo "\n👤 Buscando usuario de prueba...\n";
        
        try {
            $db = DB::getInstance();
            $query = "SELECT * FROM usuarios WHERE email = ?";
            $result = $db->query($query, [$this->testEmail]);
            
            if ($result && $result->rowCount() > 0) {
                $this->testUser = $result->fetch(\PDO::FETCH_ASSOC);
                echo "  ✅ Usuario encontrado: ID {$this->testUser['id']}\n";
                echo "  📝 Nombre: {$this->testUser['nombre']} {$this->testUser['apellido']}\n";
                echo "  📧 Email: {$this->testUser['email']}\n";
                echo "  🟢 Estado: " . ($this->testUser['estado'] ? 'Activo' : 'Inactivo') . "\n";
                
                // Verificar password
                if (password_verify($this->testPassword, $this->testUser['password'])) {
                    echo "  🔑 Password: ✅ Correcto\n";
                    $this->addResult('USER_AUTHENTICATION', true, 'Credenciales válidas');
                } else {
                    echo "  🔑 Password: ❌ Incorrecto\n";
                    $this->addResult('USER_AUTHENTICATION', false, 'Password no coincide');
                }
                
                // Limpiar intentos previos
                $this->resetUserSecurity($this->testUser['id']);
                
            } else {
                throw new \Exception("Usuario con email {$this->testEmail} no encontrado en la base de datos");
            }
            
        } catch (\Exception $e) {
            $this->addResult('USER_SEARCH', false, 'Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function resetUserSecurity($userId)
    {
        try {
            $db = DB::getInstance();
            $db->query("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = ?", [$userId]);
            $db->query("UPDATE codigos_otp SET utilizado = 1 WHERE usuario_id = ?", [$userId]);
            echo "  🧹 Estado de seguridad resetado\n";
        } catch (\Exception $e) {
            echo "  ⚠️  Error reseteando seguridad: " . $e->getMessage() . "\n";
        }
    }

    private function testOTPGeneration()
    {
        echo "\n🔢 Probando generación de códigos OTP...\n";
        
        try {
            $userId = $this->testUser['id'];
            
            // Test 1: Generar código normal
            $result = CodigoOTP::generateOTP($userId);
            
            if ($result['success'] && isset($result['codigo'])) {
                $codigo = $result['codigo'];
                echo "  ✅ Código generado: {$codigo}\n";
                echo "  ⏰ Expira en: {$result['expira_en']}\n";
                echo "  📏 Longitud: " . strlen($codigo) . " dígitos\n";
                echo "  🔢 Es numérico: " . (is_numeric($codigo) ? 'Sí' : 'No') . "\n";
                
                $valid = strlen($codigo) === 6 && is_numeric($codigo);
                $this->addResult('OTP_GENERATION', $valid, $valid ? 'Código OTP generado correctamente' : 'Formato de código incorrecto');
                
                // Guardar para próximos tests
                $this->lastGeneratedCode = $codigo;
                
            } else {
                throw new \Exception($result['error'] ?? 'Error desconocido generando OTP');
            }
            
        } catch (\Exception $e) {
            $this->addResult('OTP_GENERATION', false, 'Error: ' . $e->getMessage());
        }
    }

    private function testEmailSending()
    {
        echo "\n📧 Probando envío de emails...\n";
        
        try {
            // Mostrar configuración SMTP
            echo "  🔧 Configuración SMTP:\n";
            echo "    Host: {$_ENV['MAIL_HOST']}\n";
            echo "    Port: {$_ENV['MAIL_PORT']}\n";
            echo "    Username: {$_ENV['MAIL_USERNAME']}\n";
            echo "    From: {$_ENV['MAIL_FROM_ADDRESS']}\n";
            
            // Crear servicio de email
            $emailService = MailServiceFactory::create();
            
            // Test de conexión
            echo "  🔌 Probando conexión SMTP...\n";
            if (method_exists($emailService, 'testConnection')) {
                $connectionTest = $emailService->testConnection();
                echo "  📡 Conexión SMTP: " . ($connectionTest ? '✅ Exitosa' : '❌ Fallida') . "\n";
            }
            
            // Generar código para el test de email
            $userId = $this->testUser['id'];
            $otpResult = CodigoOTP::generateOTP($userId);
            
            if ($otpResult['success']) {
                $codigo = $otpResult['codigo'];
                echo "  📨 Enviando email con código: {$codigo}\n";
                
                // Enviar email OTP
                $emailSent = $emailService->sendOTPEmail(
                    $this->testEmail,
                    $codigo,
                    $this->testUser['nombre'] . ' ' . $this->testUser['apellido'],
                    1
                );
                
                if ($emailSent) {
                    echo "  ✅ Email enviado exitosamente\n";
                    echo "  📬 Revisa tu bandeja de entrada: {$this->testEmail}\n";
                    $this->addResult('EMAIL_SENDING', true, 'Email OTP enviado correctamente');
                    
                    // Guardar código para validación
                    $this->emailedCode = $codigo;
                } else {
                    throw new \Exception('Falló el envío del email');
                }
            } else {
                throw new \Exception('No se pudo generar código para email: ' . $otpResult['error']);
            }
            
        } catch (\Exception $e) {
            $this->addResult('EMAIL_SENDING', false, 'Error: ' . $e->getMessage());
            echo "  ❌ Error enviando email: " . $e->getMessage() . "\n";
        }
    }

    private function testOTPValidation()
    {
        echo "\n✅ Probando validación de códigos OTP...\n";
        
        try {
            $userId = $this->testUser['id'];
            
            if (isset($this->emailedCode)) {
                $validCode = $this->emailedCode;
                echo "  🔍 Validando código enviado por email: {$validCode}\n";
                
                // Test 1: Código válido
                $validation = CodigoOTP::validateOTP($userId, $validCode);
                if ($validation['success']) {
                    echo "  ✅ Código válido aceptado\n";
                    $test1 = true;
                } else {
                    echo "  ❌ Error validando código: {$validation['error']}\n";
                    $test1 = false;
                }
                
                // Test 2: Reutilización del mismo código (debe fallar)
                echo "  🔄 Probando reutilización del código...\n";
                $reuse = CodigoOTP::validateOTP($userId, $validCode);
                if (!$reuse['success']) {
                    echo "  ✅ Código reutilizado correctamente rechazado\n";
                    $test2 = true;
                } else {
                    echo "  ❌ Error: código reutilizado fue aceptado\n";
                    $test2 = false;
                }
                
                $overallSuccess = $test1 && $test2;
                $this->addResult('OTP_VALIDATION', $overallSuccess, 
                    "Validación: " . ($test1 ? "✅" : "❌") . " | Reutilización: " . ($test2 ? "✅" : "❌"));
                
            } else {
                throw new \Exception('No hay código generado para validar');
            }
            
        } catch (\Exception $e) {
            $this->addResult('OTP_VALIDATION', false, 'Error: ' . $e->getMessage());
        }
    }

    private function testFullFlow()
    {
        echo "\n🔄 Probando flujo completo de 2FA...\n";
        
        try {
            $userId = $this->testUser['id'];
            
            // Paso 1: Simulación de login exitoso
            echo "  1️⃣  Login Paso 1: Verificar credenciales\n";
            $credentials = User::attempt($this->testEmail, $this->testPassword);
            if ($credentials) {
                echo "     ✅ Credenciales verificadas\n";
                $step1 = true;
            } else {
                echo "     ❌ Credenciales inválidas\n";
                $step1 = false;
            }
            
            // Paso 2: Generar OTP
            echo "  2️⃣  Login Paso 2: Generar código OTP\n";
            $otpResult = CodigoOTP::generateOTP($userId);
            if ($otpResult['success']) {
                echo "     ✅ Código OTP generado: {$otpResult['codigo']}\n";
                $step2 = true;
                $finalCode = $otpResult['codigo'];
            } else {
                echo "     ❌ Error generando OTP: {$otpResult['error']}\n";
                $step2 = false;
            }
            
            // Paso 3: Validar OTP
            echo "  3️⃣  Login Paso 3: Validar código OTP\n";
            if ($step2) {
                $validation = CodigoOTP::validateOTP($userId, $finalCode);
                if ($validation['success']) {
                    echo "     ✅ Código OTP validado - Login completado\n";
                    $step3 = true;
                } else {
                    echo "     ❌ Error validando OTP: {$validation['error']}\n";
                    $step3 = false;
                }
            } else {
                $step3 = false;
            }
            
            $fullFlowSuccess = $step1 && $step2 && $step3;
            $message = sprintf('Paso1:%s | Paso2:%s | Paso3:%s', 
                $step1 ? '✅' : '❌',
                $step2 ? '✅' : '❌', 
                $step3 ? '✅' : '❌'
            );
            
            $this->addResult('FULL_2FA_FLOW', $fullFlowSuccess, $message);
            
            if ($fullFlowSuccess) {
                echo "  🎉 ¡FLUJO 2FA COMPLETADO EXITOSAMENTE!\n";
            } else {
                echo "  ❌ Flujo 2FA tiene problemas\n";
            }
            
        } catch (\Exception $e) {
            $this->addResult('FULL_2FA_FLOW', false, 'Error: ' . $e->getMessage());
        }
    }

    private function testCleanup()
    {
        echo "\n🧹 Probando servicio de limpieza...\n";
        
        try {
            $cleanup = OTPCleanupService::runFullCleanup();
            
            if ($cleanup['total_cleaned'] >= 0) {
                echo "  ✅ Limpieza ejecutada: {$cleanup['total_cleaned']} registros eliminados\n";
                echo "  ⏱️  Tiempo ejecución: {$cleanup['execution_time']}ms\n";
                $this->addResult('CLEANUP_SERVICE', true, "Limpieza exitosa: {$cleanup['total_cleaned']} registros");
            } else {
                throw new \Exception('Error en limpieza');
            }
            
        } catch (\Exception $e) {
            $this->addResult('CLEANUP_SERVICE', false, 'Error: ' . $e->getMessage());
        }
    }

    private function addResult($testName, $passed, $message)
    {
        $this->results[] = [
            'test' => $testName,
            'passed' => $passed,
            'message' => $message
        ];
    }

    private function showResults()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🏁 RESULTADOS FINALES DEL TESTING REAL\n";
        echo str_repeat("=", 60) . "\n";
        
        $totalTests = count($this->results);
        $passedTests = array_sum(array_column($this->results, 'passed'));
        $failedTests = $totalTests - $passedTests;
        $successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 1) : 0;
        
        echo "\n📊 ESTADÍSTICAS:\n";
        echo "  🧪 Total de tests: {$totalTests}\n";
        echo "  ✅ Tests exitosos: {$passedTests}\n";
        echo "  ❌ Tests fallidos: {$failedTests}\n";
        echo "  📈 Tasa de éxito: {$successRate}%\n";
        
        echo "\n📋 DETALLE DE RESULTADOS:\n";
        foreach ($this->results as $result) {
            $status = $result['passed'] ? '✅ PASS' : '❌ FAIL';
            echo "  {$status} {$result['test']}: {$result['message']}\n";
        }
        
        echo "\n" . str_repeat("-", 60) . "\n";
        
        if ($successRate >= 90) {
            echo "🎉 ¡EXCELENTE! Sistema 2FA funcionando perfectamente\n";
            echo "✅ Listo para usar en producción\n";
        } elseif ($successRate >= 70) {
            echo "⚠️  BUENO: Funciona con algunos problemas menores\n";
        } else {
            echo "🚨 CRÍTICO: Problemas serios que requieren atención\n";
        }
        
        echo "\n🔐 Testing completado con credenciales reales\n";
        echo "📧 Email: {$this->testEmail}\n";
        echo "📅 " . date('Y-m-d H:i:s') . "\n";
        echo str_repeat("=", 60) . "\n";
    }
}

// Ejecutar el test
echo "⚠️  AVISO: Este script enviará un email real a {$argv[1] ?? 'naxelf666@gmail.com'}\n";
echo "¿Continuar? (y/N): ";

if (php_sapi_name() === 'cli') {
    $handle = fopen("php://stdin", "r");
    $confirm = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($confirm) !== 'y') {
        echo "❌ Test cancelado por el usuario\n";
        exit;
    }
}

$tester = new Real2FATest();
$tester->runRealTests();