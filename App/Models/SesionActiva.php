<?php

namespace App\Models;

use Core\Model;

class SesionActiva extends Model
{
    protected $table = 'sesiones_activas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'usuario_id',
        'session_id',
        'ip_address',
        'user_agent',
        'activa',
        'fecha_inicio',
        'fecha_actividad'
    ];
    protected $timestamps = false;

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id', 'id');
    }

    // Scopes
    public static function activas()
    {
        return self::where('activa', 1);
    }
}
