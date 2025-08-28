<?php

namespace App\Models;

use Core\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
        'color',
        'icono',
        'estado'
    ];
    protected $timestamps = true;

    // Relaciones
    public function cursos()
    {
        return $this->hasMany(Curso::class, 'categoria_id', 'id');
    }

    public function libros()
    {
        return $this->hasMany(Libro::class, 'categoria_id', 'id');
    }

    public function componentes()
    {
        return $this->hasMany(Componente::class, 'categoria_id', 'id');
    }

    public function materiales()
    {
        return $this->hasMany(Material::class, 'categoria_id', 'id');
    }

    // Scopes
    public static function porTipo($tipo)
    {
        return self::where('tipo', $tipo);
    }

    public static function activas()
    {
        return self::where('estado', 1);
    }
}
