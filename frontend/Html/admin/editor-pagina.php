<?php 
extract($config);
$contenidos = $contenidos ?? [];
$componentesDisponibles = $componentesDisponibles ?? [];
?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit me-2"></i>Editor de Página: <span class="text-primary"><?= htmlspecialchars($nombrePagina ?? '') ?></span></h2>
    <a href="/admin/paginas" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i> Volver a páginas</a>
  </div>

  <div class="row">
    <!-- Formulario principal -->
    <div class="col-lg-7">
      <form id="formEditorPagina">
        <input type="hidden" name="nombrePagina" value="<?= htmlspecialchars($nombrePagina ?? '') ?>">

        <!-- Título de página -->
        <div class="card mb-3">
          <div class="card-header bg-primary text-white"><i class="fas fa-heading me-1"></i> Título de Página</div>
          <div class="card-body">
            <input type="text" class="form-control" name="tituloPagina" id="tituloPagina"
                   value="<?= htmlspecialchars($tituloPagina ?? '') ?>" placeholder="Título de la página"
                   oninput="actualizarPreview()">
          </div>
        </div>

        <!-- Estilos dependencia (estilosD) -->
        <div class="card mb-3">
          <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-palette me-1"></i> Estilos Dependencia (estilosD)</span>
            <button type="button" class="btn btn-sm btn-light" onclick="agregarCampo('estilosD')"><i class="fas fa-plus"></i></button>
          </div>
          <div class="card-body" id="container-estilosD">
            <?php foreach(($estilosD ?? []) as $i => $estilo): ?>
            <div class="input-group mb-2" id="estilosD-item-<?= $i ?>">
              <input type="text" class="form-control campo-dinamico" data-grupo="estilosD"
                     value="<?= htmlspecialchars($estilo) ?>" placeholder="nombre del estilo" oninput="actualizarPreview()">
              <button type="button" class="btn btn-outline-danger" onclick="eliminarCampo('estilosD-item-<?= $i ?>')"><i class="fas fa-trash"></i></button>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Estilos (estilos) -->
        <div class="card mb-3">
          <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-css3-alt me-1"></i> Estilos (estilos)</span>
            <button type="button" class="btn btn-sm btn-light" onclick="agregarCampo('estilos')"><i class="fas fa-plus"></i></button>
          </div>
          <div class="card-body" id="container-estilos">
            <?php foreach(($estilos ?? []) as $i => $estilo): ?>
            <div class="input-group mb-2" id="estilos-item-<?= $i ?>">
              <input type="text" class="form-control campo-dinamico" data-grupo="estilos"
                     value="<?= htmlspecialchars($estilo) ?>" placeholder="nombre del estilo" oninput="actualizarPreview()">
              <button type="button" class="btn btn-outline-danger" onclick="eliminarCampo('estilos-item-<?= $i ?>')"><i class="fas fa-trash"></i></button>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Scripts dependencia (scriptsD) -->
        <div class="card mb-3">
          <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <span><i class="fas fa-code me-1"></i> Scripts Dependencia (scriptsD)</span>
            <button type="button" class="btn btn-sm btn-light" onclick="agregarCampo('scriptsD')"><i class="fas fa-plus"></i></button>
          </div>
          <div class="card-body" id="container-scriptsD">
            <?php foreach(($scriptsD ?? []) as $i => $script): ?>
            <div class="input-group mb-2" id="scriptsD-item-<?= $i ?>">
              <input type="text" class="form-control campo-dinamico" data-grupo="scriptsD"
                     value="<?= htmlspecialchars($script) ?>" placeholder="nombre del script" oninput="actualizarPreview()">
              <button type="button" class="btn btn-outline-danger" onclick="eliminarCampo('scriptsD-item-<?= $i ?>')"><i class="fas fa-trash"></i></button>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Scripts (scripts) -->
        <div class="card mb-3">
          <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
            <span><i class="fas fa-js me-1"></i> Scripts (scripts)</span>
            <button type="button" class="btn btn-sm btn-light" onclick="agregarCampo('scripts')"><i class="fas fa-plus"></i></button>
          </div>
          <div class="card-body" id="container-scripts">
            <?php foreach(($scripts ?? []) as $i => $script): ?>
            <div class="input-group mb-2" id="scripts-item-<?= $i ?>">
              <input type="text" class="form-control campo-dinamico" data-grupo="scripts"
                     value="<?= htmlspecialchars($script) ?>" placeholder="nombre del script" oninput="actualizarPreview()">
              <button type="button" class="btn btn-outline-danger" onclick="eliminarCampo('scripts-item-<?= $i ?>')"><i class="fas fa-trash"></i></button>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Contenidos (componentes) -->
        <div class="card mb-3">
          <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-puzzle-piece me-1"></i> Contenidos (Componentes)</span>
            <button type="button" class="btn btn-sm btn-light" onclick="agregarComponente()"><i class="fas fa-plus"></i> Agregar componente</button>
          </div>
          <div class="card-body" id="container-contenidos">
            <?php foreach($contenidos as $i => $contenido): ?>
            <div class="card mb-2 componente-item" id="contenido-item-<?= $i ?>">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="badge bg-success">Componente #<?= $i + 1 ?></span>
                  <div>
                    <?php if($i > 0): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="moverComponente(<?= $i ?>, -1)" title="Mover arriba"><i class="fas fa-arrow-up"></i></button>
                    <?php endif; ?>
                    <?php if($i < count($contenidos) - 1): ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="moverComponente(<?= $i ?>, 1)" title="Mover abajo"><i class="fas fa-arrow-down"></i></button>
                    <?php endif; ?>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarComponente('contenido-item-<?= $i ?>')"><i class="fas fa-trash"></i></button>
                  </div>
                </div>
                <div class="mb-2">
                  <label class="form-label fw-bold">Componente</label>
                  <select class="form-select campo-componente" data-index="<?= $i ?>" onchange="actualizarPreview()">
                    <option value="">-- Seleccionar componente --</option>
                    <?php foreach($componentesDisponibles as $grupo => $comps): ?>
                    <optgroup label="<?= htmlspecialchars($grupo) ?>">
                      <?php foreach($comps as $comp): ?>
                      <option value="<?= htmlspecialchars($comp) ?>" <?= ($contenido['componente'] ?? '') === $comp ? 'selected' : '' ?>>
                        <?= htmlspecialchars($comp) ?>
                      </option>
                      <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="mb-2">
                  <label class="form-label fw-bold">Configuración (JSON)</label>
                  <textarea class="form-control campo-configuracion" data-index="<?= $i ?>" rows="2"
                            placeholder='{"op": "valor"}' oninput="actualizarPreview()"><?= htmlspecialchars(json_encode($contenido['configuracion'] ?? new stdClass(), JSON_PRETTY_PRINT)) ?></textarea>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Botones de acción -->
        <div class="d-flex gap-2 mb-4">
          <button type="button" class="btn btn-primary btn-lg" onclick="guardarPagina()">
            <i class="fas fa-save me-1"></i> Guardar Página
          </button>
          <button type="button" class="btn btn-outline-info" onclick="actualizarPreview()">
            <i class="fas fa-sync me-1"></i> Actualizar Preview
          </button>
        </div>

        <div id="mensaje-guardado" class="alert d-none"></div>
      </form>
    </div>

    <!-- Panel de previsualización JSON -->
    <div class="col-lg-5">
      <div class="card sticky-top" style="top: 20px;">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
          <span><i class="fas fa-code me-1"></i> Previsualización JSON</span>
          <button type="button" class="btn btn-sm btn-outline-light" onclick="copiarJSON()" title="Copiar JSON">
            <i class="fas fa-copy"></i>
          </button>
        </div>
        <div class="card-body p-0">
          <pre id="json-preview" class="p-3 mb-0" style="max-height: 70vh; overflow-y: auto; background: #1e1e1e; color: #d4d4d4; font-size: 0.85rem;"></pre>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Componentes disponibles en JSON para JavaScript -->
<script>
const componentesDisponibles = <?= json_encode($componentesDisponibles ?? []) ?>;
const nombrePagina = <?= json_encode($nombrePagina ?? '') ?>;
let contadorCampos = 100;

function agregarCampo(grupo) {
    const container = document.getElementById('container-' + grupo);
    const id = grupo + '-item-nuevo-' + (contadorCampos++);
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.id = id;
    div.innerHTML = `
        <input type="text" class="form-control campo-dinamico" data-grupo="${grupo}"
               placeholder="nombre del recurso" oninput="actualizarPreview()">
        <button type="button" class="btn btn-outline-danger" onclick="eliminarCampo('${id}')"><i class="fas fa-trash"></i></button>
    `;
    container.appendChild(div);
    actualizarPreview();
}

function eliminarCampo(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
    actualizarPreview();
}

function agregarComponente() {
    const container = document.getElementById('container-contenidos');
    const index = container.querySelectorAll('.componente-item').length;
    const id = 'contenido-item-nuevo-' + (contadorCampos++);

    let optionsHtml = '<option value="">-- Seleccionar componente --</option>';
    for (const [grupo, comps] of Object.entries(componentesDisponibles)) {
        optionsHtml += `<optgroup label="${grupo}">`;
        for (const comp of comps) {
            optionsHtml += `<option value="${comp}">${comp}</option>`;
        }
        optionsHtml += '</optgroup>';
    }

    const div = document.createElement('div');
    div.className = 'card mb-2 componente-item';
    div.id = id;
    div.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge bg-success">Componente #${index + 1}</span>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="eliminarComponente('${id}')"><i class="fas fa-trash"></i></button>
            </div>
            <div class="mb-2">
                <label class="form-label fw-bold">Componente</label>
                <select class="form-select campo-componente" onchange="actualizarPreview()">
                    ${optionsHtml}
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label fw-bold">Configuración (JSON)</label>
                <textarea class="form-control campo-configuracion" rows="2"
                          placeholder='{"op": "valor"}' oninput="actualizarPreview()">{}</textarea>
            </div>
        </div>
    `;
    container.appendChild(div);
    actualizarPreview();
}

function eliminarComponente(id) {
    const el = document.getElementById(id);
    if (el) el.remove();
    renumerarComponentes();
    actualizarPreview();
}

function moverComponente(index, direction) {
    const container = document.getElementById('container-contenidos');
    const items = Array.from(container.querySelectorAll('.componente-item'));
    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= items.length) return;

    if (direction === 1) {
        container.insertBefore(items[newIndex], items[index]);
    } else {
        container.insertBefore(items[index], items[newIndex]);
    }
    renumerarComponentes();
    actualizarPreview();
}

function renumerarComponentes() {
    const items = document.querySelectorAll('#container-contenidos .componente-item');
    items.forEach((item, i) => {
        const badge = item.querySelector('.badge');
        if (badge) badge.textContent = 'Componente #' + (i + 1);
    });
}

function obtenerValoresCampo(grupo) {
    const inputs = document.querySelectorAll(`#container-${grupo} .campo-dinamico`);
    const valores = [];
    inputs.forEach(input => {
        const val = input.value.trim();
        if (val) valores.push(val);
    });
    return valores;
}

function obtenerContenidos() {
    const items = document.querySelectorAll('#container-contenidos .componente-item');
    const contenidos = [];
    items.forEach(item => {
        const select = item.querySelector('.campo-componente');
        const textarea = item.querySelector('.campo-configuracion');
        const componente = select ? select.value : '';
        let configuracion = {};
        try {
            configuracion = JSON.parse(textarea.value || '{}');
        } catch(e) {
            configuracion = {};
        }
        if (componente) {
            contenidos.push({ componente, configuracion });
        }
    });
    return contenidos;
}

function construirJSON() {
    const config = {
        tituloPagina: document.getElementById('tituloPagina').value || ''
    };

    const estilos = obtenerValoresCampo('estilos');
    const estilosD = obtenerValoresCampo('estilosD');
    const scriptsD = obtenerValoresCampo('scriptsD');
    const scripts = obtenerValoresCampo('scripts');

    if (estilos.length) config.estilos = estilos;
    if (estilosD.length) config.estilosD = estilosD;
    if (scriptsD.length) config.scriptsD = scriptsD;
    if (scripts.length) config.scripts = scripts;

    config.contenidos = obtenerContenidos();
    return config;
}

function actualizarPreview() {
    const config = construirJSON();
    const preview = document.getElementById('json-preview');
    preview.textContent = JSON.stringify(config, null, 4);
}

function copiarJSON() {
    const config = construirJSON();
    const texto = JSON.stringify(config, null, 4);
    navigator.clipboard.writeText(texto).then(() => {
        mostrarMensaje('JSON copiado al portapapeles', 'success');
    }).catch(() => {
        const ta = document.createElement('textarea');
        ta.value = texto;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        mostrarMensaje('JSON copiado al portapapeles', 'success');
    });
}

function guardarPagina() {
    const config = construirJSON();
    const contenidos = config.contenidos || [];
    if (!config.tituloPagina) {
        mostrarMensaje('El título de la página es requerido', 'danger');
        return;
    }
    if (contenidos.length === 0) {
        mostrarMensaje('Debe agregar al menos un componente', 'warning');
        return;
    }

    fetch('/admin/paginas/' + encodeURIComponent(nombrePagina) + '/guardar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'config=' + encodeURIComponent(JSON.stringify(config))
    }).then(response => {
        if (response.ok || response.redirected) {
            mostrarMensaje('Página guardada correctamente', 'success');
        } else {
            return response.text().then(t => { throw new Error(t); });
        }
    }).catch(err => {
        mostrarMensaje('Error al guardar: ' + err.message, 'danger');
    });
}

function mostrarMensaje(texto, tipo) {
    const el = document.getElementById('mensaje-guardado');
    el.className = 'alert alert-' + tipo;
    el.textContent = texto;
    el.classList.remove('d-none');
    setTimeout(() => el.classList.add('d-none'), 4000);
}

document.addEventListener('DOMContentLoaded', actualizarPreview);
</script>
