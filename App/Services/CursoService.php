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
                'estado' => $cursoData['estado'] ?? 'Borrador'
            ]);

            $curso->save();
            return $curso->getKey();
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

            // Actualizar campos permitidos
            $camposPermitidos = ['titulo', 'descripcion', 'video_url', 'docente_id', 'categoria_id', 'imagen_portada', 'nivel', 'estado'];
            
            foreach ($cursoData as $field => $value) {
                if ($value !== null && in_array($field, $camposPermitidos)) {
                    $curso->$field = $value;
                }
            }

            $curso->save();
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

            $curso->estado = $status;
            $curso->save();

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
        $query = "SELECT u.* FROM usuarios u 
                  INNER JOIN model_has_roles mhr ON u.id = mhr.model_id 
                  WHERE mhr.role_id = 2 AND mhr.model_type = 'App\\Models\\User' 
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
}
