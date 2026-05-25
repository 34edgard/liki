<?php
$paginas = $paginas ?? [];
$success = $success ?? false;
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-file-alt me-2"></i> Páginas del Sistema</h2>
    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalNuevaPagina">
      <i class="fas fa-plus me-1"></i> Nueva Página
    </button>
  </div>

  <?php if($success): ?>
  <div class="alert alert-success alert-dismissible fade show">
    <i class="fas fa-check-circle me-1"></i> Página guardada correctamente.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <div class="row">
    <?php foreach($paginas as $pagina): ?>
    <div class="col-md-4 col-lg-3 mb-3">
      <div class="card h-100 shadow-sm">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title">
            <i class="fas fa-file-code me-1 text-primary"></i>
            <?= htmlspecialchars($pagina['nombre']) ?>
          </h5>
          <p class="card-text text-muted small">
            <?= htmlspecialchars($pagina['titulo'] ?? 'Sin título') ?>
          </p>
          <p class="card-text small">
            <span class="badge bg-info"><?= $pagina['numComponentes'] ?? 0 ?> componentes</span>
            <span class="badge bg-secondary"><?= $pagina['numEstilos'] ?? 0 ?> estilos</span>
            <span class="badge bg-warning text-dark"><?= $pagina['numScripts'] ?? 0 ?> scripts</span>
          </p>
          <div class="mt-auto">
            <a href="/admin/paginas/<?= htmlspecialchars($pagina['nombre']) ?>" class="btn btn-primary btn-sm w-100">
              <i class="fas fa-edit me-1"></i> Editar
            </a>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <?php if(empty($paginas)): ?>
  <div class="text-center py-5">
    <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
    <p class="text-muted">No hay páginas configuradas. Crea una nueva página para empezar.</p>
  </div>
  <?php endif; ?>
</div>

<!-- Modal para nueva página -->
<div class="modal fade" id="modalNuevaPagina" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title"><i class="fas fa-plus me-1"></i> Nueva Página</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label fw-bold">Nombre del archivo</label>
          <div class="input-group">
            <input type="text" class="form-control" id="nuevaPaginaNombre" placeholder="MiPagina">
            <span class="input-group-text">.json</span>
          </div>
          <small class="text-muted">Sin espacios ni caracteres especiales.</small>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Título de la página</label>
          <input type="text" class="form-control" id="nuevaPaginaTitulo" placeholder="Mi Nueva Página">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-success" onclick="crearNuevaPagina()">
          <i class="fas fa-plus me-1"></i> Crear Página
        </button>
      </div>
    </div>
  </div>
</div>

<script>
function crearNuevaPagina() {
    const nombre = document.getElementById('nuevaPaginaNombre').value.trim();
    const titulo = document.getElementById('nuevaPaginaTitulo').value.trim();

    if (!nombre) {
        alert('El nombre del archivo es requerido');
        return;
    }
    if (!/^[a-zA-Z0-9_-]+$/.test(nombre)) {
        alert('El nombre solo puede contener letras, números, guiones y guiones bajos');
        return;
    }

    const config = {
        tituloPagina: titulo || nombre,
        estilos: ["bootstrap.min", "estilos"],
        estilosD: [],
        scriptsD: ["htmx"],
        scripts: ["color-modes", "bootstrap.bundle.min"],
        contenidos: [
            {"componente": "estructura/Header", "configuracion": {"op": titulo || nombre}}
        ]
    };

    fetch('/admin/paginas/' + encodeURIComponent(nombre) + '/guardar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'config=' + encodeURIComponent(JSON.stringify(config))
    }).then(response => {
        if (response.ok || response.redirected) {
            window.location.href = '/admin/paginas/' + encodeURIComponent(nombre);
        } else {
            return response.text().then(t => { throw new Error(t); });
        }
    }).catch(err => {
        alert('Error al crear la página: ' + err.message);
    });
}
</script>
