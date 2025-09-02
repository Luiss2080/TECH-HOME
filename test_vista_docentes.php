<?php
require_once 'bootstrap.php';

// Simular datos que llegan a la vista
$title = 'Crear Nuevo Curso';
$errors = [];
$old = [];
$categorias = [
    ['id' => 1, 'nombre' => 'Programación'],
    ['id' => 2, 'nombre' => 'Robótica']
];
$docentes = [
    ['id' => 1, 'nombre' => 'Juan', 'apellido' => 'Pérez', 'email' => 'juan@test.com'],
    ['id' => 2, 'nombre' => 'María', 'apellido' => 'López', 'email' => 'maria@test.com']
];

echo "=== SIMULACIÓN DE VISTA CREAR CURSO ===\n\n";

// Simular las líneas de inicialización de la vista
$title = $title ?? 'Crear Nuevo Curso';
$errors = $errors ?? [];
$old = $old ?? [];
$categorias = $categorias ?? [];
$docentes = $docentes ?? [];

echo "Variables después de inicialización:\n";
echo "- title: " . $title . "\n";
echo "- errors: " . count($errors) . " elementos\n";
echo "- categorias: " . count($categorias) . " elementos\n";
echo "- docentes: " . count($docentes) . " elementos\n\n";

echo "Contenido de docentes:\n";
if (isset($docentes) && !empty($docentes)) {
    foreach ($docentes as $docente) {
        echo "- ID: {$docente['id']}, Nombre: {$docente['nombre']} {$docente['apellido']}\n";
    }
} else {
    echo "⚠️ Array de docentes vacío o no definido\n";
}

echo "\nSimulando HTML del select:\n";
echo '<select name="docente_id">' . "\n";
if (isset($docentes) && !empty($docentes)) {
    echo '    <option value="">Selecciona un docente</option>' . "\n";
    foreach ($docentes as $docente) {
        $nombre = htmlspecialchars($docente['nombre'] . ' ' . ($docente['apellido'] ?? ''));
        $email = !empty($docente['email']) ? ' (' . $docente['email'] . ')' : '';
        echo "    <option value=\"{$docente['id']}\">{$nombre}{$email}</option>\n";
    }
} else {
    echo '    <option value="">No hay docentes disponibles</option>' . "\n";
}
echo '</select>' . "\n";
?>
