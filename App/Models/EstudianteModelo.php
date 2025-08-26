<?php

namespace App\Models;

use Exception;
use PDO;
use Core\DB;



/**
 * EstudianteModelo - Consultas específicas para estudiantes basadas en BD real
 */
class EstudianteModelo
{
    private static $db;

    private static function getDB()
    {
        if (!self::$db) {
            // Asumiendo configuración de BD existente
            self::$db = DB::getInstance()->getConnection();
        }
        return self::$db;
    }

    /**
     * Obtiene estadísticas del dashboard del estudiante
     */
    public static function getDashboardStats(int $estudianteId): array
    {
        try {
            return [
                'metricas_estudiante' => self::getMetricasGenerales($estudianteId),
                'cursos_actuales' => self::getCursosConProgreso($estudianteId),
                'libros_disponibles' => self::getLibrosActivos(),
                'actividad_reciente' => self::getActividadReciente($estudianteId),
                'progreso_cursos' => self::getProgresoCompleto($estudianteId)
            ];
        } catch (Exception $e) {
            throw new Exception("Error al obtener estadísticas: " . $e->getMessage());
        }
    }

    /**
     * Métricas generales del estudiante
     */
    public static function getMetricasGenerales(int $estudianteId): array
    {
        $db = self::getDB();
        
        // Cursos inscritos
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM progreso_estudiantes WHERE estudiante_id = ?");
        $stmt->execute([$estudianteId]);
        $cursosInscritos = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Progreso general
        $stmt = $db->prepare("SELECT AVG(progreso_porcentaje) as promedio FROM progreso_estudiantes WHERE estudiante_id = ?");
        $stmt->execute([$estudianteId]);
        $progresoGeneral = round($stmt->fetch(PDO::FETCH_ASSOC)['promedio'] ?? 0, 1);

        // Cursos completados (certificados)
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM progreso_estudiantes WHERE estudiante_id = ? AND completado = 1");
        $stmt->execute([$estudianteId]);
        $certificados = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        // Tiempo total estudiado (en minutos)
        $stmt = $db->prepare("SELECT SUM(tiempo_estudiado) as total FROM progreso_estudiantes WHERE estudiante_id = ?");
        $stmt->execute([$estudianteId]);
        $tiempoTotal = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        return [
            'cursos_inscritos' => (int)$cursosInscritos,
            'progreso_general' => (float)$progresoGeneral,
            'certificados_obtenidos' => (int)$certificados,
            'tiempo_estudio_total' => round($tiempoTotal / 60, 1) // convertir a horas
        ];
    }

    /**
     * Obtiene cursos con progreso del estudiante
     */
    public static function getCursosConProgreso(int $estudianteId): array
    {
        $db = self::getDB();
        
        $query = "
            SELECT c.id, c.titulo, c.descripcion, c.nivel, cat.nombre as categoria,
                   u.nombre as profesor_nombre, u.apellido as profesor_apellido,
                   p.progreso_porcentaje, p.tiempo_estudiado, p.ultima_actividad, p.completado
            FROM progreso_estudiantes p
            INNER JOIN cursos c ON p.curso_id = c.id
            INNER JOIN categorias cat ON c.categoria_id = cat.id
            INNER JOIN users u ON c.docente_id = u.id
            WHERE p.estudiante_id = ? AND c.estado = 'Publicado'
            ORDER BY p.ultima_actividad DESC
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$estudianteId]);
        
        $cursos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cursos[] = [
                'id' => $row['id'],
                'titulo' => $row['titulo'],
                'categoria' => $row['categoria'],
                'profesor' => $row['profesor_nombre'] . ' ' . $row['profesor_apellido'],
                'progreso' => round($row['progreso_porcentaje'], 1),
                'estado' => $row['completado'] ? 'Completado' : 'En Progreso',
                'tiempo_estudiado' => $row['tiempo_estudiado'],
                'ultima_actividad' => $row['ultima_actividad']
            ];
        }
        
        return $cursos;
    }

    /**
     * Obtiene libros activos disponibles
     */
    public static function getLibrosActivos(): array
    {
        $db = self::getDB();
        
        $query = "
            SELECT l.id, l.titulo, l.autor, l.descripcion, l.precio, l.es_gratuito,
                   l.stock, l.imagen_portada, cat.nombre as categoria
            FROM libros l
            INNER JOIN categorias cat ON l.categoria_id = cat.id
            WHERE l.estado = 1 AND l.stock > 0
            ORDER BY l.fecha_creacion DESC
            LIMIT 10
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute();
        
        $libros = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $libros[] = [
                'id' => $row['id'],
                'titulo' => $row['titulo'],
                'autor' => $row['autor'],
                'categoria' => $row['categoria'],
                'precio' => $row['precio'],
                'es_gratuito' => (bool)$row['es_gratuito'],
                'stock' => $row['stock'],
                'imagen' => $row['imagen_portada']
            ];
        }
        
        return $libros;
    }

    /**
     * Actividad reciente del estudiante
     */
    public static function getActividadReciente(int $estudianteId): array
    {
        $db = self::getDB();
        
        $actividades = [];
        
        // Descargas de libros recientes
        $query = "
            SELECT 'descarga' as tipo, l.titulo as detalle, d.fecha_descarga as fecha
            FROM descargas_libros d
            INNER JOIN libros l ON d.libro_id = l.id
            WHERE d.usuario_id = ?
            ORDER BY d.fecha_descarga DESC
            LIMIT 5
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$estudianteId]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $actividades[] = [
                'tipo' => 'descarga_libro',
                'titulo' => 'Libro Descargado',
                'descripcion' => 'Descargaste "' . $row['detalle'] . '"',
                'fecha' => $row['fecha'],
                'icono' => 'download',
                'color' => '#3b82f6'
            ];
        }
        
        // Progreso en cursos (últimas actualizaciones)
        $query = "
            SELECT c.titulo, p.progreso_porcentaje, p.ultima_actividad
            FROM progreso_estudiantes p
            INNER JOIN cursos c ON p.curso_id = c.id
            WHERE p.estudiante_id = ?
            ORDER BY p.ultima_actividad DESC
            LIMIT 3
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$estudianteId]);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $actividades[] = [
                'tipo' => 'progreso_curso',
                'titulo' => 'Progreso en Curso',
                'descripcion' => 'Avanzaste en "' . $row['titulo'] . '" (' . round($row['progreso_porcentaje'], 1) . '%)',
                'fecha' => $row['ultima_actividad'],
                'icono' => 'chart-line',
                'color' => '#10b981'
            ];
        }
        
        // Ordenar por fecha
        usort($actividades, function($a, $b) {
            return strtotime($b['fecha']) - strtotime($a['fecha']);
        });
        
        return array_slice($actividades, 0, 6);
    }

    /**
     * Progreso completo de todos los cursos
     */
    public static function getProgresoCompleto(int $estudianteId): array
    {
        $db = self::getDB();
        
        $query = "
            SELECT c.titulo, p.progreso_porcentaje, p.tiempo_estudiado, p.completado,
                   p.fecha_inscripcion, p.ultima_actividad
            FROM progreso_estudiantes p
            INNER JOIN cursos c ON p.curso_id = c.id
            WHERE p.estudiante_id = ?
            ORDER BY p.ultima_actividad DESC
        ";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$estudianteId]);
        
        $progreso = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $progreso[] = [
                'curso' => $row['titulo'],
                'progreso' => round($row['progreso_porcentaje'], 1),
                'tiempo_horas' => round($row['tiempo_estudiado'] / 60, 1),
                'completado' => (bool)$row['completado'],
                'fecha_inscripcion' => $row['fecha_inscripcion'],
                'ultima_actividad' => $row['ultima_actividad']
            ];
        }
        
        return $progreso;
    }

    /**
     * Registra descarga de libro
     */
    public static function registrarDescargaLibro(int $estudianteId, int $libroId, string $ip): bool
    {
        $db = self::getDB();
        
        $query = "INSERT INTO descargas_libros (usuario_id, libro_id, ip_address, fecha_descarga) 
                  VALUES (?, ?, ?, NOW())";
        
        $stmt = $db->prepare($query);
        return $stmt->execute([$estudianteId, $libroId, $ip]);
    }

    /**
     * Actualiza progreso del estudiante en un curso
     */
    public static function actualizarProgresoEstudiante(int $estudianteId, int $cursoId, float $progreso): bool
    {
        $db = self::getDB();
        
        $query = "UPDATE progreso_estudiantes 
                  SET progreso_porcentaje = ?, ultima_actividad = NOW(), 
                      completado = CASE WHEN ? >= 100 THEN 1 ELSE 0 END
                  WHERE estudiante_id = ? AND curso_id = ?";
        
        $stmt = $db->prepare($query);
        return $stmt->execute([$progreso, $progreso, $estudianteId, $cursoId]);
    }

    /**
     * Verifica si el estudiante tiene acceso a un curso
     */
    public static function tieneAccesoCurso(int $estudianteId, int $cursoId): bool
    {
        $db = self::getDB();
        
        $query = "SELECT COUNT(*) as total FROM progreso_estudiantes 
                  WHERE estudiante_id = ? AND curso_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$estudianteId, $cursoId]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    /**
     * Métricas específicas de cursos para AJAX
     */
    public static function getMetricasCursos(int $estudianteId): array
    {
        $metricas = self::getMetricasGenerales($estudianteId);
        return [
            'inscritos' => $metricas['cursos_inscritos'],
            'completados' => $metricas['certificados_obtenidos'],
            'en_progreso' => $metricas['cursos_inscritos'] - $metricas['certificados_obtenidos']
        ];
    }

    /**
     * Métricas específicas de progreso para AJAX
     */
    public static function getMetricasProgreso(int $estudianteId): array
    {
        $metricas = self::getMetricasGenerales($estudianteId);
        return [
            'general' => $metricas['progreso_general'],
            'tiempo_total' => $metricas['tiempo_estudio_total'],
            'certificados' => $metricas['certificados_obtenidos']
        ];
    }
}