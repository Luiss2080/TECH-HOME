<?php

namespace App\Models;

use Core\Model;

class Inscripcion extends Model
{
    protected string $table = 'inscripciones';

    protected array $fillable = [
        'estudiante_id',
        'curso_id',
        'fecha_inscripcion'
    ];

    // Relación con el modelo User
    public function estudiante()
    {
        return $this->belongsTo(User::class, 'estudiante_id');
    }

    // Relación con el modelo Curso
    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }
}
