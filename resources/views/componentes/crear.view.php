<?php
// Vista de Crear Componente
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Crear Componente' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/componentes/crear' ?></p>
        <p>Esta es la vista para crear un nuevo componente.</p>
        <div class="info">
            <p>Aquí estará el formulario para agregar nuevos componentes al inventario.</p>
        </div>
    </div>
</body>
</html>
