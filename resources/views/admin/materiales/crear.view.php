<?php
// Vista de Crear Material - Admin
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Crear Material' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/admin/materiales/crear' ?></p>
        <p>Esta es la vista de creación de materiales educativos.</p>
        <div class="info">
            <p>Aquí se puede crear un nuevo material educativo.</p>
        </div>
    </div>
</body>
</html>
