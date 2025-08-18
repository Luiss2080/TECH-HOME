<?php
// Vista de Crear Libro
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Añadir Libro' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/libros/crear' ?></p>
        <p>Esta es la vista para añadir un nuevo libro a la biblioteca.</p>
        <div class="info">
            <p>Aquí estará el formulario para agregar nuevos libros a la biblioteca digital.</p>
        </div>
    </div>
</body>
</html>
