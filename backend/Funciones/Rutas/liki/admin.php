<?php
use Liki\Routing\Ruta;
use Liki\Plantillas\Flow;
use Liki\Config\ConfigManager;

/**
 * Escanea frontend/Html/ y retorna los componentes agrupados por directorio.
 */
function escanearComponentesDisponibles(): array {
    $baseDir = './frontend/Html/';
    $componentes = [];

    if (!is_dir($baseDir)) return $componentes;

    $items = scandir($baseDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $fullPath = $baseDir . $item;

        if (is_dir($fullPath)) {
            $grupo = $item;
            if ($grupo === 'admin' || $grupo === 'errores' || $grupo === 'Reportes') continue;

            $archivos = scandir($fullPath);
            $listaComps = [];
            foreach ($archivos as $archivo) {
                if ($archivo === '.' || $archivo === '..') continue;
                if (pathinfo($archivo, PATHINFO_EXTENSION) === 'php') {
                    $listaComps[] = $grupo . '/' . basename($archivo, '.php');
                }
            }
            if (!empty($listaComps)) {
                sort($listaComps);
                $componentes[$grupo] = $listaComps;
            }
        } elseif (pathinfo($item, PATHINFO_EXTENSION) === 'php') {
            $componentes['raíz'][] = basename($item, '.php');
        }
    }

    if (isset($componentes['raíz'])) {
        sort($componentes['raíz']);
    }

    return $componentes;
}

/**
 * Obtiene lista de páginas con metadatos básicos.
 */
function obtenerListaPaginas(): array {
    $dir = './frontend/Config/Paginas/';
    $paginas = [];

    if (!is_dir($dir)) return $paginas;

    $archivos = scandir($dir);
    foreach ($archivos as $archivo) {
        if ($archivo === '.' || $archivo === '..') continue;
        if (pathinfo($archivo, PATHINFO_EXTENSION) !== 'json') continue;

        $nombre = basename($archivo, '.json');
        $ruta = $dir . $archivo;
        $contenido = json_decode(file_get_contents($ruta), true);

        $paginas[] = [
            'nombre' => $nombre,
            'titulo' => $contenido['tituloPagina'] ?? '',
            'numComponentes' => count($contenido['contenidos'] ?? []),
            'numEstilos' => count($contenido['estilos'] ?? []) + count($contenido['estilosD'] ?? []),
            'numScripts' => count($contenido['scripts'] ?? []) + count($contenido['scriptsD'] ?? []),
        ];
    }

    usort($paginas, fn($a, $b) => strcasecmp($a['nombre'], $b['nombre']));
    return $paginas;
}

return function(){

    // Lista de páginas disponibles
    Ruta::get('/admin/paginas', function() {
        $success = isset($_GET['success']) && $_GET['success'] === '1';
        $paginas = obtenerListaPaginas();

        Flow::html('admin/lista-paginas', [
            'paginas' => $paginas,
            'success' => $success,
        ]);
    });

    // Editor de página individual
    Ruta::get('/admin/paginas/{nombre}', function($p) {
        $nombrePagina = $p[0];

        $configPath = './frontend/Config/Paginas/' . $nombrePagina . '.json';
        if (!file_exists($configPath)) {
            $config = [
                'tituloPagina' => $nombrePagina,
                'estilos' => ['bootstrap.min', 'estilos'],
                'estilosD' => [],
                'scriptsD' => ['htmx'],
                'scripts' => ['color-modes', 'bootstrap.bundle.min'],
                'contenidos' => [],
            ];
        } else {
            $config = ConfigManager::cargarConfig($nombrePagina);
        }

        $componentesDisponibles = escanearComponentesDisponibles();

        Flow::html('admin/editor-pagina', [
            'nombrePagina' => $nombrePagina,
            'config' => $config,
            'componentesDisponibles' => $componentesDisponibles,
        ]);
    });

    // Guardar página
    Ruta::post('/admin/paginas/{nombre}/guardar', function($p) {
        $nombrePagina = $p[0];

        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $nombrePagina)) {
            http_response_code(400);
            echo 'Nombre de página inválido';
            return;
        }

        $config = json_decode($_POST['config'] ?? '{}', true);

        if ($config === null) {
            http_response_code(400);
            echo 'JSON inválido';
            return;
        }

        ConfigManager::guardarConfig($nombrePagina, $config);
        header('Location: /admin/paginas?success=1');
    });

    // API: retornar componentes disponibles como JSON
    Ruta::get('/admin/componentes-disponibles', function() {
        $componentes = escanearComponentesDisponibles();
        Flow::json($componentes);
    });

    // Árbol de archivos de páginas (vista tree mejorada)
    Ruta::get('/pages', function() {
        $directory = './frontend/Config/Paginas/';

        if (!file_exists($directory)) {
            echo '<div class="alert alert-danger">El directorio de páginas no existe.</div>';
            return;
        }

        echo '<div class="container py-4">';
        echo '<div class="d-flex justify-content-between align-items-center mb-3">';
        echo '<h3><i class="fas fa-sitemap me-2"></i>Árbol de Páginas</h3>';
        echo '<a hx-get="/admin/paginas" hx-target="# " class="btn btn-primary btn-sm"><i class="fas fa-list me-1"></i> Vista lista</a>';
        echo '</div>';
        echo '<div class="card"><div class="card-body" style="font-family: monospace; white-space: pre;" id="pages">';
        echo htmlspecialchars(basename($directory)) . "<br />";

        generateTree($directory);

        echo '</div></div></div>';
    });
};

function generateTree($path, $prefix = "", $depth = 0, $maxDepth = -1) {
    if ($maxDepth !== -1 && $depth > $maxDepth) return;
    if (!is_dir($path)) return;

    $items = scandir($path);
    $items = array_diff($items, ['.', '..']);
    sort($items);

    $total = count($items);
    $current = 0;

    foreach ($items as $item) {
        $current++;
        $isLast = ($current === $total);
        $connector = $isLast ? "└── " : "├── ";

        $nombre = basename($item, '.json');
        echo $prefix . $connector;
        echo "<a hx-target='#pages' hx-get='/admin/paginas/" . htmlspecialchars($nombre) . "' class='btn btn-sm btn-outline-primary mb-1'>";
        echo htmlspecialchars($item) . "</a>";

        $fullPath = $path . DIRECTORY_SEPARATOR . $item;

        if (is_dir($fullPath)) {
            echo "<br />";
            $newPrefix = $prefix . ($isLast ? "    " : "│   ");
            generateTree($fullPath, $newPrefix, $depth + 1, $maxDepth);
        } else {
            echo "<br />";
        }
    }
}
