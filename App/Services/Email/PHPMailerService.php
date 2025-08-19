<?php

namespace App\Services\Email;

class PHPMailerService extends BaseEmailService
{
    /**
     * Implementación usando PHPMailer o SMTP manual
     */
    protected function sendMail(string $to, string $subject, string $body): bool
    {
        try {
            // Crear contexto SSL
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
                error_log("PHPMailerService: No se pudo conectar al servidor SMTP: $errstr ($errno)");
                return false;
            }

            stream_set_timeout($smtp, 10);

            // Proceso SMTP
            $this->readSMTPResponse($smtp);

            fwrite($smtp, "EHLO " . $this->config['smtp_host'] . "\r\n");
            $this->readSMTPResponse($smtp);

            // STARTTLS para puerto 587
            if ($this->config['smtp_port'] == 587) {
                fwrite($smtp, "STARTTLS\r\n");
                $this->readSMTPResponse($smtp);
                
                stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                
                fwrite($smtp, "EHLO " . $this->config['smtp_host'] . "\r\n");
                $this->readSMTPResponse($smtp);
            }

            // Autenticación
            fwrite($smtp, "AUTH LOGIN\r\n");
            $this->readSMTPResponse($smtp);

            fwrite($smtp, base64_encode($this->config['smtp_username']) . "\r\n");
            $this->readSMTPResponse($smtp);

            fwrite($smtp, base64_encode($this->config['smtp_password']) . "\r\n");
            $auth_response = $this->readSMTPResponse($smtp);
            
            if (strpos($auth_response, '235') === false) {
                error_log("PHPMailerService: Error de autenticación SMTP: $auth_response");
                fclose($smtp);
                return false;
            }

            // Envío del email
            fwrite($smtp, "MAIL FROM: <" . $this->config['from_email'] . ">\r\n");
            $this->readSMTPResponse($smtp);

            fwrite($smtp, "RCPT TO: <$to>\r\n");
            $this->readSMTPResponse($smtp);

            fwrite($smtp, "DATA\r\n");
            $this->readSMTPResponse($smtp);

            $email_content = $this->buildEmailContent($to, $subject, $body);
            fwrite($smtp, $email_content . "\r\n.\r\n");
            $data_response = $this->readSMTPResponse($smtp);

            fwrite($smtp, "QUIT\r\n");
            fclose($smtp);

            if (strpos($data_response, '250') !== false) {
                error_log("PHPMailerService: Email enviado exitosamente a: $to");
                return true;
            } else {
                error_log("PHPMailerService: Error en DATA: $data_response");
                return false;
            }

        } catch (\Exception $e) {
            error_log("PHPMailerService: Excepción enviando email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Leer respuesta del servidor SMTP
     */
    private function readSMTPResponse($smtp): string
    {
        $response = '';
        while (!feof($smtp)) {
            $line = fgets($smtp, 515);
            $response .= $line;
            
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }
        return trim($response);
    }

    /**
     * Construir contenido completo del email
     */
    private function buildEmailContent(string $to, string $subject, string $body): string
    {
        $headers = [
            "From: " . $this->config['from_name'] . " <" . $this->config['from_email'] . ">",
            "To: $to",
            "Subject: $subject",
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
            "Date: " . date('r'),
            "Message-ID: <" . uniqid() . "@" . $this->config['smtp_host'] . ">"
        ];
        
        return implode("\r\n", $headers) . "\r\n\r\n" . $body;
    }

    /**
     * Probar conexión SMTP
     */
    public function testConnection(): bool
    {
        try {
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

            if ($this->config['smtp_port'] == 465) {
                $smtp = stream_socket_client(
                    "ssl://" . $this->config['smtp_host'] . ":" . $this->config['smtp_port'],
                    $errno, $errstr, 5, STREAM_CLIENT_CONNECT, $context
                );
            } else {
                $smtp = fsockopen($this->config['smtp_host'], $this->config['smtp_port'], $errno, $errstr, 5);
            }
            
            if (!$smtp) {
                error_log("PHPMailerService: Test connection failed: $errstr ($errno)");
                return false;
            }

            fclose($smtp);
            return true;
            
        } catch (\Exception $e) {
            error_log("PHPMailerService: Test connection exception: " . $e->getMessage());
            return false;
        }
    }
}
