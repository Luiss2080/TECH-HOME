<?php
// Vista de Configuración del Administrador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Configuración' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> /admin/configuracion</p>
        <p>Esta es la vista de configuración del panel de administración.</p>
        <div class="info">
            <p>Aquí se configurarán los parámetros del sistema.</p>
        </div>
    </div>
</body>
</html>
