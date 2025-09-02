
<?php
// maintenance/security_cleanup.php
require_once __DIR__ . "/../bootstrap.php";

// Limpiar logs antiguos
$db = Core\DB::getInstance();

echo "Ejecutando limpieza de seguridad...\n";

// Limpiar security_log
$result = $db->query("DELETE FROM security_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)");
echo "Security logs limpiados: " . $result->rowCount() . "\n";

// Limpiar audit_log  
$result = $db->query("DELETE FROM audit_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 365 DAY)");
echo "Audit logs limpiados: " . $result->rowCount() . "\n";

// Limpiar rate_limit_attempts
$result = $db->query("DELETE FROM rate_limit_attempts WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)");
echo "Rate limit logs limpiados: " . $result->rowCount() . "\n";

echo "Limpieza completada.\n";
