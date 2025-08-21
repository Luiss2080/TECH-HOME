<?php
/**
 * Script de prueba para eliminar usuario directamente usando AdminService
 * Uso: php check_service_delete_user.php [ID_USUARIO]
 * Ejemplo: php check_service_delete_user.php 7
 */

// Cargar el sistema
require_once __DIR__ . '/bootstrap.php';
$_ENV = loadEnv(BASE_PATH . '.env');

use App\Services\AdminService;
use App\Models\User;
use Exception;

// Función para mostrar información del usuario
function mostrarInfoUsuario($id) {
    echo "=== INFORMACIÓN DEL USUARIO ID: $id ===\n";
    
    $user = User::find($id);
    if (!$user) {
        echo "❌ Usuario con ID $id NO ENCONTRADO\n";
        return null;
    }
    
    echo "✅ Usuario encontrado:\n";
    echo "   - ID: {$user->id}\n";
    echo "   - Nombre: {$user->nombre} {$user->apellido}\n";
    echo "   - Email: {$user->email}\n";
    echo "   - Estado: " . ($user->estado == 1 ? 'Activo' : 'Inactivo') . "\n";
    
    // Mostrar roles
    try {
        $roles = $user->roles();
        if (!empty($roles)) {
            echo "   - Roles: " . implode(', ', array_column($roles, 'nombre')) . "\n";
        } else {
            echo "   - Roles: Sin roles asignados\n";
        }
    } catch (Exception $e) {
        echo "   - Roles: Error al obtener roles - " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    return $user;
}

// Función para mostrar todos los usuarios
function mostrarTodosLosUsuarios() {
    echo "=== LISTA DE TODOS LOS USUARIOS ===\n";
    
    try {
        $users = User::all();
        if (empty($users)) {
            echo "❌ No hay usuarios en el sistema\n";
            return;
        }
        
        echo "📋 Total de usuarios: " . count($users) . "\n\n";
        
        foreach ($users as $user) {
            echo "• ID: {$user->id} | {$user->nombre} {$user->apellido} | {$user->email} | ";
            echo "Estado: " . ($user->estado == 1 ? 'Activo' : 'Inactivo') . "\n";
        }
        echo "\n";
        
    } catch (Exception $e) {
        echo "❌ Error al obtener usuarios: " . $e->getMessage() . "\n";
    }
}

// Función para eliminar usuario paso a paso
function eliminarUsuarioPasoAPaso($userId) {
    echo "=== PROCESO DE ELIMINACIÓN PASO A PASO ===\n";
    
    $adminService = new AdminService();
    
    try {
        echo "🔍 Paso 1: Verificando que el usuario existe...\n";
        $user = User::find($userId);
        if (!$user) {
            echo "❌ Error: Usuario con ID $userId no encontrado\n";
            return false;
        }
        echo "✅ Usuario existe: {$user->nombre} {$user->apellido}\n\n";
        
        echo "🔍 Paso 2: Verificando roles del usuario...\n";
        $roles = $user->roles();
        if (!empty($roles)) {
            echo "📋 Roles encontrados: " . implode(', ', array_column($roles, 'nombre')) . "\n";
            echo "🧹 Eliminando roles...\n";
            $user->syncRoles([]);
            echo "✅ Roles eliminados\n";
        } else {
            echo "ℹ️ Usuario sin roles asignados\n";
        }
        echo "\n";
        
        echo "🔍 Paso 3: Verificando permisos del usuario...\n";
        try {
            $permissions = $user->permissions();
            if (!empty($permissions)) {
                echo "📋 Permisos encontrados: " . count($permissions) . "\n";
                echo "🧹 Eliminando permisos...\n";
                $user->syncPermissions([]);
                echo "✅ Permisos eliminados\n";
            } else {
                echo "ℹ️ Usuario sin permisos directos\n";
            }
        } catch (Exception $e) {
            echo "⚠️ Error al verificar permisos (continuando): " . $e->getMessage() . "\n";
        }
        echo "\n";
        
        echo "🔍 Paso 4: Eliminando usuario de la base de datos...\n";
        $resultado = $adminService->deleteUser($userId);
        
        if ($resultado) {
            echo "✅ ¡USUARIO ELIMINADO EXITOSAMENTE!\n";
            
            // Verificar que realmente se eliminó
            echo "\n🔍 Verificación final: Buscando usuario eliminado...\n";
            $userVerification = User::find($userId);
            if ($userVerification) {
                echo "❌ ERROR: El usuario AÚN EXISTE en la base de datos\n";
                return false;
            } else {
                echo "✅ CONFIRMADO: El usuario fue eliminado correctamente\n";
                return true;
            }
        } else {
            echo "❌ ERROR: No se pudo eliminar el usuario\n";
            return false;
        }
        
    } catch (Exception $e) {
        echo "❌ ERROR EN EL PROCESO: " . $e->getMessage() . "\n";
        echo "📊 Información adicional del error:\n";
        echo "   - Archivo: " . $e->getFile() . "\n";
        echo "   - Línea: " . $e->getLine() . "\n";
        echo "   - Trace: " . $e->getTraceAsString() . "\n";
        return false;
    }
}

// EJECUCIÓN DEL SCRIPT
echo "🚀 INICIANDO SCRIPT DE PRUEBA - ELIMINACIÓN DE USUARIO\n";
echo "Fecha: " . date('Y-m-d H:i:s') . "\n";
echo "========================================================\n\n";

// Verificar argumentos
$userId = $argv[1] ?? null;

if (!$userId) {
    echo "❌ ERROR: Debes proporcionar el ID del usuario\n";
    echo "Uso: php check_service_delete_user.php [ID_USUARIO]\n";
    echo "Ejemplo: php check_service_delete_user.php 7\n\n";
    
    // Mostrar usuarios disponibles
    mostrarTodosLosUsuarios();
    exit(1);
}

if (!is_numeric($userId)) {
    echo "❌ ERROR: El ID del usuario debe ser un número\n";
    exit(1);
}

$userId = (int)$userId;

echo "🎯 ID de usuario a eliminar: $userId\n\n";

// Mostrar estado inicial
echo "=== ESTADO INICIAL ===\n";
mostrarTodosLosUsuarios();

// Mostrar información del usuario antes de eliminar
$userAntes = mostrarInfoUsuario($userId);
if (!$userAntes) {
    exit(1);
}

// Confirmar eliminación
echo "⚠️ ¿Estás seguro de que quieres eliminar este usuario? (y/n): ";
$confirmation = trim(fgets(STDIN));

if (strtolower($confirmation) !== 'y' && strtolower($confirmation) !== 'yes') {
    echo "❌ Eliminación cancelada por el usuario\n";
    exit(0);
}

echo "\n";

// Proceder con la eliminación
$resultado = eliminarUsuarioPasoAPaso($userId);

echo "\n=== RESULTADO FINAL ===\n";
if ($resultado) {
    echo "✅ ¡ELIMINACIÓN EXITOSA!\n";
    echo "El usuario con ID $userId ha sido eliminado correctamente del sistema.\n";
} else {
    echo "❌ ELIMINACIÓN FALLIDA\n";
    echo "No se pudo eliminar el usuario con ID $userId.\n";
}

echo "\n=== ESTADO FINAL ===\n";
mostrarTodosLosUsuarios();

echo "🏁 SCRIPT COMPLETADO\n";
echo "Fecha final: " . date('Y-m-d H:i:s') . "\n";
