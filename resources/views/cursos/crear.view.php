<?php
$title = $title ?? 'Crear Nuevo Curso';
$errors = $errors ?? [];
$old = $old ?? [];
$categorias = $categorias ?? [];
$docentes = $docentes ?? [];
$user = auth();
$isDocente = $user && $user->hasRole('docente') && !$user->hasRole('administrador');
?>

<!-- Estilos específicos para el módulo CRUD - Crear Cursos -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">

<!-- Estilos específicos para el módulo de Cursos -->
<style>
/* ============================================
   ESTILOS ESPECÍFICOS PARA CREAR CURSOS
   ============================================ */

/* Preview de video de YouTube */
.curso-video-preview {
    width: 100%;
    max-width: 500px;
    height: 280px;
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    border-radius: var(--border-radius-md);
    border: 2px dashed rgba(59, 130, 246, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    transition: var(--transition-base);
    margin: 1rem auto;
}

.curso-video-preview.has-video {
    border-color: var(--success-color);
    border-style: solid;
}

.curso-video-preview iframe {
    width: 100%;
    height: 100%;
    border: none;
    border-radius: var(--border-radius-md);
}

.video-placeholder {
    text-align: center;
    color: var(--text-secondary);
}

.video-placeholder i {
    font-size: 3rem;
    color: var(--secondary-blue);
    margin-bottom: 1rem;
    display: block;
}

.video-placeholder h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
}

.video-placeholder p {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Categorías con colores */
.categoria-option {
    display: flex;
    align-items: center;
    gap: 0.8rem;
    padding: 0.8rem;
    border-radius: var(--border-radius-sm);
    transition: var(--transition-base);
}

.categoria-color-indicator {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    flex-shrink: 0;
    border: 2px solid rgba(255, 255, 255, 0.5);
}

.categoria-option:hover {
    background: rgba(0, 0, 0, 0.05);
}

/* Selector de nivel con badges */
.nivel-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 0.5rem;
}

.nivel-option {
    position: relative;
}

.nivel-radio {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.nivel-label {
    display: block;
    padding: 1rem;
    border: 2px solid rgba(0, 0, 0, 0.1);
    border-radius: var(--border-radius-sm);
    text-align: center;
    cursor: pointer;
    transition: var(--transition-base);
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
}

.nivel-label:hover {
    border-color: var(--secondary-blue);
    transform: translateY(-2px);
    box-shadow: var(--shadow-light);
}

.nivel-radio:checked + .nivel-label {
    border-color: var(--primary-red);
    background: linear-gradient(135deg, rgba(220, 38, 38, 0.1), rgba(220, 38, 38, 0.05));
    color: var(--primary-red);
    font-weight: 700;
}

.nivel-icon {
    font-size: 1.5rem;
    margin-bottom: 0.5rem;
    display: block;
}

.nivel-principiante .nivel-icon { color: var(--success-color); }
.nivel-intermedio .nivel-icon { color: var(--warning-color); }
.nivel-avanzado .nivel-icon { color: var(--danger-color); }

/* Estado del curso con toggles */
.estado-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 1rem;
    margin-top: 0.5rem;
}

.estado-option {
    position: relative;
}

.estado-radio {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.estado-label {
    display: block;
    padding: 0.8rem;
    border: 2px solid rgba(0, 0, 0, 0.1);
    border-radius: var(--border-radius-sm);
    text-align: center;
    cursor: pointer;
    transition: var(--transition-bounce);
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
    font-size: 0.9rem;
    font-weight: 600;
}

.estado-label:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: var(--shadow-light);
}

.estado-radio:checked + .estado-label {
    transform: translateY(-2px) scale(1.02);
    box-shadow: var(--shadow-medium);
    font-weight: 700;
}

.estado-borrador .estado-radio:checked + .estado-label {
    border-color: var(--text-secondary);
    background: linear-gradient(135deg, rgba(107, 114, 128, 0.15), rgba(107, 114, 128, 0.08));
    color: var(--text-secondary);
}

.estado-publicado .estado-radio:checked + .estado-label {
    border-color: var(--success-color);
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(16, 185, 129, 0.08));
    color: var(--success-color);
}

.estado-archivado .estado-radio:checked + .estado-label {
    border-color: var(--warning-color);
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(245, 158, 11, 0.08));
    color: var(--warning-color);
}

/* Generador de imagen de portada */
.portada-generator {
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.7));
    border: 2px dashed rgba(0, 0, 0, 0.1);
    border-radius: var(--border-radius-md);
    padding: 2rem;
    text-align: center;
    margin: 1rem 0;
    transition: var(--transition-base);
}

.portada-generator:hover {
    border-color: var(--secondary-blue);
    background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.8));
}

.portada-preview {
    width: 120px;
    height: 80px;
    background: var(--gradient-primary);
    border-radius: var(--border-radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
    margin: 0 auto 1rem;
    transition: var(--transition-bounce);
}

.portada-generator:hover .portada-preview {
    transform: scale(1.1) rotate(3deg);
}

/* Validador URL de YouTube */
.url-validator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
    font-size: 0.85rem;
}

.url-validator.valid {
    color: var(--success-color);
}

.url-validator.invalid {
    color: var(--danger-color);
}

.url-validator.checking {
    color: var(--warning-color);
}

/* Contador de caracteres */
.char-counter {
    text-align: right;
    font-size: 0.8rem;
    color: var(--text-light);
    margin-top: 0.3rem;
}

.char-counter.warning {
    color: var(--warning-color);
}

.char-counter.danger {
    color: var(--danger-color);
}

/* Responsive específico para cursos */
@media (max-width: 768px) {
    .nivel-selector,
    .estado-selector {
        grid-template-columns: 1fr;
        gap: 0.8rem;
    }
    
    .curso-video-preview {
        height: 200px;
    }
    
    .video-placeholder i {
        font-size: 2rem;
    }
}

/* ============================================
   MODO OSCURO - TEMA DARK PARA CONTENEDORES
   ============================================ */
body.ithr-dark-mode .curso-video-preview,
body.dark-theme .curso-video-preview {
    background: linear-gradient(135deg, rgba(30, 41, 59, 0.8), rgba(15, 23, 42, 0.9));
    border-color: rgba(71, 85, 105, 0.4);
}

body.ithr-dark-mode .video-placeholder,
body.dark-theme .video-placeholder {
    color: var(--text-secondary);
}

body.ithr-dark-mode .video-placeholder h4,
body.dark-theme .video-placeholder h4 {
    color: var(--text-primary);
}

body.ithr-dark-mode .portada-generator,
body.dark-theme .portada-generator {
    background: linear-gradient(145deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.8));
    border-color: rgba(71, 85, 105, 0.4);
    color: var(--text-primary);
}

body.ithr-dark-mode .portada-generator:hover,
body.dark-theme .portada-generator:hover {
    border-color: var(--secondary-blue);
    background: linear-gradient(145deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.9));
}

body.ithr-dark-mode .portada-generator h4,
body.dark-theme .portada-generator h4 {
    color: var(--text-primary);
}

body.ithr-dark-mode .portada-generator p,
body.dark-theme .portada-generator p {
    color: var(--text-secondary);
}
</style>

<!-- Contenedor principal del CRUD de creación -->
<div class="crud-edit-container">
    <div class="crud-edit-wrapper">

        <!-- Header principal con información del nuevo curso -->
        <div class="crud-section-card">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="crud-section-title-group">
                        <nav aria-label="breadcrumb" class="crud-breadcrumb-nav">
                            <ol class="crud-breadcrumb">
                                <li class="crud-breadcrumb-item">
                                    <a href="<?= route('cursos') ?>">
                                        <i class="fas fa-play-circle"></i>
                                        Cursos
                                    </a>
                                </li>
                                <li class="crud-breadcrumb-item active">
                                    <i class="fas fa-plus-circle"></i>
                                    Crear Curso
                                </li>
                            </ol>
                        </nav>
                        <h1 class="crud-section-title">Crear Nuevo Curso</h1>
                        <p class="crud-section-subtitle">
                            <?= $isDocente ? 'Crea un nuevo curso educativo para tus estudiantes' : 'Registra un nuevo curso en la plataforma educativa' ?>
                        </p>
                    </div>
                </div>
                <div class="crud-section-header-actions">
                    <a href="<?= route('cursos') ?>" class="crud-section-action-header crud-btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Volver a Lista
                    </a>
                </div>
            </div>
        </div>

        <!-- Alertas de sesión -->
        <?php if (flashGet('error')): ?>
            <div class="crud-alert crud-alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><strong>ERROR:</strong> <?= htmlspecialchars(flashGet('error')) ?></span>
                <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php if (flashGet('success')): ?>
            <div class="crud-alert crud-alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars(flashGet('success')) ?></span>
                <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Errores de validación -->
        <?php if (flashGet('errors')): ?>
            <div class="crud-alert crud-alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="crud-alert-content">
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul class="crud-error-list">
                        <?php $errors = flashGet('errors'); ?>
                        <?php foreach ($errors as $field => $fieldErrors): ?>
                            <?php if (is_array($fieldErrors)): ?>
                                <?php foreach ($fieldErrors as $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li><?= htmlspecialchars($fieldErrors) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="crud-btn-close" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Sección: Información Básica del Curso -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-info-circle"></i>
                    Información Básica del Curso
                </h2>
                <p class="crud-section-subtitle">Datos principales y descripción del nuevo curso educativo</p>
            </div>
            
            <div class="crud-form-body">
                <form method="POST" action="<?= route('cursos.store') ?>" id="crudFormCrearCurso" class="crud-form">
                    <?= CSRF() ?>
                    
                    <div class="crud-form-grid">
                        <div class="crud-form-group crud-form-group-full">
                            <label for="crudTitulo" class="crud-form-label">
                                <i class="fas fa-heading"></i>
                                Título del Curso
                                <span class="crud-required">*</span>
                            </label>
                            <input type="text" 
                                   class="crud-form-control <?= isset($errors['titulo']) ? 'is-invalid' : '' ?>"
                                   id="crudTitulo" 
                                   name="titulo" 
                                   value="<?= htmlspecialchars(flashGet('old')['titulo'] ?? '') ?>" 
                                   required 
                                   maxlength="200"
                                   placeholder="Ej: Introducción a la Robótica con Arduino">
                            <div class="char-counter" id="tituloCounter">0/200 caracteres</div>
                            <?php if (isset($errors['titulo'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array($errors['titulo']) ? $errors['titulo'][0] : $errors['titulo'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="crud-form-group crud-form-group-full">
                            <label for="crudDescripcion" class="crud-form-label">
                                <i class="fas fa-align-left"></i>
                                Descripción del Curso
                                <span class="crud-required">*</span>
                            </label>
                            <textarea class="crud-form-control <?= isset($errors['descripcion']) ? 'is-invalid' : '' ?>"
                                      id="crudDescripcion" 
                                      name="descripcion" 
                                      required
                                      rows="4"
                                      maxlength="1000"
                                      placeholder="Describe detalladamente el contenido, objetivos y beneficios del curso..."><?= htmlspecialchars(flashGet('old')['descripcion'] ?? '') ?></textarea>
                            <div class="char-counter" id="descripcionCounter">0/1000 caracteres</div>
                            <div class="crud-form-text">
                                <i class="fas fa-lightbulb"></i>
                                Incluye objetivos de aprendizaje, prerrequisitos y metodología
                            </div>
                            <?php if (isset($errors['descripcion'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array($errors['descripcion']) ? $errors['descripcion'][0] : $errors['descripcion'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="crud-form-group">
                            <label for="crudCategoria" class="crud-form-label">
                                <i class="fas fa-tags"></i>
                                Categoría del Curso
                                <span class="crud-required">*</span>
                            </label>
                            <select class="crud-form-control <?= isset($errors['categoria_id']) ? 'is-invalid' : '' ?>"
                                    id="crudCategoria" 
                                    name="categoria_id" 
                                    required>
                                <option value="">Selecciona una categoría</option>
                                <?php if (isset($categorias) && !empty($categorias)): ?>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <?php if ($categoria->tipo === 'curso'): ?>
                                            <option value="<?= $categoria->id ?>" 
                                                    <?= ($old['categoria_id'] ?? '') == $categoria->id ? 'selected' : '' ?>
                                                    data-color="<?= $categoria->color ?? '#3498db' ?>"
                                                    data-icon="<?= $categoria->icono ?? 'fas fa-tag' ?>">
                                                <?= htmlspecialchars($categoria->nombre) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <div class="crud-form-text">
                                <i class="fas fa-info-circle"></i>
                                La categoría ayuda a los estudiantes a encontrar el curso
                            </div>
                            <?php if (isset($errors['categoria_id'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array($errors['categoria_id']) ? $errors['categoria_id'][0] : $errors['categoria_id'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="crud-form-group">
                            <label for="crudDocente" class="crud-form-label">
                                <i class="fas fa-chalkboard-teacher"></i>
                                Docente Instructor
                                <span class="crud-required">*</span>
                            </label>
                            <select class="crud-form-control <?= isset($errors['docente_id']) ? 'is-invalid' : '' ?>"
                                    id="crudDocente" 
                                    name="docente_id" 
                                    required
                                    <?= $isDocente ? 'readonly' : '' ?>>
                                <?php if ($isDocente): ?>
                                    <option value="<?= $user->id ?>" selected>
                                        <?= htmlspecialchars($user->nombre . ' ' . ($user->apellido ?? '')) ?> (Tú)
                                    </option>
                                <?php else: ?>
                                    <option value="">Selecciona un docente</option>
                                    <?php if (isset($docentes) && !empty($docentes)): ?>
                                        <?php foreach ($docentes as $docente): ?>
                                            <option value="<?= $docente['id'] ?>" 
                                                    <?= ($old['docente_id'] ?? '') == $docente['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($docente['nombre'] . ' ' . ($docente['apellido'] ?? '')) ?>
                                                <?= !empty($docente['email']) ? ' (' . $docente['email'] . ')' : '' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">No hay docentes disponibles - Contacta al administrador</option>
                                        <!-- Debug info for development -->
                                        <?php if (defined('DEBUG_MODE') && DEBUG_MODE): ?>
                                            <option value="">Debug: docentes variable = <?= var_export($docentes, true) ?></option>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </select>
                            <div class="crud-form-text">
                                <i class="fas fa-info-circle"></i>
                                <?= $isDocente ? 'Como docente, el curso se asignará automáticamente a ti' : 'Selecciona el docente responsable del curso' ?>
                            </div>
                            <?php if (isset($errors['docente_id'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array($errors['docente_id']) ? $errors['docente_id'][0] : $errors['docente_id'] ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sección: Contenido Multimedia -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-video"></i>
                    Contenido Multimedia
                </h2>
                <p class="crud-section-subtitle">Video principal y recursos visuales del curso</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-form-grid">
                    <div class="crud-form-group crud-form-group-full">
                        <label for="crudVideoUrl" class="crud-form-label">
                            <i class="fas fa-play-circle"></i>
                            URL del Video de YouTube
                            <span class="crud-required">*</span>
                        </label>
                        <input type="url" 
                               class="crud-form-control <?= isset($errors['video_url']) ? 'is-invalid' : '' ?>"
                               id="crudVideoUrl" 
                               name="video_url"
                               form="crudFormCrearCurso"
                               value="<?= htmlspecialchars($old['video_url'] ?? '') ?>" 
                               required
                               placeholder="https://www.youtube.com/watch?v=...">
                        <div class="url-validator" id="urlValidator">
                            <i class="fas fa-info-circle"></i>
                            <span>Ingresa una URL válida de YouTube</span>
                        </div>
                        <?php if (isset($errors['video_url'])): ?>
                            <div class="crud-invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?= is_array($errors['video_url']) ? $errors['video_url'][0] : $errors['video_url'] ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Preview del video -->
                        <div class="curso-video-preview" id="videoPreview">
                            <div class="video-placeholder">
                                <i class="fas fa-video"></i>
                                <h4>Vista Previa del Video</h4>
                                <p>El video aparecerá aquí cuando ingreses una URL válida</p>
                            </div>
                        </div>
                    </div>

                    <div class="crud-form-group crud-form-group-full">
                        <label for="crudImagenPortada" class="crud-form-label">
                            <i class="fas fa-image"></i>
                            Imagen de Portada
                        </label>
                        <input type="text" 
                               class="crud-form-control <?= isset($errors['imagen_portada']) ? 'is-invalid' : '' ?>"
                               id="crudImagenPortada" 
                               name="imagen_portada"
                               form="crudFormCrearCurso"
                               value="<?= htmlspecialchars($old['imagen_portada'] ?? '') ?>"
                               placeholder="nombre-imagen.jpg">
                        <div class="crud-form-text">
                            <i class="fas fa-info-circle"></i>
                            Opcional - Se generará automáticamente desde el video si no se especifica
                        </div>
                        
                        <!-- Generador de imagen -->
                        <div class="portada-generator">
                            <div class="portada-preview">
                                <i class="fas fa-image"></i>
                            </div>
                            <h4>Generador Automático</h4>
                            <p>Se creará una imagen de portada usando el thumbnail del video de YouTube</p>
                            <button type="button" class="crud-btn-action" onclick="generarPortadaAutomatica()">
                                <i class="fas fa-magic"></i>
                                Generar Automáticamente
                            </button>
                        </div>
                        
                        <?php if (isset($errors['imagen_portada'])): ?>
                            <div class="crud-invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?= is_array($errors['imagen_portada']) ? $errors['imagen_portada'][0] : $errors['imagen_portada'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Configuración del Curso -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-cogs"></i>
                    Configuración del Curso
                </h2>
                <p class="crud-section-subtitle">Nivel de dificultad y estado de publicación</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-form-grid">
                    <div class="crud-form-group">
                        <label class="crud-form-label">
                            <i class="fas fa-signal"></i>
                            Nivel de Dificultad
                            <span class="crud-required">*</span>
                        </label>
                        <div class="nivel-selector">
                            <div class="nivel-option nivel-principiante">
                                <input type="radio" 
                                       class="nivel-radio" 
                                       id="nivelPrincipiante" 
                                       name="nivel"
                                       form="crudFormCrearCurso"
                                       value="Principiante"
                                       <?= ($old['nivel'] ?? 'Principiante') === 'Principiante' ? 'checked' : '' ?>>
                                <label for="nivelPrincipiante" class="nivel-label">
                                    <i class="fas fa-seedling nivel-icon"></i>
                                    <strong>Principiante</strong>
                                    <div style="font-size: 0.8rem; opacity: 0.8;">Sin conocimientos previos</div>
                                </label>
                            </div>
                            
                            <div class="nivel-option nivel-intermedio">
                                <input type="radio" 
                                       class="nivel-radio" 
                                       id="nivelIntermedio" 
                                       name="nivel"
                                       form="crudFormCrearCurso"
                                       value="Intermedio"
                                       <?= ($old['nivel'] ?? '') === 'Intermedio' ? 'checked' : '' ?>>
                                <label for="nivelIntermedio" class="nivel-label">
                                    <i class="fas fa-chart-line nivel-icon"></i>
                                    <strong>Intermedio</strong>
                                    <div style="font-size: 0.8rem; opacity: 0.8;">Conocimientos básicos</div>
                                </label>
                            </div>
                            
                            <div class="nivel-option nivel-avanzado">
                                <input type="radio" 
                                       class="nivel-radio" 
                                       id="nivelAvanzado" 
                                       name="nivel"
                                       form="crudFormCrearCurso"
                                       value="Avanzado"
                                       <?= ($old['nivel'] ?? '') === 'Avanzado' ? 'checked' : '' ?>>
                                <label for="nivelAvanzado" class="nivel-label">
                                    <i class="fas fa-rocket nivel-icon"></i>
                                    <strong>Avanzado</strong>
                                    <div style="font-size: 0.8rem; opacity: 0.8;">Experiencia requerida</div>
                                </label>
                            </div>
                        </div>
                        <?php if (isset($errors['nivel'])): ?>
                            <div class="crud-invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?= is_array($errors['nivel']) ? $errors['nivel'][0] : $errors['nivel'] ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="crud-form-group">
                        <label class="crud-form-label">
                            <i class="fas fa-toggle-on"></i>
                            Estado del Curso
                            <span class="crud-required">*</span>
                        </label>
                        <div class="estado-selector">
                            <div class="estado-option estado-borrador">
                                <input type="radio" 
                                       class="estado-radio" 
                                       id="estadoBorrador" 
                                       name="estado"
                                       form="crudFormCrearCurso"
                                       value="Borrador"
                                       <?= ($old['estado'] ?? 'Borrador') === 'Borrador' ? 'checked' : '' ?>>
                                <label for="estadoBorrador" class="estado-label">
                                    <i class="fas fa-edit"></i>
                                    <strong>Borrador</strong>
                                </label>
                            </div>
                            
                            <div class="estado-option estado-publicado">
                                <input type="radio" 
                                       class="estado-radio" 
                                       id="estadoPublicado" 
                                       name="estado"
                                       form="crudFormCrearCurso"
                                       value="Publicado"
                                       <?= ($old['estado'] ?? '') === 'Publicado' ? 'checked' : '' ?>>
                                <label for="estadoPublicado" class="estado-label">
                                    <i class="fas fa-check-circle"></i>
                                    <strong>Publicado</strong>
                                </label>
                            </div>
                            
                            <div class="estado-option estado-archivado">
                                <input type="radio" 
                                       class="estado-radio" 
                                       id="estadoArchivado" 
                                       name="estado"
                                       form="crudFormCrearCurso"
                                       value="Archivado"
                                       <?= ($old['estado'] ?? '') === 'Archivado' ? 'checked' : '' ?>>
                                <label for="estadoArchivado" class="estado-label">
                                    <i class="fas fa-archive"></i>
                                    <strong>Archivado</strong>
                                </label>
                            </div>
                        </div>
                        <div class="crud-form-text">
                            <i class="fas fa-info-circle"></i>
                            <strong>Borrador:</strong> Visible solo para docentes y administradores. 
                            <strong>Publicado:</strong> Visible para todos los estudiantes.
                            <strong>Archivado:</strong> No visible pero conserva el contenido.
                        </div>
                        <?php if (isset($errors['estado'])): ?>
                            <div class="crud-invalid-feedback">
                                <i class="fas fa-exclamation-triangle"></i>
                                <?= is_array($errors['estado']) ? $errors['estado'][0] : $errors['estado'] ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Información de Ayuda -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-info-circle"></i>
                    Guía para Crear Cursos
                </h2>
                <p class="crud-section-subtitle">Consejos y mejores prácticas para crear cursos exitosos</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-info-panel">
                    <div class="crud-info-tabs">
                        <button class="crud-info-tab active" data-tab="contenido">
                            <i class="fas fa-video"></i>
                            Contenido de Video
                        </button>
                        <button class="crud-info-tab" data-tab="estructura">
                            <i class="fas fa-sitemap"></i>
                            Estructura del Curso
                        </button>
                        <button class="crud-info-tab" data-tab="estudiantes">
                            <i class="fas fa-users"></i>
                            Para Estudiantes
                        </button>
                        <button class="crud-info-tab" data-tab="tips">
                            <i class="fas fa-lightbulb"></i>
                            Tips Educativos
                        </button>
                    </div>
                    
                    <div class="crud-info-pane active" id="contenido">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-video"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Videos de YouTube:</strong> Utiliza videos de alta calidad con audio claro. La duración recomendada es de 10-30 minutos por lección para mantener la atención de los estudiantes.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Imagen de portada:</strong> Si no subes una imagen personalizada, se generará automáticamente desde el thumbnail del video de YouTube en alta resolución.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-closed-captioning"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Subtítulos y accesibilidad:</strong> Asegúrate de que tus videos de YouTube tengan subtítulos para mejorar la accesibilidad y comprensión.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="estructura">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-list-ol"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Estructura clara:</strong> Organiza el contenido con introducción, desarrollo y conclusión. Presenta los objetivos de aprendizaje al inicio del curso.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-tasks"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Prerrequisitos:</strong> Especifica claramente qué conocimientos previos necesitan los estudiantes antes de tomar el curso.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-graduation-cap"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Objetivos medibles:</strong> Define objetivos específicos y medibles que los estudiantes podrán alcanzar al completar el curso.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="estudiantes">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-search"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Fácil de encontrar:</strong> Usa títulos descriptivos y selecciona la categoría correcta para que los estudiantes encuentren fácilmente tu curso.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Estimación de tiempo:</strong> Indica claramente cuánto tiempo necesitarán los estudiantes para completar el curso y cada sección.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-comments"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Interacción:</strong> Fomenta la participación de los estudiantes a través de preguntas, ejercicios prácticos y proyectos.
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="crud-info-pane" id="tips">
                        <div class="crud-info-list">
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-rocket"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Comenzar como borrador:</strong> Publica tu curso como borrador primero para revisar todo el contenido antes de hacerlo público.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Progresión lógica:</strong> Asegúrate de que cada concepto se base en el anterior, creando una progresión natural del aprendizaje.
                                </div>
                            </div>
                            
                            <div class="crud-info-item">
                                <div class="crud-info-item-icon">
                                    <i class="fas fa-sync-alt"></i>
                                </div>
                                <div class="crud-info-item-content">
                                    <strong>Actualización continua:</strong> Revisa y actualiza regularmente el contenido para mantenerlo relevante y actualizado.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sección: Acciones Rápidas -->
        <div class="crud-section-card">
            <div class="crud-form-header">
                <h2 class="crud-section-title">
                    <i class="fas fa-bolt"></i>
                    Acciones Rápidas
                </h2>
                <p class="crud-section-subtitle">Herramientas útiles para la creación del curso</p>
            </div>
            
            <div class="crud-form-body">
                <div class="crud-actions-grid">
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-magic"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Generar Título</h4>
                            <p>Crear título atractivo basado en la categoría</p>
                            <button type="button" class="crud-btn-action" onclick="generarTitulo()">
                                <i class="fas fa-wand-magic-sparkles"></i>
                                Generar
                            </button>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Validar YouTube</h4>
                            <p>Verificar que el video sea accesible</p>
                            <button type="button" class="crud-btn-action" onclick="validarYoutube()">
                                <i class="fas fa-search"></i>
                                Validar
                            </button>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Ver Cursos Existentes</h4>
                            <p>Revisar catálogo actual de cursos</p>
                            <a href="<?= route('cursos') ?>" class="crud-btn-action">
                                <i class="fas fa-external-link-alt"></i>
                                Ver Catálogo
                            </a>
                        </div>
                    </div>
                    
                    <div class="crud-action-card">
                        <div class="crud-action-icon">
                            <i class="fas fa-save"></i>
                        </div>
                        <div class="crud-action-content">
                            <h4>Guardar Borrador</h4>
                            <p>Guardar progreso sin publicar</p>
                            <button type="button" class="crud-btn-action" onclick="guardarBorrador()">
                                <i class="fas fa-file-alt"></i>
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción principal -->
        <div class="crud-section-card">
            <div class="crud-form-actions">
                <a href="<?= route('cursos') ?>" class="crud-btn crud-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Cancelar y Volver
                </a>
                <button type="submit" form="crudFormCrearCurso" class="crud-btn crud-btn-primary" id="crudBtnSubmit">
                    <i class="fas fa-save"></i>
                    Crear Curso
                </button>
            </div>
        </div>

        <!-- Espacio de separación -->
        <div style="height: 20px;"></div> 

    </div>
</div>

<!-- JavaScript específico para crear curso -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('crudFormCrearCurso');
    const submitBtn = document.getElementById('crudBtnSubmit');
    const videoUrlField = document.getElementById('crudVideoUrl');
    const videoPreview = document.getElementById('videoPreview');
    const urlValidator = document.getElementById('urlValidator');

    // Contadores de caracteres
    function setupCharCounter(fieldId, counterId, maxLength) {
        const field = document.getElementById(fieldId);
        const counter = document.getElementById(counterId);
        
        if (field && counter) {
            field.addEventListener('input', function() {
                const length = this.value.length;
                counter.textContent = `${length}/${maxLength} caracteres`;
                
                if (length > maxLength * 0.9) {
                    counter.classList.add('danger');
                    counter.classList.remove('warning');
                } else if (length > maxLength * 0.7) {
                    counter.classList.add('warning');
                    counter.classList.remove('danger');
                } else {
                    counter.classList.remove('warning', 'danger');
                }
            });
            
            // Trigger inicial
            field.dispatchEvent(new Event('input'));
        }
    }

    setupCharCounter('crudTitulo', 'tituloCounter', 200);
    setupCharCounter('crudDescripcion', 'descripcionCounter', 1000);

    // Validador de URL de YouTube
    function validateYouTubeUrl(url) {
        const patterns = [
            /(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11})/,
            /youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/,
            /youtube\.com\/v\/([a-zA-Z0-9_-]{11})/
        ];
        
        for (const pattern of patterns) {
            const match = url.match(pattern);
            if (match) {
                return match[1];
            }
        }
        return null;
    }

    function updateVideoPreview(url) {
        const videoId = validateYouTubeUrl(url);
        
        if (videoId) {
            urlValidator.className = 'url-validator valid';
            urlValidator.innerHTML = '<i class="fas fa-check-circle"></i><span>URL de YouTube válida</span>';
            
            videoPreview.classList.add('has-video');
            videoPreview.innerHTML = `
                <iframe src="https://www.youtube.com/embed/${videoId}" 
                        allowfullscreen>
                </iframe>
            `;
            
            videoUrlField.classList.remove('crud-form-error');
            videoUrlField.classList.add('crud-form-success');
        } else if (url.length > 0) {
            urlValidator.className = 'url-validator invalid';
            urlValidator.innerHTML = '<i class="fas fa-times-circle"></i><span>URL de YouTube inválida</span>';
            
            videoPreview.classList.remove('has-video');
            videoPreview.innerHTML = `
                <div class="video-placeholder">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h4>URL Inválida</h4>
                    <p>Por favor ingresa una URL válida de YouTube</p>
                </div>
            `;
            
            videoUrlField.classList.remove('crud-form-success');
            videoUrlField.classList.add('crud-form-error');
        } else {
            urlValidator.className = 'url-validator';
            urlValidator.innerHTML = '<i class="fas fa-info-circle"></i><span>Ingresa una URL válida de YouTube</span>';
            
            videoPreview.classList.remove('has-video');
            videoPreview.innerHTML = `
                <div class="video-placeholder">
                    <i class="fas fa-video"></i>
                    <h4>Vista Previa del Video</h4>
                    <p>El video aparecerá aquí cuando ingreses una URL válida</p>
                </div>
            `;
            
            videoUrlField.classList.remove('crud-form-success', 'crud-form-error');
        }
    }

    if (videoUrlField) {
        videoUrlField.addEventListener('input', function() {
            updateVideoPreview(this.value);
        });
        
        // Validación inicial si hay valor
        if (videoUrlField.value) {
            updateVideoPreview(videoUrlField.value);
        }
    }

    // Función para cambiar tabs de información
    document.querySelectorAll('.crud-info-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            // Remover clase active de todos los tabs
            document.querySelectorAll('.crud-info-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.crud-info-pane').forEach(p => p.classList.remove('active'));
            
            // Activar tab seleccionado
            this.classList.add('active');
            const targetPane = document.getElementById(this.dataset.tab);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    // Validación en tiempo real de campos requeridos
    const requiredFields = document.querySelectorAll('input[required], textarea[required], select[required]');
    requiredFields.forEach(field => {
        field.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('crud-form-error');
                this.classList.add('crud-form-success');
            } else {
                this.classList.remove('crud-form-success');
                if (this.classList.contains('is-invalid')) {
                    this.classList.add('crud-form-error');
                }
            }
        });

        field.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('crud-form-error');
                this.classList.remove('crud-form-success');
            }
        });
    });

    // Manejo del envío del formulario
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            const titulo = document.getElementById('crudTitulo').value.trim();
            const descripcion = document.getElementById('crudDescripcion').value.trim();
            const videoUrl = document.getElementById('crudVideoUrl').value.trim();
            const categoria = document.getElementById('crudCategoria').value;
            const docente = document.getElementById('crudDocente').value;

            let isValid = true;
            const errors = [];

            if (!titulo) {
                errors.push('El título es obligatorio');
                document.getElementById('crudTitulo').classList.add('crud-form-error');
                isValid = false;
            }

            if (!descripcion) {
                errors.push('La descripción es obligatoria');
                document.getElementById('crudDescripcion').classList.add('crud-form-error');
                isValid = false;
            }

            if (!videoUrl || !validateYouTubeUrl(videoUrl)) {
                errors.push('La URL de YouTube es obligatoria y debe ser válida');
                document.getElementById('crudVideoUrl').classList.add('crud-form-error');
                isValid = false;
            }

            if (!categoria) {
                errors.push('Debe seleccionar una categoría');
                document.getElementById('crudCategoria').classList.add('crud-form-error');
                isValid = false;
            }

            if (!docente) {
                errors.push('Debe seleccionar un docente');
                document.getElementById('crudDocente').classList.add('crud-form-error');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                alert('Errores encontrados:\n• ' + errors.join('\n• '));
                
                // Scroll al primer error
                const firstError = document.querySelector('.crud-form-error');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstError.focus();
                }
                return false;
            }

            submitBtn.classList.add('crud-form-loading');
            submitBtn.disabled = true;
            
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando Curso...';
            
            setTimeout(() => {
                if (document.querySelector('.crud-alert-danger')) {
                    submitBtn.classList.remove('crud-form-loading');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                }
            }, 1000);
        });
    }

    // Funciones para acciones rápidas
    window.generarTitulo = function() {
        const categoriaSelect = document.getElementById('crudCategoria');
        const tituloField = document.getElementById('crudTitulo');
        
        if (!categoriaSelect.value) {
            alert('Por favor selecciona una categoría primero');
            categoriaSelect.focus();
            return;
        }
        
        const categoriaNombre = categoriaSelect.options[categoriaSelect.selectedIndex].text;
        const niveles = ['Introducción a', 'Fundamentos de', 'Curso Completo de', 'Masterclass de', 'Especialización en'];
        const nivel = niveles[Math.floor(Math.random() * niveles.length)];
        
        const tituloGenerado = `${nivel} ${categoriaNombre}`;
        tituloField.value = tituloGenerado;
        tituloField.dispatchEvent(new Event('input'));
        
        alert(`Título generado: "${tituloGenerado}"\n\nPuedes modificarlo según tus necesidades.`);
    };

    window.validarYoutube = function() {
        const videoUrl = document.getElementById('crudVideoUrl').value.trim();
        
        if (!videoUrl) {
            alert('Por favor ingresa una URL de YouTube para validar');
            document.getElementById('crudVideoUrl').focus();
            return;
        }
        
        const videoId = validateYouTubeUrl(videoUrl);
        
        if (videoId) {
            alert(`URL de YouTube válida!\n\nID del video: ${videoId}\nEl video se puede reproducir correctamente.`);
        } else {
            alert('La URL ingresada no es una URL válida de YouTube.\n\nFormatos aceptados:\n• https://www.youtube.com/watch?v=...\n• https://youtu.be/...\n• https://www.youtube.com/embed/...');
            document.getElementById('crudVideoUrl').focus();
        }
    };

    window.generarPortadaAutomatica = function() {
        const videoUrl = document.getElementById('crudVideoUrl').value.trim();
        const imagenField = document.getElementById('crudImagenPortada');
        
        if (!videoUrl) {
            alert('Por favor ingresa una URL de YouTube primero');
            document.getElementById('crudVideoUrl').focus();
            return;
        }
        
        const videoId = validateYouTubeUrl(videoUrl);
        
        if (videoId) {
            const imageName = `curso_${videoId}_${Date.now()}.jpg`;
            imagenField.value = imageName;
            
            alert(`Imagen de portada generada automáticamente!\n\nNombre: ${imageName}\n\nSe utilizará el thumbnail de YouTube en alta resolución.`);
        } else {
            alert('URL de YouTube inválida. No se puede generar la portada automáticamente.');
        }
    };

    window.guardarBorrador = function() {
        // Cambiar estado a Borrador
        document.getElementById('estadoBorrador').checked = true;
        
        // Enviar formulario
        if (form) {
            form.submit();
        }
    };

    // Auto-dismiss de alertas después de 6 segundos
    const alerts = document.querySelectorAll('.crud-alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px) scale(0.95)';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, 300);
        }, 6000);
    });

    // Mostrar colores de categorías en el select
    const categoriaSelect = document.getElementById('crudCategoria');
    if (categoriaSelect) {
        categoriaSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const color = selectedOption.getAttribute('data-color');
            
            if (color && color !== '#3498db') {
                this.style.borderLeftColor = color;
                this.style.borderLeftWidth = '4px';
            } else {
                this.style.borderLeft = '';
            }
        });
    }

    // Autocompletado inteligente de descripción basado en categoría y título
    function sugerirDescripcion() {
        const titulo = document.getElementById('crudTitulo').value.trim();
        const categoria = document.getElementById('crudCategoria');
        const descripcion = document.getElementById('crudDescripcion');
        
        if (titulo && categoria.value && !descripcion.value) {
            const categoriaNombre = categoria.options[categoria.selectedIndex].text;
            
            const plantillas = {
                'Robótica': `Aprende ${titulo.toLowerCase()} de manera práctica y aplicada. Este curso te enseñará los fundamentos de la robótica, desde conceptos básicos hasta proyectos avanzados. Incluye ejercicios prácticos con componentes reales.`,
                'Programación': `Domina ${titulo.toLowerCase()} con este curso completo y actualizado. Aprenderás desde los conceptos fundamentales hasta técnicas avanzadas, con ejemplos prácticos y proyectos reales.`,
                'Electrónica': `Descubre ${titulo.toLowerCase()} a través de explicaciones claras y proyectos hands-on. Desde teoría básica hasta aplicaciones prácticas en electrónica moderna.`,
                'Inteligencia Artificial': `Explora ${titulo.toLowerCase()} con enfoques modernos y aplicaciones reales. Comprende los algoritmos, implementaciones y casos de uso en el mundo actual de la IA.`,
                'Ciencias de Datos': `Analiza y visualiza datos con ${titulo.toLowerCase()}. Aprende técnicas estadísticas, herramientas modernas y metodologías para extraer insights valiosos de los datos.`
            };
            
            const sugerencia = plantillas[categoriaNombre] || `Aprende ${titulo.toLowerCase()} de forma completa y práctica. Este curso te proporcionará los conocimientos y habilidades necesarias para dominar esta área.`;
            
            descripcion.placeholder = `Sugerencia: ${sugerencia}`;
        }
    }

    document.getElementById('crudTitulo')?.addEventListener('blur', sugerirDescripcion);
    document.getElementById('crudCategoria')?.addEventListener('change', sugerirDescripcion);
});
</script>