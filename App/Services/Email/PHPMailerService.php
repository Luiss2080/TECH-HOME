<?php

namespace App\Services\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerService extends BaseEmailService
{
    /**
     * Implementación usando PHPMailer
     */
    protected function sendMail(string $to, string $subject, string $body): bool
    {
        try {
            $mail = new PHPMailer(true);

            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = $this->config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp_username'];
            $mail->Password = $this->config['smtp_password'];
            $mail->SMTPSecure = ($this->config['smtp_port'] == 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['smtp_port'];

            // Configuración del remitente y destinatario
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($to);
            $mail->addReplyTo($this->config['from_email'], $this->config['from_name']);

            // Contenido del email
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->CharSet = 'UTF-8';

            // Configuraciones adicionales para Gmail/SMTP
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Configurar timeout
            $mail->Timeout = 30;

            $result = $mail->send();

            if ($result) {
                error_log("PHPMailerService: Email enviado exitosamente a $to");
                return true;
            } else {
                error_log("PHPMailerService: Error enviando email - " . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("PHPMailerService: Excepción enviando email - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Probar conexión al servidor SMTP
     */
    public function testConnection(): bool
    {
        try {
            $mail = new PHPMailer(true);

            // Configuración básica para test
            $mail->isSMTP();
            $mail->Host = $this->config['smtp_host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['smtp_username'];
            $mail->Password = $this->config['smtp_password'];
            $mail->SMTPSecure = ($this->config['smtp_port'] == 465) ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['smtp_port'];
            $mail->Timeout = 10;

            // Configuraciones SSL
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];

            // Test de conexión usando SMTPConnect
            $connected = $mail->smtpConnect();

            if ($connected) {
                $mail->smtpClose();
                error_log("PHPMailerService: Test de conexión exitoso");
                return true;
            } else {
                error_log("PHPMailerService: Test de conexión falló - " . $mail->ErrorInfo);
                return false;
            }
        } catch (Exception $e) {
            error_log("PHPMailerService: Error en test de conexión - " . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log("PHPMailerService: Error general en test - " . $e->getMessage());
            return false;
        }
    }
}
