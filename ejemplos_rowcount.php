<?php
/**
 * Ejemplos de uso correcto de rowCount() 
 * Mejores prácticas para evitar warnings de deprecated
 */

require_once 'bootstrap.php';

use Core\DB;

echo "=== MEJORES PRÁCTICAS PARA ROWCOUNT ===\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n\n";

try {
    $db = DB::getInstance();
    
    echo "✅ EJEMPLOS DE USO CORRECTO:\n\n";
    
    // ✅ Ejemplo 1: DELETE con rowCount
    echo "1. DELETE con conteo de filas afectadas:\n";
    $stmt = $db->query("DELETE FROM rate_limit_attempts WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)");
    $deletedRows = $stmt->rowCount();
    echo "   ✓ Filas eliminadas: $deletedRows\n";
    echo "   📝 Código: \$stmt = \$db->query(...); \$count = \$stmt->rowCount();\n\n";
    
    // ✅ Ejemplo 2: UPDATE con conteo de filas afectadas
    echo "2. UPDATE con conteo de filas modificadas:\n";
    $stmt = $db->query("UPDATE componentes SET fecha_actualizacion = NOW() WHERE id = ?", [999999]);
    $updatedRows = $stmt->rowCount();
    echo "   ✓ Filas actualizadas: $updatedRows\n";
    echo "   📝 Código: \$stmt = \$db->query('UPDATE...', \$params); \$count = \$stmt->rowCount();\n\n";
    
    // ✅ Ejemplo 3: INSERT con verificación
    echo "3. Verificar si INSERT fue exitoso:\n";
    $stmt = $db->query("INSERT INTO movimientos_stock 
        (componente_id, tipo_movimiento, cantidad, stock_anterior, stock_nuevo, motivo) 
        VALUES (1, 'entrada', 10, 0, 10, 'Test de inserción')");
    $insertedRows = $stmt->rowCount();
    echo "   ✓ Filas insertadas: $insertedRows\n";
    if ($insertedRows > 0) {
        echo "   ✓ INSERT exitoso - ID: " . $db->getConnection()->lastInsertId() . "\n";
        
        // Limpiar el test
        $cleanStmt = $db->query("DELETE FROM movimientos_stock WHERE motivo = 'Test de inserción'");
        echo "   🧹 Limpieza: " . $cleanStmt->rowCount() . " filas eliminadas\n";
    }
    echo "   📝 Código: \$stmt = \$db->query('INSERT...'); if(\$stmt->rowCount() > 0) {...}\n\n";
    
    // ✅ Ejemplo 4: Contar registros con SELECT COUNT()
    echo "4. Contar registros (forma correcta):\n";
    $stmt = $db->query("SELECT COUNT(*) as total FROM componentes WHERE estado = 'Disponible'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   ✓ Componentes disponibles: {$result['total']}\n";
    echo "   📝 Código: \$stmt = \$db->query('SELECT COUNT(*)...'); \$total = \$stmt->fetch()['total'];\n\n";
    
    // ⚠️ Ejemplo de lo que NO hacer
    echo "❌ EJEMPLOS DE USO INCORRECTO (EVITAR):\n\n";
    
    echo "5. NO usar rowCount() para contar resultados de SELECT:\n";
    $stmt = $db->query("SELECT * FROM componentes LIMIT 10");
    $selectRowCount = $stmt->rowCount();
    echo "   ⚠️ SELECT rowCount(): $selectRowCount (puede ser impreciso)\n";
    echo "   📝 Problema: En SELECT, rowCount() puede no reflejar el total real\n";
    echo "   ✅ Mejor usar: SELECT COUNT(*) as total FROM tabla\n\n";
    
    // 📚 Resumen de mejores prácticas
    echo "📚 RESUMEN DE MEJORES PRÁCTICAS:\n\n";
    echo "✅ Para DELETE/UPDATE/INSERT:\n";
    echo "   \$stmt = \$db->query(\$sql, \$params);\n";
    echo "   \$affectedRows = \$stmt->rowCount();\n\n";
    
    echo "✅ Para contar registros:\n";
    echo "   \$stmt = \$db->query('SELECT COUNT(*) as total FROM tabla');\n";
    echo "   \$total = \$stmt->fetch()['total'];\n\n";
    
    echo "❌ NUNCA usar:\n";
    echo "   \$db->rowCount() // Método deprecated\n";
    echo "   \$pdo->rowCount() // No existe\n\n";
    
    echo "=== GUÍA COMPLETADA ===\n";
    echo "🎯 Sigue estos patrones para evitar warnings y errores\n";
    
} catch (Exception $e) {
    echo "❌ Error durante los ejemplos: " . $e->getMessage() . "\n";
    echo "   Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
