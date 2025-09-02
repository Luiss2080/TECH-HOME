<?php
$title = $title ?? 'Crear Nuevo Curso';
$errors = Session::flashGet('errors') ?? [];
$old = Session::flashGet('old') ?? [];
$categorias = $categorias ?? [];
?>

<!-- Estilos específicos para crear curso -->
<link rel="stylesheet" href="<?= asset('css/vistas.css'); ?>">

<style>
/* ============================================
   ESTILOS ESPECÍFICOS PARA CREAR CURSOS DOCENTE
   ============================================ */

.docente-curso-form {
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

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), #1e40af);
    color: white;
    padding: 0.875rem 2rem;
    border: none;
    border-radius: var(--border-radius-md);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition-base);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-strong);
}

.error-message {
    color: #dc2626;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.curso-preview {
    background: #f8fafc;
    border: 2px dashed #e2e8f0;
    border-radius: var(--border-radius-md);
    padding: 2rem;
    text-align: center;
    margin-top: 1rem;
}
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-primary">
                <i class="fas fa-plus-circle me-2"></i>
                Crear Nuevo Curso
            </h1>
            <p class="text-muted">Crea un nuevo curso para tus estudiantes</p>
        </div>
        <div>
            <a href="<?= route('docente.cursos') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Volver a Mis Cursos
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

    <!-- Formulario -->
    <div class="row">
        <div class="col-lg-8">
            <div class="docente-curso-form">
                <form action="<?= route('docente.cursos.guardar') ?>" method="POST" id="formCrearCurso">
                    
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
                               placeholder="Ej: Introducción a Arduino"
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
                                  placeholder="Describe el contenido y objetivos del curso..."
                                  required><?= htmlspecialchars($old['descripcion'] ?? '') ?></textarea>
                        <?php if (isset($errors['descripcion'])): ?>
                            <div class="error-message"><?= $errors['descripcion'] ?></div>
                        <?php endif; ?>
                    </div>

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

                    <!-- Duración y Precio -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duracion_estimada" class="form-label">
                                    <i class="fas fa-clock me-2"></i>
                                    Duración Estimada (horas) *
                                </label>
                                <input type="number" 
                                       id="duracion_estimada" 
                                       name="duracion_estimada" 
                                       class="form-control" 
                                       min="1"
                                       placeholder="Ej: 20"
                                       value="<?= htmlspecialchars($old['duracion_estimada'] ?? '') ?>"
                                       required>
                                <?php if (isset($errors['duracion_estimada'])): ?>
                                    <div class="error-message"><?= $errors['duracion_estimada'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
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
                                       placeholder="0.00"
                                       value="<?= htmlspecialchars($old['precio'] ?? '0') ?>">
                                <small class="text-muted">Dejar en 0 para curso gratuito</small>
                                <?php if (isset($errors['precio'])): ?>
                                    <div class="error-message"><?= $errors['precio'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Configuraciones -->
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" 
                                   id="es_gratuito" 
                                   name="es_gratuito" 
                                   class="form-check-input" 
                                   value="1"
                                   <?= ($old['es_gratuito'] ?? '1') == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="es_gratuito">
                                <i class="fas fa-gift me-2 text-success"></i>
                                Curso Gratuito
                            </label>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="form-group d-flex gap-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Crear Curso
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="limpiarFormulario()">
                            <i class="fas fa-eraser me-2"></i>
                            Limpiar
                        </button>
                    </div>

                </form>
            </div>
        </div>

        <!-- Preview del Curso -->
        <div class="col-lg-4">
            <div class="curso-preview">
                <div class="mb-3">
                    <i class="fas fa-eye fa-2x text-primary mb-2"></i>
                    <h5>Vista Previa</h5>
                    <p class="text-muted">Así se verá tu curso una vez creado</p>
                </div>
                
                <div id="curso-preview-content" class="text-start">
                    <div class="mb-2">
                        <strong id="preview-titulo">Título del curso...</strong>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">
                            <span id="preview-nivel">Nivel</span> • 
                            <span id="preview-duracion">0</span> horas
                        </small>
                    </div>
                    <div class="mb-2">
                        <p id="preview-descripcion" class="text-muted">
                            Descripción del curso...
                        </p>
                    </div>
                    <div>
                        <span class="badge bg-primary" id="preview-precio">Gratuito</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Preview en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const campos = {
        titulo: document.getElementById('titulo'),
        descripcion: document.getElementById('descripcion'),
        nivel: document.getElementById('nivel'),
        duracion_estimada: document.getElementById('duracion_estimada'),
        precio: document.getElementById('precio'),
        es_gratuito: document.getElementById('es_gratuito')
    };
    
    const preview = {
        titulo: document.getElementById('preview-titulo'),
        descripcion: document.getElementById('preview-descripcion'),
        nivel: document.getElementById('preview-nivel'),
        duracion: document.getElementById('preview-duracion'),
        precio: document.getElementById('preview-precio')
    };
    
    function actualizarPreview() {
        // Título
        preview.titulo.textContent = campos.titulo.value || 'Título del curso...';
        
        // Descripción
        const desc = campos.descripcion.value || 'Descripción del curso...';
        preview.descripcion.textContent = desc.length > 100 ? desc.substring(0, 100) + '...' : desc;
        
        // Nivel
        preview.nivel.textContent = campos.nivel.value || 'Nivel';
        
        // Duración
        preview.duracion.textContent = campos.duracion_estimada.value || '0';
        
        // Precio
        const esGratuito = campos.es_gratuito.checked;
        const precio = parseFloat(campos.precio.value) || 0;
        
        if (esGratuito || precio === 0) {
            preview.precio.textContent = 'Gratuito';
            preview.precio.className = 'badge bg-success';
        } else {
            preview.precio.textContent = `Bs. ${precio.toFixed(2)}`;
            preview.precio.className = 'badge bg-primary';
        }
    }
    
    // Eventos para actualizar preview
    Object.values(campos).forEach(campo => {
        if (campo) {
            campo.addEventListener('input', actualizarPreview);
            campo.addEventListener('change', actualizarPreview);
        }
    });
    
    // Preview inicial
    actualizarPreview();
});

function limpiarFormulario() {
    if (confirm('¿Estás seguro de que quieres limpiar el formulario?')) {
        document.getElementById('formCrearCurso').reset();
        // Actualizar preview
        document.getElementById('preview-titulo').textContent = 'Título del curso...';
        document.getElementById('preview-descripcion').textContent = 'Descripción del curso...';
        document.getElementById('preview-nivel').textContent = 'Nivel';
        document.getElementById('preview-duracion').textContent = '0';
        document.getElementById('preview-precio').textContent = 'Gratuito';
        document.getElementById('preview-precio').className = 'badge bg-success';
    }
}
</script>