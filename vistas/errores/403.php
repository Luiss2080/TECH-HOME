<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado - Tech Home Bolivia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            background: white;
            padding: 3rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="display-1 text-danger">403</h1>
        <h2>Acceso Denegado</h2>
        <p class="lead">No tienes permisos para acceder a esta página.</p>
        <a href="/TECH-HOME/" class="btn btn-primary">Volver al Inicio</a>
        <a href="/TECH-HOME/login.php" class="btn btn-secondary">Iniciar Sesión</a>
    </div>
</body>
</html>