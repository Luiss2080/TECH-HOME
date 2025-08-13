<?php

namespace App\Models;

use Core\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'id';
    protected $fillable = [
        'numero_venta',
        'cliente_id',
        'vendedor_id',
        'subtotal',
        'impuestos',
        'descuento',
        'total',
        'estado',
        'metodo_pago',
        'fecha_venta',
        'notas'
    ];
    protected $timestamps = true;

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id', 'id');
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id', 'id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta_id', 'id');
    }

    // Scopes
    public static function completadas()
    {
        return self::where('estado', '=', 'Completada');
    }

    public static function delMes($mes = null, $año = null)
    {
        $mes = $mes ?: date('m');
        $año = $año ?: date('Y');
        
        return self::whereRaw('MONTH(fecha_venta) = ? AND YEAR(fecha_venta) = ?', [$mes, $año]);
    }

    public static function recientes(int $dias = 7)
    {
        return self::whereRaw('fecha_venta >= DATE_SUB(NOW(), INTERVAL ? DAY)', [$dias])
                   ->orderBy('fecha_venta', 'desc');
    }
}
