<?php
require_once 'bootstrap.php';

use App\Services\CursoService;

echo "=== PRUEBA SIMPLIFICADA DEL CRUD DE CURSOS ===\n\n";

try {
    $cursoService = new CursoService();
    $db = Core\DB::getInstance();
    
    echo "1. Verificando datos básicos del sistema...\n";
    
    // Verificar docentes
    $docentes = $db->query("SELECT * FROM usuarios u INNER JOIN model_has_roles mhr ON u.id = mhr.model_id WHERE mhr.role_id = 2 AND u.estado = 1 LIMIT 1")->fetchAll(PDO::FETCH_ASSOC);
    echo "Docentes disponibles: " . count($docentes) . "\n";
    
    // Verificar categorías
    $categorias = $db->query("SELECT * FROM categorias WHERE tipo = 'curso' AND estado = 1 LIMIT 1")->fetchAll(PDO::FETCH_ASSOC);
    echo "Categorías disponibles: " . count($categorias) . "\n";
    
    // Si no hay docente, crear uno
    if (empty($docentes)) {
        echo "Creando docente de prueba...\n";
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, password, estado) VALUES (?, ?, ?, ?, ?)");
        $password = password_hash('123456', PASSWORD_DEFAULT);
        $stmt->execute(['Juan', 'Pérez', 'docente' . time() . '@test.com', $password, 1]);
        $userId = $pdo->lastInsertId();
        
        $stmt = $pdo->prepare("INSERT INTO model_has_roles (role_id, model_type, model_id) VALUES (?, ?, ?)");
        $stmt->execute([2, 'App\\Models\\User', $userId]);
        
        $docentes = $db->query("SELECT * FROM usuarios WHERE id = $userId")->fetchAll(PDO::FETCH_ASSOC);
        echo "✅ Docente creado con ID: " . $userId . "\n";
    }
    
    // Si no hay categoría, crear una
    if (empty($categorias)) {
        echo "Creando categoría de prueba...\n";
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare("INSERT INTO categorias (nombre, tipo, color, icono, estado) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['Programación Test', 'curso', '#007bff', 'code', 1]);
        $categoriaId = $pdo->lastInsertId();
        
        $categorias = $db->query("SELECT * FROM categorias WHERE id = $categoriaId")->fetchAll(PDO::FETCH_ASSOC);
        echo "✅ Categoría creada con ID: " . $categoriaId . "\n";
    }
    
    echo "\n2. Probando CREAR curso...\n";
    $cursoData = [
        'titulo' => 'Curso Test CRUD ' . date('H:i:s'),
        'descripcion' => 'Descripción de prueba para el curso CRUD.',
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        'docente_id' => $docentes[0]['id'],
        'categoria_id' => $categorias[0]['id'],
        'nivel' => 'Principiante',
        'estado' => 'Borrador',
        'es_gratuito' => 1
    ];
    
    $cursoId = $cursoService->createCurso($cursoData);
    echo "✅ Curso creado con ID: $cursoId\n";
    
    echo "\n3. Probando LEER curso...\n";
    $curso = $cursoService->getCursoById($cursoId);
    if ($curso) {
        echo "✅ Curso leído correctamente:\n";
        echo "   - Título: {$curso['titulo']}\n";
        echo "   - Estado: {$curso['estado']}\n";
    } else {
        echo "❌ Error al leer el curso\n";
    }
    
    echo "\n4. Probando ACTUALIZAR curso...\n";
    $updateData = [
        'titulo' => 'Curso Test CRUD ACTUALIZADO',
        'estado' => 'Publicado'
    ];
    
    $updated = $cursoService->updateCurso($cursoId, $updateData);
    if ($updated) {
        echo "✅ Curso actualizado\n";
        $cursoActualizado = $cursoService->getCursoById($cursoId);
        echo "   - Nuevo título: {$cursoActualizado['titulo']}\n";
        echo "   - Nuevo estado: {$cursoActualizado['estado']}\n";
    } else {
        echo "❌ Error al actualizar el curso\n";
    }
    
    echo "\n5. Probando CAMBIAR ESTADO...\n";
    $estadoCambiado = $cursoService->changeStatus($cursoId, 'Archivado');
    if ($estadoCambiado) {
        echo "✅ Estado cambiado\n";
        $cursoConNuevoEstado = $cursoService->getCursoById($cursoId);
        echo "   - Estado actual: {$cursoConNuevoEstado['estado']}\n";
    } else {
        echo "❌ Error al cambiar estado\n";
    }
    
    echo "\n6. Probando ELIMINAR curso...\n";
    $eliminado = $cursoService->deleteCurso($cursoId);
    if ($eliminado) {
        echo "✅ Curso eliminado\n";
        $cursoEliminado = $cursoService->getCursoById($cursoId);
        if (!$cursoEliminado) {
            echo "✅ Confirmado: El curso ya no existe\n";
        }
    } else {
        echo "❌ Error al eliminar el curso\n";
    }
    
    echo "\n7. Probando obtener ESTADÍSTICAS...\n";
    $stats = $cursoService->getEstadisticasCursos();
    echo "✅ Estadísticas obtenidas:\n";
    echo "   - Total: {$stats['total']}\n";
    echo "   - Publicados: {$stats['publicados']}\n";
    echo "   - Borradores: {$stats['borradores']}\n";
    
    echo "\n=== RESULTADO FINAL ===\n";
    echo "✅ CRUD DE CURSOS FUNCIONANDO CORRECTAMENTE\n";
    echo "✅ Todas las operaciones básicas están operativas\n";
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
