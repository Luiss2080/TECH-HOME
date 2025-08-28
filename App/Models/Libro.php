<?php

namespace App\Models;

use Core\Model;
use Core\DB;
use PDO;
use Exception;

class Libro extends Model
{
    protected $table = 'libros';
    protected $primaryKey = 'id';
    protected $fillable = [
        'titulo',
        'slug',
        'autor',
        'descripcion',
        'categoria_id',
        'isbn',
        'paginas',
        'editorial',
        'año_publicacion',
        'idioma',
        'formato',
        'descargas_totales',
        'calificacion_promedio',
        'total_calificaciones',
        'palabras_clave',
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
    protected $hidden = [];
    protected $timestamps = true;
    protected $softDeletes = false;

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Obtener la categoría del libro
     */
    public function categoria()
    {
        try {
            return Categoria::find($this->categoria_id);
        } catch (Exception $e) {
            return null;
        }
    }

    // ==========================================
    // SCOPES ESTÁTICOS
    // ==========================================

    /**
     * Obtener libros disponibles
     */
    public static function disponibles()
    {
        return self::where('estado', '=', 1);
    }

    /**
     * Obtener libros con stock disponible
     */
    public static function conStock()
    {
        return self::where('estado', '=', 1)->where('stock', '>', 0);
    }

    /**
     * Obtener libros con stock bajo
     */
    public static function stockBajo()
    {
        return self::whereRaw('stock <= stock_minimo')->where('estado', '=', 1);
    }

    /**
     * Obtener libros gratuitos
     */
    public static function gratuitos()
    {
        return self::where('es_gratuito', '=', 1)->where('estado', '=', 1);
    }

    /**
     * Obtener libros de pago
     */
    public static function dePago()
    {
        return self::where('es_gratuito', '=', 0)->where('estado', '=', 1);
    }

    /**
     * Obtener libros por categoría
     */
    public static function porCategoria(int $categoriaId)
    {
        return self::where('categoria_id', '=', $categoriaId)->where('estado', '=', 1);
    }

    /**
     * Obtener libros por autor
     */
    public static function porAutor(string $autor)
    {
        return self::where('autor', 'LIKE', "%{$autor}%")->where('estado', '=', 1);
    }

    /**
     * Obtener libros por editorial
     */
    public static function porEditorial(string $editorial)
    {
        return self::where('editorial', 'LIKE', "%{$editorial}%")->where('estado', '=', 1);
    }

    /**
     * Buscar libros
     */
    public static function buscar(string $termino)
    {
        return self::where('estado', '=', 1)
                   ->whereRaw('(titulo LIKE ? OR autor LIKE ? OR descripcion LIKE ?)', 
                             ["%{$termino}%", "%{$termino}%", "%{$termino}%"])
                   ->orderBy('titulo');
    }

    /**
     * Contar libros con stock bajo
     */
    public static function countStockBajo(): int
    {
        return self::whereRaw('stock <= stock_minimo')->where('estado', '=', 1)->count();
    }

    // ==========================================
    // MÉTODOS DE INSTANCIA
    // ==========================================

    /**
     * Verificar si el libro está disponible para descarga
     */
    public function estaDisponible(): bool
    {
        return $this->estado == 1 && ($this->es_gratuito || $this->stock > 0);
    }

    /**
     * Verificar si tiene stock bajo
     */
    public function tieneStockBajo(): bool
    {
        return !$this->es_gratuito && $this->stock <= $this->stock_minimo;
    }

    /**
     * Verificar si está agotado
     */
    public function estaAgotado(): bool
    {
        return !$this->es_gratuito && $this->stock <= 0;
    }

    /**
     * Actualizar stock
     */
    public function actualizarStock(int $nuevoStock): bool
    {
        try {
            $this->stock = max(0, $nuevoStock);
            $this->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Incrementar stock
     */
    public function incrementarStock(int $cantidad): bool
    {
        try {
            $this->stock += $cantidad;
            $this->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtener precio formateado
     */
    public function getPrecioFormateado(): string
    {
        if ($this->es_gratuito || $this->precio <= 0) {
            return 'Gratuito';
        }
        
        return 'Bs. ' . number_format($this->precio, 2);
    }

    /**
     * Obtener estado del stock
     */
    public function getEstadoStock(): array
    {
        if ($this->es_gratuito) {
            return [
                'status' => 'unlimited',
                'class' => 'success',
                'text' => 'Ilimitado',
                'icon' => 'infinity'
            ];
        }

        if ($this->stock <= 0) {
            return [
                'status' => 'out',
                'class' => 'danger',
                'text' => 'Agotado',
                'icon' => 'x-circle'
            ];
        }

        if ($this->stock <= $this->stock_minimo) {
            return [
                'status' => 'low',
                'class' => 'warning',
                'text' => 'Stock Bajo (' . $this->stock . ')',
                'icon' => 'alert-triangle'
            ];
        }

        return [
            'status' => 'available',
            'class' => 'success',
            'text' => 'Disponible (' . $this->stock . ')',
            'icon' => 'check-circle'
        ];
    }

    /**
     * Obtener clase CSS según el estado
     */
    public function getEstadoClass(): string
    {
        return $this->estado == 1 ? 'success' : 'secondary';
    }

    /**
     * Obtener URL de la imagen de portada
     */
    public function getImagenPortadaUrl(): string
    {
        if (!$this->imagen_portada) {
            return asset('images/libros/default.jpg');
        }
        
        return asset('images/libros/' . $this->imagen_portada);
    }

    /**
     * Verificar si el libro puede ser eliminado
     */
    public function puedeSerEliminado(): bool
    {
        return $this->getTotalDescargas() === 0;
    }

    /**
     * Obtener total de descargas (para compatibilidad)
     */
    public function getTotalDescargas(): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT COUNT(*) as count FROM descargas_libros WHERE libro_id = ?", [$this->id]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener información completa del libro
     */
    public function getInformacionCompleta(): array
    {
        $categoria = $this->categoria();
        
        return [
            'basica' => $this->getAttributes(),
            'categoria' => $categoria ? [
                'nombre' => $categoria->nombre,
                'color' => $categoria->color,
                'icono' => $categoria->icono
            ] : null,
            'estado_stock' => $this->getEstadoStock(),
            'formateado' => [
                'precio' => $this->getPrecioFormateado(),
                'disponibilidad' => $this->estaDisponible() ? 'Disponible' : 'No disponible'
            ]
        ];
    }
}
