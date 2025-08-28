<?php
$title = $title ?? 'Editar Curso';
$curso = $curso ?? null;
$categorias = $categorias ?? [];
$docentes = $docentes ?? [];
$errors = $errors ?? [];
$user = auth();
$isDocente = $user && $user->hasRole('docente') && !$user->hasRole('administrador');

// Verificar que el curso existe
if (!$curso) {
    header('Location: ' . route('cursos'));
    exit();
}
?>

<!-- Estilos específicos para editar cursos -->
<link rel="stylesheet" href="<?= asset('css/index.css'); ?>">

<style>
/* Estilos específicos para editar curso */
.edit-header {
    background: linear-gradient(135deg, var(--secondary-blue), var(--primary-red));
    color: white;
    padding: 2rem;
    border-radius: var(--border-radius-lg);
    margin-bottom: 2rem;
    text-align: center;
}

.edit-title {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.edit-subtitle {
    opacity: 0.9;
    font-size: 1.1rem;
}

.form-section {
    background: var(--background-card);
    border-radius: var(--border-radius-lg);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-medium);
}

.form-section-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.8rem;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-grid-full {
    grid-column: 1 / -1;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--text-primary);
}

.form-label .required {
    color: var(--danger-color);
    margin-left: 0.2rem;
}

.form-control {
    width: 100%;
    padding: 1rem;
    border: 2px solid rgba(0,0,0,0.1);
    border-radius: var(--border-radius-md);
    font-size: 1rem;
    transition: var(--transition-base);
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: var(--secondary-blue);
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control.is-invalid {
    border-color: var(--danger-color);
}

.invalid-feedback {
    display: block;
    color: var(--danger-color);
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

.video-preview {
    width: 100%;
    height: 300px;
    background: #f8f9fa;
    border: 2px dashed #dee2e6;
    border-radius: var(--border-radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 1rem;
    position: relative;
    overflow: hidden;
}

.video-preview iframe {
    width: 100%;
    height: 100%;
    border: none;
}

.video-placeholder {
    text-align: center;
    color: var(--text-secondary);
}

.video-placeholder i {
    font-size: 3rem;
    color: var(--secondary-blue);
    margin-bottom: 1rem;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid rgba(0,0,0,0.1);
}

.btn-group {
    display: flex;
    gap: 1rem;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn-group {
        justify-content: center;
    }
}
</style>

<!-- Contenedor principal -->
<div class="crud-container">
    <div class="crud-content-wrapper">

        <!-- Header -->
        <div class="edit-header">
            <h1 class="edit-title">Editar Curso</h1>
            <p class="edit-subtitle">
                Actualiza la información de "<?= htmlspecialchars($curso['titulo']) ?>"
            </p>
        </div>

        <!-- Mensajes -->
        <?php if (flashGet('success')): ?>
            <div class="crud-alert crud-alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars(flashGet('success')) ?></span>
                <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (flashGet('error')): ?>
            <div class="crud-alert crud-alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <span><?= htmlspecialchars(flashGet('error')) ?></span>
                <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <form method="POST" action="<?= route('cursos.update', ['id' => $curso['id']]) ?>" id="editCourseForm">
            <?= CSRF() ?>
            <input type="hidden" name="_method" value="PUT">

            <!-- Información Básica -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-info-circle"></i>
                    Información Básica
                </h3>

                <div class="form-grid">
                    <!-- Título -->
                    <div class="form-group form-grid-full">
                        <label for="titulo" class="form-label">
                            Título del Curso <span class="required">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?= isset($errors['titulo']) ? 'is-invalid' : '' ?>"
                               id="titulo" 
                               name="titulo" 
                               value="<?= htmlspecialchars(old('titulo', $curso['titulo'])) ?>" 
                               required 
                               maxlength="200"
                               placeholder="Ej: Introducción a la Robótica con Arduino">
                        <?php if (isset($errors['titulo'])): ?>
                            <div class="invalid-feedback">
                                <?= is_array($errors['titulo']) ? $errors['titulo'][0] : $errors['titulo'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Descripción -->
                    <div class="form-group form-grid-full">
                        <label for="descripcion" class="form-label">
                            Descripción <span class="required">*</span>
                        </label>
                        <textarea class="form-control <?= isset($errors['descripcion']) ? 'is-invalid' : '' ?>"
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="4" 
                                  required 
                                  placeholder="Describe el contenido y objetivos del curso..."><?= htmlspecialchars(old('descripcion', $curso['descripcion'])) ?></textarea>
                        <?php if (isset($errors['descripcion'])): ?>
                            <div class="invalid-feedback">
                                <?= is_array($errors['descripcion']) ? $errors['descripcion'][0] : $errors['descripcion'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Categoría -->
                    <div class="form-group">
                        <label for="categoria_id" class="form-label">
                            Categoría <span class="required">*</span>
                        </label>
                        <select class="form-control <?= isset($errors['categoria_id']) ? 'is-invalid' : '' ?>"
                                id="categoria_id" 
                                name="categoria_id" 
                                required>
                            <option value="">Seleccionar categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= $categoria['id'] ?>" 
                                        <?= (old('categoria_id', $curso['categoria_id']) == $categoria['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['categoria_id'])): ?>
                            <div class="invalid-feedback">
                                <?= is_array($errors['categoria_id']) ? $errors['categoria_id'][0] : $errors['categoria_id'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Docente -->
                    <div class="form-group">
                        <label for="docente_id" class="form-label">
                            Docente <span class="required">*</span>
                        </label>
                        <select class="form-control <?= isset($errors['docente_id']) ? 'is-invalid' : '' ?>"
                                id="docente_id" 
                                name="docente_id" 
                                required>
                            <option value="">Seleccionar docente</option>
                            <?php foreach ($docentes as $docente): ?>
                                <option value="<?= $docente['id'] ?>" 
                                        <?= (old('docente_id', $curso['docente_id']) == $docente['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($docente['nombre'] . ' ' . $docente['apellido']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['docente_id'])): ?>
                            <div class="invalid-feedback">
                                <?= is_array($errors['docente_id']) ? $errors['docente_id'][0] : $errors['docente_id'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Nivel -->
                    <div class="form-group">
                        <label for="nivel" class="form-label">
                            Nivel <span class="required">*</span>
                        </label>
                        <select class="form-control <?= isset($errors['nivel']) ? 'is-invalid' : '' ?>"
                                id="nivel" 
                                name="nivel" 
                                required>
                            <option value="">Seleccionar nivel</option>
                            <option value="Principiante" <?= (old('nivel', $curso['nivel']) == 'Principiante') ? 'selected' : '' ?>>
                                Principiante
                            </option>
                            <option value="Intermedio" <?= (old('nivel', $curso['nivel']) == 'Intermedio') ? 'selected' : '' ?>>
                                Intermedio
                            </option>
                            <option value="Avanzado" <?= (old('nivel', $curso['nivel']) == 'Avanzado') ? 'selected' : '' ?>>
                                Avanzado
                            </option>
                        </select>
                        <?php if (isset($errors['nivel'])): ?>
                            <div class="invalid-feedback">
                                <?= is_array($errors['nivel']) ? $errors['nivel'][0] : $errors['nivel'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Estado -->
                    <div class="form-group">
                        <label for="estado" class="form-label">
                            Estado <span class="required">*</span>
                        </label>
                        <select class="form-control <?= isset($errors['estado']) ? 'is-invalid' : '' ?>"
                                id="estado" 
                                name="estado" 
                                required>
                            <option value="">Seleccionar estado</option>
                            <option value="Borrador" <?= (old('estado', $curso['estado']) == 'Borrador') ? 'selected' : '' ?>>
                                Borrador
                            </option>
                            <option value="Publicado" <?= (old('estado', $curso['estado']) == 'Publicado') ? 'selected' : '' ?>>
                                Publicado
                            </option>
                            <option value="Archivado" <?= (old('estado', $curso['estado']) == 'Archivado') ? 'selected' : '' ?>>
                                Archivado
                            </option>
                        </select>
                        <?php if (isset($errors['estado'])): ?>
                            <div class="invalid-feedback">
                                <?= is_array($errors['estado']) ? $errors['estado'][0] : $errors['estado'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Video y Configuraciones -->
            <div class="form-section">
                <h3 class="form-section-title">
                    <i class="fas fa-video"></i>
                    Video y Configuraciones
                </h3>

                <div class="form-grid">
                    <!-- URL del Video -->
                    <div class="form-group form-grid-full">
                        <label for="video_url" class="form-label">
                            URL del Video (YouTube) <span class="required">*</span>
                        </label>
                        <input type="url" 
                               class="form-control <?= isset($errors['video_url']) ? 'is-invalid' : '' ?>"
                               id="video_url" 
                               name="video_url" 
                               value="<?= htmlspecialchars(old('video_url', $curso['video_url'])) ?>" 
                               required 
                               placeholder="https://www.youtube.com/watch?v=..."
                               onchange="updateVideoPreview()">
                        <?php if (isset($errors['video_url'])): ?>
                            <div class="invalid-feedback">
                                <?= is_array($errors['video_url']) ? $errors['video_url'][0] : $errors['video_url'] ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Preview del video -->
                        <div class="video-preview" id="videoPreview">
                            <?php if (!empty($curso['video_url'])): ?>
                                <?php
                                $videoId = '';
                                if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $curso['video_url'], $matches)) {
                                    $videoId = $matches[1];
                                }
                                ?>
                                <?php if ($videoId): ?>
                                    <iframe src="https://www.youtube.com/embed/<?= $videoId ?>"
                                            frameborder="0" 
                                            allowfullscreen>
                                    </iframe>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="video-placeholder">
                                    <i class="fas fa-video"></i>
                                    <h4>Vista previa del video</h4>
                                    <p>Ingresa una URL de YouTube válida para ver la vista previa</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Duración en horas -->
                    <div class="form-group">
                        <label for="duracion_horas" class="form-label">
                            Duración (horas)
                        </label>
                        <input type="number" 
                               class="form-control <?= isset($errors['duracion_horas']) ? 'is-invalid' : '' ?>"
                               id="duracion_horas" 
                               name="duracion_horas" 
                               value="<?= old('duracion_horas', $curso['duracion_horas']) ?>" 
                               min="1" 
                               max="500"
                               placeholder="Ej: 40">
                        <?php if (isset($errors['duracion_horas'])): ?>
                            <div class="invalid-feedback">
                                <?= is_array($errors['duracion_horas']) ? $errors['duracion_horas'][0] : $errors['duracion_horas'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Precio -->
                    <div class="form-group">
                        <label for="precio" class="form-label">
                            Precio (Bs.) - Dejar vacío para curso gratuito
                        </label>
                        <input type="number" 
                               class="form-control <?= isset($errors['precio']) ? 'is-invalid' : '' ?>"
                               id="precio" 
                               name="precio" 
                               value="<?= old('precio', $curso['precio']) ?>" 
                               min="0" 
                               step="0.01"
                               placeholder="Ej: 150.00">
                        <?php if (isset($errors['precio'])): ?>
                            <div class="invalid-feedback">
                                <?= is_array($errors['precio']) ? $errors['precio'][0] : $errors['precio'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="form-actions">
                <div>
                    <a href="<?= route('cursos') ?>" class="crud-btn crud-btn-clear">
                        <i class="fas fa-arrow-left"></i>
                        Cancelar
                    </a>
                </div>
                
                <div class="btn-group">
                    <a href="<?= route('cursos.ver', ['id' => $curso['id']]) ?>" 
                       class="crud-btn crud-btn-outline-primary">
                        <i class="fas fa-eye"></i>
                        Ver Curso
                    </a>
                    
                    <button type="submit" class="crud-btn crud-btn-primary">
                        <i class="fas fa-save"></i>
                        Actualizar Curso
                    </button>
                </div>
            </div>
        </form>

    </div>
</div>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alertas
    document.querySelectorAll('.crud-alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // Validación del formulario
    const form = document.getElementById('editCourseForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const titulo = document.getElementById('titulo').value.trim();
            const descripcion = document.getElementById('descripcion').value.trim();
            const video_url = document.getElementById('video_url').value.trim();
            const categoria_id = document.getElementById('categoria_id').value;
            const docente_id = document.getElementById('docente_id').value;
            const nivel = document.getElementById('nivel').value;
            const estado = document.getElementById('estado').value;

            let hasErrors = false;

            // Limpiar errores previos
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.remove();
            });

            // Validar campos requeridos
            if (!titulo) {
                showError('titulo', 'El título es requerido');
                hasErrors = true;
            } else if (titulo.length < 5) {
                showError('titulo', 'El título debe tener al menos 5 caracteres');
                hasErrors = true;
            }

            if (!descripcion) {
                showError('descripcion', 'La descripción es requerida');
                hasErrors = true;
            } else if (descripcion.length < 10) {
                showError('descripcion', 'La descripción debe tener al menos 10 caracteres');
                hasErrors = true;
            }

            if (!video_url) {
                showError('video_url', 'La URL del video es requerida');
                hasErrors = true;
            } else if (!isValidYouTubeUrl(video_url)) {
                showError('video_url', 'Debe ser una URL válida de YouTube');
                hasErrors = true;
            }

            if (!categoria_id) {
                showError('categoria_id', 'Debe seleccionar una categoría');
                hasErrors = true;
            }

            if (!docente_id) {
                showError('docente_id', 'Debe seleccionar un docente');
                hasErrors = true;
            }

            if (!nivel) {
                showError('nivel', 'Debe seleccionar un nivel');
                hasErrors = true;
            }

            if (!estado) {
                showError('estado', 'Debe seleccionar un estado');
                hasErrors = true;
            }

            if (hasErrors) {
                e.preventDefault();
                // Scroll al primer error
                const firstError = document.querySelector('.is-invalid');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
            }
        });
    }

    function showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (field) {
            field.classList.add('is-invalid');
            
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;
            
            field.parentNode.appendChild(feedback);
        }
    }

    function isValidYouTubeUrl(url) {
        const youtubeRegex = /^(https?:\/\/)?(www\.)?(youtube\.com\/watch\?v=|youtu\.be\/)[\w-]+(&\S*)?$/;
        return youtubeRegex.test(url);
    }
});

function updateVideoPreview() {
    const videoUrl = document.getElementById('video_url').value;
    const preview = document.getElementById('videoPreview');
    
    if (!videoUrl) {
        preview.innerHTML = `
            <div class="video-placeholder">
                <i class="fas fa-video"></i>
                <h4>Vista previa del video</h4>
                <p>Ingresa una URL de YouTube válida para ver la vista previa</p>
            </div>
        `;
        return;
    }

    // Extraer ID del video de YouTube
    const videoIdMatch = videoUrl.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/);
    
    if (videoIdMatch) {
        const videoId = videoIdMatch[1];
        preview.innerHTML = `
            <iframe src="https://www.youtube.com/embed/${videoId}"
                    frameborder="0" 
                    allowfullscreen>
            </iframe>
        `;
    } else {
        preview.innerHTML = `
            <div class="video-placeholder">
                <i class="fas fa-exclamation-triangle" style="color: var(--danger-color);"></i>
                <h4>URL no válida</h4>
                <p>Por favor ingresa una URL válida de YouTube</p>
            </div>
        `;
    }
}
</script>