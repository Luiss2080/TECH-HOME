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

<!-- Estilos específicos para el módulo CRUD - Editar -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">

<!-- Estilos adicionales para correcciones -->
<style>
/* Alineación en fila para Nivel y Estado */
.crud-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    width: 100%;
}

/* Corrección de visibilidad para textos de ayuda en modo oscuro */
body.ithr-dark-mode .crud-form-help,
body.dark-theme .crud-form-help {
    color: var(--text-secondary);
}

body.ithr-dark-mode .crud-form-help i,
body.dark-theme .crud-form-help i {
    color: var(--secondary-blue);
}

/* Responsivo para dispositivos móviles */
@media (max-width: 768px) {
    .crud-form-row {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
}
</style>

<!-- Contenedor principal del CRUD de edición -->
<div class="crud-edit-container">
    <div class="crud-edit-wrapper">

        <!-- Header principal con información del curso -->
        <div class="crud-section-card">
            <div class="crud-section-header">
                <div class="crud-section-header-content">
                    <div class="crud-section-icon">
                        <i class="fas fa-edit"></i>
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
                                    <i class="fas fa-edit"></i>
                                    Editar Curso
                                </li>
                            </ol>
                        </nav>
                        <h1 class="crud-section-title">Editar Curso</h1>
                        <p class="crud-section-subtitle">
                            Actualiza la información de "<?= $curso ? htmlspecialchars($curso['titulo']) : 'Curso' ?>"
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
                        <?php if (is_array($errors)): ?>
                            <?php foreach ($errors as $field => $fieldErrors): ?>
                                <?php if (is_array($fieldErrors)): ?>
                                    <?php foreach ($fieldErrors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li><?= htmlspecialchars($fieldErrors) ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
                <p class="crud-section-subtitle">Datos principales y descripción del curso</p>
            </div>
            
            <div class="crud-form-body">
                <form method="POST" action="<?= $curso ? route('cursos.update', ['id' => $curso['id']]) : '#' ?>" id="crudFormEditarCurso" class="crud-form">
                    <?php CSRF() ?>
                    <input type="hidden" name="_method" value="PUT">
                    
                    <div class="crud-form-grid">
                        <div class="crud-form-group crud-form-group-full">
                            <label for="crudTitulo" class="crud-form-label">
                                <i class="fas fa-heading"></i>
                                Título del Curso
                                <span class="crud-required">*</span>
                            </label>
                            <input type="text" 
                                   class="crud-form-control <?= flashGet('errors') && isset(flashGet('errors')['titulo']) ? 'is-invalid' : '' ?>"
                                   id="crudTitulo" 
                                   name="titulo" 
                                   value="<?= $curso ? htmlspecialchars(flashGet('old')['titulo'] ?? $curso['titulo']) : '' ?>" 
                                   required 
                                   maxlength="200"
                                   placeholder="Ej: Introducción a la Robótica con Arduino">
                            <?php if (flashGet('errors') && isset(flashGet('errors')['titulo'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array(flashGet('errors')['titulo']) ? flashGet('errors')['titulo'][0] : flashGet('errors')['titulo'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="crud-form-group crud-form-group-full">
                            <label for="crudDescripcion" class="crud-form-label">
                                <i class="fas fa-align-left"></i>
                                Descripción del Curso
                                <span class="crud-required">*</span>
                            </label>
                            <textarea class="crud-form-control <?= flashGet('errors') && isset(flashGet('errors')['descripcion']) ? 'is-invalid' : '' ?>"
                                      id="crudDescripcion" 
                                      name="descripcion" 
                                      rows="4" 
                                      required 
                                      placeholder="Describe el contenido y objetivos del curso..."><?= $curso ? htmlspecialchars(flashGet('old')['descripcion'] ?? $curso['descripcion']) : '' ?></textarea>
                            <?php if (flashGet('errors') && isset(flashGet('errors')['descripcion'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array(flashGet('errors')['descripcion']) ? flashGet('errors')['descripcion'][0] : flashGet('errors')['descripcion'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="crud-form-group">
                            <label for="crudVideoUrl" class="crud-form-label">
                                <i class="fas fa-video"></i>
                                URL del Video Principal
                                <span class="crud-required">*</span>
                            </label>
                            <input type="url" 
                                   class="crud-form-control <?= flashGet('errors') && isset(flashGet('errors')['video_url']) ? 'is-invalid' : '' ?>"
                                   id="crudVideoUrl" 
                                   name="video_url" 
                                   value="<?= $curso ? htmlspecialchars(flashGet('old')['video_url'] ?? $curso['video_url']) : '' ?>" 
                                   required 
                                   placeholder="https://www.youtube.com/watch?v=...">
                            <div class="crud-form-help">
                                <i class="fas fa-info-circle"></i>
                                Solo se admiten URLs de YouTube válidas
                            </div>
                            <?php if (flashGet('errors') && isset(flashGet('errors')['video_url'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array(flashGet('errors')['video_url']) ? flashGet('errors')['video_url'][0] : flashGet('errors')['video_url'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="crud-form-group">
                            <label for="crudCategoria" class="crud-form-label">
                                <i class="fas fa-tags"></i>
                                Categoría del Curso
                                <span class="crud-required">*</span>
                            </label>
                            <select class="crud-form-control <?= flashGet('errors') && isset(flashGet('errors')['categoria_id']) ? 'is-invalid' : '' ?>"
                                    id="crudCategoria" 
                                    name="categoria_id" 
                                    required>
                                <option value="">Selecciona una categoría</option>
                                <?php if (isset($categorias) && !empty($categorias)): ?>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <?php if ($categoria->tipo === 'curso'): ?>
                                            <option value="<?= $categoria->id ?>" 
                                                    <?= ($curso && $curso['categoria_id'] == $categoria->id) || (flashGet('old') && flashGet('old')['categoria_id'] == $categoria->id) ? 'selected' : '' ?>
                                                    data-color="<?= $categoria->color ?? '#3498db' ?>"
                                                    data-icon="<?= $categoria->icono ?? 'fas fa-tag' ?>">
                                                <?= htmlspecialchars($categoria->nombre) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (flashGet('errors') && isset(flashGet('errors')['categoria_id'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array(flashGet('errors')['categoria_id']) ? flashGet('errors')['categoria_id'][0] : flashGet('errors')['categoria_id'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="crud-form-group">
                            <label for="crudDocente" class="crud-form-label">
                                <i class="fas fa-user-tie"></i>
                                Docente Responsable
                                <span class="crud-required">*</span>
                            </label>
                            <select class="crud-form-control <?= flashGet('errors') && isset(flashGet('errors')['docente_id']) ? 'is-invalid' : '' ?>"
                                    id="crudDocente" 
                                    name="docente_id" 
                                    required <?= $isDocente ? 'disabled' : '' ?>>
                                <option value="">Selecciona un docente</option>
                                <?php if (isset($docentes) && !empty($docentes)): ?>
                                    <?php foreach ($docentes as $docente): ?>
                                        <option value="<?= $docente['id'] ?>" 
                                                <?= ($curso && $curso['docente_id'] == $docente['id']) || (flashGet('old') && flashGet('old')['docente_id'] == $docente['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($docente['nombre'] . ' ' . ($docente['apellido'] ?? '')) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if ($isDocente): ?>
                                <input type="hidden" name="docente_id" value="<?= $curso['docente_id'] ?? auth()->id ?>">
                            <?php endif; ?>
                            <div class="crud-form-help">
                                <i class="fas fa-info-circle"></i>
                                <?= $isDocente ? 'Como docente, el curso se mantendrá asignado a ti' : 'Selecciona el docente responsable del curso' ?>
                            </div>
                            <?php if (flashGet('errors') && isset(flashGet('errors')['docente_id'])): ?>
                                <div class="crud-invalid-feedback">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= is_array(flashGet('errors')['docente_id']) ? flashGet('errors')['docente_id'][0] : flashGet('errors')['docente_id'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="crud-form-row">
                            <div class="crud-form-group">
                                <label class="crud-form-label">
                                    <i class="fas fa-signal"></i>
                                    Nivel de Dificultad
                                    <span class="crud-required">*</span>
                                </label>
                                <div class="nivel-selector">
                                    <?php $selectedNivel = $curso['nivel'] ?? (flashGet('old')['nivel'] ?? ''); ?>
                                    <div class="nivel-option nivel-principiante <?= $selectedNivel == 'Principiante' ? 'selected' : '' ?>">
                                        <input type="radio" name="nivel" value="Principiante" id="nivelPrincipiante" 
                                               <?= $selectedNivel == 'Principiante' ? 'checked' : '' ?>>
                                        <label for="nivelPrincipiante">
                                            <i class="fas fa-seedling"></i>
                                            <span>Principiante</span>
                                        </label>
                                    </div>
                                    <div class="nivel-option nivel-intermedio <?= $selectedNivel == 'Intermedio' ? 'selected' : '' ?>">
                                        <input type="radio" name="nivel" value="Intermedio" id="nivelIntermedio" 
                                               <?= $selectedNivel == 'Intermedio' ? 'checked' : '' ?>>
                                        <label for="nivelIntermedio">
                                            <i class="fas fa-chart-line"></i>
                                            <span>Intermedio</span>
                                        </label>
                                    </div>
                                    <div class="nivel-option nivel-avanzado <?= $selectedNivel == 'Avanzado' ? 'selected' : '' ?>">
                                        <input type="radio" name="nivel" value="Avanzado" id="nivelAvanzado" 
                                               <?= $selectedNivel == 'Avanzado' ? 'checked' : '' ?>>
                                        <label for="nivelAvanzado">
                                            <i class="fas fa-rocket"></i>
                                            <span>Avanzado</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="crud-form-group">
                                <label class="crud-form-label">
                                    <i class="fas fa-toggle-on"></i>
                                    Estado del Curso
                                    <span class="crud-required">*</span>
                                </label>
                                <div class="estado-selector">
                                <?php $selectedEstado = $curso['estado'] ?? (flashGet('old')['estado'] ?? ''); ?>
                                <div class="estado-option estado-borrador <?= $selectedEstado == 'Borrador' ? 'selected' : '' ?>">
                                    <input type="radio" name="estado" value="Borrador" id="estadoBorrador" 
                                           <?= $selectedEstado == 'Borrador' ? 'checked' : '' ?>>
                                    <label for="estadoBorrador">
                                        <i class="fas fa-edit"></i>
                                        <span>Borrador</span>
                                    </label>
                                </div>
                                <div class="estado-option estado-publicado <?= $selectedEstado == 'Publicado' ? 'selected' : '' ?>">
                                    <input type="radio" name="estado" value="Publicado" id="estadoPublicado" 
                                           <?= $selectedEstado == 'Publicado' ? 'checked' : '' ?>>
                                    <label for="estadoPublicado">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Publicado</span>
                                    </label>
                                </div>
                                <div class="estado-option estado-archivado <?= $selectedEstado == 'Archivado' ? 'selected' : '' ?>">
                                    <input type="radio" name="estado" value="Archivado" id="estadoArchivado" 
                                           <?= $selectedEstado == 'Archivado' ? 'checked' : '' ?>>
                                    <label for="estadoArchivado">
                                        <i class="fas fa-archive"></i>
                                        <span>Archivado</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Botones de acción principal -->
        <div class="crud-section-card">
            <div class="crud-form-actions">
                <a href="<?= route('cursos') ?>" class="crud-btn crud-btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Cancelar y Volver
                </a>
                <button type="submit" form="crudFormEditarCurso" class="crud-btn crud-btn-primary" id="crudBtnSubmit">
                    <i class="fas fa-save"></i>
                    Actualizar Curso
                </button>
            </div>
        </div>

        <!-- Espacio de separación -->
        <div style="height: 20px;"></div> 

    </div>
</div>

<!-- JavaScript específico para editar curso -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('crudFormEditarCurso');
    const submitBtn = document.getElementById('crudBtnSubmit');

    // Selectores personalizados para nivel
    document.querySelectorAll('.nivel-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.nivel-option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });

    // Selectores personalizados para estado
    document.querySelectorAll('.estado-option').forEach(option => {
        option.addEventListener('click', function() {
            document.querySelectorAll('.estado-option').forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
        });
    });

    // Manejo del envío del formulario
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            submitBtn.classList.add('crud-form-loading');
            submitBtn.disabled = true;
            
            const originalContent = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando Curso...';
            
            setTimeout(() => {
                if (document.querySelector('.crud-alert-danger')) {
                    submitBtn.classList.remove('crud-form-loading');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalContent;
                }
            }, 1000);
        });
    }
});
</script>

<!-- CSS adicional para los selectores personalizados -->
<style>
.nivel-selector, .estado-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
    margin-top: 0.5rem;
}

.nivel-option, .estado-option {
    position: relative;
    cursor: pointer;
    transition: var(--transition-base);
}

.nivel-option input, .estado-option input {
    display: none;
}

.nivel-option label, .estado-option label {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    border: 2px solid rgba(0,0,0,0.1);
    border-radius: var(--border-radius-md);
    background: white;
    transition: var(--transition-base);
    cursor: pointer;
}

.nivel-option.selected label, .estado-option.selected label {
    border-color: var(--primary-color);
    background: rgba(59, 130, 246, 0.05);
    color: var(--primary-color);
}

.nivel-option:hover label, .estado-option:hover label {
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

.nivel-option label i, .estado-option label i {
    font-size: 1.5rem;
}

.nivel-principiante.selected label { border-color: #10b981; color: #10b981; background: rgba(16, 185, 129, 0.05); }
.nivel-intermedio.selected label { border-color: #f59e0b; color: #f59e0b; background: rgba(245, 158, 11, 0.05); }
.nivel-avanzado.selected label { border-color: #ef4444; color: #ef4444; background: rgba(239, 68, 68, 0.05); }

.estado-borrador.selected label { border-color: #6b7280; color: #6b7280; background: rgba(107, 114, 128, 0.05); }
.estado-publicado.selected label { border-color: #10b981; color: #10b981; background: rgba(16, 185, 129, 0.05); }
.estado-archivado.selected label { border-color: #ef4444; color: #ef4444; background: rgba(239, 68, 68, 0.05); }
</style>
