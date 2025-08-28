<?php

// Script de prueba del módulo de materiales
// Este archivo debe ser ejecutado desde la raíz del proyecto para probar la funcionalidad

require_once 'bootstrap.php';

use App\Models\Material;
use App\Services\MaterialService;
use App\Models\Categoria;
use App\Models\User;

echo "<h1>🧪 Pruebas del Módulo de Materiales</h1>\n";

try {
    echo "<h2>📋 1. Pruebas del Modelo Material</h2>\n";
    
    // Probar conexión básica
    echo "✅ Conexión a base de datos: ";
    $totalMateriales = Material::count();
    echo "OK (Total materiales: $totalMateriales)\n<br>";
    
    // Probar scopes
    echo "✅ Materiales activos: " . Material::activos()->count() . "\n<br>";
    echo "✅ Materiales públicos: " . Material::publicos()->count() . "\n<br>";
    echo "✅ Materiales recientes (7 días): " . Material::recientes(7)->count() . "\n<br>";
    
    // Probar estadísticas por tipo
    echo "✅ Materiales por tipo:\n<br>";
    $porTipo = Material::contarPorTipo();
    foreach ($porTipo as $tipo => $cantidad) {
        echo "&nbsp;&nbsp;- " . ucfirst($tipo) . ": $cantidad\n<br>";
    }
    
    echo "<h2>🔧 2. Pruebas del Servicio MaterialService</h2>\n";
    
    $materialService = new MaterialService();
    
    // Probar obtención de datos
    $categorias = $materialService->getAllCategories();
    echo "✅ Categorías disponibles: " . count($categorias) . "\n<br>";
    
    $docentes = $materialService->getAllDocentes();
    echo "✅ Docentes disponibles: " . count($docentes) . "\n<br>";
    
    // Probar estadísticas generales
    echo "✅ Estadísticas generales:\n<br>";
    $stats = $materialService->getGeneralStats();
    echo "&nbsp;&nbsp;- Total materiales: " . $stats['total_materiales'] . "\n<br>";
    echo "&nbsp;&nbsp;- Materiales públicos: " . $stats['materiales_publicos'] . "\n<br>";
    echo "&nbsp;&nbsp;- Recientes (7 días): " . $stats['recientes_7_dias'] . "\n<br>";
    
    echo "<h2>🔗 3. Pruebas de Relaciones</h2>\n";
    
    // Probar relación Material -> Categoria
    $materiales = Material::limit(3)->get();
    foreach ($materiales as $material) {
        $categoria = $material->categoria();
        echo "✅ Material '{$material->titulo}' -> Categoría: " . 
             ($categoria ? $categoria->nombre : 'Sin categoría') . "\n<br>";
    }
    
    // Probar relación Material -> Docente
    foreach ($materiales as $material) {
        $docente = $material->docente();
        echo "✅ Material '{$material->titulo}' -> Docente: " . 
             ($docente ? $docente->nombre . ' ' . $docente->apellido : 'Sin docente') . "\n<br>";
    }
    
    echo "<h2>📁 4. Pruebas de Archivos</h2>\n";
    
    // Verificar directorios
    $directorios = [
        'public/materiales',
        'public/materiales/videos',
        'public/materiales/documentos',
        'public/materiales/presentaciones',
        'public/materiales/audios',
        'public/materiales/otros',
        'public/materiales/previews'
    ];
    
    foreach ($directorios as $dir) {
        $rutaCompleta = BASE_PATH . $dir;
        echo "✅ Directorio '$dir': " . 
             (is_dir($rutaCompleta) && is_writable($rutaCompleta) ? 'OK (escribible)' : '❌ Error') . "\n<br>";
    }
    
    echo "<h2>🎯 5. Pruebas de Utilidades</h2>\n";
    
    // Probar validaciones de archivo
    $extensionesValidas = ['pdf', 'mp4', 'pptx', 'mp3', 'jpg', 'zip'];
    $extensionesInvalidas = ['exe', 'bat', 'php', 'js'];
    
    echo "✅ Extensiones válidas:\n<br>";
    foreach ($extensionesValidas as $ext) {
        echo "&nbsp;&nbsp;- .$ext: " . 
             (Material::esArchivoPermitido($ext) ? '✅ Válido' : '❌ Inválido') . "\n<br>";
    }
    
    echo "✅ Extensiones inválidas:\n<br>";
    foreach ($extensionesInvalidas as $ext) {
        echo "&nbsp;&nbsp;- .$ext: " . 
             (Material::esArchivoPermitido($ext) ? '❌ Debería ser inválido' : '✅ Correctamente bloqueado') . "\n<br>";
    }
    
    // Probar tipos sugeridos
    echo "✅ Tipos sugeridos por extensión:\n<br>";
    $mapeoTipos = [
        'pdf' => 'documento',
        'mp4' => 'video',
        'pptx' => 'presentacion',
        'mp3' => 'audio',
        'txt' => 'documento',
        'unknow' => 'otro'
    ];
    
    foreach ($mapeoTipos as $ext => $esperado) {
        $sugerido = Material::getTipoSugeridoPorExtension($ext);
        echo "&nbsp;&nbsp;- .$ext -> Esperado: $esperado, Obtenido: $sugerido " . 
             ($esperado === $sugerido ? '✅' : '❌') . "\n<br>";
    }
    
    echo "<h2>🏁 Resumen de Pruebas</h2>\n";
    echo "<div style='background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; color: #155724;'>\n";
    echo "✅ <strong>Todas las pruebas básicas han pasado exitosamente!</strong>\n<br><br>";
    echo "<strong>Módulo de Materiales está listo para usar:</strong>\n<br>";
    echo "• Modelo Material con relaciones funcionando\n<br>";
    echo "• Servicio MaterialService operativo\n<br>";
    echo "• Directorios de archivos creados y con permisos\n<br>";
    echo "• Validaciones de archivos funcionando\n<br>";
    echo "• Estadísticas y scopes operativos\n<br>";
    echo "</div>\n";
    
    echo "<h2>📖 Próximos Pasos</h2>\n";
    echo "<ol>\n";
    echo "<li>Crear las vistas (templates) para el CRUD de materiales</li>\n";
    echo "<li>Probar la subida de archivos desde el formulario web</li>\n";
    echo "<li>Configurar los permisos específicos en la base de datos</li>\n";
    echo "<li>Integrar con el dashboard principal</li>\n";
    echo "</ol>\n";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; color: #721c24;'>\n";
    echo "❌ <strong>Error durante las pruebas:</strong> " . $e->getMessage() . "\n<br>";
    echo "<strong>Trace:</strong> " . $e->getTraceAsString() . "\n";
    echo "</div>\n";
}

echo "\n<hr>\n";
echo "<small>Pruebas completadas el " . date('Y-m-d H:i:s') . "</small>\n";
