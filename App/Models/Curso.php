<?php

namespace App\Models;

use Core\Model;

class Curso extends Model
{
    protected $table = 'cursos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'titulo',
        'descripcion',
        'contenido',
        'docente_id',
        'categoria_id',
        'imagen_portada',
        'precio',
        'duracion_horas',
        'nivel',
        'requisitos',
        'objetivos',
        'estado'
    ];
    protected $timestamps = true;

    // Relaciones
    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id', 'id');
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }

    // Scopes
    public static function publicados()
    {
        return self::where('estado', '=', 'Publicado');
    }

    public static function porNivel($nivel)
    {
        return self::where('nivel', '=', $nivel);
    }

    public static function recientes(int $dias = 7)
    {
        return self::where('estado', '=', 'Publicado')
                   ->whereRaw('fecha_actualizacion >= DATE_SUB(NOW(), INTERVAL ? DAY)', [$dias])
                   ->orderBy('fecha_actualizacion', 'desc');
    }
}
