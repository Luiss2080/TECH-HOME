<?php

namespace App\Services;

use App\Models\Curso;
use App\Models\Categoria;
use App\Models\User;
use Core\DB;
use PDO;
use Exception;

class CursoService
{
    /**
     * Obtener todos los cursos con información básica
     */
    public function getAllCursos(): array
    {
        $cursos = Curso::all();
        $cursosData = [];

        foreach ($cursos as $curso) {
            $cursoData = $curso->getAttributes();
            
            // Obtener información del docente
            $docente = User::find($curso->docente_id);
            $cursoData['docente_nombre'] = $docente ? $docente->nombre . ' ' . $docente->apellido : 'Sin docente';
            
            // Obtener información de la categoría
            $categoria = Categoria::find($curso->categoria_id);
            $cursoData['categoria_nombre'] = $categoria ? $categoria->nombre : 'Sin categoría';
            
            // Agregar información del video de YouTube
            $cursoData['video_info'] = [
                'video_id' => $curso->getYoutubeVideoId(),
                'embed_url' => $curso->getYoutubeEmbedUrl(),
                'thumbnail' => $curso->getYoutubeThumbnail(),
                'es_youtube' => $curso->tieneVideoYoutube()
            ];
            
            $cursosData[] = $cursoData;
        }

        return $cursosData;
    }

    /**
     * Obtener cursos por docente
     */
    public function getCursosByDocente(int $docenteId): array
    {
        $cursos = Curso::where('docente_id', '=', $docenteId)->get();
        $cursosData = [];

        foreach ($cursos as $curso) {
            $cursoData = $curso->getAttributes();
            
            // Obtener información de la categoría
            $categoria = Categoria::find($curso->categoria_id);
            $cursoData['categoria_nombre'] = $categoria ? $categoria->nombre : 'Sin categoría';
            
            // Agregar información del video de YouTube
            $cursoData['video_info'] = [
                'video_id' => $curso->getYoutubeVideoId(),
                'embed_url' => $curso->getYoutubeEmbedUrl(),
                'thumbnail' => $curso->getYoutubeThumbnail(),
                'es_youtube' => $curso->tieneVideoYoutube()
            ];
            
            $cursosData[] = $cursoData;
        }

        return $cursosData;
    }

    /**
     * Obtener curso por ID con información completa
     */
    public function getCursoById(int $id)
    {
        $curso = Curso::find($id);
        if (!$curso) {
            return null;
        }

        $cursoData = $curso->getAttributes();
        
        // Obtener información del docente
        $docente = User::find($curso->docente_id);
        $cursoData['docente'] = $docente ? [
            'id' => $docente->id,
            'nombre' => $docente->nombre,
            'apellido' => $docente->apellido,
            'email' => $docente->email
        ] : null;
        
        // Obtener información de la categoría
        $categoria = Categoria::find($curso->categoria_id);
        $cursoData['categoria'] = $categoria ? [
            'id' => $categoria->id,
            'nombre' => $categoria->nombre,
            'color' => $categoria->color ?? '#007bff',
            'icono' => $categoria->icono ?? 'play-circle'
        ] : null;
        
        // Agregar información del video de YouTube
        $cursoData['video_info'] = [
            'video_id' => $curso->getYoutubeVideoId(),
            'embed_url' => $curso->getYoutubeEmbedUrl(),
            'thumbnail' => $curso->getYoutubeThumbnail(),
            'es_youtube' => $curso->tieneVideoYoutube()
        ];

        return $cursoData;
    }

    /**
     * Crear nuevo curso
     */
    public function createCurso(array $cursoData): int
    {
        try {
            // Validar que el docente existe
            $docente = User::find($cursoData['docente_id']);
            if (!$docente) {
                throw new Exception('El docente especificado no existe');
            }

            // Validar que la categoría existe
            $categoria = Categoria::find($cursoData['categoria_id']);
            if (!$categoria) {
                throw new Exception('La categoría especificada no existe');
            }

            // Crear el curso con campos simplificados
            $curso = new Curso([
                'titulo' => $cursoData['titulo'],
                'descripcion' => $cursoData['descripcion'],
                'video_url' => $cursoData['video_url'],
                'docente_id' => $cursoData['docente_id'],
                'categoria_id' => $cursoData['categoria_id'],
                'imagen_portada' => $cursoData['imagen_portada'] ?? null,
                'nivel' => $cursoData['nivel'] ?? 'Principiante',
                'estado' => $cursoData['estado'] ?? 'Borrador',
                'es_gratuito' => $cursoData['es_gratuito'] ?? 1
            ]);

            $curso->save();
            
            // Obtener el ID directamente de la base de datos
            $db = DB::getInstance();
            return (int) $db->getConnection()->lastInsertId();
        } catch (Exception $e) {
            throw new Exception('Error al crear curso: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar curso
     */
    public function updateCurso(int $id, array $cursoData): bool
    {
        try {
            $curso = Curso::find($id);
            if (!$curso) {
                throw new Exception('Curso no encontrado');
            }

            // Validar docente si se proporciona
            if (isset($cursoData['docente_id'])) {
                $docente = User::find($cursoData['docente_id']);
                if (!$docente) {
                    throw new Exception('El docente especificado no existe');
                }
            }

            // Validar categoría si se proporciona
            if (isset($cursoData['categoria_id'])) {
                $categoria = Categoria::find($cursoData['categoria_id']);
                if (!$categoria) {
                    throw new Exception('La categoría especificada no existe');
                }
            }

            // Actualizar usando SQL directo para evitar problemas con el modelo
            $db = DB::getInstance();
            $setParts = [];
            $values = [];
            
            $camposPermitidos = ['titulo', 'descripcion', 'video_url', 'docente_id', 'categoria_id', 'imagen_portada', 'nivel', 'estado'];
            
            foreach ($cursoData as $field => $value) {
                if ($value !== null && in_array($field, $camposPermitidos)) {
                    $setParts[] = "$field = ?";
                    $values[] = $value;
                }
            }
            
            if (!empty($setParts)) {
                $setParts[] = "fecha_actualizacion = ?";
                $values[] = date('Y-m-d H:i:s');
                $values[] = $id;
                
                $sql = "UPDATE cursos SET " . implode(', ', $setParts) . " WHERE id = ?";
                $db->query($sql, $values);
            }

            return true;
        } catch (Exception $e) {
            throw new Exception('Error al actualizar curso: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar curso (simplificado)
     */
    public function deleteCurso(int $id): bool
    {
        try {
            $curso = Curso::find($id);
            if (!$curso) {
                throw new Exception('Curso no encontrado');
            }

            $curso->delete();
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al eliminar curso: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del curso
     */
    public function changeStatus(int $id, string $status): bool
    {
        try {
            $curso = Curso::find($id);
            if (!$curso) {
                throw new Exception('Curso no encontrado');
            }

            $validStatuses = ['Borrador', 'Publicado', 'Archivado'];
            if (!in_array($status, $validStatuses)) {
                throw new Exception('Estado inválido');
            }

            // Actualizar usando SQL directo
            $db = DB::getInstance();
            $sql = "UPDATE cursos SET estado = ?, fecha_actualizacion = ? WHERE id = ?";
            $db->query($sql, [$status, date('Y-m-d H:i:s'), $id]);

            return true;
        } catch (Exception $e) {
            throw new Exception('Error al cambiar estado: ' . $e->getMessage());
        }
    }

    /**
     * Obtener todas las categorías de cursos
     */
    public function getAllCategoriasCursos(): array
    {
        return Categoria::where('tipo', '=', 'curso')->where('estado', '=', 1)->get();
    }

    /**
     * Obtener todos los docentes
     */
    public function getAllDocentes(): array
    {
        $db = DB::getInstance();
        $query = "SELECT u.id, u.nombre, u.apellido, u.email 
                  FROM usuarios u 
                  INNER JOIN model_has_roles mhr ON u.id = mhr.model_id 
                  WHERE mhr.role_id = 2 
                  AND u.estado = 1
                  ORDER BY u.nombre, u.apellido";
        
        $result = $db->query($query);
        return $result ? $result->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    /**
     * Obtener estadísticas básicas de cursos
     */
    public function getEstadisticasCursos(): array
    {
        try {
            return [
                'total' => Curso::count(),
                'publicados' => Curso::where('estado', '=', 'Publicado')->count(),
                'borradores' => Curso::where('estado', '=', 'Borrador')->count(),
                'archivados' => Curso::where('estado', '=', 'Archivado')->count(),
                'por_nivel' => [
                    'principiante' => Curso::where('nivel', '=', 'Principiante')->count(),
                    'intermedio' => Curso::where('nivel', '=', 'Intermedio')->count(),
                    'avanzado' => Curso::where('nivel', '=', 'Avanzado')->count()
                ]
            ];
        } catch (Exception $e) {
            return [
                'total' => 0,
                'publicados' => 0,
                'borradores' => 0,
                'archivados' => 0,
                'por_nivel' => ['principiante' => 0, 'intermedio' => 0, 'avanzado' => 0]
            ];
        }
    }

    /**
     * Buscar cursos con filtros
     */
    public function buscarCursos(string $termino = '', string $categoria = '', string $nivel = '', string $estado = ''): array
    {
        try {
            $cursos = $this->getAllCursos();

            // Filtrar por término de búsqueda si se proporciona
            if (!empty($termino)) {
                $cursos = array_filter($cursos, function($curso) use ($termino) {
                    return stripos($curso['titulo'], $termino) !== false || 
                           stripos($curso['descripcion'], $termino) !== false;
                });
            }

            // Filtrar por categoría
            if (!empty($categoria)) {
                $cursos = array_filter($cursos, function($curso) use ($categoria) {
                    return $curso['categoria_id'] == $categoria;
                });
            }

            // Filtrar por nivel
            if (!empty($nivel)) {
                $cursos = array_filter($cursos, function($curso) use ($nivel) {
                    return $curso['nivel'] == $nivel;
                });
            }

            // Filtrar por estado
            if (!empty($estado)) {
                $cursos = array_filter($cursos, function($curso) use ($estado) {
                    return $curso['estado'] == $estado;
                });
            }

            return array_values($cursos);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Verificar si un estudiante está inscrito en un curso
     */
    public function estaInscrito(int $estudianteId, int $cursoId): bool
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT COUNT(*) as count FROM inscripciones WHERE estudiante_id = ? AND curso_id = ?";
            $result = $db->query($query, [$estudianteId, $cursoId]);
            $row = $result->fetch(PDO::FETCH_ASSOC);
            return $row['count'] > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Obtener otros cursos en los que está inscrito el estudiante (excluyendo el actual)
     */
    public function getCursosInscrito(int $estudianteId, int $cursoActualId): array
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT c.id, c.titulo, c.imagen, c.nivel, 
                     cat.nombre as categoria_nombre, 
                     p.progreso_porcentaje, p.completado,
                     u.nombre as docente_nombre, u.apellido as docente_apellido
                     FROM inscripciones i
                     INNER JOIN cursos c ON i.curso_id = c.id
                     LEFT JOIN progreso_estudiantes p ON i.estudiante_id = p.estudiante_id AND i.curso_id = p.curso_id
                     LEFT JOIN categorias cat ON c.categoria_id = cat.id
                     LEFT JOIN usuarios u ON c.docente_id = u.id
                     WHERE i.estudiante_id = ? AND c.id != ?
                     ORDER BY i.fecha_inscripcion DESC
                     LIMIT 6";
            
            $result = $db->query($query, [$estudianteId, $cursoActualId]);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener estadísticas específicas del estudiante en un curso
     */
    public function getEstadisticasEstudiante(int $estudianteId, int $cursoId): array
    {
        try {
            $db = DB::getInstance();
            
            // Obtener información básica del progreso
            $queryProgreso = "SELECT progreso_porcentaje, tiempo_estudiado, completado, 
                             fecha_inscripcion, ultima_actividad 
                             FROM progreso_estudiantes 
                             WHERE estudiante_id = ? AND curso_id = ?";
            $resultProgreso = $db->query($queryProgreso, [$estudianteId, $cursoId]);
            $progreso = $resultProgreso->fetch(PDO::FETCH_ASSOC);

            // Obtener notas si existen
            $queryNotas = "SELECT nota, fecha_calificacion 
                          FROM notas 
                          WHERE estudiante_id = ? AND curso_id = ?
                          ORDER BY fecha_calificacion DESC";
            $resultNotas = $db->query($queryNotas, [$estudianteId, $cursoId]);
            $notas = $resultNotas->fetchAll(PDO::FETCH_ASSOC);

            // Calcular días desde inscripción
            $queryInscripcion = "SELECT fecha_inscripcion FROM inscripciones 
                                WHERE estudiante_id = ? AND curso_id = ?";
            $resultInscripcion = $db->query($queryInscripcion, [$estudianteId, $cursoId]);
            $inscripcion = $resultInscripcion->fetch(PDO::FETCH_ASSOC);

            $diasInscritos = 0;
            if ($inscripcion) {
                $fechaInscripcion = new \DateTime($inscripcion['fecha_inscripcion']);
                $fechaActual = new \DateTime();
                $diasInscritos = $fechaActual->diff($fechaInscripcion)->days;
            }

            return [
                'progreso_porcentaje' => $progreso['progreso_porcentaje'] ?? 0,
                'tiempo_estudiado' => $progreso['tiempo_estudiado'] ?? 0,
                'completado' => $progreso['completado'] ?? 0,
                'fecha_inicio' => $progreso['fecha_inscripcion'] ?? null,
                'fecha_ultimo_acceso' => $progreso['ultima_actividad'] ?? null,
                'notas' => $notas,
                'promedio_notas' => !empty($notas) ? array_sum(array_column($notas, 'nota')) / count($notas) : null,
                'dias_inscritos' => $diasInscritos,
                'tiempo_formateado' => $this->formatearTiempo($progreso['tiempo_estudiado'] ?? 0)
            ];
        } catch (Exception $e) {
            return [
                'progreso_porcentaje' => 0,
                'tiempo_estudiado' => 0,
                'completado' => 0,
                'fecha_inicio' => null,
                'fecha_ultimo_acceso' => null,
                'notas' => [],
                'promedio_notas' => null,
                'dias_inscritos' => 0,
                'tiempo_formateado' => '0 min'
            ];
        }
    }

    /**
     * Formatear tiempo en minutos a formato legible
     */
    private function formatearTiempo(int $minutos): string
    {
        if ($minutos < 60) {
            return $minutos . ' min';
        } elseif ($minutos < 1440) { // Menos de 24 horas
            $horas = floor($minutos / 60);
            $mins = $minutos % 60;
            return $horas . 'h ' . $mins . 'min';
        } else {
            $dias = floor($minutos / 1440);
            $horas = floor(($minutos % 1440) / 60);
            return $dias . 'd ' . $horas . 'h';
        }
    }

    /**
     * Inscribir estudiante a un curso
     */
    public function inscribirEstudiante(int $estudianteId, int $cursoId): bool
    {
        try {
            // Verificar que no esté ya inscrito
            if ($this->estaInscrito($estudianteId, $cursoId)) {
                return true; // Ya está inscrito
            }

            $db = DB::getInstance();
            
            // Insertar inscripción
            $query = "INSERT INTO inscripciones (estudiante_id, curso_id, fecha_inscripcion) VALUES (?, ?, NOW())";
            $db->query($query, [$estudianteId, $cursoId]);

            // Crear registro inicial de progreso
            $queryProgreso = "INSERT INTO progreso_estudiantes (estudiante_id, curso_id, progreso_porcentaje, tiempo_estudiado, completado) VALUES (?, ?, 0, 0, 0)";
            $db->query($queryProgreso, [$estudianteId, $cursoId]);

            return true;
        } catch (Exception $e) {
            throw new Exception('Error al inscribir estudiante: ' . $e->getMessage());
        }
    }

    /**
     * Obtener progreso de un estudiante en un curso
     */
    public function getProgresoEstudiante(int $estudianteId, int $cursoId): ?array
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT * FROM progreso_estudiantes WHERE estudiante_id = ? AND curso_id = ?";
            $result = $db->query($query, [$estudianteId, $cursoId]);
            $progreso = $result->fetch(PDO::FETCH_ASSOC);
            
            return $progreso ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Actualizar progreso de un estudiante
     */
    public function actualizarProgreso(int $estudianteId, int $cursoId, int $porcentaje, int $tiempoEstudiado): bool
    {
        try {
            $db = DB::getInstance();
            $completado = $porcentaje >= 100 ? 1 : 0;
            
            $query = "UPDATE progreso_estudiantes SET 
                     progreso_porcentaje = ?, 
                     tiempo_estudiado = ?, 
                     completado = ?,
                     ultima_actividad = NOW()
                     WHERE estudiante_id = ? AND curso_id = ?";
            
            $db->query($query, [$porcentaje, $tiempoEstudiado, $completado, $estudianteId, $cursoId]);
            
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al actualizar progreso: ' . $e->getMessage());
        }
    }

    /**
     * Obtener estudiantes inscritos en un curso (para profesores)
     */
    public function getEstudiantesInscritosCurso(int $cursoId): array
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT u.id, u.nombre, u.apellido, u.email, i.fecha_inscripcion, 
                     p.progreso_porcentaje, p.tiempo_estudiado, p.completado, p.fecha_actualizacion
                     FROM inscripciones i
                     INNER JOIN usuarios u ON i.estudiante_id = u.id
                     LEFT JOIN progreso_estudiantes p ON i.estudiante_id = p.estudiante_id AND i.curso_id = p.curso_id
                     WHERE i.curso_id = ?
                     ORDER BY i.fecha_inscripcion DESC";
            
            $result = $db->query($query, [$cursoId]);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Obtener cursos en los que está inscrito un estudiante
     */
    public function getCursosInscritoEstudiante(int $estudianteId): array
    {
        try {
            $db = DB::getInstance();
            $query = "SELECT c.*, i.fecha_inscripcion, p.progreso_porcentaje, p.tiempo_estudiado, p.completado,
                     cat.nombre as categoria_nombre, u.nombre as docente_nombre, u.apellido as docente_apellido
                     FROM inscripciones i
                     INNER JOIN cursos c ON i.curso_id = c.id
                     LEFT JOIN progreso_estudiantes p ON i.estudiante_id = p.estudiante_id AND i.curso_id = p.curso_id
                     LEFT JOIN categorias cat ON c.categoria_id = cat.id
                     LEFT JOIN usuarios u ON c.docente_id = u.id
                     WHERE i.estudiante_id = ?
                     ORDER BY i.fecha_inscripcion DESC";
            
            $result = $db->query($query, [$estudianteId]);
            return $result->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Actualizar último acceso del estudiante al curso
     */
    public function actualizarUltimoAcceso(int $estudianteId, int $cursoId): bool
    {
        try {
            $db = DB::getInstance();
            $query = "UPDATE progreso_estudiantes 
                     SET ultima_actividad = NOW() 
                     WHERE estudiante_id = ? AND curso_id = ?";
            $db->query($query, [$estudianteId, $cursoId]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
