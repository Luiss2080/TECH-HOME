<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Restablecer Contraseña' ?> - Tech Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background: var(--success);
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        .shape-4 {
            width: 60px;
            height: 60px;
            background: var(--purple);
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
            max-width: 520px;
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
        .logo {
            text-align: center;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 2;
        }

        .logo i {
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

        .logo i::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(transparent, rgba(255,255,255,0.3), transparent);
            animation: iconRotate 3s linear infinite;
        }

        .logo h2 {
            color: var(--text-dark);
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.8rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .logo p {
            color: var(--text-light);
            font-size: 1.1rem;
            font-weight: 500;
            opacity: 0.9;
            margin: 0;
        }

        /* Formulario */
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 2;
        }

        .form-label {
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .input-group {
            position: relative;
            margin-bottom: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 1rem 1.2rem;
            padding-right: 3rem;
            border: 2px solid rgba(107, 114, 128, 0.2);
            border-radius: var(--border-radius-sm);
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.8);
            transition: var(--transition);
            color: var(--text-dark);
            font-family: inherit;
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

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-light);
            z-index: 3;
            transition: var(--transition);
            font-size: 1.1rem;
            padding: 0.5rem;
            border-radius: 50%;
        }

        .password-toggle:hover {
            color: var(--primary-red);
            background: rgba(220, 38, 38, 0.1);
            transform: translateY(-50%) scale(1.1);
        }

        /* Medidor de fuerza */
        .strength-meter {
            margin: 15px 0 20px 0;
            position: relative;
            z-index: 2;
        }

        .strength-bar {
            height: 8px;
            background: linear-gradient(145deg, rgba(226, 232, 240, 0.8), rgba(241, 245, 249, 0.8));
            border-radius: 25px;
            overflow: hidden;
            margin-bottom: 8px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 25px;
            position: relative;
            overflow: hidden;
        }

        .strength-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: strengthShimmer 2s ease-in-out infinite;
        }

        .strength-text {
            font-size: 0.85rem;
            color: var(--text-light);
            text-align: center;
            font-weight: 600;
            transition: var(--transition);
        }

        /* Botón principal */
        .btn {
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
            z-index: 2;
        }

        .btn:hover:not(:disabled) {
            transform: translateY(-3px) scale(1.02);
            box-shadow: var(--shadow-lg);
            background: linear-gradient(135deg, var(--primary-red-dark), var(--primary-red));
        }

        .btn:active:not(:disabled) {
            transform: translateY(-1px) scale(1.01);
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
            box-shadow: var(--shadow-sm);
            background: linear-gradient(135deg, #9ca3af, #6b7280);
        }

        .btn i {
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
            text-decoration: none;
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
            z-index: 2;
        }

        .alert-danger {
            background: rgba(220, 38, 38, 0.1);
            color: var(--primary-red);
            border-left: 4px solid var(--primary-red);
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

        /* Hidden inputs */
        input[type="hidden"] {
            display: none;
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

        @keyframes strengthShimmer {
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
            
            .logo h2 {
                font-size: 1.6rem;
            }
            
            .logo i {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .form-control {
                padding: 0.9rem 1rem;
                padding-right: 2.8rem;
            }
            
            .btn {
                padding: 0.9rem 1.5rem;
                font-size: 1rem;
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
            
            .logo h2 {
                color: #f8fafc;
            }
            
            .logo p,
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
            
            .strength-bar {
                background: linear-gradient(145deg, rgba(51, 65, 85, 0.8), rgba(71, 85, 105, 0.8));
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
            <div class="logo">
                <i class="fas fa-lock"></i>
                <h2>Nueva Contraseña</h2>
                <p>Crea una contraseña segura para <?= htmlspecialchars($email) ?></p>
            </div>

            <?php if (session('error')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= session('error') ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= route('password.update') ?>">
                <?= CSRF() ?>
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fas fa-key" style="margin-right: 0.5rem; color: var(--primary-red);"></i>
                        Nueva Contraseña
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" 
                               required placeholder="Mínimo 8 caracteres">
                        <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                    </div>
                    <?php if (isset($errors['password'])): ?>
                        <?php foreach ($errors['password'] as $error): ?>
                            <div class="text-danger"><?= $error ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-check-double" style="margin-right: 0.5rem; color: var(--primary-red);"></i>
                        Confirmar Contraseña
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password_confirmation" 
                               name="password_confirmation" required placeholder="Repite la contraseña">
                        <i class="fas fa-eye password-toggle" id="togglePasswordConfirm"></i>
                    </div>
                    <?php if (isset($errors['password_confirmation'])): ?>
                        <?php foreach ($errors['password_confirmation'] as $error): ?>
                            <div class="text-danger"><?= $error ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="strength-meter">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <div class="strength-text" id="strengthText">Ingresa una contraseña</div>
                </div>

                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                    <i class="fas fa-shield-alt"></i>
                    Actualizar Contraseña
                </button>
            </form>

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
            const form = document.querySelector('form');
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
        });

        // Toggle password visibility
        function togglePasswordVisibility(inputId, toggleId) {
            document.getElementById(toggleId).addEventListener('click', function() {
                const passwordInput = document.getElementById(inputId);
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        togglePasswordVisibility('password', 'togglePassword');
        togglePasswordVisibility('password_confirmation', 'togglePasswordConfirm');

        // Password strength checker
        const password = document.getElementById('password');
        const passwordConfirm = document.getElementById('password_confirmation');
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        const submitBtn = document.getElementById('submitBtn');

        function checkPasswordStrength(pass) {
            let score = 0;
            let feedback = [];

            if (pass.length >= 8) score++;
            else feedback.push('mínimo 8 caracteres');

            if (/[a-z]/.test(pass) && /[A-Z]/.test(pass)) score++;
            else feedback.push('mayúsculas y minúsculas');

            if (/\d/.test(pass)) score++;
            else feedback.push('números');

            if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\?]/.test(pass)) score++;
            else feedback.push('símbolos especiales');

            const strength = ['Muy débil', 'Débil', 'Regular', 'Buena', 'Fuerte'];
            const colors = ['#dc2626', '#f59e0b', '#f59e0b', '#10b981', '#10b981'];
            
            strengthFill.style.width = (score * 25) + '%';
            strengthFill.style.background = `linear-gradient(135deg, ${colors[score] || '#e2e8f0'}, ${colors[score] || '#f1f5f9'})`;
            strengthText.textContent = strength[score] || 'Ingresa una contraseña';
            strengthText.style.color = colors[score] || '#6b7280';
            
            if (feedback.length > 0 && pass.length > 0) {
                strengthText.textContent += ' (falta: ' + feedback.join(', ') + ')';
                strengthText.style.color = '#6b7280';
            }

            return score;
        }

        function validateForm() {
            const pass = password.value;
            const passConfirm = passwordConfirm.value;
            const strength = checkPasswordStrength(pass);
            
            // Validación en tiempo real de coincidencia
            if (passConfirm && pass !== passConfirm) {
                passwordConfirm.style.borderColor = '#dc2626';
                passwordConfirm.style.boxShadow = '0 0 0 3px rgba(220, 38, 38, 0.1)';
            } else if (passConfirm && pass === passConfirm) {
                passwordConfirm.style.borderColor = '#10b981';
                passwordConfirm.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.1)';
            } else {
                passwordConfirm.style.borderColor = 'rgba(107, 114, 128, 0.2)';
                passwordConfirm.style.boxShadow = 'none';
            }
            
            // Validación del password principal
            if (pass && strength >= 2) {
                password.style.borderColor = '#10b981';
                password.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.1)';
            } else if (pass && strength < 2) {
                password.style.borderColor = '#f59e0b';
                password.style.boxShadow = '0 0 0 3px rgba(245, 158, 11, 0.1)';
            } else {
                password.style.borderColor = 'rgba(107, 114, 128, 0.2)';
                password.style.boxShadow = 'none';
            }
            
            const isValid = strength >= 2 && pass === passConfirm && pass.length >= 8;
            submitBtn.disabled = !isValid;
            
            // Cambiar estilo del botón según validez
            if (isValid) {
                submitBtn.style.background = 'linear-gradient(135deg, #dc2626, #ef4444)';
                submitBtn.style.cursor = 'pointer';
            } else {
                submitBtn.style.background = 'linear-gradient(135deg, #9ca3af, #6b7280)';
                submitBtn.style.cursor = 'not-allowed';
            }
        }

        password.addEventListener('input', validateForm);
        passwordConfirm.addEventListener('input', validateForm);

        console.log('🔐 Reset Password - Sistema TECH HOME activo');
    </script>
</body>
</html>
