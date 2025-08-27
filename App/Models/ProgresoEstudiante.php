<?php

namespace App\Models;

use Core\Model;
use Core\DB;
use PDO;
use Exception;

class ProgresoEstudiante extends Model
{
    protected $table = 'progreso_estudiantes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'estudiante_id',
        'curso_id',
        'progreso_porcentaje',
        'tiempo_estudiado',
        'completado'
    ];
    protected $hidden = [];
    protected $timestamps = true;
    protected $softDeletes = false;

    // ==========================================
    // RELACIONES
    // ==========================================

    /**
     * Obtener el estudiante
     */
    public function estudiante()
    {
        try {
            return User::find($this->estudiante_id);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Obtener el curso
     */
    public function curso()
    {
        try {
            return Curso::find($this->curso_id);
        } catch (Exception $e) {
            return null;
        }
    }

    // ==========================================
    // SCOPES ESTÁTICOS
    // ==========================================

    /**
     * Obtener progreso por estudiante
     */
    public static function porEstudiante(int $estudianteId)
    {
        return self::where('estudiante_id', '=', $estudianteId);
    }

    /**
     * Obtener progreso por curso
     */
    public static function porCurso(int $cursoId)
    {
        return self::where('curso_id', '=', $cursoId);
    }

    /**
     * Obtener cursos completados
     */
    public static function completados()
    {
        return self::where('completado', '=', 1);
    }

    /**
     * Obtener cursos en progreso
     */
    public static function enProgreso()
    {
        return self::where('completado', '=', 0)
                   ->where('progreso_porcentaje', '>', 0);
    }

    /**
     * Obtener actividad reciente
     */
    public static function actividadReciente(int $dias = 7)
    {
        return self::whereRaw('ultima_actividad >= DATE_SUB(NOW(), INTERVAL ? DAY)', [$dias])
                   ->orderBy('ultima_actividad', 'desc');
    }

    // ==========================================
    // MÉTODOS ESTÁTICOS DE CONSULTA
    // ==========================================

    /**
     * Obtener progreso de un estudiante en un curso específico
     */
    public static function getProgreso(int $estudianteId, int $cursoId)
    {
        return self::where('estudiante_id', '=', $estudianteId)
                   ->where('curso_id', '=', $cursoId)
                   ->first();
    }

    /**
     * Crear o actualizar progreso
     */
    public static function actualizarProgreso(int $estudianteId, int $cursoId, float $progreso, int $tiempoAdicional = 0): bool
    {
        try {
            $progresoExistente = self::getProgreso($estudianteId, $cursoId);
            
            if ($progresoExistente) {
                // Actualizar progreso existente
                $progresoExistente->progreso_porcentaje = min(100, $progreso);
                $progresoExistente->tiempo_estudiado += $tiempoAdicional;
                $progresoExistente->completado = $progreso >= 100 ? 1 : 0;
                $progresoExistente->save();
            } else {
                // Crear nuevo registro de progreso
                $nuevoProgreso = new self([
                    'estudiante_id' => $estudianteId,
                    'curso_id' => $cursoId,
                    'progreso_porcentaje' => min(100, $progreso),
                    'tiempo_estudiado' => $tiempoAdicional,
                    'completado' => $progreso >= 100 ? 1 : 0
                ]);
                $nuevoProgreso->save();
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtener estadísticas de progreso de un estudiante
     */
    public static function getEstadisticasEstudiante(int $estudianteId): array
    {
        try {
            $db = DB::getInstance();
            
            // Estadísticas básicas
            $stats = $db->query("
                SELECT 
                    COUNT(*) as total_cursos,
                    COUNT(CASE WHEN completado = 1 THEN 1 END) as cursos_completados,
                    COUNT(CASE WHEN progreso_porcentaje > 0 AND completado = 0 THEN 1 END) as cursos_en_progreso,
                    AVG(progreso_porcentaje) as progreso_promedio,
                    SUM(tiempo_estudiado) as tiempo_total
                FROM progreso_estudiantes 
                WHERE estudiante_id = ?
            ", [$estudianteId])->fetch(PDO::FETCH_ASSOC);
            
            // Actividad reciente
            $actividadReciente = $db->query("
                SELECT COUNT(*) as actividad_reciente
                FROM progreso_estudiantes 
                WHERE estudiante_id = ? 
                AND ultima_actividad >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ", [$estudianteId])->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_cursos' => (int)($stats['total_cursos'] ?? 0),
                'cursos_completados' => (int)($stats['cursos_completados'] ?? 0),
                'cursos_en_progreso' => (int)($stats['cursos_en_progreso'] ?? 0),
                'progreso_promedio' => round((float)($stats['progreso_promedio'] ?? 0), 2),
                'tiempo_total' => (int)($stats['tiempo_total'] ?? 0),
                'actividad_reciente' => (int)($actividadReciente['actividad_reciente'] ?? 0)
            ];
        } catch (Exception $e) {
            return [
                'total_cursos' => 0,
                'cursos_completados' => 0,
                'cursos_en_progreso' => 0,
                'progreso_promedio' => 0,
                'tiempo_total' => 0,
                'actividad_reciente' => 0
            ];
        }
    }

    /**
     * Obtener estadísticas de un curso específico
     */
    public static function getEstadisticasCurso(int $cursoId): array
    {
        try {
            $db = DB::getInstance();
            
            $stats = $db->query("
                SELECT 
                    COUNT(*) as total_estudiantes,
                    COUNT(CASE WHEN completado = 1 THEN 1 END) as estudiantes_completados,
                    COUNT(CASE WHEN progreso_porcentaje > 0 AND completado = 0 THEN 1 END) as estudiantes_en_progreso,
                    AVG(progreso_porcentaje) as progreso_promedio,
                    AVG(tiempo_estudiado) as tiempo_promedio,
                    MAX(progreso_porcentaje) as mejor_progreso,
                    MIN(progreso_porcentaje) as menor_progreso
                FROM progreso_estudiantes 
                WHERE curso_id = ?
            ", [$cursoId])->fetch(PDO::FETCH_ASSOC);
            
            return [
                'total_estudiantes' => (int)($stats['total_estudiantes'] ?? 0),
                'estudiantes_completados' => (int)($stats['estudiantes_completados'] ?? 0),
                'estudiantes_en_progreso' => (int)($stats['estudiantes_en_progreso'] ?? 0),
                'progreso_promedio' => round((float)($stats['progreso_promedio'] ?? 0), 2),
                'tiempo_promedio' => round((float)($stats['tiempo_promedio'] ?? 0), 2),
                'mejor_progreso' => (float)($stats['mejor_progreso'] ?? 0),
                'menor_progreso' => (float)($stats['menor_progreso'] ?? 0)
            ];
        } catch (Exception $e) {
            return [
                'total_estudiantes' => 0,
                'estudiantes_completados' => 0,
                'estudiantes_en_progreso' => 0,
                'progreso_promedio' => 0,
                'tiempo_promedio' => 0,
                'mejor_progreso' => 0,
                'menor_progreso' => 0
            ];
        }
    }

    // ==========================================
    // MÉTODOS DE INSTANCIA
    // ==========================================

    /**
     * Verificar si el curso está completado
     */
    public function estaCompletado(): bool
    {
        return $this->completado == 1 || $this->progreso_porcentaje >= 100;
    }

    /**
     * Obtener porcentaje de progreso formateado
     */
    public function getProgresoFormateado(): string
    {
        return number_format($this->progreso_porcentaje, 1) . '%';
    }

    /**
     * Obtener tiempo estudiado formateado
     */
    public function getTiempoFormateado(): string
    {
        if ($this->tiempo_estudiado < 60) {
            return $this->tiempo_estudiado . ' min';
        }
        
        $horas = floor($this->tiempo_estudiado / 60);
        $minutos = $this->tiempo_estudiado % 60;
        
        if ($horas < 24) {
            return $horas . 'h ' . $minutos . 'm';
        }
        
        $dias = floor($horas / 24);
        $horasRestantes = $horas % 24;
        
        return $dias . 'd ' . $horasRestantes . 'h';
    }

    /**
     * Obtener estado del progreso
     */
    public function getEstado(): array
    {
        if ($this->completado) {
            return [
                'texto' => 'Completado',
                'clase' => 'success',
                'icono' => 'check-circle'
            ];
        }
        
        if ($this->progreso_porcentaje >= 75) {
            return [
                'texto' => 'Casi Terminado',
                'clase' => 'warning',
                'icono' => 'clock'
            ];
        }
        
        if ($this->progreso_porcentaje >= 1) {
            return [
                'texto' => 'En Progreso',
                'clase' => 'info',
                'icono' => 'play-circle'
            ];
        }
        
        return [
            'texto' => 'No Iniciado',
            'clase' => 'secondary',
            'icono' => 'circle'
        ];
    }

    /**
     * Actualizar última actividad
     */
    public function actualizarActividad(): bool
    {
        try {
            $db = DB::getInstance();
            $db->query(
                "UPDATE progreso_estudiantes SET ultima_actividad = NOW() WHERE id = ?",
                [$this->id]
            );
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
