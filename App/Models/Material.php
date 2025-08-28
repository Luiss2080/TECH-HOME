<?php

namespace App\Models;

use Core\Model;

class Material extends Model
{
    protected $table = 'materiales';
    protected $primaryKey = 'id';
    protected $fillable = [
        'titulo',
        'descripcion',
        'tipo',
        'archivo',
        'enlace_externo',
        'tamaño_archivo',
        'duracion',
        'categoria_id',
        'docente_id',
        'imagen_preview',
        'publico',
        'descargas',
        'estado',
        'fecha_creacion',
        'fecha_actualizacion'
    ];
    protected $hidden = [];
    protected $timestamps = false; // Manejo manual de timestamps

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Relación con Categoria
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id');
    }

    /**
     * Relación con Docente (Usuario)
     */
    public function docente()
    {
        return $this->belongsTo(User::class, 'docente_id', 'id');
    }

    // ==========================================
    // SCOPES
    // ==========================================

    /**
     * Materiales activos
     */
    public static function activos()
    {
        return self::where('estado', '=', 1);
    }

    /**
     * Materiales públicos
     */
    public static function publicos()
    {
        return self::where('publico', '=', 1);
    }

    /**
     * Materiales disponibles (activos y públicos)
     */
    public static function disponibles()
    {
        return self::where('estado', '=', 1)->where('publico', '=', 1);
    }

    /**
     * Materiales por tipo
     */
    public static function porTipo($tipo)
    {
        return self::where('tipo', '=', $tipo);
    }

    /**
     * Materiales por categoría
     */
    public static function porCategoria($categoriaId)
    {
        return self::where('categoria_id', '=', $categoriaId);
    }

    /**
     * Materiales por docente
     */
    public static function porDocente($docenteId)
    {
        return self::where('docente_id', '=', $docenteId);
    }

    /**
     * Materiales recientes
     */
    public static function recientes(int $dias = 7)
    {
        return self::whereRaw('fecha_creacion >= DATE_SUB(NOW(), INTERVAL ? DAY)', [$dias])
            ->orderBy('fecha_creacion', 'desc');
    }

    /**
     * Materiales con más descargas
     */
    public static function masDescargados($limit = 10)
    {
        return self::where('estado', '=', 1)
            ->orderBy('descargas', 'DESC')
            ->limit($limit);
    }

    /**
     * Buscar materiales por título o descripción
     */
    public static function buscar($termino)
    {
        return self::where('estado', '=', 1)
            ->whereRaw("(titulo LIKE ? OR descripcion LIKE ?)", ["%{$termino}%", "%{$termino}%"]);
    }

    // ==========================================
    // MÉTODOS AUXILIARES
    // ==========================================

    /**
     * Incrementar contador de descargas
     */
    public function incrementarDescargas()
    {
        $this->descargas = ($this->descargas ?? 0) + 1;
        $this->save();
    }

    /**
     * Obtener tamaño de archivo formateado
     */
    public function getTamañoFormateado()
    {
        if (!$this->tamaño_archivo || $this->tamaño_archivo == 0) {
            return 'N/A';
        }
        return formatearBytes($this->tamaño_archivo);
    }

    /**
     * Obtener duración formateada
     */
    public function getDuracionFormateada()
    {
        if (!$this->duracion) {
            return 'N/A';
        }
        return formatearTiempo($this->duracion / 60); // Convertir segundos a minutos
    }

    /**
     * Verificar si es archivo local
     */
    public function esArchivoLocal()
    {
        return !empty($this->archivo) && empty($this->enlace_externo);
    }

    /**
     * Verificar si es enlace externo
     */
    public function esEnlaceExterno()
    {
        return !empty($this->enlace_externo);
    }

    /**
     * Obtener ruta completa del archivo
     */
    public function getRutaCompleta()
    {
        if ($this->esArchivoLocal()) {
            return asset('materiales/' . ltrim($this->archivo, '/'));
        }
        return $this->enlace_externo;
    }

    /**
     * Obtener icono según el tipo
     */
    public function getIcono()
    {
        $iconos = [
            'video' => 'fas fa-play-circle',
            'documento' => 'fas fa-file-pdf',
            'presentacion' => 'fas fa-file-powerpoint',
            'audio' => 'fas fa-file-audio',
            'enlace' => 'fas fa-link',
            'otro' => 'fas fa-file'
        ];

        return $iconos[$this->tipo] ?? $iconos['otro'];
    }

    /**
     * Obtener clase CSS según el tipo
     */
    public function getClaseTipo()
    {
        $clases = [
            'video' => 'danger',
            'documento' => 'primary',
            'presentacion' => 'warning',
            'audio' => 'success',
            'enlace' => 'info',
            'otro' => 'secondary'
        ];

        return $clases[$this->tipo] ?? $clases['otro'];
    }

    /**
     * Verificar si el usuario puede acceder al material
     */
    public function puedeAcceder($userId = null)
    {
        // Si es público, todos pueden acceder
        if ($this->publico == 1) {
            return true;
        }

        // Si no está activo, nadie puede acceder
        if ($this->estado != 1) {
            return false;
        }

        // Si no hay usuario logueado y no es público, no puede acceder
        if (!$userId) {
            return false;
        }

        // El docente creador siempre puede acceder
        if ($this->docente_id == $userId) {
            return true;
        }

        // Los administradores siempre pueden acceder
        $user = User::find($userId);
        if ($user && $user->hasRole('administrador')) {
            return true;
        }

        // Verificar acceso específico (si se implementa tabla acceso_materiales)
        // Por ahora, asumimos que los usuarios autenticados pueden acceder a materiales no públicos
        return true;
    }

    /**
     * Estadísticas del material
     */
    public function getEstadisticas()
    {
        return [
            'descargas' => $this->descargas ?? 0,
            'tamaño' => $this->getTamañoFormateado(),
            'duracion' => $this->getDuracionFormateada(),
            'tipo' => ucfirst($this->tipo),
            'publico' => $this->publico ? 'Sí' : 'No',
            'fecha_creacion' => $this->fecha_creacion
        ];
    }

    // ==========================================
    // MÉTODOS ESTÁTICOS DE ESTADÍSTICAS
    // ==========================================

    /**
     * Contar materiales por tipo
     */
    public static function contarPorTipo()
    {
        $tipos = ['video', 'documento', 'presentacion', 'audio', 'enlace', 'otro'];
        $estadisticas = [];

        foreach ($tipos as $tipo) {
            $estadisticas[$tipo] = self::porTipo($tipo)->where('estado', '=', 1)->count();
        }

        return $estadisticas;
    }

    /**
     * Total de descargas de todos los materiales
     */
    public static function totalDescargas()
    {
        $result = self::query()->whereRaw('SUM(descargas) as total')->first();
        return $result->total ?? 0;
    }

    /**
     * Materiales más populares por categoría
     */
    public static function popularesPorCategoria($categoriaId, $limit = 5)
    {
        return self::porCategoria($categoriaId)
            ->where('estado', '=', 1)
            ->orderBy('descargas', 'DESC')
            ->limit($limit)
            ->get();
    }

    /**
     * Validar archivo permitido
     */
    public static function esArchivoPermitido($extension)
    {
        $extensionesPermitidas = [
            // Documentos
            'pdf', 'doc', 'docx', 'txt', 'rtf',
            // Presentaciones
            'ppt', 'pptx', 'odp',
            // Videos
            'mp4', 'avi', 'mov', 'wmv', 'flv', 'webm',
            // Audios
            'mp3', 'wav', 'ogg', 'wma', 'aac',
            // Imágenes (para previews)
            'jpg', 'jpeg', 'png', 'gif', 'webp',
            // Otros
            'zip', 'rar', '7z'
        ];

        return in_array(strtolower($extension), $extensionesPermitidas);
    }

    /**
     * Obtener tipo sugerido según extensión
     */
    public static function getTipoSugeridoPorExtension($extension)
    {
        $extension = strtolower($extension);
        
        $mapa = [
            // Videos
            'mp4' => 'video', 'avi' => 'video', 'mov' => 'video', 
            'wmv' => 'video', 'flv' => 'video', 'webm' => 'video',
            // Documentos
            'pdf' => 'documento', 'doc' => 'documento', 'docx' => 'documento',
            'txt' => 'documento', 'rtf' => 'documento',
            // Presentaciones
            'ppt' => 'presentacion', 'pptx' => 'presentacion', 'odp' => 'presentacion',
            // Audios
            'mp3' => 'audio', 'wav' => 'audio', 'ogg' => 'audio',
            'wma' => 'audio', 'aac' => 'audio'
        ];

        return $mapa[$extension] ?? 'otro';
    }
}
