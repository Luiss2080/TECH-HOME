<?php
require_once 'bootstrap.php';

// Cargar variables de entorno
$_ENV = loadEnv(BASE_PATH . '.env');

use Core\DB;

// Obtener ID del usuario
$userId = $argv[1] ?? 7;

echo "🔍 Verificando restricciones para usuario ID: $userId\n\n";

try {
    $db = DB::getInstance();
    
    // Verificar ventas
    echo "📊 Verificando tabla VENTAS...\n";
    $ventas = $db->query("SELECT * FROM ventas WHERE vendedor_id = ?", [$userId])->fetchAll();
    if (!empty($ventas)) {
        echo "❌ El usuario tiene " . count($ventas) . " ventas asociadas:\n";
        foreach ($ventas as $venta) {
            echo "   - Venta ID: {$venta->id}, Total: {$venta->total}, Fecha: {$venta->fecha_venta}\n";
        }
    } else {
        echo "✅ Sin ventas asociadas\n";
    }
    
    echo "\n";
    
    // Buscar otras posibles referencias
    echo "🔍 Buscando otras referencias en la base de datos...\n";
    
    // Obtener todas las tablas
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        // Obtener columnas de cada tabla
        $columns = $db->query("SHOW COLUMNS FROM `$table`")->fetchAll();
        
        foreach ($columns as $column) {
            // Buscar columnas que puedan referenciar usuarios
            if (stripos($column->Field, 'user') !== false || 
                stripos($column->Field, 'usuario') !== false ||
                $column->Field === 'vendedor_id' ||
                $column->Field === 'comprador_id' ||
                $column->Field === 'creado_por') {
                
                try {
                    $count = $db->query("SELECT COUNT(*) as count FROM `$table` WHERE `{$column->Field}` = ?", [$userId])->fetch();
                    if ($count->count > 0) {
                        echo "❌ Tabla '$table', columna '{$column->Field}': {$count->count} registros\n";
                    }
                } catch (Exception $e) {
                    // Ignorar errores de columnas que no son del tipo correcto
                }
            }
        }
    }
    
    echo "\n✅ Verificación completada\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
