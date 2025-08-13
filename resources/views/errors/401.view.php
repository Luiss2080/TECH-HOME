<?php
use Core\Session;
$redirectUrl = Session::get('back') ?? route('dashboard');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>401 - No Autenticado | TECH-HOME</title>
    <link href="<?= BASE_URL ?>/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/public/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        .error-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 3rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .error-icon {
            font-size: 4rem;
            color: #ffc107;
            margin-bottom: 1rem;
        }
        .error-code {
            font-size: 3rem;
            font-weight: bold;
            color: #ffc107;
            margin-bottom: 1rem;
        }
        .error-title {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1rem;
        }
        .error-message {
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.6;
        }
        .login-info {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            border-left: 4px solid #2196f3;
        }
        .btn-login {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            padding: 12px 30px;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }
        .btn-home {
            background: #6c757d;
            border: none;
            padding: 10px 25px;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: 1rem;
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-person-lock"></i>
        </div>
        
        <div class="error-code">401</div>
        
        <h1 class="error-title">Autenticación Requerida</h1>
        
        <p class="error-message">
            Necesitas iniciar sesión para acceder a esta página.
        </p>
        
        <div class="login-info">
            <i class="bi bi-info-circle text-primary"></i>
            <strong>¿Por qué necesitas iniciar sesión?</strong><br>
            Esta página contiene información privada que solo está disponible para usuarios registrados y autenticados en el sistema.
        </div>
        
        <div class="mt-4">
            <a href="<?= route('login') ?>" class="btn-login">
                <i class="bi bi-box-arrow-in-right"></i>
                Iniciar Sesión
            </a>
            
            <a href="<?= route('home') ?>" class="btn-home">
                <i class="bi bi-house-door"></i>
                Inicio
            </a>
        </div>
        
        <?php if (flashGet('error')): ?>
        <div class="alert alert-warning mt-3" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            <?= htmlspecialchars(flashGet('error')) ?>
        </div>
        <?php endif; ?>
        
        <div class="mt-4">
            <small class="text-muted">
                Después de iniciar sesión, serás redirigido automáticamente a la página que intentabas visitar.
            </small>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
