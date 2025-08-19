<?php

require_once 'bootstrap.php';
$_ENV = loadEnv(BASE_PATH . '.env');

// Verificar que se haya proporcionado un parámetro
if ($argc < 2) {
    echo "❌ Uso: php verificar_permisos.php <user_id|email>\n";
    echo "Ejemplo: php verificar_permisos.php 1\n";
    echo "Ejemplo: php verificar_permisos.php admin@example.com\n";
    exit(1);
}

$userInput = $argv[1];

try {
    // Buscar usuario por ID o email
    $user = null;
    if (is_numeric($userInput)) {
        $user = \App\Models\User::find($userInput);
    } else {
        $users = \App\Models\User::where('email', $userInput);
        $user = !empty($users) ? $users[0] : null;
    }

    if (!$user) {
        echo "❌ Usuario no encontrado: $userInput\n";
        exit(1);
    }
    $roles = $user->roles();
    echo "=== INFORMACIÓN DEL USUARIO ===\n";
    echo "ID: {$user->id}\n";
    echo "Nombre: {$user->nombre} {$user->apellido}\n";
    echo "Email: {$user->email}\n";
    echo "Estado: " . ($user->estado == 1 ? 'Activo' : 'Inactivo') . "\n";
    echo "\n";
    echo "Roles:\n";
    if (empty($roles)) {
        echo "❌ Este usuario no tiene roles asignados\n";
    } else {
        foreach ($roles as $role) {
            echo "- Nombre:" . htmlspecialchars($role['nombre']) . " Descripción: " . htmlspecialchars($role['descripcion']) . "\n";
        }
    }

    // Obtener todos los permisos únicos del usuario usando el método permissions()
    $allPermissions = $user->permissions();

    if (empty($allPermissions)) {
        echo "❌ Este usuario no tiene permisos asignados\n";
        exit(0);
    }

    echo "=== PERMISOS DEL USUARIO ===\n";

    // Obtener permisos directos usando el mismo método que el modelo User
    $directPermissions = \App\Models\ModelHasPermissions::getPermissionsForModel('App\\Models\\User', $user->id);
    $directPermissionIds = array_column($directPermissions, 'id');

    // Mostrar tabla de permisos
    echo sprintf("%-5s %-40s %-15s\n", "ID", "PERMISO", "ORIGEN");
    echo str_repeat("-", 62) . "\n";

    foreach ($allPermissions as $permission) {
        $origen = in_array($permission['id'], $directPermissionIds) ? "📋 Directo" : "👥 Por Rol";
        echo sprintf(
            "%-5s %-40s %-15s\n",
            $permission['id'],
            $permission['name'],
            $origen
        );
    }

    echo str_repeat("-", 62) . "\n";
    echo "Total de permisos: " . count($allPermissions) . "\n";
    echo "Permisos directos: " . count($directPermissionIds) . "\n";
    echo "Permisos por roles: " . (count($allPermissions) - count($directPermissionIds)) . "\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Verificación completada ===\n";
