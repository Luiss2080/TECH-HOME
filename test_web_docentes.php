<?php
require_once 'bootstrap.php';

echo "<html><head><title>Test Crear Curso</title></head><body>";
echo "<h1>Prueba de Formulario Crear Curso</h1>";

try {
    // Simular sesión de administrador (como en tu captura de pantalla)
    $_SESSION = [];
    $_SESSION['user_id'] = 1; 
    $_SESSION['user_roles'] = ['administrador'];
    
    $controller = new App\Controllers\CursoController();
    $cursoService = new App\Services\CursoService();
    
    echo "<h2>1. Datos del backend:</h2>";
    
    $docentes = $cursoService->getAllDocentes();
    echo "<p><strong>Docentes disponibles:</strong> " . count($docentes) . "</p>";
    
    if (count($docentes) > 0) {
        echo "<ul>";
        foreach ($docentes as $docente) {
            echo "<li>ID: {$docente['id']} - {$docente['nombre']} {$docente['apellido']} ({$docente['email']})</li>";
        }
        echo "</ul>";
    }
    
    $categorias = $cursoService->getAllCategoriasCursos();
    echo "<p><strong>Categorías disponibles:</strong> " . count($categorias) . "</p>";
    
    echo "<h2>2. Simulación del select de docentes:</h2>";
    
    // Simular las variables que llegan a la vista
    $user = auth();
    $isDocente = $user && $user->hasRole('docente') && !$user->hasRole('administrador');
    $old = [];
    
    echo "<p><strong>Usuario actual:</strong> " . ($user ? $user->nombre . ' (' . implode(', ', $_SESSION['user_roles']) . ')' : 'No autenticado') . "</p>";
    echo "<p><strong>Es docente (no admin):</strong> " . ($isDocente ? 'SÍ' : 'NO') . "</p>";
    
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
    echo "<h3>Select generado:</h3>";
    echo '<select name="docente_id" style="width: 300px; padding: 5px;">';
    
    if ($isDocente) {
        echo '<option value="' . $user->id . '" selected>';
        echo htmlspecialchars($user->nombre . ' ' . ($user->apellido ?? '')) . ' (Tú)';
        echo '</option>';
    } else {
        echo '<option value="">Selecciona un docente</option>';
        if (isset($docentes) && !empty($docentes)) {
            foreach ($docentes as $docente) {
                echo '<option value="' . $docente['id'] . '"';
                echo ($old['docente_id'] ?? '') == $docente['id'] ? ' selected' : '';
                echo '>';
                echo htmlspecialchars($docente['nombre'] . ' ' . ($docente['apellido'] ?? ''));
                echo !empty($docente['email']) ? ' (' . $docente['email'] . ')' : '';
                echo '</option>';
            }
        } else {
            echo '<option value="">No hay docentes disponibles</option>';
        }
    }
    echo '</select>';
    echo "</div>";
    
    echo "<h2>3. Diagnóstico:</h2>";
    if (count($docentes) == 0) {
        echo "<p style='color: red;'>❌ <strong>PROBLEMA:</strong> No se encontraron docentes en la base de datos.</p>";
    } elseif ($isDocente) {
        echo "<p style='color: green;'>✅ Como docente, el select debe mostrar solo tu usuario.</p>";
    } else {
        echo "<p style='color: green;'>✅ Como administrador, el select debe mostrar todos los docentes (" . count($docentes) . " disponibles).</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Archivo:</strong> " . $e->getFile() . ":" . $e->getLine() . "</p>";
}

echo "</body></html>";
?>
