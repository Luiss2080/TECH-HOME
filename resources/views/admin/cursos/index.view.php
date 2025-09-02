<?php
// Verificar autenticación y permisos
$user = auth();
if (!$user) {
    header('Location: ' . route('login'));
    exit;
}
?>

<link rel="stylesheet" href="<?= asset('css/admin/admin.css'); ?>">

<div class="dashboard-content">
    <!-- Header de la sección -->
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title">
                    <i class="fas fa-graduation-cap"></i>
                    Gestión de Cursos
                </h2>
                <p class="section-subtitle">Administra todos los cursos de la plataforma</p>
            </div>
            <a href="<?= route('admin.cursos.crear') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Crear Curso
            </a>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-book"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number"><?= count($cursos) ?></div>
                        <div class="stat-label">Total Cursos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <?= count(array_filter($cursos, function($c) { return $c->estado === 'Publicado'; })) ?>
                        </div>
                        <div class="stat-label">Publicados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <?= count(array_filter($cursos, function($c) { return $c->estado === 'Borrador'; })) ?>
                        </div>
                        <div class="stat-label">Borradores</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon bg-info">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">
                            <?= count(array_filter($cursos, function($c) { return $c->es_gratuito == 1; })) ?>
                        </div>
                        <div class="stat-label">Gratuitos</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filters-section mb-4">
            <div class="row">
                <div class="col-md-3">
                    <select class="form-control" id="filtroEstado">
                        <option value="">Todos los estados</option>
                        <option value="Publicado">Publicado</option>
                        <option value="Borrador">Borrador</option>
                        <option value="Archivado">Archivado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="filtroGratuito">
                        <option value="">Todos los tipos</option>
                        <option value="1">Gratuitos</option>
                        <option value="0">De pago</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscarCurso" placeholder="Buscar cursos...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de cursos -->
        <div class="table-responsive">
            <table class="table table-hover" id="tablaCursos">
                <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Docente</th>
                        <th>Categoría</th>
                        <th>Nivel</th>
                        <th>Estado</th>
                        <th>Tipo</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cursos as $curso): ?>
                        <tr data-estado="<?= htmlspecialchars($curso->estado) ?>" 
                            data-gratuito="<?= $curso->es_gratuito ?>"
                            data-titulo="<?= htmlspecialchars(strtolower($curso->titulo)) ?>">
                            <td><?= $curso->id ?></td>
                            <td>
                                <div class="curso-info">
                                    <strong><?= htmlspecialchars($curso->titulo) ?></strong>
                                    <small class="text-muted d-block">
                                        <?= substr(htmlspecialchars($curso->descripcion), 0, 60) ?>...
                                    </small>
                                </div>
                            </td>
                            <td>
                                <?php $docente = \App\Models\User::find($curso->docente_id); ?>
                                <?= $docente ? htmlspecialchars($docente->nombre . ' ' . $docente->apellido) : 'No asignado' ?>
                            </td>
                            <td>
                                <?php $categoria = \App\Models\Categoria::find($curso->categoria_id); ?>
                                <?= $categoria ? htmlspecialchars($categoria->nombre) : 'Sin categoría' ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= $curso->nivel === 'Principiante' ? 'success' : ($curso->nivel === 'Intermedio' ? 'warning' : 'danger') ?>">
                                    <?= htmlspecialchars($curso->nivel) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $curso->estado === 'Publicado' ? 'success' : ($curso->estado === 'Borrador' ? 'warning' : 'secondary') ?>">
                                    <?= htmlspecialchars($curso->estado) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $curso->es_gratuito ? 'info' : 'primary' ?>">
                                    <?= $curso->es_gratuito ? 'Gratuito' : 'De pago' ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m/Y', strtotime($curso->fecha_creacion)) ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?= route('cursos.ver', ['id' => $curso->id]) ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Ver curso">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= route('cursos.editar', ['id' => $curso->id]) ?>" 
                                       class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger" 
                                            onclick="confirmarEliminacion(<?= $curso->id ?>, '<?= htmlspecialchars($curso->titulo) ?>')"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (empty($cursos)): ?>
            <div class="text-center py-5">
                <i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No hay cursos registrados</h5>
                <p class="text-muted">Comienza creando tu primer curso</p>
                <a href="<?= route('admin.cursos.crear') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Crear Primer Curso
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtros
    const filtroEstado = document.getElementById('filtroEstado');
    const filtroGratuito = document.getElementById('filtroGratuito');
    const buscarCurso = document.getElementById('buscarCurso');
    const tabla = document.getElementById('tablaCursos');

    function aplicarFiltros() {
        const estadoSeleccionado = filtroEstado.value;
        const gratuitoSeleccionado = filtroGratuito.value;
        const textoBusqueda = buscarCurso.value.toLowerCase();
        
        const filas = tabla.querySelectorAll('tbody tr');
        
        filas.forEach(fila => {
            const estado = fila.dataset.estado;
            const esGratuito = fila.dataset.gratuito;
            const titulo = fila.dataset.titulo;
            
            let mostrar = true;
            
            // Filtro por estado
            if (estadoSeleccionado && estado !== estadoSeleccionado) {
                mostrar = false;
            }
            
            // Filtro por tipo (gratuito/pago)
            if (gratuitoSeleccionado && esGratuito !== gratuitoSeleccionado) {
                mostrar = false;
            }
            
            // Filtro por texto
            if (textoBusqueda && !titulo.includes(textoBusqueda)) {
                mostrar = false;
            }
            
            fila.style.display = mostrar ? '' : 'none';
        });
    }

    // Event listeners para filtros
    filtroEstado.addEventListener('change', aplicarFiltros);
    filtroGratuito.addEventListener('change', aplicarFiltros);
    buscarCurso.addEventListener('input', aplicarFiltros);
});

function confirmarEliminacion(id, titulo) {
    if (confirm(`¿Estás seguro de que quieres eliminar el curso "${titulo}"?\n\nEsta acción no se puede deshacer.`)) {
        // Crear formulario para eliminar
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= route('admin.cursos.eliminar', ['id' => '']) ?>${id}`;
        
        // Agregar token CSRF si existe
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = '_token';
            input.value = csrfToken.getAttribute('content');
            form.appendChild(input);
        }
        
        // Agregar método DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>