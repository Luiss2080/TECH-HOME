<?php
// Vista de Materiales
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Materiales' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/materiales' ?></p>
        <p>Esta es la vista de gestión de materiales educativos.</p>
        <div class="info">
            <p>Aquí se mostrarán todos los materiales educativos disponibles.</p>
        </div>
    </div>
</body>
</html>
