<?php
$user = auth();
if (!$user) {
    header('Location: ' . route('login'));
    exit;
}
?>

<link rel="stylesheet" href="<?= asset('css/admin/admin.css'); ?>">

<div class="dashboard-content">
    <div class="section-card">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title">
                    <i class="fas fa-plus-circle"></i>
                    Crear Nuevo Curso
                </h2>
                <p class="section-subtitle">Agrega un nuevo curso a la plataforma</p>
            </div>
            <a href="<?= route('admin.cursos') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
        </div>

        <!-- Formulario -->
        <form action="<?= route('admin.cursos.store') ?>" method="POST" id="formCrearCurso">
            <?php CSRF(); ?>
            
            <div class="row">
                <!-- Información básica -->
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información Básica</h5>
                        </div>
                        <div class="card-body">
                            <!-- Título -->
                            <div class="form-group">
                                <label for="titulo" class="form-label required">Título del Curso</label>
                                <input type="text" 
                                       class="form-control <?= isset($errors['titulo']) ? 'is-invalid' : '' ?>" 
                                       id="titulo" 
                                       name="titulo" 
                                       value="<?= old('titulo') ?>"
                                       placeholder="Ej: Robótica Básica con Arduino"
                                       maxlength="200"
                                       required>
                                <?php if (isset($errors['titulo'])): ?>
                                    <div class="invalid-feedback"><?= $errors['titulo'][0] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Descripción -->
                            <div class="form-group">
                                <label for="descripcion" class="form-label required">Descripción</label>
                                <textarea class="form-control <?= isset($errors['descripcion']) ? 'is-invalid' : '' ?>" 
                                          id="descripcion" 
                                          name="descripcion" 
                                          rows="4"
                                          placeholder="Describe de qué trata el curso, qué aprenderán los estudiantes..."
                                          required><?= old('descripcion') ?></textarea>
                                <?php if (isset($errors['descripcion'])): ?>
                                    <div class="invalid-feedback"><?= $errors['descripcion'][0] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- URL del Video -->
                            <div class="form-group">
                                <label for="video_url" class="form-label required">URL del Video</label>
                                <input type="url" 
                                       class="form-control <?= isset($errors['video_url']) ? 'is-invalid' : '' ?>" 
                                       id="video_url" 
                                       name="video_url" 
                                       value="<?= old('video_url') ?>"
                                       placeholder="https://youtu.be/video_id"
                                       required>
                                <small class="form-text text-muted">URL de YouTube del curso</small>
                                <?php if (isset($errors['video_url'])): ?>
                                    <div class="invalid-feedback"><?= $errors['video_url'][0] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Imagen de Portada -->
                            <div class="form-group">
                                <label for="imagen_portada" class="form-label">Imagen de Portada</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="imagen_portada" 
                                       name="imagen_portada" 
                                       value="<?= old('imagen_portada') ?>"
                                       placeholder="/img/cursos/mi_curso.jpg">
                                <small class="form-text text-muted">Ruta de la imagen de portada (opcional)</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuración -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-cogs"></i> Configuración</h5>
                        </div>
                        <div class="card-body">
                            <!-- Docente -->
                            <div class="form-group">
                                <label for="docente_id" class="form-label required">Docente</label>
                                <select class="form-control <?= isset($errors['docente_id']) ? 'is-invalid' : '' ?>" 
                                        id="docente_id" 
                                        name="docente_id" 
                                        required>
                                    <option value="">Seleccionar docente...</option>
                                    <?php foreach ($docentes as $docente): ?>
                                        <option value="<?= $docente->id ?>" 
                                                <?= old('docente_id') == $docente->id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($docente->nombre . ' ' . $docente->apellido) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['docente_id'])): ?>
                                    <div class="invalid-feedback"><?= $errors['docente_id'][0] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Categoría -->
                            <div class="form-group">
                                <label for="categoria_id" class="form-label required">Categoría</label>
                                <select class="form-control <?= isset($errors['categoria_id']) ? 'is-invalid' : '' ?>" 
                                        id="categoria_id" 
                                        name="categoria_id" 
                                        required>
                                    <option value="">Seleccionar categoría...</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria->id ?>" 
                                                <?= old('categoria_id') == $categoria->id ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($categoria->nombre) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (isset($errors['categoria_id'])): ?>
                                    <div class="invalid-feedback"><?= $errors['categoria_id'][0] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Nivel -->
                            <div class="form-group">
                                <label for="nivel" class="form-label required">Nivel</label>
                                <select class="form-control <?= isset($errors['nivel']) ? 'is-invalid' : '' ?>" 
                                        id="nivel" 
                                        name="nivel" 
                                        required>
                                    <option value="">Seleccionar nivel...</option>
                                    <option value="Principiante" <?= old('nivel') === 'Principiante' ? 'selected' : '' ?>>Principiante</option>
                                    <option value="Intermedio" <?= old('nivel') === 'Intermedio' ? 'selected' : '' ?>>Intermedio</option>
                                    <option value="Avanzado" <?= old('nivel') === 'Avanzado' ? 'selected' : '' ?>>Avanzado</option>
                                </select>
                                <?php if (isset($errors['nivel'])): ?>
                                    <div class="invalid-feedback"><?= $errors['nivel'][0] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Estado -->
                            <div class="form-group">
                                <label for="estado" class="form-label required">Estado</label>
                                <select class="form-control <?= isset($errors['estado']) ? 'is-invalid' : '' ?>" 
                                        id="estado" 
                                        name="estado" 
                                        required>
                                    <option value="">Seleccionar estado...</option>
                                    <option value="Borrador" <?= old('estado') === 'Borrador' ? 'selected' : '' ?>>Borrador</option>
                                    <option value="Publicado" <?= old('estado') === 'Publicado' ? 'selected' : '' ?>>Publicado</option>
                                    <option value="Archivado" <?= old('estado') === 'Archivado' ? 'selected' : '' ?>>Archivado</option>
                                </select>
                                <?php if (isset($errors['estado'])): ?>
                                    <div class="invalid-feedback"><?= $errors['estado'][0] ?></div>
                                <?php endif; ?>
                            </div>

                            <!-- Tipo (Gratuito/Pago) -->
                            <div class="form-group">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="es_gratuito" 
                                           name="es_gratuito" 
                                           value="1"
                                           <?= old('es_gratuito') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="es_gratuito">
                                        <strong>Curso Gratuito</strong>
                                        <small class="text-muted d-block">Los estudiantes pueden acceder sin costo</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Vista previa -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-eye"></i> Vista Previa</h6>
                        </div>
                        <div class="card-body">
                            <div id="vistaPrevia" class="curso-preview">
                                <div class="preview-placeholder">
                                    <i class="fas fa-video fa-2x text-muted mb-2"></i>
                                    <p class="text-muted small">Completa el formulario para ver la vista previa</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="row">
                <div class="col-12">
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save"></i> Crear Curso
                        </button>
                        <a href="<?= route('admin.cursos') ?>" class="btn btn-secondary btn-lg ml-2">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Vista previa dinámica
    const campos = ['titulo', 'descripcion', 'nivel', 'estado'];
    const vistaPrevia = document.getElementById('vistaPrevia');
    
    function actualizarVistaPrevia() {
        const titulo = document.getElementById('titulo').value || 'Título del curso';
        const descripcion = document.getElementById('descripcion').value || 'Descripción del curso';
        const nivel = document.getElementById('nivel').value || 'Nivel';
        const estado = document.getElementById('estado').value || 'Estado';
        const esGratuito = document.getElementById('es_gratuito').checked;
        
        vistaPrevia.innerHTML = `
            <div class="preview-content">
                <h6 class="preview-title">${titulo}</h6>
                <p class="preview-description small text-muted">${descripcion.substring(0, 80)}...</p>
                <div class="preview-badges">
                    <span class="badge badge-${nivel === 'Principiante' ? 'success' : (nivel === 'Intermedio' ? 'warning' : 'danger')} badge-sm">
                        ${nivel}
                    </span>
                    <span class="badge badge-${estado === 'Publicado' ? 'success' : 'warning'} badge-sm">
                        ${estado}
                    </span>
                    ${esGratuito ? '<span class="badge badge-info badge-sm">Gratuito</span>' : '<span class="badge badge-primary badge-sm">De pago</span>'}
                </div>
            </div>
        `;
    }
    
    // Event listeners para actualizar vista previa
    campos.forEach(campo => {
        const elemento = document.getElementById(campo);
        if (elemento) {
            elemento.addEventListener('input', actualizarVistaPrevia);
        }
    });
    
    document.getElementById('es_gratuito').addEventListener('change', actualizarVistaPrevia);
    
    // Validación del formulario
    document.getElementById('formCrearCurso').addEventListener('submit', function(e) {
        const camposRequeridos = ['titulo', 'descripcion', 'video_url', 'docente_id', 'categoria_id', 'nivel', 'estado'];
        let valido = true;
        
        camposRequeridos.forEach(campo => {
            const elemento = document.getElementById(campo);
            if (!elemento.value.trim()) {
                elemento.classList.add('is-invalid');
                valido = false;
            } else {
                elemento.classList.remove('is-invalid');
            }
        });
        
        if (!valido) {
            e.preventDefault();
            alert('Por favor, completa todos los campos requeridos.');
        }
    });
});
</script>

<style>
.required::after {
    content: " *";
    color: #dc3545;
}

.curso-preview {
    min-height: 120px;
}

.preview-placeholder {
    text-align: center;
    padding: 20px;
}

.preview-content {
    padding: 15px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background: #f8f9fa;
}

.preview-title {
    font-weight: 600;
    margin-bottom: 8px;
}

.preview-description {
    margin-bottom: 10px;
    line-height: 1.4;
}

.preview-badges .badge {
    margin-right: 5px;
    margin-bottom: 5px;
}

.form-actions {
    padding: 20px 0;
    border-top: 1px solid #e9ecef;
    margin-top: 20px;
}
</style>