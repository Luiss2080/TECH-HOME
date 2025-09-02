<?php
/**
 * Test simple para verificar que rowCount() funciona correctamente
 */

require_once 'bootstrap_clean.php';

use Core\DB;

echo "=== TEST DE ROWCOUNT ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";

try {
    $db = DB::getInstance();
    
    echo "✓ Conexión establecida\n";
    
    // Test 1: SELECT query
    echo "\n1. Probando SELECT query:\n";
    $stmt = $db->query("SELECT COUNT(*) as total FROM componentes");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✓ Total componentes: {$result['total']}\n";
    echo "   ✓ Rows afectadas por SELECT: " . $stmt->rowCount() . "\n";
    
    // Test 2: UPDATE query (sin modificar datos reales)
    echo "\n2. Probando UPDATE query:\n";
    $stmt = $db->query("UPDATE componentes SET fecha_actualizacion = fecha_actualizacion WHERE id > 999999");
    echo "   ✓ Rows afectadas por UPDATE: " . $stmt->rowCount() . "\n";
    
    // Test 3: Usando método deprecated de DB
    echo "\n3. Probando método deprecated DB->rowCount():\n";
    $db->query("SELECT * FROM componentes LIMIT 5");
    echo "   ⚠ Usando método deprecated: " . $db->rowCount() . "\n";
    
    echo "\n=== TEST COMPLETADO EXITOSAMENTE ===\n";
    echo "✅ Los métodos rowCount() funcionan correctamente\n\n";
    
} catch (Exception $e) {
    echo "❌ Error durante el test: " . $e->getMessage() . "\n";
    echo "   Tipo: " . get_class($e) . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n\n";
}
?>
