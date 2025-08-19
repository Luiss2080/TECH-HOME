<?php

namespace App\Services;

class EmailService
{
    private $config;

    public function __construct()
    {
        $this->config = [
            'smtp_host' => $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com',
            'smtp_port' => $_ENV['MAIL_PORT'] ?? 587,
            'smtp_username' => $_ENV['MAIL_USERNAME'] ?? '',
            'smtp_password' => $_ENV['MAIL_PASSWORD'] ?? '',
            'from_email' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@techhome.bo',
            'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Tech Home Bolivia',
        ];
    }

    /**
     * Enviar email de recuperación de contraseña
     */
    public function sendPasswordResetEmail(string $email, string $token): bool
    {
        try {
            $resetLink = $this->generateResetLink($token);
            $subject = 'Recuperación de Contraseña - Tech Home';
            $body = $this->getPasswordResetTemplate($resetLink);

            return $this->sendEmail($email, $subject, $body);
        } catch (\Exception $e) {
            error_log("Error enviando email de recuperación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generar link de recuperación
     */
    private function generateResetLink(string $token): string
    {
        $baseUrl = $_ENV['APP_URL'] ?? 'http://localhost/TECH-HOME';
        return $baseUrl . '/reset-password?token=' . $token;
    }

    /**
     * Template para email de recuperación
     */
    private function getPasswordResetTemplate(string $resetLink): string
    {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #007bff; color: white; padding: 20px; text-align: center; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 5px; margin: 20px 0; }
                .button { display: inline-block; background: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
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
                        <a href='{$resetLink}' class='button'>Restablecer Contraseña</a>
                    </div>
                    
                    <div class='warning'>
                        <strong>⚠️ Importante:</strong>
                        <ul>
                            <li>Este enlace expirará en <strong>15 minutos</strong></li>
                            <li>Solo puede ser usado una vez</li>
                            <li>Si no solicitaste este cambio, ignora este email</li>
                        </ul>
                    </div>
                    
                    <p>Si el botón no funciona, copia y pega el siguiente enlace en tu navegador:</p>
                    <p style='word-break: break-all; background: #e9ecef; padding: 10px; border-radius: 3px;'>{$resetLink}</p>
                </div>
                
                <div class='footer'>
                    <p>Este es un email automático, por favor no responder.</p>
                    <p>&copy; " . date('Y') . " Tech Home Bolivia. Todos los derechos reservados.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Enviar email usando configuración SMTP
     */
    private function sendEmail(string $to, string $subject, string $body): bool
    {
        try {
            // Crear contexto SSL para puerto 465
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

            // Conectar según el puerto
            if ($this->config['smtp_port'] == 465) {
                // SSL directo para puerto 465
                $smtp = stream_socket_client(
                    "ssl://" . $this->config['smtp_host'] . ":" . $this->config['smtp_port'],
                    $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context
                );
            } else {
                // Conexión normal para puerto 587
                $smtp = fsockopen($this->config['smtp_host'], $this->config['smtp_port'], $errno, $errstr, 10);
            }
            
            if (!$smtp) {
                error_log("No se pudo conectar al servidor SMTP: $errstr ($errno)");
                return false;
            }

            // Configurar timeout más corto para respuestas rápidas
            stream_set_timeout($smtp, 10);

            // Leer respuesta inicial
            $this->readSMTPResponse($smtp);

            // EHLO
            fwrite($smtp, "EHLO " . $this->config['smtp_host'] . "\r\n");
            $this->readSMTPResponse($smtp);

            // STARTTLS solo si es puerto 587
            if ($this->config['smtp_port'] == 587) {
                fwrite($smtp, "STARTTLS\r\n");
                $this->readSMTPResponse($smtp);
                
                stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                
                fwrite($smtp, "EHLO " . $this->config['smtp_host'] . "\r\n");
                $this->readSMTPResponse($smtp);
            }

            // Autenticación LOGIN
            fwrite($smtp, "AUTH LOGIN\r\n");
            $this->readSMTPResponse($smtp);

            fwrite($smtp, base64_encode($this->config['smtp_username']) . "\r\n");
            $this->readSMTPResponse($smtp);

            fwrite($smtp, base64_encode($this->config['smtp_password']) . "\r\n");
            $auth_response = $this->readSMTPResponse($smtp);
            
            if (strpos($auth_response, '235') === false) {
                error_log("Error de autenticación SMTP: $auth_response");
                fclose($smtp);
                return false;
            }

            // Comandos SMTP de envío
            fwrite($smtp, "MAIL FROM: <" . $this->config['from_email'] . ">\r\n");
            $mail_response = $this->readSMTPResponse($smtp);

            fwrite($smtp, "RCPT TO: <$to>\r\n");
            $rcpt_response = $this->readSMTPResponse($smtp);

            fwrite($smtp, "DATA\r\n");
            $this->readSMTPResponse($smtp);

            // Construir email completo
            $email_content = $this->buildEmailContent($to, $subject, $body);
            
            // Enviar contenido y terminar con punto
            fwrite($smtp, $email_content . "\r\n.\r\n");
            $data_response = $this->readSMTPResponse($smtp);

            // QUIT rápido
            fwrite($smtp, "QUIT\r\n");
            fclose($smtp);

            // Verificar éxito
            if (strpos($data_response, '250') !== false) {
                error_log("Email enviado exitosamente a: $to");
                return true;
            } else {
                error_log("Error en DATA: $data_response");
                return false;
            }

        } catch (\Exception $e) {
            error_log("Excepción enviando email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Construir contenido completo del email
     */
    private function buildEmailContent(string $to, string $subject, string $body): string
    {
        $headers = [];
        $headers[] = "From: " . $this->config['from_name'] . " <" . $this->config['from_email'] . ">";
        $headers[] = "To: $to";
        $headers[] = "Subject: $subject";
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-Type: text/html; charset=UTF-8";
        $headers[] = "Content-Transfer-Encoding: 8bit";
        $headers[] = "Date: " . date('r');
        $headers[] = "Message-ID: <" . uniqid() . "@" . $this->config['smtp_host'] . ">";
        
        return implode("\r\n", $headers) . "\r\n\r\n" . $body;
    }

    /**
     * Leer respuesta del servidor SMTP con timeout
     */
    private function readSMTPResponse($smtp): string
    {
        $response = '';
        $start_time = time();
        
        while ($line = fgets($smtp, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) == ' ') break;
            
            // Timeout de seguridad
            if (time() - $start_time > 5) break;
        }
        return trim($response);
    }

    /**
     * Verificar si la configuración de email está completa
     */
    public function isConfigured(): bool
    {
        return !empty($this->config['smtp_username']) && 
               !empty($this->config['smtp_password']) && 
               !empty($this->config['from_email']);
    }

    /**
     * Probar la conexión SMTP
     */
    public function testConnection(): array
    {
        try {
            if (!$this->isConfigured()) {
                return [
                    'success' => false,
                    'message' => 'Configuración SMTP incompleta'
                ];
            }

            // Crear contexto SSL si es necesario
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

            // Conectar según el puerto
            if ($this->config['smtp_port'] == 465) {
                $smtp = stream_socket_client(
                    "ssl://" . $this->config['smtp_host'] . ":" . $this->config['smtp_port'],
                    $errno, $errstr, 10, STREAM_CLIENT_CONNECT, $context
                );
            } else {
                $smtp = fsockopen($this->config['smtp_host'], $this->config['smtp_port'], $errno, $errstr, 10);
            }
            
            if (!$smtp) {
                return [
                    'success' => false,
                    'message' => "No se pudo conectar: $errstr ($errno)"
                ];
            }

            stream_set_timeout($smtp, 5);
            $response = $this->readSMTPResponse($smtp);
            
            fwrite($smtp, "QUIT\r\n");
            fclose($smtp);

            return [
                'success' => true,
                'message' => 'Conexión SMTP exitosa',
                'server_response' => $response,
                'port' => $this->config['smtp_port'],
                'host' => $this->config['smtp_host']
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error de conexión: ' . $e->getMessage()
            ];
        }
    }
}
