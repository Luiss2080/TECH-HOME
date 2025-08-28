<?php
require_once 'bootstrap.php';

try {
    $service = new \App\Services\LibroService();
    $categorias = $service->getCategorias();
    
    echo 'Categorías obtenidas: ' . count($categorias) . PHP_EOL;
    
    foreach($categorias as $cat) {
        if (is_array($cat)) {
            echo '- ' . ($cat['nombre'] ?? 'Sin nombre') . ' (Array)' . PHP_EOL;
        } else {
            echo '- ' . (is_object($cat) ? get_class($cat) : 'Unknown type') . PHP_EOL;
        }
    }
    
    echo 'Test completado exitosamente.' . PHP_EOL;
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    echo 'Trace: ' . $e->getTraceAsString() . PHP_EOL;
}
