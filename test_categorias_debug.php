<?php
require_once 'bootstrap.php';

try {
    // Primero vamos a ver todas las categorías en la base de datos
    echo "=== VERIFICANDO CATEGORÍAS EN LA BASE DE DATOS ===" . PHP_EOL;
    
    $db = \Core\DB::getInstance();
    
    // Todas las categorías
    $todasCategorias = $db->query('SELECT * FROM categorias')->fetchAll();
    echo 'Total categorías en DB: ' . count($todasCategorias) . PHP_EOL;
    
    foreach($todasCategorias as $cat) {
        echo "- ID: {$cat->id}, Nombre: {$cat->nombre}, Estado: {$cat->estado}, Tipo: {$cat->tipo}" . PHP_EOL;
    }
    
    echo PHP_EOL . "=== CATEGORÍAS ACTIVAS (estado = 1) ===" . PHP_EOL;
    
    // Categorías activas
    $categoriasActivas = $db->query('SELECT * FROM categorias WHERE estado = 1')->fetchAll();
    echo 'Categorías activas: ' . count($categoriasActivas) . PHP_EOL;
    
    foreach($categoriasActivas as $cat) {
        echo "- ID: {$cat->id}, Nombre: {$cat->nombre}, Tipo: {$cat->tipo}" . PHP_EOL;
    }
    
    echo PHP_EOL . "=== USANDO EL MODELO ===" . PHP_EOL;
    
    // Usando el modelo
    $modelCategorias = \App\Models\Categoria::where('estado', '=', 1)->get();
    echo 'Categorías del modelo: ' . count($modelCategorias) . PHP_EOL;
    var_dump($modelCategorias);
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    echo 'Trace: ' . $e->getTraceAsString() . PHP_EOL;
}
