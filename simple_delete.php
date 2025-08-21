<?php
require_once 'bootstrap.php';

// Cargar variables de entorno
$_ENV = loadEnv(BASE_PATH . '.env');

use App\Services\AdminService;

// Obtener ID del usuario
$userId = $argv[1] ?? null;

if (!$userId || !is_numeric($userId)) {
    echo "❌ ERROR: Debes proporcionar un ID de usuario válido\n";
    echo "💡 Uso: php simple_delete.php <ID_USUARIO>\n";
    exit(1);
}

echo "🚀 Eliminando usuario ID: $userId\n";

try {
    $adminService = new AdminService();
    
    // Verificar que existe
    $usuario = $adminService->getUserById($userId);
    if (!$usuario) {
        echo "❌ Usuario no encontrado\n";
        exit(1);
    }
    
    echo "👤 Usuario: {$usuario->nombre} ({$usuario->email})\n";
    
    // Eliminar
    $resultado = $adminService->deleteUser($userId);
    
    if ($resultado) {
        echo "✅ Usuario eliminado exitosamente\n";
    } else {
        echo "❌ Error al eliminar usuario\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}

echo "🏁 Proceso completado\n";
