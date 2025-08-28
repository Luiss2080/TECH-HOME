<?php
// Vista de Laboratorios - Admin
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
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/admin/laboratorios' ?></p>
        <p>Esta es la vista de administración de laboratorios.</p>
        <div class="info">
            <p>Aquí se mostrarán todos los laboratorios disponibles para administración.</p>
        </div>
    </div>
</body>
</html>
