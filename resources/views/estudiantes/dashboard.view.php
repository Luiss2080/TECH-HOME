<?php
// Vista de Dashboard Docente
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard Docente' ?></title>
</head>

<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/Docentes/dashboard' ?></p>
        <p>Esta es la vista del dashboard para Docentes.</p>
        <div class="info">
            <p>Aquí se mostrará el panel de control personalizado para Docentes.</p>
        </div>
    </div>
</body>

</html>