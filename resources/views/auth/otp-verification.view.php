<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA - Tech Home Bolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css">
    
    <!-- Headers anti-cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Fondo animado */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            overflow: hidden;
        }

        .floating-shapes {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }

        .shape-1 { width: 80px; height: 80px; top: 20%; left: 10%; animation-delay: 0s; }
        .shape-2 { width: 60px; height: 60px; top: 70%; left: 80%; animation-delay: 1s; }
        .shape-3 { width: 40px; height: 40px; top: 40%; left: 70%; animation-delay: 2s; }
        .shape-4 { width: 100px; height: 100px; top: 60%; left: 20%; animation-delay: 3s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-20px) rotate(120deg); }
            66% { transform: translateY(-10px) rotate(240deg); }
        }

        .otp-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 25px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 480px;
            padding: 50px 40px;
            text-align: center;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: slideInUp 0.8s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-icon {
            font-size: 64px;
            color: #dc2626;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
            filter: drop-shadow(0 4px 8px rgba(220, 38, 38, 0.3));
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .otp-title {
            font-size: 28px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .otp-subtitle {
            color: #6b7280;
            margin-bottom: 30px;
            font-size: 16px;
            line-height: 1.5;
        }

        .email-display {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 2px dashed #9ca3af;
            display: inline-block;
            font-weight: 600;
            color: #374151;
            font-family: 'Courier New', monospace;
        }

        .timer-container {
            background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%);
            border: 2px solid #f59e0b;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .timer-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 49%, rgba(255, 255, 255, 0.1) 50%, transparent 51%);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .timer-label {
            color: #92400e;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .timer-display {
            font-size: 48px;
            font-weight: bold;
            color: #dc2626;
            font-family: 'Courier New', monospace;
            text-shadow: 0 2px 4px rgba(220, 38, 38, 0.3);
            position: relative;
            z-index: 2;
        }

        .timer-expired {
            color: #dc2626 !important;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.5; }
        }

        .otp-input-container {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 30px;
        }

        .otp-digit {
            width: 60px;
            height: 70px;
            border: 3px solid #e5e7eb;
            border-radius: 12px;
            text-align: center;
            font-size: 32px;
            font-weight: bold;
            color: #1f2937;
            background: white;
            transition: all 0.3s ease;
            font-family: 'Courier New', monospace;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .otp-digit:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.2), 0 4px 15px rgba(0, 0, 0, 0.15);
            transform: translateY(-2px);
        }

        .otp-digit.filled {
            border-color: #10b981;
            background: #f0fdf4;
            color: #065f46;
        }

        .otp-digit.error {
            border-color: #ef4444;
            background: #fef2f2;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .btn {
            padding: 16px 32px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.5);
        }

        .btn-primary:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
            box-shadow: 0 6px 20px rgba(107, 114, 128, 0.4);
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(107, 114, 128, 0.5);
        }

        .resend-container {
            text-align: center;
            margin-bottom: 25px;
        }

        .resend-text {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .resend-link {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .resend-link:hover {
            background: #dbeafe;
            color: #1d4ed8;
            transform: translateY(-1px);
        }

        .resend-link.disabled {
            color: #9ca3af;
            cursor: not-allowed;
            pointer-events: none;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            color: #374151;
            transform: translateX(-3px);
        }

        .back-link i {
            margin-right: 8px;
        }

        .security-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin-top: 25px;
            text-align: left;
        }

        .security-info h4 {
            color: #1d4ed8;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .security-info ul {
            list-style: none;
            padding: 0;
        }

        .security-info li {
            color: #374151;
            font-size: 14px;
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
        }

        .security-info li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #10b981;
            font-weight: bold;
        }

        .loading {
            display: none;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #6b7280;
            font-size: 14px;
        }

        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid #e5e7eb;
            border-top: 2px solid #dc2626;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 640px) {
            .otp-container {
                margin: 20px;
                padding: 30px 25px;
                max-width: 100%;
            }

            .otp-digit {
                width: 45px;
                height: 55px;
                font-size: 24px;
            }

            .otp-input-container {
                gap: 8px;
            }

            .timer-display {
                font-size: 36px;
            }

            .otp-title {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <!-- Fondo animado -->
    <div class="bg-animation">
        <div class="floating-shapes shape-1"></div>
        <div class="floating-shapes shape-2"></div>
        <div class="floating-shapes shape-3"></div>
        <div class="floating-shapes shape-4"></div>
    </div>

    <div class="otp-container">
        <div class="header-icon">
            <i class="fas fa-shield-alt"></i>
        </div>
        
        <h1 class="otp-title">Verificación de Seguridad</h1>
        <p class="otp-subtitle">
            Hemos enviado un código de verificación de 6 dígitos a tu email registrado
        </p>
        
        <div class="email-display">
            <i class="fas fa-envelope"></i>
            <?= htmlspecialchars($email ?? '') ?>
        </div>

        <!-- Timer -->
        <div class="timer-container">
            <div class="timer-label">⏱️ Tiempo restante</div>
            <div class="timer-display" id="timer">01:00</div>
        </div>

        <!-- Formulario OTP -->
        <form method="POST" action="<?= route('auth.verify.otp') ?>" id="otpForm">
            <?= CSRF() ?>
            <input type="hidden" name="email" value="<?= htmlspecialchars($email ?? '') ?>">
            
            <!-- Campos OTP -->
            <div class="otp-input-container">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                    <input type="text" 
                           class="otp-digit" 
                           maxlength="1" 
                           inputmode="numeric" 
                           pattern="[0-9]*"
                           name="otp_digit_<?= $i ?>"
                           id="digit-<?= $i ?>"
                           autocomplete="off"
                           required>
                <?php endfor; ?>
            </div>
            <input type="hidden" name="otp_code" id="otp_code">

            <div class="btn-group">
                <button type="submit" class="btn btn-primary" id="verifyBtn">
                    <i class="fas fa-check"></i>
                    Verificar Código
                </button>
                
                <div class="loading" id="loading">
                    <div class="spinner"></div>
                    <span>Verificando...</span>
                </div>
            </div>
        </form>

        <!-- Reenviar código -->
        <div class="resend-container">
            <p class="resend-text">¿No recibiste el código?</p>
            <a href="#" class="resend-link" id="resendLink" onclick="resendCode()">
                <i class="fas fa-paper-plane"></i>
                Reenviar código
            </a>
            <div id="resendTimer" class="resend-text" style="display: none;">
                Podrás solicitar un nuevo código en: <span id="resendCountdown">30</span>s
            </div>
        </div>

        <!-- Enlace de regreso -->
        <a href="<?= route('login') ?>" class="back-link">
            <i class="fas fa-arrow-left"></i>
            Volver al inicio de sesión
        </a>

        <!-- Información de seguridad -->
        <div class="security-info">
            <h4><i class="fas fa-info-circle"></i> Información de seguridad</h4>
            <ul>
                <li>Este código expira en 60 segundos</li>
                <li>Solo puede ser utilizado una vez</li>
                <li>Después de 3 intentos fallidos tu cuenta será bloqueada temporalmente</li>
                <li>Si no solicitaste este acceso, cambia tu contraseña inmediatamente</li>
            </ul>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // Configuración
        const TIMER_DURATION = <?= $timer_duration ?? 60 ?>; // Segundos
        const RESEND_COOLDOWN = 30; // Segundos
        
        // Variables globales
        let timeLeft = TIMER_DURATION;
        let timerInterval;
        let resendCooldown = 0;
        let resendInterval;
        
        // SweetAlert2 personalizado
        const customSwal = Swal.mixin({
            customClass: {
                confirmButton: 'swal-confirm-btn',
                cancelButton: 'swal-cancel-btn',
                popup: 'swal-popup'
            },
            buttonsStyling: false
        });

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            initializeOTPInputs();
            startTimer();
            showInitialMessage();
        });

        // Configurar inputs OTP
        function initializeOTPInputs() {
            const inputs = document.querySelectorAll('.otp-digit');
            
            inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => {
                    const value = e.target.value.replace(/[^0-9]/g, '');
                    e.target.value = value;
                    
                    if (value) {
                        e.target.classList.add('filled');
                        // Mover al siguiente input
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    } else {
                        e.target.classList.remove('filled');
                    }
                    
                    updateOTPCode();
                    validateForm();
                });
                
                input.addEventListener('keydown', (e) => {
                    // Backspace: mover al input anterior
                    if (e.key === 'Backspace' && !e.target.value && index > 0) {
                        inputs[index - 1].focus();
                    }
                    
                    // Enter: enviar formulario si está completo
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (isFormValid()) {
                            submitForm();
                        }
                    }
                });
                
                input.addEventListener('paste', (e) => {
                    e.preventDefault();
                    const pasteData = (e.clipboardData || window.clipboardData).getData('text');
                    const digits = pasteData.replace(/[^0-9]/g, '').substr(0, 6);
                    
                    if (digits.length === 6) {
                        inputs.forEach((inp, idx) => {
                            if (idx < digits.length) {
                                inp.value = digits[idx];
                                inp.classList.add('filled');
                            }
                        });
                        updateOTPCode();
                        validateForm();
                        inputs[5].focus();
                    }
                });
            });
            
            // Focus en el primer input
            inputs[0].focus();
        }

        // Actualizar código OTP oculto
        function updateOTPCode() {
            const inputs = document.querySelectorAll('.otp-digit');
            let code = '';
            inputs.forEach(input => code += input.value);
            document.getElementById('otp_code').value = code;
        }

        // Validar formulario
        function isFormValid() {
            const code = document.getElementById('otp_code').value;
            return code.length === 6 && /^\d{6}$/.test(code);
        }

        function validateForm() {
            const verifyBtn = document.getElementById('verifyBtn');
            const isValid = isFormValid();
            verifyBtn.disabled = !isValid || timeLeft <= 0;
        }

        // Enviar formulario
        function submitForm() {
            if (!isFormValid() || timeLeft <= 0) return;
            
            document.getElementById('verifyBtn').style.display = 'none';
            document.getElementById('loading').style.display = 'flex';
            
            // Enviar formulario
            document.getElementById('otpForm').submit();
        }

        // Timer countdown
        function startTimer() {
            const timerDisplay = document.getElementById('timer');
            
            timerInterval = setInterval(() => {
                timeLeft--;
                
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                const display = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
                
                timerDisplay.textContent = display;
                
                if (timeLeft <= 10) {
                    timerDisplay.classList.add('timer-expired');
                }
                
                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    handleTimerExpired();
                }
                
                validateForm();
            }, 1000);
        }

        // Manejar expiración del timer
        function handleTimerExpired() {
            document.getElementById('timer').textContent = '00:00';
            document.getElementById('verifyBtn').disabled = true;
            document.querySelectorAll('.otp-digit').forEach(input => {
                input.disabled = true;
                input.classList.add('error');
            });
            
            customSwal.fire({
                icon: 'error',
                title: '⏰ Código Expirado',
                text: 'El código de verificación ha expirado. Debes solicitar uno nuevo.',
                confirmButtonText: 'Solicitar Nuevo Código',
                background: '#1f2937',
                color: '#fff'
            }).then(() => {
                resendCode();
            });
        }

        // Reenviar código
        function resendCode() {
            if (resendCooldown > 0) return;
            
            // Mostrar loading
            customSwal.fire({
                title: 'Enviando código...',
                text: 'Por favor espera mientras generamos un nuevo código',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Realizar petición AJAX
            fetch('<?= route("auth.resend.otp") ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    email: '<?= htmlspecialchars($email ?? '') ?>',
                    _token: document.querySelector('input[name="_token"]').value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Resetear timer
                    clearInterval(timerInterval);
                    timeLeft = TIMER_DURATION;
                    document.getElementById('timer').classList.remove('timer-expired');
                    
                    // Rehabilitar inputs
                    document.querySelectorAll('.otp-digit').forEach(input => {
                        input.disabled = false;
                        input.classList.remove('error');
                        input.value = '';
                        input.classList.remove('filled');
                    });
                    
                    // Resetear formulario
                    document.getElementById('otp_code').value = '';
                    document.getElementById('verifyBtn').disabled = true;
                    document.getElementById('verifyBtn').style.display = 'block';
                    document.getElementById('loading').style.display = 'none';
                    
                    // Reiniciar timer
                    startTimer();
                    
                    // Iniciar cooldown de reenvío
                    startResendCooldown();
                    
                    // Mostrar éxito
                    customSwal.fire({
                        icon: 'success',
                        title: '📧 Código Enviado',
                        text: 'Te hemos enviado un nuevo código de verificación a tu email.',
                        timer: 3000,
                        background: '#1f2937',
                        color: '#fff'
                    });
                    
                    // Focus en primer input
                    document.getElementById('digit-1').focus();
                } else {
                    customSwal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'No se pudo enviar el código. Intenta de nuevo.',
                        background: '#1f2937',
                        color: '#fff'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                customSwal.fire({
                    icon: 'error',
                    title: 'Error de Conexión',
                    text: 'Hubo un problema al enviar el código. Verifica tu conexión.',
                    background: '#1f2937',
                    color: '#fff'
                });
            });
        }

        // Cooldown para reenvío
        function startResendCooldown() {
            resendCooldown = RESEND_COOLDOWN;
            const resendLink = document.getElementById('resendLink');
            const resendTimer = document.getElementById('resendTimer');
            const countdown = document.getElementById('resendCountdown');
            
            resendLink.style.display = 'none';
            resendTimer.style.display = 'block';
            
            resendInterval = setInterval(() => {
                resendCooldown--;
                countdown.textContent = resendCooldown;
                
                if (resendCooldown <= 0) {
                    clearInterval(resendInterval);
                    resendLink.style.display = 'inline-block';
                    resendTimer.style.display = 'none';
                }
            }, 1000);
        }

        // Mostrar mensaje inicial
        function showInitialMessage() {
            customSwal.fire({
                icon: 'info',
                title: '🔐 Verificación Requerida',
                text: 'Te hemos enviado un código de 6 dígitos a tu email. Tienes 60 segundos para ingresarlo.',
                timer: 4000,
                timerProgressBar: true,
                background: '#1f2937',
                color: '#fff',
                showConfirmButton: false
            });
        }

        // Manejar envío del formulario
        document.getElementById('otpForm').addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm();
        });

        // Mostrar errores del servidor
        <?php 
        $errors = flashGet('errors') ?? [];
        $error = flashGet('error');
        $success = flashGet('success');
        ?>
        
        <?php if (!empty($errors) || $error): ?>
            setTimeout(() => {
                customSwal.fire({
                    icon: 'error',
                    title: '❌ Error de Verificación',
                    text: '<?= addslashes($error ?? 'Código OTP inválido o expirado. Intenta nuevamente.') ?>',
                    confirmButtonText: 'Entendido',
                    background: '#1f2937',
                    color: '#fff'
                }).then(() => {
                    // Limpiar campos en caso de error
                    document.querySelectorAll('.otp-digit').forEach(input => {
                        input.value = '';
                        input.classList.remove('filled');
                        input.classList.add('error');
                        setTimeout(() => input.classList.remove('error'), 500);
                    });
                    document.getElementById('otp_code').value = '';
                    document.getElementById('digit-1').focus();
                });
            }, 500);
        <?php endif; ?>

        <?php if ($success): ?>
            customSwal.fire({
                icon: 'success',
                title: '✅ ¡Verificación Exitosa!',
                text: '<?= addslashes($success) ?>',
                timer: 2000,
                background: '#1f2937',
                color: '#fff'
            });
        <?php endif; ?>

        // Prevenir clic derecho y selección
        document.addEventListener('contextmenu', e => e.preventDefault());
        document.addEventListener('selectstart', e => e.preventDefault());
        
        console.log('🔐 OTP Verification page loaded');
        console.log('⏱️ Timer duration:', TIMER_DURATION, 'seconds');
    </script>

    <!-- Estilos personalizados para SweetAlert2 -->
    <style>
        .swal-popup {
            border-radius: 15px !important;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3) !important;
        }
        
        .swal-confirm-btn {
            background: linear-gradient(45deg, #dc2626, #b91c1c) !important;
            color: white !important;
            border: none !important;
            border-radius: 8px !important;
            padding: 12px 24px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            margin: 0 5px !important;
            transition: all 0.3s ease !important;
        }
        
        .swal-confirm-btn:hover {
            background: linear-gradient(45deg, #b91c1c, #991b1b) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 5px 15px rgba(220, 38, 38, 0.4) !important;
        }
    </style>
</body>

</html>