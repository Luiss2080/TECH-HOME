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
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 5px; margin: 20px 0; }
                .button { 
                    display: inline-block; 
                    background: #007bff; 
                    color: white !important; 
                    padding: 15px 30px; 
                    text-decoration: none !important; 
                    border-radius: 8px; 
                    margin: 20px 0; 
                    font-weight: bold;
                    font-size: 16px;
                    border: none;
                    box-shadow: 0 2px 4px rgba(0,123,255,0.3);
                }
                .button:hover { 
                    background: #0056b3; 
                    color: white !important;
                    text-decoration: none !important;
                    box-shadow: 0 4px 8px rgba(0,123,255,0.4);
                }
                a { color: #007bff; }
                a.button { color: white !important; }
                .footer { text-align: center; color: #666; font-size: 12px; padding: 20px; }
                .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 15px 0; color: #856404; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Tech Home Bolivia</h1>
                    <p>Instituto de Robótica y Tecnología Avanzada</p>
                </div>
                
                <div class='content'>
                    <h2>Recuperación de Contraseña</h2>
                    <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.</p>
                    <p>Haz clic en el siguiente botón para crear una nueva contraseña:</p>
                    
                    <div style='text-align: center;'>
                        <a href='$resetUrl' class='button' style='color: white !important; text-decoration: none !important;'>Restablecer Contraseña</a>
                    </div>
                    
                    <div class='warning'>
                        <strong>⚠️ Importante:</strong>
                        <ul>
                            <li>Este enlace expirará en <strong>$tokenExpirationMinutes minutos</strong></li>
                            <li>Solo puede ser usado una vez</li>
                            <li>Si no solicitaste este cambio, ignora este email</li>
                        </ul>
                    </div>
                    
                    <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
                    <p style='word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 3px;'>$resetUrl</p>
                </div>
                
                <div class='footer'>
                    <p>Este es un email automático, por favor no responder.</p>
                    <p>&copy; " . date('Y') . " Tech Home Bolivia. Todos los derechos reservados.</p>
                </div>
            </div>
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
        
        $activationUrl = $this->config['app_url'] . "/activate-account?token=" . urlencode($token);
        $loginUrl = $this->config['app_url'] . "/login";
        $coursesUrl = $this->config['app_url'] . "/cursos";
        $booksUrl = $this->config['app_url'] . "/libros";
        
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
                .activation-box {
                    background: #fff3cd;
                    border: 2px solid #ffc107;
                    padding: 25px;
                    border-radius: 12px;
                    margin: 25px 0;
                    text-align: center;
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
                .button.activation {
                    background: #28a745;
                    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
                    font-size: 18px;
                    padding: 20px 40px;
                }
                .button.activation:hover {
                    background: #218838;
                    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
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
                .warning-box { background: #fff3cd; border-left: 4px solid #ffc107; padding: 20px; margin: 20px 0; border-radius: 5px; color: #856404; }
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
                            Tu cuenta ha sido creada exitosamente en Tech Home Bolivia. 
                        </p>
                        <p><strong>Email registrado:</strong> " . htmlspecialchars($user->email) . "</p>
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

                    <div class='info-box'>
                        <h3>🎯 ¿Qué puedes hacer después de activar tu cuenta?</h3>
                        <div style='text-align: center; margin: 20px 0;'>
                            <a href='$coursesUrl' class='button secondary'>📚 Explorar Cursos</a>
                            <a href='$booksUrl' class='button secondary'>📖 Ver Biblioteca</a>
                        </div>
                    </div>

                    <div class='features'>
                        <div class='feature'>
                            <div class='feature-icon'>🤖</div>
                            <h3>Robótica Avanzada</h3>
                            <p>Aprende programación de robots, Arduino, Raspberry Pi y más</p>
                        </div>
                        <div class='feature'>
                            <div class='feature-icon'>💻</div>
                            <h3>Tecnología Moderna</h3>
                            <p>Domina las últimas tecnologías en automatización e IoT</p>
                        </div>
                    </div>

                    <p style='text-align: center; color: #6c757d;'>
                        Si el botón de activación no funciona, copia y pega este enlace en tu navegador:<br>
                        <span style='word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 3px; display: inline-block; margin-top: 10px;'>$activationUrl</span>
                    </p>
                </div>
                
                <div class='footer'>
                    <h3 style='color: white; margin-top: 0;'>¡Síguenos en nuestras redes sociales!</h3>
                    <div class='social-links'>
                        <a href='#'>📘 Facebook</a>
                        <a href='#'>📸 Instagram</a>
                        <a href='#'>💼 LinkedIn</a>
                        <a href='#'>🎬 YouTube</a>
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
}
