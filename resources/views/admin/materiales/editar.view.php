<?php
// Vista de Editar Material - Admin
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Editar Material' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/admin/materiales/editar' ?></p>
        <p>Esta es la vista de edición de materiales educativos.</p>
        <div class="info">
            <p>Aquí se puede editar un material educativo existente.</p>
        </div>
    </div>
</body>
</html>
