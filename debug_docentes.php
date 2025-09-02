<?php
require_once 'bootstrap.php';

use App\Services\CursoService;

echo "=== PRUEBA DE OBTENCIÓN DE DOCENTES ===\n\n";

try {
    $cursoService = new CursoService();
    
    echo "1. Obteniendo lista de docentes...\n";
    $docentes = $cursoService->getAllDocentes();
    
    echo "Total de docentes encontrados: " . count($docentes) . "\n\n";
    
    if (count($docentes) > 0) {
        echo "Lista de docentes:\n";
        foreach ($docentes as $docente) {
            echo "- ID: {$docente['id']}, Nombre: {$docente['nombre']} {$docente['apellido']}, Email: {$docente['email']}\n";
        }
    } else {
        echo "⚠️ No se encontraron docentes. Verificando usuarios con rol docente...\n\n";
        
        // Verificar directamente en la base de datos
        $db = Core\DB::getInstance();
        
        echo "2. Verificando roles disponibles:\n";
        $roles = $db->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);
        echo "Estructura de tabla roles:\n";
        $estructura = $db->query("DESCRIBE roles")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($estructura as $col) {
            echo "  - {$col['Field']} ({$col['Type']})\n";
        }
        echo "\nRoles encontrados:\n";
        foreach ($roles as $rol) {
            print_r($rol);
        }
        
        echo "\n3. Verificando usuarios con roles asignados:\n";
        $usuariosConRoles = $db->query("
            SELECT u.id, u.nombre, u.apellido, r.nombre as rol_nombre, mhr.role_id 
            FROM usuarios u 
            INNER JOIN model_has_roles mhr ON u.id = mhr.model_id 
            INNER JOIN roles r ON mhr.role_id = r.id 
            WHERE u.estado = 1
            ORDER BY u.nombre"
        )->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($usuariosConRoles as $usuario) {
            echo "- {$usuario['nombre']} {$usuario['apellido']} - Rol: {$usuario['rol_nombre']} (ID: {$usuario['role_id']})\n";
        }
        
        echo "\n4. Verificando específicamente usuarios con rol docente:\n";
        $docentes = $db->query("
            SELECT u.id, u.nombre, u.apellido, u.email 
            FROM usuarios u 
            INNER JOIN model_has_roles mhr ON u.id = mhr.model_id 
            WHERE mhr.role_id = 2 
            AND u.estado = 1
            ORDER BY u.nombre, u.apellido"
        )->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($docentes as $docente) {
            echo "- {$docente['nombre']} {$docente['apellido']} ({$docente['email']})\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
?>
