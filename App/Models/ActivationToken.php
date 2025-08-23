<?php

namespace App\Models;

use Core\Model;

class ActivationToken extends Model
{
    protected $table = 'activation_tokens';
    protected $primaryKey = 'id';
    protected $fillable = [
        'email',
        'token',
        'usado',
        'fecha_creacion'
    ];
    protected $timestamps = false;

    /**
     * Crear un token de activación
     */
    public static function createToken(string $email): string
    {
        // Generar token único
        $token = bin2hex(random_bytes(32));
        
        // Eliminar tokens anteriores del mismo email que no hayan sido usados
        $db = \Core\DB::getInstance();
        $db->query("DELETE FROM activation_tokens WHERE email = ? AND usado = 0", [$email]);
        
        // Crear nuevo token
        $activationToken = new static();
        $activationToken->email = $email;
        $activationToken->token = $token;
        $activationToken->usado = 0;
        $activationToken->fecha_creacion = date('Y-m-d H:i:s');
        $activationToken->save();
        
        return $token;
    }

    /**
     * Validar un token de activación
     */
    public static function validateToken(string $token): ?array
    {
        $activationToken = static::where('token', $token)->first();
        
        if (!$activationToken) {
            return null;
        }
        
        // Verificar si ya fue usado
        if ($activationToken->usado) {
            return null;
        }
        
        return [
            'email' => $activationToken->email,
            'token' => $activationToken->token
        ];
    }

    /**
     * Marcar token como usado
     */
    public static function markAsUsed(string $token): bool
    {
        $activationToken = static::where('token', $token)->first();
        
        if (!$activationToken) {
            return false;
        }
        
        $activationToken->usado = 1;
        $result = $activationToken->save();
        return $result !== false;
    }

    /**
     * Obtener token por email
     */
    public static function getByEmail(string $email): ?array
    {
        $activationToken = static::where('email', $email)
                                ->where('usado', '=', 0)
                                ->orderBy('fecha_creacion', 'DESC')
                                ->first();
        
        if (!$activationToken) {
            return null;
        }
        
        return [
            'email' => $activationToken->email,
            'token' => $activationToken->token,
            'fecha_creacion' => $activationToken->fecha_creacion
        ];
    }

    /**
     * Eliminar tokens expirados (opcional - para limpieza)
     */
    public static function deleteExpiredTokens(): int
    {
        $db = \Core\DB::getInstance();
        // Eliminar tokens usados de más de 30 días
        $result = $db->query("DELETE FROM activation_tokens WHERE usado = 1 AND fecha_creacion < DATE_SUB(NOW(), INTERVAL 30 DAY)");
        
        return $result->rowCount();
    }
}
