<?php
// Vista de Dashboard Laboratorios - Docente
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Laboratorios' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/docente/laboratorios/dashboard' ?></p>
        <p>Esta es la vista de dashboard de laboratorios para docentes.</p>
        <div class="info">
            <p>Aquí se muestra el dashboard de laboratorios del docente.</p>
        </div>
    </div>
</body>
</html>
