<?php
// Vista de Reportes del Administrador
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Reportes' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> /admin/reportes</p>
        <p>Esta es la vista de reportes del panel de administración.</p>
        <div class="info">
            <p>Aquí se mostrarán los reportes y estadísticas del sistema.</p>
        </div>
    </div>
</body>
</html>
