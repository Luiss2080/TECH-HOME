<?php

namespace App\Services\Email;

interface MailServiceInterface
{
    /**
     * Enviar email
     */
    public function sendEmail(string $to, string $subject, string $body): bool;
    
    /**
     * Enviar email de recuperación de contraseña
     */
    public function sendPasswordResetEmail(string $email, string $token): bool;
    
    /**
     * Probar conexión del servicio
     */
    public function testConnection(): bool;
}
