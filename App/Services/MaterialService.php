<?php

namespace App\Services;

use App\Models\Material;
use App\Models\User;
use App\Models\Categoria;
use Core\DB;
use Exception;

class MaterialService
{
    /**
     * Obtener todos los materiales con información relacionada
     */
    public function getAllMaterials(): array
    {
        $materials = Material::all();
        $materialsData = [];

        foreach ($materials as $material) {
            $materialData = $material->getAttributes();
            
            // Obtener información del docente
            $docente = $material->docente();
            if ($docente) {
                $materialData['docente_nombre'] = $docente->nombre . ' ' . $docente->apellido;
            } else {
                $materialData['docente_nombre'] = 'No asignado';
            }

            // Obtener información de la categoría
            $categoria = $material->categoria();
            if ($categoria) {
                $materialData['categoria_nombre'] = $categoria->nombre;
                $materialData['categoria_color'] = $categoria->color;
                $materialData['categoria_icono'] = $categoria->icono;
            } else {
                $materialData['categoria_nombre'] = 'Sin categoría';
                $materialData['categoria_color'] = '#6c757d';
                $materialData['categoria_icono'] = 'fas fa-folder';
            }

            // Agregar información adicional
            $materialData['tamaño_formateado'] = $material->getTamañoFormateado();
            $materialData['duracion_formateada'] = $material->getDuracionFormateada();
            $materialData['icono_tipo'] = $material->getIcono();
            $materialData['clase_tipo'] = $material->getClaseTipo();
            
            $materialsData[] = $materialData;
        }

        return $materialsData;
    }

    /**
     * Obtener material por ID
     */
    public function getMaterialById(int $id): ?Material
    {
        return Material::find($id);
    }

    /**
     * Crear nuevo material
     */
    public function createMaterial(array $materialData): int
    {
        // Procesar archivo si existe
        if (isset($materialData['archivo_upload']) && !empty($materialData['archivo_upload'])) {
            $archivoInfo = $this->procesarArchivo($materialData['archivo_upload']);
            $materialData['archivo'] = $archivoInfo['ruta'];
            $materialData['tamaño_archivo'] = $archivoInfo['tamaño'];
            
            // Sugerir tipo basado en extensión si no se especificó
            if (empty($materialData['tipo']) || $materialData['tipo'] === 'otro') {
                $materialData['tipo'] = Material::getTipoSugeridoPorExtension($archivoInfo['extension']);
            }
        }

        // Si es enlace externo, limpiar datos de archivo local
        if (!empty($materialData['enlace_externo'])) {
            $materialData['archivo'] = null;
            $materialData['tamaño_archivo'] = 0;
        }

        // Establecer timestamps
        $materialData['fecha_creacion'] = date('Y-m-d H:i:s');
        $materialData['fecha_actualizacion'] = date('Y-m-d H:i:s');

        // Crear el material
        $material = new Material([
            'titulo' => $materialData['titulo'],
            'descripcion' => $materialData['descripcion'],
            'tipo' => $materialData['tipo'],
            'archivo' => $materialData['archivo'] ?? null,
            'enlace_externo' => $materialData['enlace_externo'] ?? null,
            'tamaño_archivo' => $materialData['tamaño_archivo'] ?? 0,
            'duracion' => $materialData['duracion'] ?? null,
            'categoria_id' => $materialData['categoria_id'],
            'docente_id' => $materialData['docente_id'],
            'imagen_preview' => $materialData['imagen_preview'] ?? null,
            'publico' => $materialData['publico'] ?? 1,
            'estado' => $materialData['estado'] ?? 1,
            'fecha_creacion' => $materialData['fecha_creacion'],
            'fecha_actualizacion' => $materialData['fecha_actualizacion']
        ]);

        $material->save();
        return $material->getKey();
    }

    /**
     * Actualizar material existente
     */
    public function updateMaterial(int $id, array $materialData): bool
    {
        $material = Material::find($id);
        if (!$material) {
            throw new Exception('Material no encontrado');
        }

        // Procesar nuevo archivo si se subió
        if (isset($materialData['archivo_upload']) && !empty($materialData['archivo_upload'])) {
            // Eliminar archivo anterior si existe
            if ($material->esArchivoLocal()) {
                $this->eliminarArchivo($material->archivo);
            }

            $archivoInfo = $this->procesarArchivo($materialData['archivo_upload']);
            $materialData['archivo'] = $archivoInfo['ruta'];
            $materialData['tamaño_archivo'] = $archivoInfo['tamaño'];
        }

        // Si cambió a enlace externo, limpiar archivo local
        if (!empty($materialData['enlace_externo']) && $material->esArchivoLocal()) {
            $this->eliminarArchivo($material->archivo);
            $materialData['archivo'] = null;
            $materialData['tamaño_archivo'] = 0;
        }

        // Actualizar timestamp
        $materialData['fecha_actualizacion'] = date('Y-m-d H:i:s');

        // Actualizar campos permitidos
        $camposPermitidos = [
            'titulo', 'descripcion', 'tipo', 'archivo', 'enlace_externo',
            'tamaño_archivo', 'duracion', 'categoria_id', 'imagen_preview',
            'publico', 'estado', 'fecha_actualizacion'
        ];

        foreach ($camposPermitidos as $campo) {
            if (array_key_exists($campo, $materialData)) {
                $material->$campo = $materialData[$campo];
            }
        }

        $material->save();
        return true;
    }

    /**
     * Eliminar material
     */
    public function deleteMaterial(int $id): bool
    {
        $material = Material::find($id);
        if (!$material) {
            throw new Exception('Material no encontrado');
        }

        try {
            // Eliminar archivo físico si existe
            if ($material->esArchivoLocal()) {
                $this->eliminarArchivo($material->archivo);
            }

            // Eliminar imagen preview si existe
            if ($material->imagen_preview) {
                $this->eliminarArchivo('previews/' . $material->imagen_preview);
            }

            // Eliminar de la base de datos
            $material->delete();
            return true;
        } catch (Exception $e) {
            throw new Exception('Error al eliminar material: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar estado del material
     */
    public function changeStatus(int $id, int $estado): bool
    {
        $material = Material::find($id);
        if (!$material) {
            throw new Exception('Material no encontrado');
        }

        $material->estado = $estado;
        $material->fecha_actualizacion = date('Y-m-d H:i:s');
        $material->save();

        return true;
    }

    /**
     * Cambiar visibilidad pública del material
     */
    public function changePublicStatus(int $id, int $publico): bool
    {
        $material = Material::find($id);
        if (!$material) {
            throw new Exception('Material no encontrado');
        }

        $material->publico = $publico;
        $material->fecha_actualizacion = date('Y-m-d H:i:s');
        $material->save();

        return true;
    }

    /**
     * Obtener todas las categorías disponibles
     */
    public function getAllCategories(): array
    {
        return Categoria::where('estado', '=', 1)->get();
    }

    /**
     * Obtener todos los docentes disponibles
     */
    public function getAllDocentes(): array
    {
        // Obtener usuarios con rol de docente o administrador
        $docentes = [];
        $usuarios = User::where('estado', '=', 1)->get();
        
        foreach ($usuarios as $usuario) {
            if ($usuario->hasRole('docente') || $usuario->hasRole('administrador')) {
                $docentes[] = $usuario;
            }
        }
        
        return $docentes;
    }

    /**
     * Buscar materiales con filtros
     */
    public function searchMaterials(array $filters): array
    {
        $query = Material::query();

        // Filtro por término de búsqueda
        if (!empty($filters['buscar'])) {
            $termino = $filters['buscar'];
            $query = $query->whereRaw(
                "(titulo LIKE ? OR descripcion LIKE ?)", 
                ["%{$termino}%", "%{$termino}%"]
            );
        }

        // Filtro por tipo
        if (!empty($filters['tipo']) && $filters['tipo'] !== 'todos') {
            $query = $query->where('tipo', '=', $filters['tipo']);
        }

        // Filtro por categoría
        if (!empty($filters['categoria']) && $filters['categoria'] !== 'todas') {
            $query = $query->where('categoria_id', '=', $filters['categoria']);
        }

        // Filtro por docente
        if (!empty($filters['docente']) && $filters['docente'] !== 'todos') {
            $query = $query->where('docente_id', '=', $filters['docente']);
        }

        // Filtro por estado
        if (isset($filters['estado']) && $filters['estado'] !== '') {
            $query = $query->where('estado', '=', $filters['estado']);
        } else {
            // Por defecto, solo activos
            $query = $query->where('estado', '=', 1);
        }

        // Filtro por visibilidad pública
        if (isset($filters['publico']) && $filters['publico'] !== '') {
            $query = $query->where('publico', '=', $filters['publico']);
        }

        // Ordenamiento
        $orden = $filters['orden'] ?? 'fecha_desc';
        switch ($orden) {
            case 'titulo_asc':
                $query = $query->orderBy('titulo', 'ASC');
                break;
            case 'titulo_desc':
                $query = $query->orderBy('titulo', 'DESC');
                break;
            case 'descargas_desc':
                $query = $query->orderBy('descargas', 'DESC');
                break;
            case 'fecha_asc':
                $query = $query->orderBy('fecha_creacion', 'ASC');
                break;
            case 'fecha_desc':
            default:
                $query = $query->orderBy('fecha_creacion', 'DESC');
                break;
        }

        return $query->get();
    }

    /**
     * Obtener estadísticas generales de materiales
     */
    public function getGeneralStats(): array
    {
        return [
            'total_materiales' => Material::where('estado', '=', 1)->count(),
            'materiales_publicos' => Material::where('estado', '=', 1)->where('publico', '=', 1)->count(),
            'total_descargas' => Material::totalDescargas(),
            'por_tipo' => Material::contarPorTipo(),
            'recientes_7_dias' => Material::recientes(7)->count(),
            'mas_descargados' => Material::masDescargados(5)->get()
        ];
    }

    /**
     * Procesar archivo subido
     */
    private function procesarArchivo($archivo): array
    {
        // Validar que es un array válido de archivo subido
        if (!is_array($archivo) || !isset($archivo['tmp_name'], $archivo['name'], $archivo['size'])) {
            throw new Exception('Archivo inválido');
        }

        // Validar que se subió correctamente
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Error al subir archivo: ' . $archivo['error']);
        }

        // Obtener extensión
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        
        // Validar extensión
        if (!Material::esArchivoPermitido($extension)) {
            throw new Exception('Tipo de archivo no permitido: ' . $extension);
        }

        // Generar nombre único
        $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;
        
        // Definir carpeta según tipo
        $tipoSugerido = Material::getTipoSugeridoPorExtension($extension);
        $carpeta = $this->getCarpetaPorTipo($tipoSugerido);
        
        // Crear directorio si no existe
        $rutaCompleta = BASE_PATH . 'public/materiales/' . $carpeta;
        if (!is_dir($rutaCompleta)) {
            mkdir($rutaCompleta, 0755, true);
        }

        // Mover archivo
        $rutaDestino = $rutaCompleta . '/' . $nombreArchivo;
        if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            throw new Exception('Error al mover archivo subido');
        }

        return [
            'ruta' => $carpeta . '/' . $nombreArchivo,
            'tamaño' => $archivo['size'],
            'extension' => $extension,
            'nombre_original' => $archivo['name']
        ];
    }

    /**
     * Obtener carpeta según tipo de material
     */
    private function getCarpetaPorTipo($tipo): string
    {
        $carpetas = [
            'video' => 'videos',
            'documento' => 'documentos',
            'presentacion' => 'presentaciones',
            'audio' => 'audios',
            'otro' => 'otros'
        ];

        return $carpetas[$tipo] ?? 'otros';
    }

    /**
     * Eliminar archivo físico
     */
    private function eliminarArchivo($rutaRelativa): bool
    {
        if (empty($rutaRelativa)) {
            return true;
        }

        $rutaCompleta = BASE_PATH . 'public/materiales/' . ltrim($rutaRelativa, '/');
        
        if (file_exists($rutaCompleta)) {
            return unlink($rutaCompleta);
        }

        return true; // Si no existe, consideramos que ya está "eliminado"
    }

    /**
     * Validar permisos de acceso para un usuario
     */
    public function canAccess(int $materialId, ?int $userId = null): bool
    {
        $material = Material::find($materialId);
        if (!$material) {
            return false;
        }

        return $material->puedeAcceder($userId);
    }

    /**
     * Registrar descarga de material
     */
    public function registerDownload(int $materialId, ?int $userId = null): bool
    {
        $material = Material::find($materialId);
        if (!$material) {
            throw new Exception('Material no encontrado');
        }

        // Verificar permisos
        if (!$this->canAccess($materialId, $userId)) {
            throw new Exception('No tienes permisos para descargar este material');
        }

        // Incrementar contador
        $material->incrementarDescargas();

        // Aquí se podría registrar el acceso en la tabla acceso_materiales
        // por ahora solo incrementamos el contador

        return true;
    }

    /**
     * Obtener materiales por docente
     */
    public function getMaterialsByDocente(int $docenteId): array
    {
        return Material::porDocente($docenteId)
            ->where('estado', '=', 1)
            ->orderBy('fecha_creacion', 'DESC')
            ->get();
    }

    /**
     * Obtener materiales por categoría
     */
    public function getMaterialsByCategoria(int $categoriaId): array
    {
        return Material::porCategoria($categoriaId)
            ->where('estado', '=', 1)
            ->orderBy('fecha_creacion', 'DESC')
            ->get();
    }

    /**
     * Duplicar material
     */
    public function duplicateMaterial(int $id, int $nuevoDocenteId): int
    {
        $material = Material::find($id);
        if (!$material) {
            throw new Exception('Material no encontrado');
        }

        $nuevoMaterialData = $material->getAttributes();
        
        // Limpiar campos que no deben duplicarse
        unset($nuevoMaterialData['id']);
        $nuevoMaterialData['titulo'] = 'Copia de ' . $nuevoMaterialData['titulo'];
        $nuevoMaterialData['docente_id'] = $nuevoDocenteId;
        $nuevoMaterialData['descargas'] = 0;
        $nuevoMaterialData['fecha_creacion'] = date('Y-m-d H:i:s');
        $nuevoMaterialData['fecha_actualizacion'] = date('Y-m-d H:i:s');

        // Si es archivo local, copiarlo
        if ($material->esArchivoLocal()) {
            $nuevoMaterialData['archivo'] = $this->copiarArchivo($material->archivo);
        }

        return $this->createMaterial($nuevoMaterialData);
    }

    /**
     * Copiar archivo físico
     */
    private function copiarArchivo($rutaOriginal): string
    {
        $rutaCompleta = BASE_PATH . 'public/materiales/' . ltrim($rutaOriginal, '/');
        
        if (!file_exists($rutaCompleta)) {
            throw new Exception('Archivo original no encontrado');
        }

        $extension = pathinfo($rutaOriginal, PATHINFO_EXTENSION);
        $nombreNuevo = uniqid() . '_copy_' . time() . '.' . $extension;
        
        $carpeta = dirname($rutaOriginal);
        $rutaNueva = $carpeta . '/' . $nombreNuevo;
        $rutaCompletaNueva = BASE_PATH . 'public/materiales/' . $rutaNueva;

        if (!copy($rutaCompleta, $rutaCompletaNueva)) {
            throw new Exception('Error al copiar archivo');
        }

        return $rutaNueva;
    }
}
