<?php

namespace App\Models;

use Core\Model;

class User extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'password',
        'rol_id',
        'telefono',
        'fecha_nacimiento',
        'avatar',
        'estado',
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    protected $hidden = [
        'password'
    ];
    protected $timestamps = true;
    protected $softDeletes = false;

    // Relaciones
    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id', 'id');
    }

    /**
     * Intenta autenticar un usuario por email y password
     * @param string $email
     * @param string $password
     * @return User|false
     */
    public static function attempt($email, $password)
    {
        $user = self::where('email', '=', $email)->first();
        if ($user && password_verify($password, $user->password)) {
            return $user;
        }
        return false;
    }

    // Scopes
    public static function activos()
    {
        return self::where('estado', '=', 1);
    }

    public static function porRol($rolId)
    {
        return self::where('rol_id', '=', $rolId);
    }

    public static function registradosHoy()
    {
        return self::whereRaw('DATE(fecha_creacion) = CURRENT_DATE');
    }

    public static function recientes(int $dias = 7)
    {
        return self::whereRaw('fecha_creacion >= DATE_SUB(NOW(), INTERVAL ? DAY)', [$dias])
                   ->orderBy('fecha_creacion', 'desc');
    }
}
