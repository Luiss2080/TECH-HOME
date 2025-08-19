<?php
require_once 'bootstrap.php';

// Cargar variables de entorno
$_ENV = loadEnv(BASE_PATH . '.env');

use Core\DB;

echo "=== CONSULTANDO PERMISOS EN BASE DE DATOS ===\n\n";

try {
    // Consultar todos los permisos
    $db = \Core\DB::getInstance();
    $permisos = $db->query("SELECT * FROM permissions ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

    if (empty($permisos)) {
        echo "❌ No se encontraron permisos en la base de datos.\n";
    } else {
        echo "✅ Permisos encontrados:\n";
        echo str_repeat("=", 60) . "\n";

        foreach ($permisos as $permiso) {
            echo sprintf(
                "ID: %-3s | Nombre: %-30s | Guard: %s\n",
                $permiso['id'],
                $permiso['name'],
                $permiso['guard_name'] ?? 'N/A'
            );
        }

        echo str_repeat("=", 60) . "\n";
        echo "Total de permisos: " . count($permisos) . "\n\n";

        // Agrupar permisos por módulo
        echo "=== PERMISOS AGRUPADOS POR MÓDULO ===\n\n";

        $grupos = [];
        foreach ($permisos as $permiso) {
            $partes = explode('.', $permiso['name']);
            $modulo = $partes[0] ?? 'sin_modulo';

            if (!isset($grupos[$modulo])) {
                $grupos[$modulo] = [];
            }
            $grupos[$modulo][] = $permiso['name'];
        }

        foreach ($grupos as $modulo => $permisos_modulo) {
            echo "📂 Módulo: " . strtoupper($modulo) . "\n";
            foreach ($permisos_modulo as $permiso) {
                echo "   └─ " . $permiso . "\n";
            }
            echo "\n";
        }

        // Verificar patrones específicos
        echo "=== ANÁLISIS DE PATRONES ===\n\n";

        $admin_permisos = array_filter(array_column($permisos, 'name'), function ($name) {
            return strpos($name, 'admin.') === 0;
        });

        $docente_permisos = array_filter(array_column($permisos, 'name'), function ($name) {
            return strpos($name, 'docente.') === 0;
        });

        $estudiante_permisos = array_filter(array_column($permisos, 'name'), function ($name) {
            return strpos($name, 'estudiante.') === 0;
        });

        echo "📊 Estadísticas:\n";
        echo "   - Permisos admin.*: " . count($admin_permisos) . "\n";
        echo "   - Permisos docente.*: " . count($docente_permisos) . "\n";
        echo "   - Permisos estudiante.*: " . count($estudiante_permisos) . "\n";
        echo "   - Otros permisos: " . (count($permisos) - count($admin_permisos) - count($docente_permisos) - count($estudiante_permisos)) . "\n\n";

        if (!empty($admin_permisos)) {
            echo "🔧 Permisos de Admin encontrados:\n";
            foreach ($admin_permisos as $permiso) {
                echo "   ✓ " . $permiso . "\n";
            }
            echo "\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Error al consultar permisos: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFICANDO RUTAS ACTUALES ===\n\n";

// Leer el archivo de rutas actual
$rutasContent = file_get_contents('routes/web.php');

// Extraer permisos usados en las rutas
preg_match_all('/has:([a-zA-Z0-9._-]+)/', $rutasContent, $matches);
$permisosEnRutas = array_unique($matches[1]);

echo "📋 Permisos usados en routes/web.php:\n";
foreach ($permisosEnRutas as $permiso) {
    echo "   → " . $permiso . "\n";
}

echo "\n=== COMPARACIÓN ===\n\n";

if (!empty($permisos)) {
    $permisosDB = array_column($permisos, 'name');

    echo "🔍 Verificando coincidencias:\n";
    foreach ($permisosEnRutas as $permisoRuta) {
        if (in_array($permisoRuta, $permisosDB)) {
            echo "   ✅ " . $permisoRuta . " - EXISTE en BD\n";
        } else {
            echo "   ❌ " . $permisoRuta . " - NO EXISTE en BD\n";
        }
    }

    echo "\n🔧 Permisos de BD que NO se usan en rutas:\n";
    foreach ($permisosDB as $permisoBD) {
        if (!in_array($permisoBD, $permisosEnRutas)) {
            echo "   ⚠️  " . $permisoBD . " - disponible pero no usado\n";
        }
    }
}
