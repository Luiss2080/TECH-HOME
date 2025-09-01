<?php
require_once __DIR__ . '/Core/DB.php';

try {
    $db = Core\DB::getInstance();
    
    echo "🔧 Creando tabla rate_limit_attempts...\n";
    
    $sql = "CREATE TABLE IF NOT EXISTS `rate_limit_attempts` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $db->query($sql);
    echo "✅ Tabla rate_limit_attempts creada exitosamente!\n";
    
    // Verificar que la tabla existe
    $result = $db->query("SHOW TABLES LIKE 'rate_limit_attempts'");
    if ($result && $result->rowCount() > 0) {
        echo "✅ Verificación: Tabla existe en la base de datos\n";
    } else {
        echo "❌ Error: Tabla no fue creada\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>