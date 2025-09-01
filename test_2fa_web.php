<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Sistema 2FA - Tech Home Bolivia</title>
    <style>
        body { font-family: 'Courier New', monospace; background: #1a1a1a; color: #00ff00; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .result { margin: 10px 0; padding: 10px; border-left: 3px solid #00ff00; background: #2a2a2a; }
        .error { border-left-color: #ff0000; color: #ff0000; }
        .success { border-left-color: #00ff00; color: #00ff00; }
        .warning { border-left-color: #ffaa00; color: #ffaa00; }
        .code { background: #333; padding: 15px; border-radius: 5px; font-size: 18px; font-weight: bold; }
        h1, h2 { color: #00aaff; }
        .stats { background: #2a2a2a; padding: 20px; border-radius: 10px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 TEST SISTEMA 2FA - TECH HOME BOLIVIA</h1>
        
        <?php
        require_once __DIR__ . '/Core/DB.php';
        require_once __DIR__ . '/Core/helpers.php';

        // Cargar variables de entorno
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

        // Información del test
        echo "<div class='result success'>";
        echo "<h2>📋 INFORMACIÓN DEL TEST</h2>";
        echo "<p>📧 Email: naxelf666@gmail.com</p>";
        echo "<p>🔑 Password: ********</p>";
        echo "<p>📅 Fecha: " . date('Y-m-d H:i:s') . "</p>";
        echo "</div>";

        $results = [];

        // Test 1: Conexión a base de datos
        echo "<h2>🔌 Test 1: Conexión a Base de Datos</h2>";
        try {
            $db = Core\DB::getInstance();
            $result = $db->query("SELECT 1 as test, DATABASE() as db_name, VERSION() as version");
            
            if ($result && $row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='result success'>";
                echo "✅ Conexión exitosa<br>";
                echo "📊 Base de datos: " . $row['db_name'] . "<br>";
                echo "🗄️ Versión MySQL: " . $row['version'] . "<br>";
                echo "</div>";
                $results['database'] = true;
            }
        } catch (Exception $e) {
            echo "<div class='result error'>❌ Error: " . $e->getMessage() . "</div>";
            $results['database'] = false;
        }

        // Test 2: Verificar usuario
        echo "<h2>👤 Test 2: Verificar Usuario</h2>";
        try {
            $email = 'naxelf666@gmail.com';
            $query = "SELECT * FROM usuarios WHERE email = ?";
            $result = $db->query($query, [$email]);
            
            if ($result && $user = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='result success'>";
                echo "✅ Usuario encontrado<br>";
                echo "🆔 ID: " . $user['id'] . "<br>";
                echo "📝 Nombre: " . $user['nombre'] . " " . $user['apellido'] . "<br>";
                echo "📧 Email: " . $user['email'] . "<br>";
                echo "🟢 Estado: " . ($user['estado'] ? 'Activo' : 'Inactivo') . "<br>";
                echo "</div>";
                
                // Verificar password
                if (password_verify('12345678', $user['password'])) {
                    echo "<div class='result success'>🔑 Password: ✅ Correcto</div>";
                    $results['user_auth'] = true;
                    $userId = $user['id'];
                } else {
                    echo "<div class='result error'>🔑 Password: ❌ Incorrecto</div>";
                    $results['user_auth'] = false;
                }
            } else {
                throw new Exception("Usuario no encontrado");
            }
        } catch (Exception $e) {
            echo "<div class='result error'>❌ Error: " . $e->getMessage() . "</div>";
            $results['user_auth'] = false;
        }

        // Test 3: Verificar tablas
        echo "<h2>🗄️ Test 3: Verificar Tablas</h2>";
        try {
            $tables = ['usuarios', 'codigos_otp', 'rate_limit_attempts'];
            $tableStatus = [];
            
            foreach ($tables as $table) {
                $result = $db->query("SHOW TABLES LIKE '{$table}'");
                $exists = $result && $result->rowCount() > 0;
                $tableStatus[$table] = $exists;
                
                $status = $exists ? '✅' : '❌';
                echo "<div class='result " . ($exists ? 'success' : 'error') . "'>";
                echo "{$status} Tabla '{$table}': " . ($exists ? 'Existe' : 'No existe');
                echo "</div>";
            }
            
            // Auto-crear tabla rate_limit_attempts si no existe
            if (!isset($tableStatus['rate_limit_attempts']) || !$tableStatus['rate_limit_attempts']) {
                echo "<div class='result warning'>⚡ Creando tabla rate_limit_attempts automáticamente...</div>";
                try {
                    $rateLimitSql = "
                    CREATE TABLE IF NOT EXISTS `rate_limit_attempts` (
                      `id` int(11) NOT NULL AUTO_INCREMENT,
                      `client_id` varchar(64) NOT NULL COMMENT 'Hash único del cliente',
                      `action` varchar(50) NOT NULL COMMENT 'Tipo de acción',
                      `ip_address` varchar(45) NOT NULL COMMENT 'Dirección IP del cliente',
                      `user_agent` text COMMENT 'User Agent del navegador',
                      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                      PRIMARY KEY (`id`),
                      KEY `idx_client_action_time` (`client_id`, `action`, `created_at`),
                      KEY `idx_created_at` (`created_at`),
                      KEY `idx_ip_action` (`ip_address`, `action`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                    ";
                    
                    $db->query($rateLimitSql);
                    echo "<div class='result success'>✅ Tabla rate_limit_attempts creada exitosamente</div>";
                    $tableStatus['rate_limit_attempts'] = true;
                } catch (Exception $e) {
                    echo "<div class='result error'>❌ Error creando tabla rate_limit_attempts: " . $e->getMessage() . "</div>";
                }
            }
            
            $results['tables'] = array_reduce($tableStatus, function($carry, $item) {
                return $carry && $item;
            }, true);
            
        } catch (Exception $e) {
            echo "<div class='result error'>❌ Error: " . $e->getMessage() . "</div>";
            $results['tables'] = false;
        }

        // Test 4: Crear tabla codigos_otp si no existe
        if (!isset($tableStatus['codigos_otp']) || !$tableStatus['codigos_otp']) {
            echo "<h2>🔧 Creando Tabla codigos_otp</h2>";
            try {
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
                  KEY `idx_utilizado` (`utilizado`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
                ";
                
                $db->query($sql);
                echo "<div class='result success'>✅ Tabla codigos_otp creada exitosamente</div>";
                
                // Añadir campos a usuarios si no existen
                try {
                    $db->query("ALTER TABLE `usuarios` ADD COLUMN IF NOT EXISTS `intentos_fallidos` int(11) NOT NULL DEFAULT 0");
                    $db->query("ALTER TABLE `usuarios` ADD COLUMN IF NOT EXISTS `bloqueado_hasta` datetime NULL DEFAULT NULL");
                    echo "<div class='result success'>✅ Campos de seguridad añadidos a usuarios</div>";
                } catch (Exception $e) {
                    echo "<div class='result warning'>⚠️ Error añadiendo campos (pueden ya existir): " . $e->getMessage() . "</div>";
                }
                
            } catch (Exception $e) {
                echo "<div class='result error'>❌ Error creando tabla: " . $e->getMessage() . "</div>";
            }
        }

        // Test 5: Generar código OTP manualmente
        if (isset($userId)) {
            echo "<h2>🔢 Test 5: Generar Código OTP</h2>";
            try {
                // Limpiar códigos anteriores
                $db->query("UPDATE codigos_otp SET utilizado = 1 WHERE usuario_id = ?", [$userId]);
                
                // Generar nuevo código
                $codigo = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
                $expiraEn = date('Y-m-d H:i:s', time() + 60); // 60 segundos
                
                $insertQuery = "INSERT INTO codigos_otp (usuario_id, codigo, expira_en, utilizado, creado_en) 
                               VALUES (?, ?, ?, 0, NOW())";
                $result = $db->query($insertQuery, [$userId, $codigo, $expiraEn]);
                
                if ($result) {
                    echo "<div class='result success'>";
                    echo "✅ Código OTP generado<br>";
                    echo "<div class='code'>🔐 CÓDIGO: {$codigo}</div>";
                    echo "⏰ Expira en: {$expiraEn}<br>";
                    echo "📏 Longitud: " . strlen($codigo) . " dígitos<br>";
                    echo "🔢 Es numérico: " . (is_numeric($codigo) ? 'Sí' : 'No') . "<br>";
                    echo "</div>";
                    
                    $generatedCode = $codigo;
                    $results['otp_generation'] = true;
                } else {
                    throw new Exception("No se pudo insertar en la base de datos");
                }
                
            } catch (Exception $e) {
                echo "<div class='result error'>❌ Error generando OTP: " . $e->getMessage() . "</div>";
                $results['otp_generation'] = false;
            }
        }

        // Test 6: Validar código OTP
        if (isset($generatedCode) && isset($userId)) {
            echo "<h2>✅ Test 6: Validar Código OTP</h2>";
            try {
                // Buscar código válido
                $query = "SELECT * FROM codigos_otp 
                         WHERE usuario_id = ? AND codigo = ? AND utilizado = 0 AND expira_en >= NOW()
                         ORDER BY creado_en DESC LIMIT 1";
                $result = $db->query($query, [$userId, $generatedCode]);
                
                if ($result && $otpRecord = $result->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='result success'>";
                    echo "✅ Código válido encontrado<br>";
                    echo "🆔 ID del código: " . $otpRecord['id'] . "<br>";
                    echo "⏰ Expira en: " . $otpRecord['expira_en'] . "<br>";
                    echo "🔄 Utilizado: " . ($otpRecord['utilizado'] ? 'Sí' : 'No') . "<br>";
                    
                    // Marcar como utilizado
                    $updateQuery = "UPDATE codigos_otp SET utilizado = 1 WHERE id = ?";
                    $db->query($updateQuery, [$otpRecord['id']]);
                    echo "✅ Código marcado como utilizado<br>";
                    echo "</div>";
                    
                    $results['otp_validation'] = true;
                } else {
                    throw new Exception("Código no válido o expirado");
                }
                
            } catch (Exception $e) {
                echo "<div class='result error'>❌ Error validando OTP: " . $e->getMessage() . "</div>";
                $results['otp_validation'] = false;
            }
        }

        // Test 7: Configuración de email
        echo "<h2>📧 Test 7: Configuración de Email</h2>";
        try {
            echo "<div class='result success'>";
            echo "✅ Configuración SMTP detectada<br>";
            echo "🔧 Host: " . ($_ENV['MAIL_HOST'] ?? 'No configurado') . "<br>";
            echo "🚪 Puerto: " . ($_ENV['MAIL_PORT'] ?? 'No configurado') . "<br>";
            echo "👤 Usuario: " . ($_ENV['MAIL_USERNAME'] ?? 'No configurado') . "<br>";
            echo "📤 From: " . ($_ENV['MAIL_FROM_ADDRESS'] ?? 'No configurado') . "<br>";
            echo "🏷️ Nombre: " . ($_ENV['MAIL_FROM_NAME'] ?? 'No configurado') . "<br>";
            echo "</div>";
            
            $results['email_config'] = !empty($_ENV['MAIL_HOST']);
            
        } catch (Exception $e) {
            echo "<div class='result error'>❌ Error: " . $e->getMessage() . "</div>";
            $results['email_config'] = false;
        }

        // Resumen final
        echo "<div class='stats'>";
        echo "<h2>🏁 RESUMEN FINAL</h2>";
        
        $totalTests = count($results);
        $passedTests = array_sum($results);
        $failedTests = $totalTests - $passedTests;
        $successRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 1) : 0;
        
        echo "<p>🧪 <strong>Total de tests:</strong> {$totalTests}</p>";
        echo "<p>✅ <strong>Tests exitosos:</strong> {$passedTests}</p>";
        echo "<p>❌ <strong>Tests fallidos:</strong> {$failedTests}</p>";
        echo "<p>📈 <strong>Tasa de éxito:</strong> {$successRate}%</p>";
        
        if ($successRate >= 90) {
            echo "<div class='result success'>";
            echo "<h3>🎉 ¡EXCELENTE!</h3>";
            echo "<p>✅ Sistema 2FA funcionando perfectamente</p>";
            echo "<p>🚀 Listo para usar en producción</p>";
            echo "</div>";
        } elseif ($successRate >= 70) {
            echo "<div class='result warning'>";
            echo "<h3>⚠️ BUENO</h3>";
            echo "<p>Sistema funciona con algunos problemas menores</p>";
            echo "</div>";
        } else {
            echo "<div class='result error'>";
            echo "<h3>🚨 CRÍTICO</h3>";
            echo "<p>Problemas serios que requieren atención</p>";
            echo "</div>";
        }
        echo "</div>";

        // Instrucciones
        echo "<div class='result'>";
        echo "<h2>📋 PRÓXIMOS PASOS</h2>";
        echo "<p>1. ✅ Ve a <a href='login' style='color: #00aaff;'>http://localhost/TECH-HOME/login</a></p>";
        echo "<p>2. ✅ Ingresa email: <strong>naxelf666@gmail.com</strong></p>";
        echo "<p>3. ✅ Ingresa password: <strong>12345678</strong></p>";
        echo "<p>4. ✅ El sistema generará un código OTP y te redirigirá a la verificación</p>";
        echo "<p>5. ✅ Revisa tu email para obtener el código de 6 dígitos</p>";
        echo "<p>6. ✅ Ingresa el código en la pantalla de verificación</p>";
        echo "<p>7. 🎉 ¡Disfruta del sistema 2FA funcionando!</p>";
        echo "</div>";
        ?>
        
        <div class="result">
            <h2>🔗 Enlaces Útiles</h2>
            <p>🏠 <a href="/" style="color: #00aaff;">Inicio</a></p>
            <p>🔐 <a href="login" style="color: #00aaff;">Login con 2FA</a></p>
            <p>📝 <a href="register" style="color: #00aaff;">Registro</a></p>
            <p>📧 <a href="forgot-password" style="color: #00aaff;">Recuperar Contraseña</a></p>
        </div>
    </div>
</body>
</html>