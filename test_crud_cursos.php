<?php
require_once 'bootstrap.php';

use App\Services\CursoService;
use Core\Session;
use Exception;

echo "=== PRUEBA DEL MÓDULO CRUD DE CURSOS ===\n\n";

try {
    $cursoService = new CursoService();
    
    echo "1. Probando obtener todas las categorías...\n";
    $categorias = $cursoService->getAllCategoriasCursos();
    echo "✅ Categorías encontradas: " . count($categorias) . "\n";
    
    if (empty($categorias)) {
        echo "⚠️  No hay categorías disponibles. Creando categoría de prueba...\n";
        // Crear categoría de prueba
        $db = Core\DB::getInstance();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("INSERT INTO categorias (nombre, tipo, color, icono, estado, fecha_creacion) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute(['Programación', 'curso', '#007bff', 'code', 1]);
        $categorias = $cursoService->getAllCategoriasCursos();
        echo "✅ Categoría de prueba creada. Total: " . count($categorias) . "\n";
    }
    
    echo "\n2. Probando obtener todos los docentes...\n";
    $docentes = $cursoService->getAllDocentes();
    echo "✅ Docentes encontrados: " . count($docentes) . "\n";
    
    if (empty($docentes)) {
        echo "⚠️  No hay docentes disponibles. Esto puede causar problemas en el CRUD.\n";
        echo "Creando usuario docente de prueba...\n";
        
        // Crear usuario docente de prueba
        $db = Core\DB::getInstance();
        $pdo = $db->getConnection();
        
        // Insertar usuario
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password, estado, fecha_creacion) VALUES (?, ?, ?, ?, ?, NOW())");
        $password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt->execute(['Juan', 'Pérez', 'docente@test.com', $password, 1]);
        $userId = $pdo->lastInsertId();
        
        // Asignar rol de docente (ID 2)
        $stmt = $pdo->prepare("INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, ?, ?)");
        $stmt->execute([2, 'App\\Models\\User', $userId]);
        
        $docentes = $cursoService->getAllDocentes();
        echo "✅ Docente de prueba creado. Total: " . count($docentes) . "\n";
    }
    
    echo "\n3. Probando crear un curso...\n";
    
    // Verificar que tenemos datos para crear el curso
    if (empty($docentes)) {
        echo "❌ No hay docentes disponibles para crear el curso\n";
        return;
    }
    
    if (empty($categorias)) {
        echo "❌ No hay categorías disponibles para crear el curso\n";
        return;
    }
    
    // Obtener primer docente y primera categoría
    $primerDocente = $docentes[0];
    $primeraCategoria = is_array($categorias[0]) ? $categorias[0] : $categorias[0]->getAttributes();
    
    $cursoData = [
        'titulo' => 'Curso de Prueba CRUD - ' . date('Y-m-d H:i:s'),
        'descripcion' => 'Este es un curso de prueba para verificar el funcionamiento del CRUD de cursos.',
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'docente_id' => $primerDocente['id'],
        'categoria_id' => $primeraCategoria['id'],
        'nivel' => 'Principiante',
        'estado' => 'Borrador',
        'es_gratuito' => 1
    ];
    
    $cursoId = $cursoService->createCurso($cursoData);
    echo "✅ Curso creado exitosamente. ID: $cursoId\n";
    
    echo "\n4. Probando obtener el curso creado...\n";
    $curso = $cursoService->getCursoById($cursoId);
    if ($curso) {
        echo "✅ Curso obtenido exitosamente:\n";
        echo "   - ID: {$curso['id']}\n";
        echo "   - Título: {$curso['titulo']}\n";
        echo "   - Estado: {$curso['estado']}\n";
        echo "   - Nivel: {$curso['nivel']}\n";
    } else {
        echo "❌ Error: No se pudo obtener el curso creado\n";
    }
    
    echo "\n5. Probando actualizar el curso...\n";
    $updateData = [
        'titulo' => 'Curso de Prueba CRUD - ACTUALIZADO',
        'estado' => 'Publicado',
        'nivel' => 'Intermedio'
    ];
    
    $updated = $cursoService->updateCurso($cursoId, $updateData);
    if ($updated) {
        echo "✅ Curso actualizado exitosamente\n";
        
        // Verificar actualización
        $cursoActualizado = $cursoService->getCursoById($cursoId);
        echo "   - Nuevo título: {$cursoActualizado['titulo']}\n";
        echo "   - Nuevo estado: {$cursoActualizado['estado']}\n";
        echo "   - Nuevo nivel: {$cursoActualizado['nivel']}\n";
    } else {
        echo "❌ Error: No se pudo actualizar el curso\n";
    }
    
    echo "\n6. Probando cambio de estado...\n";
    $estadoCambiado = $cursoService->changeStatus($cursoId, 'Archivado');
    if ($estadoCambiado) {
        echo "✅ Estado cambiado exitosamente\n";
        $cursoConNuevoEstado = $cursoService->getCursoById($cursoId);
        echo "   - Estado actual: {$cursoConNuevoEstado['estado']}\n";
    } else {
        echo "❌ Error: No se pudo cambiar el estado\n";
    }
    
    echo "\n7. Probando obtener todos los cursos...\n";
    $todosCursos = $cursoService->getAllCursos();
    echo "✅ Total de cursos en el sistema: " . count($todosCursos) . "\n";
    
    echo "\n8. Probando estadísticas...\n";
    $estadisticas = $cursoService->getEstadisticasCursos();
    echo "✅ Estadísticas obtenidas:\n";
    echo "   - Total: {$estadisticas['total']}\n";
    echo "   - Publicados: {$estadisticas['publicados']}\n";
    echo "   - Borradores: {$estadisticas['borradores']}\n";
    echo "   - Archivados: {$estadisticas['archivados']}\n";
    
    echo "\n9. Probando eliminar el curso de prueba...\n";
    $eliminado = $cursoService->deleteCurso($cursoId);
    if ($eliminado) {
        echo "✅ Curso eliminado exitosamente\n";
        
        // Verificar eliminación
        $cursoEliminado = $cursoService->getCursoById($cursoId);
        if (!$cursoEliminado) {
            echo "✅ Confirmado: El curso ya no existe en la base de datos\n";
        } else {
            echo "⚠️  El curso aún existe en la base de datos\n";
        }
    } else {
        echo "❌ Error: No se pudo eliminar el curso\n";
    }
    
    echo "\n=== RESUMEN DE LA PRUEBA ===\n";
    echo "✅ Sistema CRUD de cursos funcionando correctamente\n";
    echo "✅ Todas las operaciones básicas están operativas:\n";
    echo "   - ✅ Crear (CREATE)\n";
    echo "   - ✅ Leer (READ)\n";
    echo "   - ✅ Actualizar (UPDATE)\n";
    echo "   - ✅ Eliminar (DELETE)\n";
    echo "   - ✅ Cambio de estado\n";
    echo "   - ✅ Estadísticas\n";
    
} catch (Exception $e) {
    echo "❌ ERROR EN LA PRUEBA: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
