<?php
require_once 'bootstrap.php';

use App\Controllers\CursoController;

echo "=== PRUEBA DE CREAR CURSO - CARGA DE DOCENTES ===\n\n";

try {
    // Simular sesión de administrador
    session_start();
    $_SESSION['user_id'] = 1;
    $_SESSION['user_roles'] = ['administrador'];
    
    $controller = new CursoController();
    
    echo "1. Ejecutando método crearCurso()...\n";
    
    // Capturar la salida (ya que no podemos interceptar view() directamente)
    ob_start();
    $resultado = $controller->crearCurso();
    ob_end_clean();
    
    echo "✅ Método crearCurso() ejecutado sin errores\n";
    
    echo "\n2. Probando servicio directamente...\n";
    $cursoService = new App\Services\CursoService();
    
    $docentes = $cursoService->getAllDocentes();
    echo "Docentes obtenidos: " . count($docentes) . "\n";
    
    if (count($docentes) > 0) {
        echo "Lista de docentes para el formulario:\n";
        foreach ($docentes as $docente) {
            $nombre = $docente['nombre'] . ' ' . ($docente['apellido'] ?? '');
            $email = !empty($docente['email']) ? ' (' . $docente['email'] . ')' : '';
            echo "  - ID: {$docente['id']} - {$nombre}{$email}\n";
        }
    }
    
    $categorias = $cursoService->getAllCategoriasCursos();
    echo "\nCategorías obtenidas: " . count($categorias) . "\n";
    
    if (count($categorias) > 0) {
        echo "Lista de categorías para el formulario:\n";
        foreach ($categorias as $categoria) {
            // Verificar si es array o objeto
            if (is_array($categoria)) {
                echo "  - ID: {$categoria['id']} - {$categoria['nombre']}\n";
            } else {
                echo "  - ID: {$categoria->id} - {$categoria->nombre}\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
