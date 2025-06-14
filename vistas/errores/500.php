<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error del Servidor - Tech Home Bolivia</title>
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
        <h1 class="display-1 text-danger">500</h1>
        <h2>Error Interno del Servidor</h2>
        <p class="lead">Algo salió mal. Nuestro equipo técnico ha sido notificado.</p>
        <a href="/TECH-HOME/" class="btn btn-primary">Volver al Inicio</a>
        <a href="javascript:location.reload()" class="btn btn-secondary">Intentar de nuevo</a>
    </div>
</body>
</html>