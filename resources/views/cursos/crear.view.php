<?php
// Vista de Crear Curso
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Crear Curso' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/cursos/crear' ?></p>
        <p>Esta es la vista para crear un nuevo curso.</p>
        <div class="info">
            <p>Aquí estará el formulario para crear nuevos cursos en la plataforma.</p>
        </div>
    </div>
</body>
</html>
