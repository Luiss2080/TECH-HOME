<?php
// Vista de Crear Usuario del Administrador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Crear Usuario' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> /admin/usuarios/crear</p>
        <p>Esta es la vista para crear un nuevo usuario desde el panel de administración.</p>
        <div class="info">
            <p>Aquí estará el formulario para registrar nuevos usuarios en el sistema.</p>
        </div>
    </div>
</body>
</html>
