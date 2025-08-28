<?php
// Vista de Editar Laboratorio - Admin
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Editar Laboratorio' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/admin/laboratorios/editar' ?></p>
        <p>Esta es la vista de edición de laboratorios.</p>
        <div class="info">
            <p>Aquí se puede editar un laboratorio existente.</p>
        </div>
    </div>
</body>
</html>
