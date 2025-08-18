<?php
// Vista de Usuarios del Administrador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Usuarios' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> /admin/usuarios</p>
        <p>Esta es la vista de gestión de usuarios del panel de administración.</p>
        <div class="info">
            <p>Aquí se listarán y gestionarán todos los usuarios del sistema.</p>
        </div>
    </div>
</body>
</html>
