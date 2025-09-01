<?php

namespace App\Services\Email;

use App\Models\User;

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
     * Enviar email de bienvenida con token de activación
     */
    public function sendWelcomeEmail(User $user, string $token): bool;

    /**
     * Enviar código OTP por email
     */
    public function sendOTPEmail(string $email, string $codigo, string $nombreUsuario = '', int $expirationMinutes = 1): bool;

    /**
     * Probar conexión del servicio
     */
    public function testConnection(): bool;
}
