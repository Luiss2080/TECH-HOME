<?php

require_once __DIR__ . '/bootstrap.php';

use Core\DB;

try {
    echo "<h2>🔧 Reparando Sistema de Bloqueo de Login</h2>";
    
    $db = DB::getInstance();
    
    // 1. Verificar si las columnas existen
    echo "<h3>1. Verificando estructura de tabla usuarios...</h3>";
    
    $columns = $db->query("SHOW COLUMNS FROM usuarios");
    $columnNames = [];
    while ($col = $columns->fetch(PDO::FETCH_ASSOC)) {
        $columnNames[] = $col['Field'];
    }
    
    $hasIntentosFallidos = in_array('intentos_fallidos', $columnNames);
    $hasBloqueadoHasta = in_array('bloqueado_hasta', $columnNames);
    
    echo "- intentos_fallidos: " . ($hasIntentosFallidos ? "✅ Existe" : "❌ No existe") . "<br>";
    echo "- bloqueado_hasta: " . ($hasBloqueadoHasta ? "✅ Existe" : "❌ No existe") . "<br>";
    
    // 2. Agregar columnas si no existen
    if (!$hasIntentosFallidos || !$hasBloqueadoHasta) {
        echo "<h3>2. Agregando columnas faltantes...</h3>";
        
        if (!$hasIntentosFallidos) {
            $db->query("ALTER TABLE `usuarios` ADD COLUMN `intentos_fallidos` int(11) NOT NULL DEFAULT 0");
            echo "✅ Agregada columna intentos_fallidos<br>";
        }
        
        if (!$hasBloqueadoHasta) {
            $db->query("ALTER TABLE `usuarios` ADD COLUMN `bloqueado_hasta` datetime NULL DEFAULT NULL");
            echo "✅ Agregada columna bloqueado_hasta<br>";
        }
        
        // Agregar índices
        try {
            $db->query("ALTER TABLE `usuarios` ADD INDEX `idx_intentos_fallidos` (`intentos_fallidos`)");
        } catch (Exception $e) {
            // Ya existe
        }
        
        try {
            $db->query("ALTER TABLE `usuarios` ADD INDEX `idx_bloqueado_hasta` (`bloqueado_hasta`)");
        } catch (Exception $e) {
            // Ya existe
        }
        
        echo "✅ Índices agregados<br>";
    }
    
    // 3. Verificar tabla rate_limit_attempts
    echo "<h3>3. Verificando tabla rate_limit_attempts...</h3>";
    
    $tableExists = $db->query("SHOW TABLES LIKE 'rate_limit_attempts'")->fetch();
    if (!$tableExists) {
        echo "❌ Tabla rate_limit_attempts no existe. Creando...<br>";
        
        $rateLimitSql = "
        CREATE TABLE `rate_limit_attempts` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `client_id` varchar(255) NOT NULL,
          `action` varchar(50) NOT NULL,
          `ip_address` varchar(45) NOT NULL,
          `user_agent` text,
          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`),
          KEY `idx_client_action` (`client_id`, `action`),
          KEY `idx_created_at` (`created_at`),
          KEY `idx_ip_address` (`ip_address`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $db->query($rateLimitSql);
        echo "✅ Tabla rate_limit_attempts creada<br>";
    } else {
        echo "✅ Tabla rate_limit_attempts existe<br>";
    }
    
    // 4. Test de funcionalidad
    echo "<h3>4. Testeando funcionalidad...</h3>";
    
    // Crear usuario de prueba si no existe
    $testUser = $db->query("SELECT * FROM usuarios WHERE email = 'test@techhome.bo'")->fetch();
    if (!$testUser) {
        $db->query("INSERT INTO usuarios (nombre, apellido, email, password, estado, fecha_creacion, fecha_actualizacion) VALUES (?, ?, ?, ?, ?, NOW(), NOW())", [
            'Test', 'User', 'test@techhome.bo', password_hash('test123', PASSWORD_DEFAULT), 1
        ]);
        echo "✅ Usuario de prueba creado: test@techhome.bo / test123<br>";
    }
    
    // Reset intentos del usuario de prueba
    $db->query("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE email = 'test@techhome.bo'");
    echo "✅ Reset intentos del usuario de prueba<br>";
    
    echo "<h3>🎉 Reparación completada!</h3>";
    echo "<p><strong>Ahora puedes probar:</strong></p>";
    echo "<ul>";
    echo "<li>Ir a <a href='login'>Login</a></li>";
    echo "<li>Usar email: test@techhome.bo</li>";
    echo "<li>Contraseña incorrecta 3 veces seguidas</li>";
    echo "<li>Debería mostrarse el mensaje de bloqueo</li>";
    echo "</ul>";
    
    // Mostrar algunos usuarios para debug
    echo "<h3>Debug - Usuarios en la tabla:</h3>";
    $users = $db->query("SELECT id, nombre, email, intentos_fallidos, bloqueado_hasta FROM usuarios LIMIT 5");
    echo "<table border='1' style='border-collapse:collapse;'>";
    echo "<tr><th>ID</th><th>Nombre</th><th>Email</th><th>Intentos</th><th>Bloqueado hasta</th></tr>";
    while ($user = $users->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['nombre']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['intentos_fallidos']}</td>";
        echo "<td>{$user['bloqueado_hasta']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

?>