<?php
// Vista de Laboratorios
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Laboratorios' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/laboratorios' ?></p>
        <p>Esta es la vista de laboratorios virtuales.</p>
        <div class="info">
            <p>Aquí se mostrarán todos los laboratorios virtuales disponibles.</p>
        </div>
    </div>
</body>
</html>
