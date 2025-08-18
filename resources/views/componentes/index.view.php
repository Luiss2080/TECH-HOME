<?php
// Vista de Componentes
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Componentes' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/componentes' ?></p>
        <p>Esta es la vista de gestión de componentes.</p>
        <div class="info">
            <p>Aquí se mostrarán todos los componentes disponibles en el inventario.</p>
        </div>
    </div>
</body>
</html>
