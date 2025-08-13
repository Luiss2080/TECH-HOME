<?php

namespace App\Models;

use Core\Model;

class Libro extends Model
{
    protected $table = 'libros';
    protected $primaryKey = 'id';
    protected $fillable = [
        'titulo',
        'autor',
        'descripcion',
        'categoria_id',
        'isbn',
        'paginas',
        'editorial',
        'año_publicacion',
        'imagen_portada',
        'archivo_pdf',
        'enlace_externo',
        'tamaño_archivo',
        'stock',
        'stock_minimo',
        'precio',
        'es_gratuito',
        'estado'
    ];
    protected $timestamps = true;

    // Relaciones
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }

    // Scopes
    public static function disponibles()
    {
        return self::where('estado', '=', 1)->where('stock', '>', 0);
    }

    public static function stockBajo()
    {
        return self::whereRaw('stock <= stock_minimo')->where('estado', '=', 1);
    }

    public static function gratuitos()
    {
        return self::where('es_gratuito', '=', 1);
    }

    public static function countStockBajo()
    {
        return self::whereRaw('stock <= stock_minimo')->where('estado', '=', 1)->count();
    }
}
