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
