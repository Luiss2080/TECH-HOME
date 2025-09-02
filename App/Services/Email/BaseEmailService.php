<?php

namespace App\Services\Email;

abstract class BaseEmailService implements MailServiceInterface
{
    protected array $config = [];

    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * Cargar configuración desde el entorno
     */
    protected function loadConfig(): void
    {
        $this->config = [
            'smtp_host' => $_ENV['MAIL_HOST'] ?? 'localhost',
            'smtp_port' => $_ENV['MAIL_PORT'] ?? 587,
            'smtp_username' => $_ENV['MAIL_USERNAME'] ?? '',
            'smtp_password' => $_ENV['MAIL_PASSWORD'] ?? '',
            'from_email' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@example.com',
            'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Sistema',
            'app_url' => $_ENV['APP_URL'] ?? 'http://localhost'
        ];
    }

    /**
     * Método abstracto que debe implementar cada servicio
     */
    abstract protected function sendMail(string $to, string $subject, string $body): bool;

    /**
     * Implementación común de sendEmail
     */
    public function sendEmail(string $to, string $subject, string $body): bool
    {
        return $this->sendMail($to, $subject, $body);
    }

    /**
     * Enviar email de recuperación de contraseña
     */
    public function sendPasswordResetEmail(string $email, string $token): bool
    {
        $resetUrl = $this->config['app_url'] . "/reset-password?token=" . urlencode($token);
        
        $subject = 'Recuperar Contraseña - ' . $this->config['from_name'];
        
        // Obtener duración configurable del token
        $tokenExpirationMinutes = $_ENV['PASSWORD_RESET_TOKEN_EXPIRATION_MINUTES'] ?? 15;
        
        $body = "
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <style>
                /* Reset y base */
                * { margin: 0; padding: 0; box-sizing: border-box; }
                
                body { 
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Arial, sans-serif; 
                    line-height: 1.6; 
                    color: #1f2937;
                    background-color: #f8fafc;
                    margin: 0;
                    padding: 20px;
                }
                
                table { border-collapse: collapse; width: 100%; }
                
                .email-container { 
                    max-width: 600px; 
                    margin: 0 auto; 
                    background-color: #ffffff;
                    border-radius: 20px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
                }
                
                .header-section { 
                    background: linear-gradient(135deg, #dc2626 0%, #ef4444 50%, #b91c1c 100%);
                    color: #ffffff; 
                    padding: 40px 30px; 
                    text-align: center;
                }
                
                .header-section h1 {
                    font-size: 32px;
                    font-weight: 800;
                    margin: 0 0 8px 0;
                    color: #ffffff;
                }
                
                .header-section p {
                    font-size: 16px;
                    font-weight: 500;
                    margin: 0;
                    color: #ffffff;
                    opacity: 0.95;
                }
                
                .content-section { 
                    padding: 40px 30px;
                    background-color: #ffffff;
                }
                
                .content-section h2 {
                    color: #1f2937;
                    font-size: 28px;
                    font-weight: 800;
                    margin: 0 0 20px 0;
                    text-align: center;
                }
                
                .content-section p {
                    color: #6b7280;
                    font-size: 16px;
                    margin: 0 0 16px 0;
                    text-align: center;
                    line-height: 1.6;
                }
                
                .button-container {
                    text-align: center;
                    margin: 30px 0;
                }
                
                .reset-button { 
                    display: inline-block; 
                    background: linear-gradient(135deg, #dc2626, #ef4444);
                    color: #ffffff !important; 
                    padding: 18px 36px; 
                    text-decoration: none !important; 
                    border-radius: 12px; 
                    font-weight: 700;
                    font-size: 16px;
                    border: none;
                    box-shadow: 0 8px 25px rgba(220, 38, 38, 0.3);
                    transition: all 0.3s ease;
                }
                
                .reset-button:hover { 
                    background: linear-gradient(135deg, #b91c1c, #dc2626);
                    color: #ffffff !important;
                    text-decoration: none !important;
                    box-shadow: 0 12px 35px rgba(220, 38, 38, 0.4);
                    transform: translateY(-2px);
                }
                
                .warning-box { 
                    background-color: #fffbeb;
                    border: 2px solid #fbbf24;
                    border-left: 4px solid #f59e0b;
                    border-radius: 12px;
                    padding: 20px; 
                    margin: 30px 0;
                    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.1);
                }
                
                .warning-box strong {
                    color: #f59e0b;
                    font-weight: 700;
                    font-size: 16px;
                    display: block;
                    margin-bottom: 12px;
                }
                
                .warning-box ul {
                    margin: 12px 0 0 20px;
                    padding: 0;
                    list-style-type: disc;
                }
                
                .warning-box li {
                    color: #6b7280;
                    margin-bottom: 8px;
                    font-weight: 500;
                    line-height: 1.5;
                }
                
                .warning-box li strong {
                    color: #1f2937;
                    display: inline;
                    margin: 0;
                    font-size: inherit;
                }
                
                .url-container {
                    background-color: #f1f5f9;
                    border: 2px solid #e2e8f0;
                    border-radius: 12px;
                    padding: 16px;
                    margin: 20px 0;
                    word-break: break-all;
                    font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
                    font-size: 14px;
                    color: #1f2937;
                    text-align: left;
                }
                
                .footer-section { 
                    background-color: #f8fafc;
                    border-top: 1px solid #e2e8f0;
                    text-align: center; 
                    color: #6b7280; 
                    font-size: 14px; 
                    padding: 30px;
                }
                
                .footer-section p {
                    margin: 8px 0;
                    font-weight: 500;
                    color: #6b7280;
                }
                
                /* Enlaces */
                a { 
                    color: #dc2626;
                    text-decoration: none;
                    font-weight: 600;
                }
                
                a:hover {
                    color: #b91c1c;
                    text-decoration: underline;
                }
                
                a.reset-button { 
                    color: #ffffff !important; 
                    text-decoration: none !important;
                }
                
                /* Responsive */
                @media only screen and (max-width: 600px) {
                    body { padding: 10px; }
                    
                    .email-container { 
                        border-radius: 16px;
                        margin: 0;
                    }
                    
                    .header-section { 
                        padding: 30px 20px; 
                    }
                    
                    .header-section h1 { 
                        font-size: 28px; 
                    }
                    
                    .content-section { 
                        padding: 30px 20px; 
                    }
                    
                    .content-section h2 { 
                        font-size: 24px; 
                    }
                    
                    .reset-button { 
                        padding: 16px 28px; 
                        font-size: 15px; 
                    }
                    
                    .footer-section { 
                        padding: 20px; 
                    }
                    
                    .warning-box {
                        padding: 16px;
                        margin: 20px 0;
                    }
                    
                    .url-container {
                        padding: 12px;
                        font-size: 12px;
                    }
                }
            </style>
        </head>
        <body>
            <table role='presentation' cellspacing='0' cellpadding='0' border='0' width='100%'>
                <tr>
                    <td align='center' style='padding: 20px;'>
                        <table role='presentation' cellspacing='0' cellpadding='0' border='0' class='email-container'>
                            <!-- Header -->
                            <tr>
                                <td class='header-section'>
                                    <h1>Tech Home Bolivia</h1>
                                    <p>Instituto de Robótica y Tecnología Avanzada</p>
                                </td>
                            </tr>
                            
                            <!-- Contenido Principal -->
                            <tr>
                                <td class='content-section'>
                                    <h2>Recuperación de Contraseña</h2>
                                    <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.</p>
                                    <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>
                                    
                                    <div class='button-container'>
                                        <a href='$resetUrl' class='reset-button'>Restablecer Contraseña</a>
                                    </div>
                                    
                                    <div class='warning-box'>
                                        <strong>⚠️ Importante:</strong>
                                        <ul>
                                            <li>Este enlace expirará en <strong>$tokenExpirationMinutes minutos</strong></li>
                                            <li>Solo puede ser usado una vez</li>
                                            <li>Si no solicitaste este cambio, ignora este email</li>
                                        </ul>
                                    </div>
                                    
                                    <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
                                    <div class='url-container'>$resetUrl</div>
                                </td>
                            </tr>
                            
                            <!-- Footer -->
                            <tr>
                                <td class='footer-section'>
                                    <p>Este es un email automático, por favor no responder.</p>
                                    <p>&copy; " . date('Y') . " Tech Home Bolivia. Todos los derechos reservados.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";
        
        return $this->sendMail($email, $subject, $body);
    }

    /**
     * Enviar email de bienvenida con token de activación
     */
    public function sendWelcomeEmail($user, string $token): bool
    {
        $subject = 'Bienvenido a Tech Home Bolivia - Activa tu cuenta';
        
        $activationUrl = $this->config['app_url'] . "/account/activation?token=" . urlencode($token);
        
        $body = "
        <html>
        <head>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 650px; margin: 0 auto; background: white; }
                .header { 
                    background: linear-gradient(135deg, #dc2626 0%, #991b1b 50%, #1f2937 100%); 
                    color: white; 
                    padding: 40px 30px; 
                    text-align: center; 
                }
                .header h1 { margin: 0; font-size: 28px; font-weight: bold; }
                .header p { margin: 10px 0 0 0; opacity: 0.9; }
                .content { padding: 40px 30px; background: #f8f9fa; }
                .welcome-box { 
                    background: white; 
                    padding: 30px; 
                    border-radius: 12px; 
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    margin-bottom: 30px;
                }
                .button { 
                    display: inline-block; 
                    background: #dc2626; 
                    color: white !important; 
                    padding: 16px 32px; 
                    text-decoration: none !important; 
                    border-radius: 10px; 
                    margin: 15px 10px; 
                    font-weight: bold;
                    font-size: 16px;
                    border: none;
                    box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
                    transition: all 0.3s ease;
                }
                .button:hover { 
                    background: #b91c1c; 
                    color: white !important;
                    text-decoration: none !important;
                    box-shadow: 0 6px 20px rgba(220, 38, 38, 0.4);
                    transform: translateY(-2px);
                }
                .button.secondary {
                    background: #3498db;
                    box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
                }
                .button.secondary:hover {
                    background: #2980b9;
                    box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
                }
                .features { display: flex; flex-wrap: wrap; margin: 20px 0; }
                .feature { 
                    flex: 1; 
                    min-width: 200px; 
                    margin: 10px; 
                    padding: 20px; 
                    background: white; 
                    border-radius: 8px;
                    text-align: center;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                }
                .feature-icon { font-size: 32px; margin-bottom: 15px; }
                .feature h3 { color: #dc2626; margin: 10px 0; }
                .footer { text-align: center; color: #666; font-size: 14px; padding: 30px; background: #2c3e50; color: white; }
                .footer a { color: #3498db; text-decoration: none; }
                .info-box { background: #e8f4f8; border-left: 4px solid #3498db; padding: 20px; margin: 20px 0; border-radius: 5px; }
                .social-links { text-align: center; margin: 20px 0; }
                .social-links a { 
                    display: inline-block; 
                    margin: 0 10px; 
                    padding: 10px 15px; 
                    background: #34495e; 
                    color: white !important; 
                    text-decoration: none !important; 
                    border-radius: 25px;
                    font-size: 14px;
                }
                @media (max-width: 600px) {
                    .features { flex-direction: column; }
                    .feature { margin: 5px 0; }
                    .button { display: block; margin: 10px 0; }
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🤖 Tech Home Bolivia</h1>
                    <p>Instituto de Robótica y Tecnología Avanzada</p>
                </div>
                
                <div class='content'>
                    <div class='welcome-box'>
                        <h2 style='color: #dc2626; margin-top: 0;'>¡Bienvenido, " . htmlspecialchars($user->nombre) . "! 🎉</h2>
                        <p style='font-size: 18px; color: #555;'>
                            Tu cuenta en Tech Home Bolivia ha sido creada exitosamente. Nos complace darte la bienvenida a nuestra comunidad de innovadores y tecnólogos.
                        </p>
                        <p><strong>Email registrado:</strong> " . htmlspecialchars($user->email) . "</p>
                        <p><strong>Rol asignado:</strong> Invitado (acceso completo por tiempo limitado)</p>
                    </div>

                    <div class='activation-box'>
                        <h3 style='color: #856404; margin-top: 0;'>🔑 ¡Importante! Activa tu cuenta</h3>
                        <p style='margin-bottom: 20px;'>
                            Para acceder a todo nuestro contenido y comenzar a aprender, necesitas activar tu cuenta haciendo clic en el siguiente botón:
                        </p>
                        <a href='$activationUrl' class='button activation'>✅ ACTIVAR MI CUENTA</a>
                        <p style='font-size: 14px; color: #6c757d; margin-top: 15px;'>
                            Este enlace es válido y solo se puede usar una vez.
                        </p>
                    </div>

                    <div class='warning-box'>
                        <h3 style='margin-top: 0;'>⚠️ Nota importante:</h3>
                        <p>Tu cuenta está actualmente <strong>inactiva</strong>. Una vez que actives tu cuenta:</p>
                        <ul style='text-align: left; margin: 0; padding-left: 20px;'>
                            <li>✅ Podrás acceder a todos nuestros cursos de robótica</li>
                            <li>✅ Descargar libros y materiales educativos</li>
                            <li>✅ Participar en laboratorios virtuales</li>
                            <li>✅ Recibir certificaciones</li>
                            <li>✅ Unirte a nuestra comunidad de aprendizaje</li>
                        </ul>
                    </div>

                    <p style='text-align: center; color: #6c757d;'>
                        Si el botón de activación no funciona, copia y pega este enlace en tu navegador:<br>
                        <span style='word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 3px; display: inline-block; margin-top: 10px;'>$activationUrl</span>
                    </p>
                </div>
                
                <div class='footer'>
                    <h3 style='color: white; margin-top: 0;'>¡Síguenos en nuestras redes sociales!</h3>
                    <div class='social-links'>
                        <a href='#'>📱 TikTok</a>
                        <a href='#'>📘 Facebook</a>
                        <a href='#'>📸 Instagram</a>
                        <a href='#'>💬 WhatsApp</a>
                    </div>
                    <p style='margin-top: 20px;'>
                        📧 ¿Tienes preguntas? Contáctanos: info@techhomebolivia.com<br>
                        📞 Teléfono: +591 123 456 789
                    </p>
                    <p style='font-size: 12px; opacity: 0.8;'>
                        © " . date('Y') . " Tech Home Bolivia. Todos los derechos reservados.<br>
                        Este es un email automático, por favor no responder directamente.
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendMail($user->email, $subject, $body);
    }

    /**
     * Enviar código OTP por email
     */
    public function sendOTPEmail(string $email, string $codigo, string $nombreUsuario = '', int $expirationMinutes = 1): bool
    {
        $subject = '🔐 Código de Verificación - Tech Home Bolivia';
        
        $body = "
        <html>
        <head>
            <style>
                body { 
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
                    line-height: 1.6; 
                    color: #333; 
                    margin: 0; 
                    padding: 0; 
                    background: #f8f9fa;
                }
                .container { 
                    max-width: 600px; 
                    margin: 20px auto; 
                    background: white; 
                    border-radius: 15px;
                    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
                    overflow: hidden;
                }
                .header { 
                    background: linear-gradient(135deg, #dc2626 0%, #991b1b 50%, #1f2937 100%); 
                    color: white; 
                    padding: 30px; 
                    text-align: center; 
                    position: relative;
                }
                .header::before {
                    content: '🔐';
                    font-size: 48px;
                    display: block;
                    margin-bottom: 15px;
                }
                .header h1 { 
                    margin: 0; 
                    font-size: 24px; 
                    font-weight: bold; 
                    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
                }
                .header p { 
                    margin: 8px 0 0 0; 
                    opacity: 0.9; 
                    font-size: 16px;
                }
                .content { 
                    padding: 40px 30px; 
                    background: white;
                    text-align: center;
                }
                .otp-container {
                    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                    border: 3px dashed #dc2626;
                    border-radius: 20px;
                    padding: 30px;
                    margin: 30px 0;
                    position: relative;
                    box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
                }
                .otp-label {
                    background: white;
                    color: #dc2626;
                    padding: 8px 20px;
                    border-radius: 25px;
                    font-weight: bold;
                    font-size: 14px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    display: inline-block;
                    margin-bottom: 20px;
                    border: 2px solid #dc2626;
                }
                .otp-code {
                    font-size: 48px;
                    font-weight: bold;
                    color: #dc2626;
                    background: white;
                    padding: 20px 30px;
                    border-radius: 15px;
                    box-shadow: 0 8px 25px rgba(220, 38, 38, 0.2);
                    letter-spacing: 8px;
                    font-family: 'Courier New', monospace;
                    border: 3px solid #dc2626;
                    display: inline-block;
                    margin: 15px 0;
                    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    animation: pulse 2s infinite;
                }
                @keyframes pulse {
                    0% { transform: scale(1); }
                    50% { transform: scale(1.05); }
                    100% { transform: scale(1); }
                }
                .timer-container {
                    background: #fff3cd;
                    border: 2px solid #ffc107;
                    border-radius: 12px;
                    padding: 20px;
                    margin: 25px 0;
                    position: relative;
                }
                .timer-container::before {
                    content: '⏰';
                    font-size: 24px;
                    position: absolute;
                    top: -12px;
                    left: 20px;
                    background: white;
                    padding: 0 10px;
                }
                .timer-text {
                    color: #856404;
                    font-weight: bold;
                    margin: 0;
                    font-size: 16px;
                }
                .timer-countdown {
                    font-size: 32px;
                    color: #dc2626;
                    font-weight: bold;
                    margin: 10px 0;
                    font-family: 'Courier New', monospace;
                }
                .instructions {
                    background: #e8f4f8;
                    border-left: 5px solid #3498db;
                    padding: 25px;
                    margin: 25px 0;
                    border-radius: 0 10px 10px 0;
                    text-align: left;
                }
                .instructions h3 {
                    color: #2980b9;
                    margin-top: 0;
                    font-size: 18px;
                }
                .instructions ol {
                    margin: 15px 0;
                    padding-left: 20px;
                }
                .instructions li {
                    margin: 8px 0;
                    font-size: 15px;
                    line-height: 1.5;
                }
                .security-notice {
                    background: #f8d7da;
                    border: 2px solid #dc3545;
                    border-radius: 10px;
                    padding: 20px;
                    margin: 25px 0;
                    color: #721c24;
                }
                .security-notice h4 {
                    margin-top: 0;
                    color: #dc3545;
                    font-size: 16px;
                }
                .security-notice ul {
                    margin: 10px 0;
                    text-align: left;
                    padding-left: 20px;
                }
                .footer { 
                    text-align: center; 
                    color: #666; 
                    font-size: 14px; 
                    padding: 30px; 
                    background: #2c3e50; 
                    color: white; 
                }
                .footer p {
                    margin: 5px 0;
                }
                .highlight {
                    background: linear-gradient(120deg, #f093fb 0%, #f5576c 100%);
                    color: white;
                    padding: 3px 8px;
                    border-radius: 5px;
                    font-weight: bold;
                }
                .welcome-text {
                    font-size: 18px;
                    color: #555;
                    margin-bottom: 30px;
                }
                .device-info {
                    background: #f8f9fa;
                    border-radius: 8px;
                    padding: 15px;
                    margin: 20px 0;
                    font-size: 14px;
                    color: #6c757d;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Verificación de Seguridad</h1>
                    <p>Tech Home Bolivia - Sistema 2FA</p>
                </div>
                
                <div class='content'>
                    <div class='welcome-text'>
                        " . ($nombreUsuario ? "¡Hola <strong>" . htmlspecialchars($nombreUsuario) . "</strong>!" : "¡Hola!") . "
                        <br>Se ha solicitado acceso a tu cuenta.
                    </div>

                    <div class='otp-container'>
                        <div class='otp-label'>Tu Código de Verificación</div>
                        <div class='otp-code'>" . $codigo . "</div>
                        <p style='color: #6c757d; font-size: 14px; margin: 15px 0 0 0;'>
                            ⚡ Usa este código para completar tu inicio de sesión
                        </p>
                    </div>

                    <div class='timer-container'>
                        <p class='timer-text'>⏱️ Este código expira en:</p>
                        <div class='timer-countdown'>" . $expirationMinutes . " minuto" . ($expirationMinutes > 1 ? 's' : '') . "</div>
                        <p style='margin: 0; font-size: 14px; color: #856404;'>
                            Fecha de expiración: " . date('d/m/Y H:i:s', time() + ($expirationMinutes * 60)) . "
                        </p>
                    </div>

                    <div class='instructions'>
                        <h3>📝 Instrucciones de uso:</h3>
                        <ol>
                            <li><strong>Regresa a la página de inicio de sesión</strong> en tu navegador</li>
                            <li><strong>Ingresa el código de 6 dígitos</strong> exactamente como se muestra arriba</li>
                            <li><strong>Presiona 'Verificar'</strong> para completar tu acceso</li>
                            <li>Si el código expira, solicita uno nuevo desde la página de login</li>
                        </ol>
                    </div>

                    <div class='device-info'>
                        <strong>🖥️ Información del acceso:</strong><br>
                        📅 Fecha: " . date('d/m/Y H:i:s') . "<br>
                        🌐 IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'No disponible') . "<br>
                        💻 Navegador: " . (substr($_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido', 0, 50) . '...') . "
                    </div>

                    <div class='security-notice'>
                        <h4>🔒 Aviso de Seguridad</h4>
                        <ul>
                            <li><strong>Este código es de un solo uso</strong> y expira en " . $expirationMinutes . " minuto" . ($expirationMinutes > 1 ? 's' : '') . "</li>
                            <li><strong>No compartas este código</strong> con nadie</li>
                            <li><strong>Si no iniciaste sesión</strong>, ignora este email y considera cambiar tu contraseña</li>
                            <li><strong>Después de 3 intentos fallidos</strong>, tu cuenta será bloqueada temporalmente</li>
                        </ul>
                    </div>
                </div>
                
                <div class='footer'>
                    <p><strong>🤖 Tech Home Bolivia</strong></p>
                    <p>Instituto de Robótica y Tecnología Avanzada</p>
                    <p style='font-size: 12px; opacity: 0.8; margin-top: 15px;'>
                        © " . date('Y') . " Tech Home Bolivia. Todos los derechos reservados.<br>
                        Este es un email automático del sistema de seguridad. No responder.
                    </p>
                    <p style='font-size: 12px; margin-top: 10px;'>
                        📧 Soporte: soporte@techhomebolivia.com | 📞 +591 123 456 789
                    </p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        return $this->sendMail($email, $subject, $body);
    }
}
