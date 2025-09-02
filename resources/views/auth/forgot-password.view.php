<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Recuperar Contraseña' ?> - Tech Home</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Variables CSS basadas en admin.css */
        :root {
            --primary-red: #dc2626;
            --primary-red-light: #ef4444;
            --primary-red-dark: #b91c1c;
            --text-dark: #1f2937;
            --text-light: #6b7280;
            --success: #10b981;
            --warning: #f59e0b;
            --info: #3b82f6;
            --purple: #8b5cf6;
            --cyan: #06b6d4;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 8px 25px rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 20px 40px rgba(0, 0, 0, 0.2);
            --border-radius: 20px;
            --border-radius-sm: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #f1f5f9 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            position: relative;
            overflow: hidden;
        }

        /* Animación de fondo */
        .bg-animation {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .floating-shapes {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 8s ease-in-out infinite;
        }

        .shape-1 {
            width: 120px;
            height: 120px;
            background: var(--primary-red);
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 80px;
            height: 80px;
            background: var(--info);
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .shape-3 {
            width: 100px;
            height: 100px;
            background: var(--purple);
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        .shape-4 {
            width: 60px;
            height: 60px;
            background: var(--cyan);
            top: 10%;
            right: 30%;
            animation-delay: 1s;
        }

        @keyframes float {
            0%, 100% { 
                transform: translateY(0px) rotate(0deg) scale(1); 
            }
            33% { 
                transform: translateY(-30px) rotate(120deg) scale(1.1); 
            }
            66% { 
                transform: translateY(20px) rotate(240deg) scale(0.9); 
            }
        }

        /* Contenedor principal */
        .reset-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 480px;
            padding: 0 2rem;
        }

        .reset-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: var(--border-radius);
            padding: 3rem 2.5rem;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            opacity: 0;
            transform: translateY(40px);
            animation: cardAppear 0.8s ease-out 0.3s forwards;
        }

        .reset-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-red), var(--primary-red-light), var(--primary-red));
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .reset-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(220, 38, 38, 0.08), transparent);
            animation: shimmer 3s ease-in-out infinite;
            pointer-events: none;
        }

        /* Header */
        .reset-header {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 2;
        }

        .reset-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-red), var(--primary-red-light));
            border-radius: var(--border-radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 2rem;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
            animation: iconPulse 2s ease-in-out infinite;
        }

        .reset-icon::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(transparent, rgba(255,255,255,0.3), transparent);
            animation: iconRotate 3s linear infinite;
        }

        .reset-title {
            color: var(--text-dark);
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.8rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .reset-subtitle {
            color: var(--text-light);
            font-size: 1.1rem;
            font-weight: 500;
            opacity: 0.9;
        }

        /* Formulario */
        .reset-form {
            position: relative;
            z-index: 2;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.2rem;
            border: 2px solid rgba(107, 114, 128, 0.2);
            border-radius: var(--border-radius-sm);
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.8);
            transition: var(--transition);
            color: var(--text-dark);
            position: relative;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
            background: rgba(255, 255, 255, 0.95);
            transform: translateY(-2px);
        }

        .form-control::placeholder {
            color: var(--text-light);
            opacity: 0.7;
        }

        /* Botón principal */
        .btn-primary {
            width: 100%;
            padding: 1rem 2rem;
            background: linear-gradient(135deg, var(--primary-red), var(--primary-red-light));
            border: none;
            border-radius: var(--border-radius-sm);
            color: white;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: var(--shadow-lg);
            background: linear-gradient(135deg, var(--primary-red-dark), var(--primary-red));
        }

        .btn-primary:active {
            transform: translateY(-1px) scale(1.01);
        }

        .btn-primary i {
            margin-right: 0.5rem;
        }

        /* Enlaces */
        .back-link {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .back-link a {
            color: var(--primary-red);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.5rem;
            border-radius: 25px;
            background: rgba(220, 38, 38, 0.1);
            transition: var(--transition);
        }

        .back-link a:hover {
            background: var(--primary-red);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Alertas */
        .alert {
            padding: 1rem 1.2rem;
            margin-bottom: 1.5rem;
            border-radius: var(--border-radius-sm);
            border: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            position: relative;
            overflow: hidden;
        }

        .alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }

        .alert-success::before {
            background: var(--success);
        }

        .alert-danger {
            background: rgba(220, 38, 38, 0.1);
            color: var(--primary-red);
            border-left: 4px solid var(--primary-red);
        }

        .alert-danger::before {
            background: var(--primary-red);
        }

        .alert i {
            font-size: 1.1rem;
        }

        /* Errores de validación */
        .text-danger {
            color: var(--primary-red) !important;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .text-danger::before {
            content: '\f071';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            font-size: 0.8rem;
        }

        /* Animaciones */
        @keyframes cardAppear {
            from { 
                opacity: 0; 
                transform: translateY(40px) scale(0.95); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            }
        }

        @keyframes shimmer {
            0%, 100% { left: -100%; }
            50% { left: 100%; }
        }

        @keyframes iconPulse {
            0%, 100% { 
                transform: scale(1);
                box-shadow: var(--shadow-md);
            }
            50% { 
                transform: scale(1.05);
                box-shadow: 0 15px 35px rgba(220, 38, 38, 0.3);
            }
        }

        @keyframes iconRotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .reset-container {
                padding: 0 1rem;
            }
            
            .reset-card {
                padding: 2rem 1.5rem;
            }
            
            .reset-title {
                font-size: 1.6rem;
            }
            
            .reset-icon {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
        }

        /* Modo oscuro */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
            }
            
            .reset-card {
                background: rgba(30, 41, 59, 0.95);
                border-color: rgba(71, 85, 105, 0.3);
            }
            
            .reset-title {
                color: #f8fafc;
            }
            
            .reset-subtitle,
            .form-label {
                color: #cbd5e1;
            }
            
            .form-control {
                background: rgba(30, 41, 59, 0.8);
                border-color: rgba(71, 85, 105, 0.3);
                color: #f8fafc;
            }
            
            .form-control:focus {
                background: rgba(30, 41, 59, 0.95);
            }
        }
    </style>
</head>
<body>
    <!-- Animación de fondo -->
    <div class="bg-animation">
        <div class="floating-shapes shape-1"></div>
        <div class="floating-shapes shape-2"></div>
        <div class="floating-shapes shape-3"></div>
        <div class="floating-shapes shape-4"></div>
    </div>

    <div class="reset-container">
        <div class="reset-card">
            <!-- Header -->
            <div class="reset-header">
                <div class="reset-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h1 class="reset-title">Recuperar Contraseña</h1>
                <p class="reset-subtitle">Ingresa tu email para recibir un enlace de recuperación</p>
            </div>

            <!-- Alertas -->
            <?php if (session('success')): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fas fa-check-circle"></i>
                    <?= session('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session('error')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= session('error') ?>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <form method="POST" action="<?= route('password.email') ?>" class="reset-form">
                <?= CSRF() ?>
                
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope" style="margin-right: 0.5rem; color: var(--primary-red);"></i>
                        Dirección de Email
                    </label>
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           value="<?= old('email') ?>" 
                           required 
                           placeholder="tu-email@ejemplo.com"
                           autocomplete="email">
                    <?php if (isset($errors['email'])): ?>
                        <?php foreach ($errors['email'] as $error): ?>
                            <div class="text-danger"><?= $error ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane"></i>
                    Enviar Enlace de Recuperación
                </button>
            </form>

            <!-- Enlaces -->
            <div class="back-link">
                <a href="<?= route('login') ?>">
                    <i class="fas fa-arrow-left"></i>
                    Volver al inicio de sesión
                </a>
            </div>
        </div>
    </div>

    <script>
        // Animación adicional para el formulario
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.reset-form');
            const inputs = document.querySelectorAll('.form-control');
            
            // Efecto de focus mejorado
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
            
            // Validación en tiempo real
            const emailInput = document.getElementById('email');
            emailInput.addEventListener('input', function() {
                if (this.value && this.validity.valid) {
                    this.style.borderColor = 'var(--success)';
                    this.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.1)';
                } else if (this.value && !this.validity.valid) {
                    this.style.borderColor = 'var(--primary-red)';
                    this.style.boxShadow = '0 0 0 3px rgba(220, 38, 38, 0.1)';
                } else {
                    this.style.borderColor = 'rgba(107, 114, 128, 0.2)';
                    this.style.boxShadow = 'none';
                }
            });
        });
    </script>
</body>
</html>
