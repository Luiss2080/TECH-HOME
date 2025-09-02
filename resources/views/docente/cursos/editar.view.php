<?php
$title = $title ?? 'Editar Curso';
$curso = $curso ?? [];
$errors = Session::flashGet('errors') ?? [];
$old = Session::flashGet('old') ?? $curso;
$categorias = $categorias ?? [];
?>

<!-- Estilos específicos -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">

<style>
/* ============================================
   ESTILOS PARA EDITAR CURSO DOCENTE
   ============================================ */

.edit-curso-form {
    background: var(--background-card);
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow-medium);
    margin: 1rem 0;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid rgba(59, 130, 246, 0.2);
    border-radius: var(--border-radius-md);
    font-size: 1rem;
    transition: var(--transition-base);
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.btn-update {
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
    padding: 0.875rem 2rem;
    border: none;
    border-radius: var(--border-radius-md);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition-base);
}

.btn-update:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-strong);
}

.error-message {
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.curso-info-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: var(--border-radius-md);
    padding: 1.5rem;
    margin-bottom: 2rem;
}
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">
                <i class="fas fa-edit me-2"></i>
                Editar Curso
            </h1>
            <p class="text-muted">Modifica los datos de tu curso</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= route('docente.cursos.ver', $curso['id'] ?? 0) ?>" class="btn btn-outline-info">
                <i class="fas fa-eye me-2"></i>
                Ver Curso
            </a>
            <a href="<?= route('docente.cursos') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- Alertas -->
    <?php if (Session::hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= Session::flashGet('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($curso)): ?>
    
    <!-- Información del Curso -->
    <div class="curso-info-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-2"><?= htmlspecialchars($curso['titulo']) ?></h5>
                <p class="text-muted mb-1">
                    <strong>ID:</strong> <?= $curso['id'] ?> | 
                    <strong>Estado:</strong> <?= $curso['estado'] ?> | 
                    <strong>Creado:</strong> <?= date('d/m/Y', strtotime($curso['fecha_creacion'])) ?>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge bg-<?= $curso['estado'] === 'Publicado' ? 'success' : ($curso['estado'] === 'Borrador' ? 'warning' : 'secondary') ?> fs-6">
                    <?= $curso['estado'] ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Formulario de Edición -->
    <div class="edit-curso-form">
        <form action="<?= route('docente.cursos.actualizar', $curso['id']) ?>" method="POST" id="formEditarCurso">
            <input type="hidden" name="_method" value="PUT">
            
            <!-- Título del Curso -->
            <div class="form-group">
                <label for="titulo" class="form-label">
                    <i class="fas fa-heading me-2"></i>
                    Título del Curso *
                </label>
                <input type="text" 
                       id="titulo" 
                       name="titulo" 
                       class="form-control" 
                       value="<?= htmlspecialchars($old['titulo'] ?? '') ?>"
                       required>
                <?php if (isset($errors['titulo'])): ?>
                    <div class="error-message"><?= $errors['titulo'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Descripción -->
            <div class="form-group">
                <label for="descripcion" class="form-label">
                    <i class="fas fa-align-left me-2"></i>
                    Descripción del Curso *
                </label>
                <textarea id="descripcion" 
                          name="descripcion" 
                          class="form-control" 
                          rows="4"
                          required><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
                <?php if (isset($errors['descripcion'])): ?>
                    <div class="error-message"><?= $errors['descripcion'] ?></div>
                <?php endif; ?>
            </div>

            <!-- URL del Video (si existe) -->
            <?php if (isset($curso['video_url'])): ?>
            <div class="form-group">
                <label for="video_url" class="form-label">
                    <i class="fab fa-youtube me-2"></i>
                    URL del Video de YouTube
                </label>
                <input type="url" 
                       id="video_url" 
                       name="video_url" 
                       class="form-control" 
                       value="<?= htmlspecialchars($old['video_url'] ?? '') ?>"
                       placeholder="https://www.youtube.com/watch?v=...">
                <small class="text-muted">Opcional: URL del video principal del curso</small>
                <?php if (isset($errors['video_url'])): ?>
                    <div class="error-message"><?= $errors['video_url'] ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Categoría y Nivel -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="categoria_id" class="form-label">
                            <i class="fas fa-tag me-2"></i>
                            Categoría *
                        </label>
                        <select id="categoria_id" name="categoria_id" class="form-control" required>
                            <option value="">Seleccionar categoría...</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id'] ?>" 
                                        <?= ($old['categoria_id'] ?? '') == $categoria['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['categoria_id'])): ?>
                            <div class="error-message"><?= $errors['categoria_id'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="nivel" class="form-label">
                            <i class="fas fa-layer-group me-2"></i>
                            Nivel *
                        </label>
                        <select id="nivel" name="nivel" class="form-control" required>
                            <option value="">Seleccionar nivel...</option>
                            <option value="Principiante" <?= ($old['nivel'] ?? '') == 'Principiante' ? 'selected' : '' ?>>Principiante</option>
                            <option value="Intermedio" <?= ($old['nivel'] ?? '') == 'Intermedio' ? 'selected' : '' ?>>Intermedio</option>
                            <option value="Avanzado" <?= ($old['nivel'] ?? '') == 'Avanzado' ? 'selected' : '' ?>>Avanzado</option>
                        </select>
                        <?php if (isset($errors['nivel'])): ?>
                            <div class="error-message"><?= $errors['nivel'] ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Estado del Curso -->
            <div class="form-group">
                <label for="estado" class="form-label">
                    <i class="fas fa-toggle-on me-2"></i>
                    Estado del Curso *
                </label>
                <select id="estado" name="estado" class="form-control" required>
                    <option value="Borrador" <?= ($old['estado'] ?? '') == 'Borrador' ? 'selected' : '' ?>>
                        📝 Borrador - Solo visible para ti
                    </option>
                    <option value="Publicado" <?= ($old['estado'] ?? '') == 'Publicado' ? 'selected' : '' ?>>
                        🌐 Publicado - Visible para estudiantes
                    </option>
                    <option value="Archivado" <?= ($old['estado'] ?? '') == 'Archivado' ? 'selected' : '' ?>>
                        📦 Archivado - Oculto pero conservado
                    </option>
                </select>
                <?php if (isset($errors['estado'])): ?>
                    <div class="error-message"><?= $errors['estado'] ?></div>
                <?php endif; ?>
            </div>

            <!-- Campos adicionales si están disponibles -->
            <?php if (isset($curso['precio']) || isset($curso['duracion_horas'])): ?>
            <div class="row">
                <?php if (isset($curso['duracion_horas'])): ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="duracion_horas" class="form-label">
                            <i class="fas fa-clock me-2"></i>
                            Duración (horas)
                        </label>
                        <input type="number" 
                               id="duracion_horas" 
                               name="duracion_horas" 
                               class="form-control" 
                               min="0"
                               value="<?= htmlspecialchars($old['duracion_horas'] ?? '0') ?>">
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (isset($curso['precio'])): ?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="precio" class="form-label">
                            <i class="fas fa-dollar-sign me-2"></i>
                            Precio (Bs.)
                        </label>
                        <input type="number" 
                               id="precio" 
                               name="precio" 
                               class="form-control" 
                               min="0"
                               step="0.01"
                               value="<?= htmlspecialchars($old['precio'] ?? '0') ?>">
                        <div class="form-check mt-2">
                            <input type="checkbox" 
                                   id="es_gratuito" 
                                   name="es_gratuito" 
                                   class="form-check-input" 
                                   value="1"
                                   <?= ($old['es_gratuito'] ?? '1') == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="es_gratuito">
                                Curso Gratuito
                            </label>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Contenido del Curso (si existe) -->
            <?php if (isset($curso['contenido'])): ?>
            <div class="form-group">
                <label for="contenido" class="form-label">
                    <i class="fas fa-file-alt me-2"></i>
                    Contenido del Curso
                </label>
                <textarea id="contenido" 
                          name="contenido" 
                          class="form-control" 
                          rows="8"
                          placeholder="Describe el contenido detallado del curso, objetivos de aprendizaje, temario, etc."><?= htmlspecialchars($old['contenido'] ?? '') ?></textarea>
                <small class="text-muted">Opcional: Contenido detallado y temario del curso</small>
            </div>
            <?php endif; ?>

            <!-- Botones de Acción -->
            <div class="form-group d-flex justify-content-between">
                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-update">
                        <i class="fas fa-save me-2"></i>
                        Actualizar Curso
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                        <i class="fas fa-undo me-2"></i>
                        Deshacer Cambios
                    </button>
                </div>
                
                <div class="d-flex gap-2">
                    <a href="<?= route('docente.cursos.ver', $curso['id']) ?>" class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>
                        Vista Previa
                    </a>
                    <button type="button" class="btn btn-outline-danger" onclick="confirmarEliminacion()">
                        <i class="fas fa-trash me-2"></i>
                        Eliminar
                    </button>
                </div>
            </div>

        </form>
    </div>

    <?php else: ?>
    <!-- Mensaje si no hay curso -->
    <div class="edit-curso-form text-center">
        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
        <h4>Curso no encontrado</h4>
        <p class="text-muted">El curso que intentas editar no existe o no tienes permisos para modificarlo.</p>
        <a href="<?= route('docente.cursos') ?>" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>
            Volver a Mis Cursos
        </a>
    </div>
    <?php endif; ?>
</div>

<script>
// Datos originales para reset
const datosOriginales = <?= json_encode($curso) ?>;

function resetForm() {
    if (confirm('¿Estás seguro de que quieres deshacer todos los cambios?')) {
        // Restaurar valores originales
        if (datosOriginales.titulo) document.getElementById('titulo').value = datosOriginales.titulo;
        if (datosOriginales.descripcion) document.getElementById('descripcion').value = datosOriginales.descripcion;
        if (datosOriginales.categoria_id) document.getElementById('categoria_id').value = datosOriginales.categoria_id;
        if (datosOriginales.nivel) document.getElementById('nivel').value = datosOriginales.nivel;
        if (datosOriginales.estado) document.getElementById('estado').value = datosOriginales.estado;
        
        // Campos opcionales
        const videoUrl = document.getElementById('video_url');
        if (videoUrl && datosOriginales.video_url) videoUrl.value = datosOriginales.video_url;
        
        const duracionHoras = document.getElementById('duracion_horas');
        if (duracionHoras && datosOriginales.duracion_horas) duracionHoras.value = datosOriginales.duracion_horas;
        
        const precio = document.getElementById('precio');
        if (precio && datosOriginales.precio !== undefined) precio.value = datosOriginales.precio;
        
        const esGratuito = document.getElementById('es_gratuito');
        if (esGratuito && datosOriginales.es_gratuito !== undefined) esGratuito.checked = datosOriginales.es_gratuito == '1';
        
        const contenido = document.getElementById('contenido');
        if (contenido && datosOriginales.contenido) contenido.value = datosOriginales.contenido;
        
        console.log('✅ Formulario restaurado a valores originales');
    }
}

function confirmarEliminacion() {
    const titulo = datosOriginales.titulo || 'este curso';
    const mensaje = `⚠️ ¿Estás seguro de que quieres ELIMINAR "${titulo}"?\n\n` +
                   `Esta acción es IRREVERSIBLE y se perderán:\n` +
                   `• Toda la información del curso\n` +
                   `• Las inscripciones de estudiantes\n` +
                   `• El progreso y calificaciones\n` +
                   `• Los materiales asociados\n\n` +
                   `Escribe "ELIMINAR" para confirmar:`;
    
    const confirmacion = prompt(mensaje);
    
    if (confirmacion === 'ELIMINAR') {
        // Crear formulario para DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= route('docente.cursos.eliminar', $curso['id'] ?? 0) ?>`;
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    } else if (confirmacion !== null) {
        alert('❌ Confirmación incorrecta. El curso NO ha sido eliminado.');
    }
}

// Prevenir salida accidental con cambios sin guardar
let formModificado = false;
document.getElementById('formEditarCurso').addEventListener('input', function() {
    formModificado = true;
});

document.getElementById('formEditarCurso').addEventListener('submit', function() {
    formModificado = false;
});

window.addEventListener('beforeunload', function(e) {
    if (formModificado) {
        const mensaje = 'Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?';
        e.returnValue = mensaje;
        return mensaje;
    }
});
</script>