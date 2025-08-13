<?php
// Obtener información del usuario actual para personalizar el mensaje
$user = auth();
$userRole = $user ? ($user->rol() ? $user->rol()->nombre : 'sin rol') : 'anónimo';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Acceso Denegado | TECH-HOME</title>
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
            color: #dc3545;
            margin-bottom: 1rem;
        }
        .error-code {
            font-size: 3rem;
            font-weight: bold;
            color: #dc3545;
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
        .user-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            border-left: 4px solid #ffc107;
        }
        .btn-home {
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
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }
        .btn-back {
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
        .btn-back:hover {
            background: #5a6268;
            color: white;
            text-decoration: none;
        }
        .role-badge {
            background: #007bff;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="bi bi-shield-x"></i>
        </div>
        
        <div class="error-code">403</div>
        
        <h1 class="error-title">Acceso Denegado</h1>
        
        <p class="error-message">
            Lo sentimos, no tienes los permisos necesarios para acceder a esta página.
        </p>
        
        <?php if ($user): ?>
        <div class="user-info">
            <strong>Usuario actual:</strong> <?= htmlspecialchars($user->nombre . ' ' . $user->apellido) ?><br>
            <strong>Rol:</strong> <span class="role-badge"><?= htmlspecialchars(ucfirst($userRole)) ?></span>
        </div>
        <?php endif; ?>
        
        <div class="error-message">
            <?php if ($userRole === 'estudiante'): ?>
                <p><i class="bi bi-info-circle text-info"></i> Como estudiante, tienes acceso a cursos, materiales de estudio y tu progreso académico.</p>
            <?php elseif ($userRole === 'docente'): ?>
                <p><i class="bi bi-info-circle text-info"></i> Como docente, puedes gestionar cursos, estudiantes y materiales, pero no funciones administrativas.</p>
            <?php elseif ($userRole === 'administrador'): ?>
                <p><i class="bi bi-exclamation-triangle text-warning"></i> Aunque eres administrador, esta página específica podría tener restricciones adicionales.</p>
            <?php else: ?>
                <p><i class="bi bi-person-x text-muted"></i> Por favor, contacta al administrador para obtener los permisos adecuados.</p>
            <?php endif; ?>
        </div>
        
        <div class="mt-4">
            <a href="<?= route('dashboard') ?>" class="btn-home">
                <i class="bi bi-house-door"></i>
                Ir al Dashboard
            </a>
            
            <a href="javascript:history.back()" class="btn-back">
                <i class="bi bi-arrow-left"></i>
                Volver
            </a>
        </div>
        
        <?php if (flashGet('error')): ?>
        <div class="alert alert-danger mt-3" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            <?= htmlspecialchars(flashGet('error')) ?>
        </div>
        <?php endif; ?>
        
        <div class="mt-4">
            <small class="text-muted">
                Si crees que esto es un error, contacta al administrador del sistema.
            </small>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/public/js/bootstrap.bundle.min.js"></script>
</body>
</html>
