<?php

namespace App\Models;

use Core\Model;
use Core\DB;
use PDO;
use Exception;

class Curso extends Model
{
    protected $table = 'cursos';
    protected $primaryKey = 'id';
    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'contenido',
        'docente_id',
        'categoria_id',
        'imagen_portada',
        'precio',
        'duracion_horas',
        'max_estudiantes',
        'nivel',
        'modalidad',
        'certificado',
        'fecha_inicio',
        'fecha_fin',
        'estudiantes_inscritos',
        'calificacion_promedio',
        'total_calificaciones',
        'requisitos',
        'objetivos',
        'estado'
    ];
    protected $hidden = [];
    protected $timestamps = true;
    protected $softDeletes = false;

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Obtener el docente que imparte el curso
     */
    public function docente()
    {
        try {
            return User::find($this->docente_id);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Obtener la categoría del curso
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
     * Obtener estudiantes inscritos en el curso (usando nueva tabla)
     */
    public function estudiantes()
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT u.*, ic.fecha_inscripcion, ic.estado as estado_inscripcion, 
                             ic.metodo_pago, ic.monto_pagado, ic.completado,
                             pe.progreso_porcentaje, pe.tiempo_estudiado, pe.ultima_actividad
                      FROM usuarios u
                      INNER JOIN inscripciones_cursos ic ON u.id = ic.estudiante_id
                      LEFT JOIN progreso_estudiantes pe ON u.id = pe.estudiante_id AND pe.curso_id = ic.curso_id
                      WHERE ic.curso_id = ? AND ic.estado != 'Cancelada'
                      ORDER BY ic.fecha_inscripcion DESC";
            
            $result = $db->query($query, [$this->id]);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener módulos del curso
     */
    public function modulos()
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT * FROM modulos_curso 
                      WHERE curso_id = ? AND estado = 1 
                      ORDER BY orden ASC";
            
            $result = $db->query($query, [$this->id]);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener inscripciones del curso
     */
    public function inscripciones()
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT ic.*, u.nombre, u.apellido, u.email
                      FROM inscripciones_cursos ic
                      INNER JOIN usuarios u ON ic.estudiante_id = u.id
                      WHERE ic.curso_id = ?
                      ORDER BY ic.fecha_inscripcion DESC";
            
            $result = $db->query($query, [$this->id]);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener calificaciones del curso
     */
    public function calificaciones()
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT cc.*, u.nombre, u.apellido
                      FROM calificaciones_cursos cc
                      INNER JOIN usuarios u ON cc.usuario_id = u.id
                      WHERE cc.curso_id = ?
                      ORDER BY cc.fecha_calificacion DESC";
            
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
            $query = "SELECT u.*, fc.fecha_agregado
                      FROM usuarios u
                      INNER JOIN favoritos_cursos fc ON u.id = fc.usuario_id
                      WHERE fc.curso_id = ?
                      ORDER BY fc.fecha_agregado DESC";
            
            $result = $db->query($query, [$this->id]);
            return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener progreso de un estudiante específico
     */
    public function getProgresoEstudiante(int $estudianteId)
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT * FROM progreso_estudiantes 
                      WHERE curso_id = ? AND estudiante_id = ?";
            
            $result = $db->query($query, [$this->id, $estudianteId]);
            return $result ? $result->fetch(PDO::FETCH_ASSOC) : null;
        } catch (Exception $e) {
            return null;
        }
    }

    // ==========================================
    // SCOPES ESTÁTICOS
    // ==========================================

    /**
     * Obtener cursos publicados
     */
    public static function publicados()
    {
        return self::where('estado', '=', 'Publicado');
    }

    /**
     * Obtener cursos por nivel
     */
    public static function porNivel(string $nivel)
    {
        return self::where('nivel', '=', $nivel);
    }

    /**
     * Obtener cursos por docente
     */
    public static function porDocente(int $docenteId)
    {
        return self::where('docente_id', '=', $docenteId);
    }

    /**
     * Obtener cursos por categoría
     */
    public static function porCategoria(int $categoriaId)
    {
        return self::where('categoria_id', '=', $categoriaId);
    }

    /**
     * Obtener cursos recientes
     */
    public static function recientes(int $dias = 7)
    {
        return self::whereRaw('fecha_actualizacion >= DATE_SUB(NOW(), INTERVAL ? DAY)', [$dias])
                   ->orderBy('fecha_actualizacion', 'desc');
    }

    /**
     * Obtener cursos gratuitos
     */
    public static function gratuitos()
    {
        return self::where('precio', '=', 0)->where('estado', '=', 'Publicado');
    }

    /**
     * Obtener cursos de pago
     */
    public static function dePago()
    {
        return self::where('precio', '>', 0)->where('estado', '=', 'Publicado');
    }

    /**
     * Obtener cursos populares (más estudiantes)
     */
    public static function populares(int $limit = 10)
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT c.*, COUNT(pe.estudiante_id) as total_estudiantes
                      FROM cursos c
                      LEFT JOIN progreso_estudiantes pe ON c.id = pe.curso_id
                      WHERE c.estado = 'Publicado'
                      GROUP BY c.id
                      ORDER BY total_estudiantes DESC, c.fecha_actualizacion DESC
                      LIMIT ?";
            
            $result = $db->query($query, [$limit]);
            $cursos = [];
            
            if ($result) {
                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    $cursos[] = new self($row);
                }
            }
            
            return $cursos;
        } catch (Exception $e) {
            return [];
        }
    }

    // ==========================================
    // MÉTODOS DE INSTANCIA
    // ==========================================

    /**
     * Verificar si un estudiante está inscrito (usando nueva tabla)
     */
    public function tieneEstudiante(int $estudianteId): bool
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT COUNT(*) as count FROM inscripciones_cursos 
                      WHERE curso_id = ? AND estudiante_id = ? AND estado != 'Cancelada'";
            
            $result = $db->query($query, [$this->id, $estudianteId]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            
            return $row['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Verificar si un usuario marcó el curso como favorito
     */
    public function esFavoritoDe(int $usuarioId): bool
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT COUNT(*) as count FROM favoritos_cursos 
                      WHERE curso_id = ? AND usuario_id = ?";
            
            $result = $db->query($query, [$this->id, $usuarioId]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            
            return $row['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtener total de estudiantes inscritos (usando nueva tabla)
     */
    public function getTotalEstudiantes(): int
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT COUNT(*) as count FROM inscripciones_cursos 
                      WHERE curso_id = ? AND estado IN ('Activa', 'Completada')";
            
            $result = $db->query($query, [$this->id]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener total de módulos del curso
     */
    public function getTotalModulos(): int
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT COUNT(*) as count FROM modulos_curso WHERE curso_id = ? AND estado = 1";
            
            $result = $db->query($query, [$this->id]);
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
            $query = "SELECT COUNT(*) as count FROM favoritos_cursos WHERE curso_id = ?";
            
            $result = $db->query($query, [$this->id]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Inscribir estudiante al curso
     */
    public function inscribirEstudiante(int $estudianteId, string $metodoPago = 'Gratuito', float $montoPagado = 0.00): bool
    {
        try {
            $db = DB::getInstance();
            
            // Verificar que no esté ya inscrito
            if ($this->tieneEstudiante($estudianteId)) {
                return false;
            }
            
            // Verificar límite de estudiantes
            if ($this->max_estudiantes && $this->getTotalEstudiantes() >= $this->max_estudiantes) {
                return false;
            }
            
            $db->beginTransaction();
            
            // Insertar inscripción
            $db->query(
                "INSERT INTO inscripciones_cursos (estudiante_id, curso_id, metodo_pago, monto_pagado) VALUES (?, ?, ?, ?)",
                [$estudianteId, $this->id, $metodoPago, $montoPagado]
            );
            
            // Crear progreso inicial
            $db->query(
                "INSERT IGNORE INTO progreso_estudiantes (estudiante_id, curso_id, progreso_porcentaje) VALUES (?, ?, 0.00)",
                [$estudianteId, $this->id]
            );
            
            // Actualizar contador
            $this->estudiantes_inscritos = $this->getTotalEstudiantes();
            $this->save();
            
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollback();
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
                $db->query(
                    "DELETE FROM favoritos_cursos WHERE curso_id = ? AND usuario_id = ?",
                    [$this->id, $usuarioId]
                );
            } else {
                // Agregar a favoritos
                $db->query(
                    "INSERT INTO favoritos_cursos (usuario_id, curso_id) VALUES (?, ?)",
                    [$usuarioId, $this->id]
                );
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Calificar curso
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
                "INSERT INTO calificaciones_cursos (usuario_id, curso_id, calificacion, comentario) 
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
                      FROM calificaciones_cursos WHERE curso_id = ?";
            
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
     * Obtener progreso promedio del curso
     */
    public function getProgresoPromedio(): float
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT AVG(progreso_porcentaje) as promedio FROM progreso_estudiantes WHERE curso_id = ?";
            
            $result = $db->query($query, [$this->id]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            
            return round($row['promedio'] ?? 0, 2);
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Obtener estudiantes que completaron el curso
     */
    public function getEstudiantesCompletados(): int
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT COUNT(*) as count FROM progreso_estudiantes 
                      WHERE curso_id = ? AND completado = 1";
            
            $result = $db->query($query, [$this->id]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            
            return $row['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }

    /**
     * Verificar si el curso puede ser eliminado
     */
    public function puedeSerEliminado(): bool
    {
        return $this->getTotalEstudiantes() === 0;
    }

    /**
     * Obtener duración formateada
     */
    public function getDuracionFormateada(): string
    {
        if ($this->duracion_horas <= 0) {
            return 'No especificada';
        }
        
        if ($this->duracion_horas < 1) {
            return round($this->duracion_horas * 60) . ' minutos';
        }
        
        if ($this->duracion_horas == 1) {
            return '1 hora';
        }
        
        return $this->duracion_horas . ' horas';
    }

    /**
     * Obtener precio formateado
     */
    public function getPrecioFormateado(): string
    {
        if ($this->precio <= 0) {
            return 'Gratuito';
        }
        
        return 'Bs. ' . number_format($this->precio, 2);
    }

    /**
     * Obtener clase CSS según el estado
     */
    public function getEstadoClass(): string
    {
        $classes = [
            'Publicado' => 'success',
            'Borrador' => 'secondary',
            'Archivado' => 'warning'
        ];
        
        return $classes[$this->estado] ?? 'secondary';
    }

    /**
     * Obtener clase CSS según el nivel
     */
    public function getNivelClass(): string
    {
        $classes = [
            'Principiante' => 'success',
            'Intermedio' => 'warning',
            'Avanzado' => 'danger'
        ];
        
        return $classes[$this->nivel] ?? 'secondary';
    }

    /**
     * Verificar si el curso está disponible para inscripción
     */
    public function estaDisponible(): bool
    {
        if ($this->estado !== 'Publicado') {
            return false;
        }
        
        // Verificar límite de estudiantes
        if ($this->max_estudiantes && $this->getTotalEstudiantes() >= $this->max_estudiantes) {
            return false;
        }
        
        return true;
    }

    /**
     * Obtener modalidad formateada
     */
    public function getModalidadFormateada(): string
    {
        $modalidades = [
            'Presencial' => 'Presencial',
            'Virtual' => 'Virtual',
            'Híbrido' => 'Híbrido'
        ];
        
        return $modalidades[$this->modalidad] ?? 'Virtual';
    }

    /**
     * Obtener clase CSS de la modalidad
     */
    public function getModalidadClass(): string
    {
        $classes = [
            'Presencial' => 'info',
            'Virtual' => 'success', 
            'Híbrido' => 'warning'
        ];
        
        return $classes[$this->modalidad] ?? 'secondary';
    }

    /**
     * Verificar si genera certificado
     */
    public function generaCertificado(): bool
    {
        return $this->certificado == 1;
    }

    /**
     * Obtener estado de disponibilidad
     */
    public function getEstadoDisponibilidad(): array
    {
        if ($this->estado !== 'Publicado') {
            return [
                'status' => 'no_disponible',
                'class' => 'secondary',
                'text' => 'No Disponible',
                'icon' => 'x-circle'
            ];
        }
        
        if ($this->max_estudiantes && $this->getTotalEstudiantes() >= $this->max_estudiantes) {
            return [
                'status' => 'completo',
                'class' => 'danger',
                'text' => 'Cupo Completo',
                'icon' => 'users'
            ];
        }
        
        $disponibles = $this->max_estudiantes ? ($this->max_estudiantes - $this->getTotalEstudiantes()) : null;
        
        return [
            'status' => 'disponible',
            'class' => 'success',
            'text' => $disponibles ? "Disponible ($disponibles cupos)" : 'Disponible',
            'icon' => 'check-circle'
        ];
    }

    /**
     * Obtener calificación con estrellas
     */
    public function getCalificacionEstrellas(): string
    {
        $calificacion = $this->calificacion_promedio;
        $estrellas = '';
        
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $calificacion) {
                $estrellas .= '★';
            } elseif ($i - 0.5 <= $calificacion) {
                $estrellas .= '☆';
            } else {
                $estrellas .= '☆';
            }
        }
        
        return $estrellas . " ({$this->total_calificaciones} reseñas)";
    }

    /**
     * Obtener información completa del curso
     */
    public function getInformacionCompleta(): array
    {
        $docente = $this->docente();
        $categoria = $this->categoria();
        
        return [
            'basica' => $this->getAttributes(),
            'docente' => $docente ? [
                'nombre' => $docente->nombre,
                'apellido' => $docente->apellido,
                'email' => $docente->email
            ] : null,
            'categoria' => $categoria ? [
                'nombre' => $categoria->nombre,
                'color' => $categoria->color,
                'icono' => $categoria->icono
            ] : null,
            'estadisticas' => [
                'total_estudiantes' => $this->getTotalEstudiantes(),
                'total_modulos' => $this->getTotalModulos(),
                'total_favoritos' => $this->getTotalFavoritos(),
                'progreso_promedio' => $this->getProgresoPromedio(),
                'estudiantes_completaron' => $this->getEstudiantesCompletados()
            ],
            'disponibilidad' => $this->getEstadoDisponibilidad(),
            'formateado' => [
                'precio' => $this->getPrecioFormateado(),
                'duracion' => $this->getDuracionFormateada(),
                'modalidad' => $this->getModalidadFormateada(),
                'calificacion' => $this->getCalificacionEstrellas()
            ]
        ];
    }

    /**
     * Obtener URL de la imagen de portada
     */
    public function getImagenPortadaUrl(): string
    {
        if (!$this->imagen_portada) {
            return asset('images/cursos/default.jpg');
        }
        
        return asset('images/cursos/' . $this->imagen_portada);
    }
}
