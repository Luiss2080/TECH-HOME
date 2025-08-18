<?php
// Vista de Libros
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Biblioteca' ?></title>
</head>
<body>
    <div class="container">
        <h1><?= $title ?></h1>
        <p><strong>Ruta actual:</strong> <?= $ruta ?? '/libros' ?></p>
        <p>Esta es la vista de la biblioteca digital.</p>
        <div class="info">
            <p>Aquí se mostrarán todos los libros disponibles en la biblioteca.</p>
        </div>
    </div>
</body>
</html>
