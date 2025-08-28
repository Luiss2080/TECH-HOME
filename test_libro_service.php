<?php
/**
 * Script de prueba para verificar el fix del LibroService
 */

require_once 'bootstrap.php';

try {
    echo "Probando LibroService::getCategorias()...\n";
    
    $libroService = new App\Services\LibroService();
    $categorias = $libroService->getCategorias();
    
    echo "✅ Método ejecutado exitosamente\n";
    echo "📊 Número de categorías obtenidas: " . count($categorias) . "\n";
    
    if (!empty($categorias)) {
        echo "📝 Ejemplo de categoría:\n";
        print_r($categorias[0]);
    }
    
    echo "\n🎉 Test completado con éxito!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Archivo: " . $e->getFile() . "\n";
    echo "📍 Línea: " . $e->getLine() . "\n";
}
?>
