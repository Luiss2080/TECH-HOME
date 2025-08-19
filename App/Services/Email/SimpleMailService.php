<?php

namespace App\Services\Email;

class SimpleMailService extends BaseEmailService
{
    /**
     * Implementación usando la función mail() de PHP
     */
    protected function sendMail(string $to, string $subject, string $body): bool
    {
        try {
            $headers = [
                'MIME-Version: 1.0',
                'Content-type: text/html; charset=UTF-8',
                'From: ' . $this->config['from_name'] . ' <' . $this->config['from_email'] . '>',
                'Reply-To: ' . $this->config['from_email'],
                'X-Mailer: PHP/' . phpversion()
            ];

            $headerString = implode("\r\n", $headers);
            
            $success = mail($to, $subject, $body, $headerString);
            
            if ($success) {
                error_log("Email enviado exitosamente con SimpleMailService a: $to");
                return true;
            } else {
                error_log("Error enviando email con SimpleMailService a: $to");
                return false;
            }
            
        } catch (\Exception $e) {
            error_log("Excepción en SimpleMailService: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Probar conexión - para SimpleMailService verificamos que la función mail esté habilitada
     */
    public function testConnection(): bool
    {
        if (!function_exists('mail')) {
            error_log("SimpleMailService: Función mail() no disponible");
            return false;
        }
        
        // Verificar configuración básica
        if (empty($this->config['from_email'])) {
            error_log("SimpleMailService: from_email no configurado");
            return false;
        }
        
        return true;
    }
}
