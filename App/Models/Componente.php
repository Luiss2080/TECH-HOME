<?php

namespace App\Models;

use Core\Model;

class Componente extends Model
{
    protected $table = 'componentes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'codigo_producto',
        'marca',
        'modelo',
        'especificaciones',
        'imagen_principal',
        'imagenes_adicionales',
        'precio',
        'stock',
        'stock_minimo',
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
        return self::where('estado', '!=', 'Descontinuado')->where('stock', '>', 0);
    }

    public static function stockBajo()
    {
        return self::whereRaw('stock <= stock_minimo')->where('estado', '!=', 'Descontinuado');
    }

    public static function countStockBajo()
    {
        return self::whereRaw('stock <= stock_minimo')->where('estado', '!=', 'Descontinuado')->count();
    }
}
