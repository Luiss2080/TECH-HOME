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

    /**
     * Obtener descargas del libro
     */
    public function descargas()
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT dl.*, u.nombre, u.apellido, u.email
                      FROM descargas_libros dl
                      INNER JOIN usuarios u ON dl.usuario_id = u.id
                      WHERE dl.libro_id = ?
                      ORDER BY dl.fecha_descarga DESC";
            
            $result = $db->query($query, [$this->id]);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener usuarios que descargaron el libro
     */
    public function usuarios()
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT DISTINCT u.id, u.nombre, u.apellido, u.email,
                             COUNT(dl.id) as total_descargas,
                             MAX(dl.fecha_descarga) as ultima_descarga
                      FROM usuarios u
                      INNER JOIN descargas_libros dl ON u.id = dl.usuario_id
                      WHERE dl.libro_id = ?
                      GROUP BY u.id
                      ORDER BY total_descargas DESC";
            
            $result = $db->query($query, [$this->id]);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener calificaciones del libro
     */
    public function calificaciones()
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT cl.*, u.nombre, u.apellido
                      FROM calificaciones_libros cl
                      INNER JOIN usuarios u ON cl.usuario_id = u.id
                      WHERE cl.libro_id = ?
                      ORDER BY cl.fecha_calificacion DESC";
            
            $result = $db->query($query, [$this->id]);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener usuarios que marcaron como favorito
     */
    public function usuariosFavoritos()
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT u.*, fl.fecha_agregado
                      FROM usuarios u
                      INNER JOIN favoritos_libros fl ON u.id = fl.usuario_id
                      WHERE fl.libro_id = ?
                      ORDER BY fl.fecha_agregado DESC";
            
            $result = $db->query($query, [$this->id]);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            return [];
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
     * Obtener libros recientes
     */
    public static function recientes(int $dias = 30)
    {
        return self::whereRaw('fecha_creacion >= DATE_SUB(NOW(), INTERVAL ? DAY)', [$dias])
                   ->where('estado', '=', 1)
                   ->orderBy('fecha_creacion', 'desc');
    }

    /**
     * Obtener libros más descargados
     */
    public static function masDescargados(int $limit = 10)
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT l.*, COUNT(dl.id) as total_descargas
                      FROM libros l
                      LEFT JOIN descargas_libros dl ON l.id = dl.libro_id
                      WHERE l.estado = 1
                      GROUP BY l.id
                      ORDER BY total_descargas DESC, l.titulo
                      LIMIT ?";
            
            $result = $db->query($query, [$limit]);
            $libros = [];
            
            if ($result) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $libros[] = new self($row);
                }
            }
            
            return $libros;
        } catch (Exception $e) {
            return [];
        }
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
     * Obtener total de descargas
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
     * Obtener descargas del mes actual
     */
    public function getDescargasMes(): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query(
                "SELECT COUNT(*) as count FROM descargas_libros 
                 WHERE libro_id = ? AND MONTH(fecha_descarga) = MONTH(CURRENT_DATE()) 
                 AND YEAR(fecha_descarga) = YEAR(CURRENT_DATE())",
                [$this->id]
            );
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener usuarios únicos que descargaron
     */
    public function getUsuariosUnicos(): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT COUNT(DISTINCT usuario_id) as count FROM descargas_libros WHERE libro_id = ?", [$this->id]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener total de favoritos
     */
    public function getTotalFavoritos(): int
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT COUNT(*) as count FROM favoritos_libros WHERE libro_id = ?", [$this->id]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Verificar si un usuario marcó el libro como favorito
     */
    public function esFavoritoDe(int $usuarioId): bool
    {
        try {
            $db = DB::getInstance();
            $result = $db->query("SELECT COUNT(*) as count FROM favoritos_libros WHERE libro_id = ? AND usuario_id = ?", [$this->id, $usuarioId]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Agregar/quitar de favoritos
     */
    public function toggleFavorito(int $usuarioId): bool
    {
        try {
            $db = DB::getInstance();
            
            if ($this->esFavoritoDe($usuarioId)) {
                // Quitar de favoritos
                $db->query("DELETE FROM favoritos_libros WHERE libro_id = ? AND usuario_id = ?", [$this->id, $usuarioId]);
            } else {
                // Agregar a favoritos
                $db->query("INSERT INTO favoritos_libros (usuario_id, libro_id) VALUES (?, ?)", [$usuarioId, $this->id]);
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Calificar libro
     */
    public function calificar(int $usuarioId, int $calificacion, string $comentario = null): bool
    {
        try {
            if ($calificacion < 1 || $calificacion > 5) {
                return false;
            }
            
            $db = DB::getInstance();
            
            // Usar INSERT ON DUPLICATE KEY UPDATE para MySQL
            $db->query(
                "INSERT INTO calificaciones_libros (usuario_id, libro_id, calificacion, comentario) 
                 VALUES (?, ?, ?, ?) 
                 ON DUPLICATE KEY UPDATE calificacion = VALUES(calificacion), comentario = VALUES(comentario), fecha_actualizacion = CURRENT_TIMESTAMP",
                [$usuarioId, $this->id, $calificacion, $comentario]
            );
            
            // Actualizar promedio (se hace automáticamente por trigger, pero por si acaso)
            $this->actualizarCalificacionPromedio();
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Actualizar calificación promedio
     */
    private function actualizarCalificacionPromedio()
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT COUNT(*) as total, AVG(calificacion) as promedio 
                      FROM calificaciones_libros WHERE libro_id = ?";
            
            $result = $db->query($query, [$this->id]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            
            $this->total_calificaciones = $row['total'] ?? 0;
            $this->calificacion_promedio = round($row['promedio'] ?? 0, 2);
            $this->save();
        } catch (Exception $e) {
            // Silenciar error
        }
    }

    /**
     * Registrar descarga
     */
    public function registrarDescarga(int $usuarioId, string $ipAddress, string $userAgent = null): bool
    {
        try {
            if (!$this->estaDisponible()) {
                throw new Exception('Libro no disponible para descarga');
            }

            $db = DB::getInstance();
            $db->query(
                "INSERT INTO descargas_libros (usuario_id, libro_id, ip_address, user_agent) VALUES (?, ?, ?, ?)",
                [$usuarioId, $this->id, $ipAddress, $userAgent]
            );

            // Reducir stock si no es gratuito
            if (!$this->es_gratuito && $this->stock > 0) {
                $this->stock -= 1;
                $this->save();
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
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
     * Obtener tamaño de archivo formateado
     */
    public function getTamañoFormateado(): string
    {
        if ($this->tamaño_archivo <= 0) {
            return 'No especificado';
        }
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->tamaño_archivo;
        $i = 0;
        
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
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
     * Obtener URL del archivo PDF
     */
    public function getArchivoPdfUrl(): ?string
    {
        if (!$this->archivo_pdf) {
            return null;
        }
        
        return asset('files/libros/' . $this->archivo_pdf);
    }

    /**
     * Verificar si el libro puede ser eliminado
     */
    public function puedeSerEliminado(): bool
    {
        return $this->getTotalDescargas() === 0;
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
            'estadisticas' => [
                'total_descargas' => $this->getTotalDescargas(),
                'descargas_mes' => $this->getDescargasMes(),
                'usuarios_unicos' => $this->getUsuariosUnicos(),
                'total_favoritos' => $this->getTotalFavoritos()
            ],
            'calificaciones' => [
                'promedio' => $this->calificacion_promedio,
                'total' => $this->calificaciones()->count()
            ],
            'estado_stock' => $this->getEstadoStock(),
            'formateado' => [
                'precio' => $this->getPrecioFormateado(),
                'tamaño' => $this->getTamañoFormateado(),
                'disponibilidad' => $this->estaDisponible() ? 'Disponible' : 'No disponible'
            ]
        ];
    }

    /**
     * Obtener estadísticas de calificaciones detalladas
     */
    public function getEstadisticasCalificacionesDetalladas(): array
    {
        try {
            $db = DB::getInstance();
            $query = "
                SELECT 
                    calificacion,
                    COUNT(*) as cantidad,
                    ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM calificaciones_libros WHERE libro_id = ?)), 1) as porcentaje
                FROM calificaciones_libros 
                WHERE libro_id = ?
                GROUP BY calificacion
                ORDER BY calificacion DESC
            ";
            
            $result = $db->query($query, [$this->id, $this->id]);
            $distribuciones = $result->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'calificacion_promedio' => $this->calificacion_promedio,
                'total_calificaciones' => $this->calificaciones()->count(),
                'distribucion' => $distribuciones
            ];
        } catch (Exception $e) {
            return [
                'calificacion_promedio' => 0.0,
                'total_calificaciones' => 0,
                'distribucion' => []
            ];
        }
    }

    /**
     * Obtener calificaciones con información de usuarios
     */
    public function getCalificacionesConUsuarios(int $limite = 10): array
    {
        try {
            $db = DB::getInstance();
            $query = "
                SELECT 
                    c.calificacion,
                    c.comentario,
                    c.fecha_calificacion,
                    u.nombre_completo,
                    u.avatar
                FROM calificaciones_libros c
                INNER JOIN usuarios u ON c.usuario_id = u.id
                WHERE c.libro_id = ?
                ORDER BY c.fecha_calificacion DESC
                LIMIT ?
            ";
            
            $result = $db->query($query, [$this->id, $limite]);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Verificar si un libro puede ser calificado por un usuario
     */
    public function puedeSerCalificadoPor(int $usuarioId): bool
    {
        try {
            // Verificar si el usuario ha descargado el libro
            $db = DB::getInstance();
            $result = $db->query(
                "SELECT COUNT(*) as count FROM descargas_libros WHERE libro_id = ? AND usuario_id = ?", 
                [$this->id, $usuarioId]
            );
            $row = $result->fetch(PDO::FETCH_ASSOC);
            
            return $row['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }
}
