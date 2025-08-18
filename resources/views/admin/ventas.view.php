<?php
// Vista de Ventas del Administrador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Ventas' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> /admin/ventas</p>
        <p>Esta es la vista de gestión de ventas del panel de administración.</p>
        <div class="info">
            <p>Aquí se mostrarán y gestionarán todas las ventas del sistema.</p>
        </div>
    </div>
</body>
</html>
