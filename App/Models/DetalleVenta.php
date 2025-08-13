<?php

namespace App\Models;

use Core\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'venta_id',
        'producto_id',
        'producto_tipo',
        'cantidad',
        'precio_unitario',
        'descuento',
        'subtotal'
    ];
    protected $timestamps = true;

    // Relaciones
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'venta_id', 'id');
    }
}
