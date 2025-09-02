<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación 2FA - Tech Home Bolivia</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS del sistema admin -->
    <link rel="stylesheet" href="<?= asset('css/admin/admin.css') ?>">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.min.css">
    
    <!-- Headers anti-cache -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <style>
        /* Root variables override para asegurar los colores */
        :root {
            --primary-red: #dc2626;
            --primary-red-light: #ef4444;
            --primary-red-dark: #b91c1c;
            --text-dark: #1f2937;
            --text-light: #f9fafb;
            --border-radius: 20px;
        }
        
        /* Estilos específicos solo para OTP usando variables del admin.css */
        .otp-page-container {
            background: linear-gradient(135deg, var(--primary-red) 0%, #991b1b 30%, #7f1d1d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 20px 10px;
        }

        .floating-shapes {
            position: absolute;
            border-radius: 50%;
            opacity: 0.15;
            animation: float 8s ease-in-out infinite;
        }

        .shape-1 {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 80px;
            height: 80px;
            background: rgba(239, 68, 68, 0.3);
            top: 60%;
            right: 15%;
            animation-delay: 2s;
        }

        .shape-3 {
            width: 100px;
            height: 100px;
            background: rgba(254, 202, 202, 0.4);
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        .shape-4 {
            width: 60px;
            height: 60px;
            background: rgba(185, 28, 28, 0.25);
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

        .otp-digit {
            width: 60px;
            height: 70px;
            border: 2px solid rgba(107, 114, 128, 0.2);
            border-radius: 12px;
            text-align: center;
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-family: 'Courier New', monospace;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            line-height: 1;
        }

        .otp-digit:focus {
            outline: none;
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.95);
        }

        .otp-digit.filled {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.1);
            color: #065f46;
        }

        .otp-digit.error {
            border-color: #ef4444;
            background: rgba(239, 68, 68, 0.1);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .otp-input-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        /* Clases de estructura y espaciado */
        .form-section {
            margin-bottom: 25px;
        }

        .button-section {
            margin-bottom: 30px;
        }

        .links-section {
            margin-bottom: 25px;
        }

        .security-section {
            margin-top: 30px;
        }

        .header-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .timer-section {
            margin-bottom: 30px;
        }

        .timer-expired {
            color: #dc2626 !important;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0.5; }
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

        /* Responsive para inputs OTP */
        @media (max-width: 640px) {
            .otp-page-container {
                padding: 10px 5px;
                align-items: flex-start;
                padding-top: 20px;
            }
            
            .otp-main-card {
                padding: 30px 20px;
                max-height: none;
                margin: 0;
            }
            
            .otp-digit {
                width: 45px;
                height: 55px;
                font-size: 24px;
            }
            
            .otp-input-container {
                gap: 8px;
            }
            
            .timer-container-custom {
                padding: 15px;
            }
            
            .timer-container-custom div[style*="font-size: 48px"] {
                font-size: 36px !important;
            }
            
            .header-section h1 {
                font-size: 24px !important;
            }
        }

        @media (max-width: 480px) {
            .otp-digit {
                width: 40px;
                height: 50px;
                font-size: 20px;
            }
            
            .otp-input-container {
                gap: 6px;
            }
        }

        /* SweetAlert2 personalizado */
        .swal-popup {
            border-radius: 20px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2) !important;
        }
        
        .swal-confirm-btn {
            background: linear-gradient(135deg, #dc2626, #ef4444) !important;
            color: white !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 12px 24px !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            margin: 0 5px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        
        .swal-confirm-btn:hover {
            background: linear-gradient(135deg, #b91c1c, #dc2626) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(220, 38, 38, 0.3) !important;
        }

        /* Clases adicionales para elementos específicos */
        .otp-main-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 40px;
            width: 100%;
            max-width: 500px;
            position: relative;
            z-index: 10;
            margin: auto;
        }
        
        .code-display {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            padding: 16px 24px;
            border-radius: 12px;
            border: 2px dashed #9ca3af;
            display: inline-block;
        }

        .icon-container i {
            color: var(--primary-red) !important;
            filter: drop-shadow(0 4px 8px rgba(220, 38, 38, 0.3));
            animation: pulse 2s infinite;
        }

        .timer-container-custom {
            background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%);
            border: 2px solid #f59e0b;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .timer-container-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent 49%, rgba(255, 255, 255, 0.1) 50%, transparent 51%);
            animation: shimmer 2s infinite;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-red), var(--primary-red-light)) !important;
            color: white !important;
            border: none !important;
            border-radius: 12px !important;
            padding: 16px 32px !important;
            font-weight: 700 !important;
            font-size: 16px !important;
            width: 100% !important;
            text-transform: uppercase !important;
            letter-spacing: 1px !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            box-shadow: 0 8px 20px rgba(220, 38, 38, 0.4) !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .btn-primary-custom::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn-primary-custom:hover::before {
            left: 100%;
        }

        .btn-primary-custom:hover {
            background: linear-gradient(135deg, var(--primary-red-dark), var(--primary-red)) !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 12px 30px rgba(220, 38, 38, 0.5) !important;
        }

        .btn-primary-custom:disabled {
            background: #9ca3af !important;
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
    </style>
</head>

<body class="otp-page-container">
    <!-- Fondo animado -->
    <div class="bg-animation">
        <div class="floating-shapes shape-1"></div>
        <div class="floating-shapes shape-2"></div>
        <div class="floating-shapes shape-3"></div>
        <div class="floating-shapes shape-4"></div>
    </div>

    <div class="otp-main-card">
        <!-- Sección de encabezado -->
        <div class="header-section">
            <div class="icon-container" style="margin-bottom: 20px;">
                <i class="fas fa-shield-alt" style="font-size: 64px;"></i>
            </div>
            
            <h1 style="color: var(--text-dark); font-size: 28px; font-weight: 700; margin-bottom: 15px;">Verificación de Seguridad</h1>
            <p style="color: #6b7280; margin-bottom: 25px; line-height: 1.6; font-size: 16px;">
                Hemos enviado un código de verificación de 6 dígitos a tu email registrado
            </p>
            
            <div class="code-display">
                <i class="fas fa-envelope" style="color: var(--primary-red); margin-right: 8px;"></i>
                <span style="font-family: 'Courier New', monospace; font-weight: 600; color: #374151;"><?= htmlspecialchars($email ?? '') ?></span>
            </div>
        </div>

        <!-- Sección del timer -->
        <div class="timer-section">
            <div class="timer-container-custom">
                <div style="color: #92400e; font-weight: bold; margin-bottom: 10px; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; position: relative; z-index: 2;">
                    ⏱️ Tiempo restante
                </div>
                <div style="font-size: 48px; font-weight: bold; color: var(--primary-red); font-family: 'Courier New', monospace; position: relative; z-index: 2;" id="timer">01:00</div>
            </div>
        </div>

        <!-- Sección del formulario -->
        <div class="form-section">
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
            </form>
        </div>

        <!-- Sección del botón -->
        <div class="button-section">
            <button type="submit" class="btn-primary-custom" id="verifyBtn" form="otpForm">
                <i class="fas fa-check" style="margin-right: 8px;"></i>
                Verificar Código
            </button>
            
            <div class="loading" id="loading" style="margin-top: 15px;">
                <div class="spinner"></div>
                <span>Verificando...</span>
            </div>
        </div>

        <!-- Sección de enlaces -->
        <div class="links-section">
            <!-- Reenviar código -->
            <div style="text-align: center; margin-bottom: 20px;">
                <p style="color: #6b7280; font-size: 14px; margin-bottom: 10px;">¿No recibiste el código?</p>
                <a href="#" style="color: var(--primary-red); text-decoration: none; font-weight: 600; padding: 8px 16px; border-radius: 8px; transition: all 0.3s ease; display: inline-block;" id="resendLink" onclick="resendCode()">
                    <i class="fas fa-paper-plane" style="margin-right: 8px;"></i>
                    Reenviar código
                </a>
                <div id="resendTimer" style="color: #6b7280; font-size: 14px; margin-top: 8px; display: none;">
                    Podrás solicitar un nuevo código en: <span id="resendCountdown" style="font-family: 'Courier New', monospace; font-weight: bold;">30</span>s
                </div>
            </div>

            <!-- Enlace de regreso -->
            <div style="text-align: center;">
                <a href="<?= route('login') ?>" style="display: inline-flex; align-items: center; color: #6b7280; text-decoration: none; font-size: 14px; transition: all 0.3s ease;">
                    <i class="fas fa-arrow-left" style="margin-right: 8px;"></i>
                    Volver al inicio de sesión
                </a>
            </div>
        </div>

        <!-- Sección de información de seguridad -->
        <div class="security-section">
            <div style="background: rgba(220, 38, 38, 0.05); border: 1px solid rgba(220, 38, 38, 0.2); border-radius: 12px; padding: 20px; text-align: left;">
                <h4 style="color: var(--primary-red); font-weight: 600; margin-bottom: 15px; display: flex; align-items: center;">
                    <i class="fas fa-info-circle" style="margin-right: 8px;"></i> 
                    Información de seguridad
                </h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    <li style="color: #374151; font-size: 14px; margin-bottom: 8px; display: flex; align-items: flex-start;">
                        <span style="color: #10b981; font-weight: bold; margin-right: 12px; margin-top: 2px;">✓</span>
                        Este código expira en 60 segundos
                    </li>
                    <li style="color: #374151; font-size: 14px; margin-bottom: 8px; display: flex; align-items: flex-start;">
                        <span style="color: #10b981; font-weight: bold; margin-right: 12px; margin-top: 2px;">✓</span>
                        Solo puede ser utilizado una vez
                    </li>
                    <li style="color: #374151; font-size: 14px; margin-bottom: 8px; display: flex; align-items: flex-start;">
                        <span style="color: #10b981; font-weight: bold; margin-right: 12px; margin-top: 2px;">✓</span>
                        Después de 3 intentos fallidos tu cuenta será bloqueada temporalmente
                    </li>
                    <li style="color: #374151; font-size: 14px; margin-bottom: 0; display: flex; align-items: flex-start;">
                        <span style="color: #10b981; font-weight: bold; margin-right: 12px; margin-top: 2px;">✓</span>
                        Si no solicitaste este acceso, cambia tu contraseña inmediatamente
                    </li>
                </ul>
            </div>
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
</body>

</html>