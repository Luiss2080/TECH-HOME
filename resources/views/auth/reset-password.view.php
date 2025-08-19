<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Restablecer Contraseña' ?> - Tech Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .reset-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            padding: 2rem;
            width: 100%;
            max-width: 450px;
        }
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo i {
            font-size: 3rem;
            color: #11998e;
            margin-bottom: 1rem;
        }
        .logo h2 {
            color: #333;
            font-weight: 600;
        }
        .form-control {
            border-radius: 10px;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            margin-bottom: 1rem;
        }
        .form-control:focus {
            border-color: #11998e;
            box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
        }
        .input-group {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            z-index: 3;
        }
        .password-toggle:hover {
            color: #11998e;
        }
        .btn-primary {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem;
            font-weight: 600;
            width: 100%;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(17, 153, 142, 0.4);
        }
        .btn-primary:disabled {
            opacity: 0.6;
            transform: none;
            box-shadow: none;
        }
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        .back-link a {
            color: #11998e;
            text-decoration: none;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        .strength-meter {
            margin: 10px 0;
        }
        .strength-bar {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 5px;
        }
        .strength-fill {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 4px;
        }
        .strength-text {
            font-size: 12px;
            color: #6c757d;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="reset-card">
        <div class="logo">
            <i class="fas fa-lock"></i>
            <h2>Nueva Contraseña</h2>
            <p class="text-muted">Crea una contraseña segura para <?= htmlspecialchars($email) ?></p>
        </div>

        <?php if (session('error')): ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= session('error') ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= route('password.update') ?>">
            <?= CSRF() ?>
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
            <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">

            <div class="mb-3">
                <label for="password" class="form-label">Nueva Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" 
                           required placeholder="Mínimo 8 caracteres">
                    <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <?php foreach ($errors['password'] as $error): ?>
                        <div class="text-danger small mt-1"><?= $error ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password_confirmation" 
                           name="password_confirmation" required placeholder="Repite la contraseña">
                    <i class="fas fa-eye password-toggle" id="togglePasswordConfirm"></i>
                </div>
                <?php if (isset($errors['password_confirmation'])): ?>
                    <?php foreach ($errors['password_confirmation'] as $error): ?>
                        <div class="text-danger small mt-1"><?= $error ?></div>
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
                <i class="fas fa-key me-2"></i>
                Actualizar Contraseña
            </button>
        </form>

        <div class="back-link">
            <a href="<?= route('login') ?>">
                <i class="fas fa-arrow-left me-1"></i>
                Volver al inicio de sesión
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
            const colors = ['#dc3545', '#fd7e14', '#ffc107', '#198754', '#0d6efd'];
            
            strengthFill.style.width = (score * 25) + '%';
            strengthFill.style.backgroundColor = colors[score] || '#e9ecef';
            strengthText.textContent = strength[score] || 'Ingresa una contraseña';
            
            if (feedback.length > 0 && pass.length > 0) {
                strengthText.textContent += ' (falta: ' + feedback.join(', ') + ')';
            }

            return score;
        }

        function validateForm() {
            const pass = password.value;
            const passConfirm = passwordConfirm.value;
            const strength = checkPasswordStrength(pass);
            
            const isValid = strength >= 2 && pass === passConfirm && pass.length >= 8;
            submitBtn.disabled = !isValid;
        }

        password.addEventListener('input', validateForm);
        passwordConfirm.addEventListener('input', validateForm);
    </script>
</body>
</html>
