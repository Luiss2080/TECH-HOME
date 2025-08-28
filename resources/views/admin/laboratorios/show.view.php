<?php
// Vista de Ver Laboratorio - Admin
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Ver Laboratorio' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/admin/laboratorios/show' ?></p>
        <p>Esta es la vista de detalle de laboratorio.</p>
        <div class="info">
            <p>Aquí se muestra la información detallada del laboratorio.</p>
        </div>
    </div>
</body>
</html>
