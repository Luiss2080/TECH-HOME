<?php
// Vista para subir material educativo

// Funciones auxiliares para manejo de errores y datos flash
function hasError($field) {
    $errors = flashGet('errors') ?? [];
    return isset($errors[$field]);
}

function getError($field) {
    $errors = flashGet('errors') ?? [];
    return is_array($errors[$field] ?? null) ? implode(', ', $errors[$field]) : ($errors[$field] ?? '');
}
?>
<link rel="stylesheet" href="<?= asset('css/docente/docentes.css') ?>">

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-upload"></i>
                        <?= $title ?? 'Subir Material Educativo' ?>
                    </h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (flashGet('success')): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?= htmlspecialchars(flashGet('success')) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (flashGet('errors')): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            Por favor corrige los errores en el formulario.
                        </div>
                    <?php endif; ?>

                    <form action="<?= route('docente.materiales.store') ?>" method="POST" enctype="multipart/form-data">
                        <?php CSRF(); ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="titulo">Título del Material *</label>
                                    <input type="text" class="form-control" id="titulo" name="titulo" 
                                           value="<?= old('titulo') ?>" required>
                                    <?php if (hasError('titulo')): ?>
                                        <div class="text-danger"><?= getError('titulo') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tipo">Tipo de Material *</label>
                                    <select class="form-control" id="tipo" name="tipo" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <option value="pdf" <?= old('tipo') === 'pdf' ? 'selected' : '' ?>>PDF</option>
                                        <option value="video" <?= old('tipo') === 'video' ? 'selected' : '' ?>>Video</option>
                                        <option value="codigo" <?= old('tipo') === 'codigo' ? 'selected' : '' ?>>Código</option>
                                        <option value="guia" <?= old('tipo') === 'guia' ? 'selected' : '' ?>>Guía</option>
                                        <option value="dataset" <?= old('tipo') === 'dataset' ? 'selected' : '' ?>>Dataset</option>
                                    </select>
                                    <?php if (hasError('tipo')): ?>
                                        <div class="text-danger"><?= getError('tipo') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="categoria_id">Categoría *</label>
                                    <select class="form-control" id="categoria_id" name="categoria_id" required>
                                        <option value="">Seleccionar categoría...</option>
                                        <?php if (!empty($categorias)): ?>
                                            <?php foreach ($categorias as $categoria): ?>
                                                <option value="<?= $categoria['id'] ?>" 
                                                        <?= old('categoria_id') == $categoria['id'] ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($categoria['nombre']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <?php if (hasError('categoria_id')): ?>
                                        <div class="text-danger"><?= getError('categoria_id') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="publico">Acceso</label>
                                    <select class="form-control" id="publico" name="publico">
                                        <option value="1" <?= old('publico', 1) == 1 ? 'selected' : '' ?>>Público</option>
                                        <option value="0" <?= old('publico') == 0 ? 'selected' : '' ?>>Solo estudiantes registrados</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción *</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required><?= old('descripcion') ?></textarea>
                            <?php if (hasError('descripcion')): ?>
                                <div class="text-danger"><?= getError('descripcion') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="archivo">Archivo (opcional)</label>
                            <input type="file" class="form-control-file" id="archivo" name="archivo" 
                                   accept=".pdf,.doc,.docx,.mp4,.avi,.zip,.rar,.txt,.py,.ino,.c,.cpp,.java">
                            <small class="form-text text-muted">Tamaño máximo: 10MB</small>
                            <?php if (hasError('archivo')): ?>
                                <div class="text-danger"><?= getError('archivo') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="form-group">
                            <label for="enlace_externo">Enlace Externo (opcional)</label>
                            <input type="url" class="form-control" id="enlace_externo" name="enlace_externo" 
                                   value="<?= old('enlace_externo') ?>" placeholder="https://ejemplo.com/recurso">
                            <small class="form-text text-muted">Si no subes archivo, puedes agregar un enlace externo</small>
                        </div>

                        <div class="form-group text-right">
                            <a href="<?= route('docente.materiales') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Subir Material
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>