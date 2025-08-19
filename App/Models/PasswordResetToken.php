<?php

namespace App\Models;

use Core\Model;

class PasswordResetToken extends Model
{
    protected $table = 'password_reset_tokens';
    protected $primaryKey = 'id';
    protected $fillable = [
        'email',
        'token',
        'expires_at',
        'used'
    ];
    protected $timestamps = false;

    /**
     * Crear un nuevo token de recuperación
     */
    public static function createToken(string $email): string
    {
        // Generar token único
        $token = bin2hex(random_bytes(32));
        
        // Fecha de expiración (configurable desde .env, por defecto 15 minutos)
        $tokenExpirationMinutes = $_ENV['PASSWORD_RESET_TOKEN_EXPIRATION_MINUTES'] ?? 15;
        $expiresAt = date('Y-m-d H:i:s', time() + ($tokenExpirationMinutes * 60));
        
        // Eliminar tokens anteriores para este email
        $db = \Core\DB::getInstance();
        $db->query("DELETE FROM password_reset_tokens WHERE email = ?", [$email]);
        
        // Crear nuevo token
        $resetToken = new static();
        $resetToken->email = $email;
        $resetToken->token = $token;
        $resetToken->expires_at = $expiresAt;
        $resetToken->used = 0;
        $resetToken->save();
        
        return $token;
    }

    /**
     * Validar un token
     */
    public static function validateToken(string $token): ?array
    {
        $resetToken = static::where('token', $token)->first();
        
        if (!$resetToken) {
            return null;
        }
        
        // Verificar si ya fue usado
        if ($resetToken->used) {
            return null;
        }
        
        // Verificar si expiró
        if (strtotime($resetToken->expires_at) < time()) {
            return null;
        }
        
        return [
            'email' => $resetToken->email,
            'token' => $resetToken->token
        ];
    }

    /**
     * Marcar token como usado
     */
    public static function markAsUsed(string $token): bool
    {
        $resetToken = static::where('token', $token)->first();
        
        if (!$resetToken) {
            return false;
        }
        
        $resetToken->used = 1;
        $result = $resetToken->save();
        return $result !== false;
    }

    /**
     * Limpiar tokens expirados
     */
    public static function cleanExpiredTokens(): int
    {
        $db = \Core\DB::getInstance();
        $result = $db->query(
            "DELETE FROM password_reset_tokens WHERE expires_at < NOW() OR used = 1"
        );
        
        return $result->rowCount();
    }
}
