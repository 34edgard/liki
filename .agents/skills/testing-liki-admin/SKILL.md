---
name: testing-liki-admin
description: Test the Liki framework admin page builder UI and API routes end-to-end. Use when verifying changes to editor-pagina.php, lista-paginas.php, or admin.php routes.
---

## Prerequisites

- PHP 8.x installed
- Repository cloned at the standard repo path

## Setup

1. **Create conf.php** — The repo ships `conf.php.template` but `conf.php` is gitignored. Copy it:
   ```bash
   cp conf.php.template conf.php
   ```

2. **Start the PHP dev server** from the repo root (relative paths in the framework require CWD to be repo root):
   ```bash
   cd /path/to/liki
   php -S 0.0.0.0:8000 index.php &
   ```
   Do NOT use the `-t` flag — it breaks `include "./conf.php"` resolution.

3. **Verify routes** before browser testing:
   ```bash
   curl -s http://localhost:8000/admin/paginas | head -5
   curl -s http://localhost:8000/admin/paginas/Index | head -5
   curl -s http://localhost:8000/admin/componentes-disponibles | head -5
   ```

## Key Routes

| Route | Method | Purpose |
|-------|--------|---------|
| `/admin/paginas` | GET | List all pages with metadata |
| `/admin/paginas/{nombre}` | GET | Editor for a specific page |
| `/admin/paginas/{nombre}/guardar` | POST | Save page JSON (body: `config=<json>`) |
| `/admin/componentes-disponibles` | GET | JSON API of available components |
| `/pages` | GET | Tree view of page files |

## Test Scenarios

1. **Page list** — Verify `/admin/paginas` shows all JSON files from `frontend/Config/Paginas/` with correct component/style/script counts
2. **Editor load** — Verify `/admin/paginas/Index` populates form fields from `Index.json` (title, estilos, scriptsD, contenidos)
3. **Dynamic fields** — Add/remove estilos/scripts fields and verify JSON preview updates in real-time
4. **Save** — Modify fields, click save, verify success message and check file on disk
5. **New page** — Create via modal on `/admin/paginas`, verify redirect to editor and file creation

## Common Pitfalls

### Ruta Framework Parameter Validation
The `Ruta` routing framework's `validar_parametros()` counts dynamic URL params (from `{nombre}`) together with data params. If a route defines `parametros_esperados`, the count check might fail because the dynamic URL param gets merged into `$all_params`. Workaround: don't use `parametros_esperados` on routes with dynamic URL segments — validate manually in the handler instead.

### HTML Fragments Without Bootstrap
Admin routes render HTML fragments via the Flow template engine without the full page wrapper (`estructura/pagina.php`). This means Bootstrap CSS/JS might not be loaded. The UI will lack styling but JavaScript functionality works. To test with full styling, you would need to load the page through the normal Liki rendering pipeline.

### JSON Config Structure
Page configs in `frontend/Config/Paginas/*.json` follow this schema:
```json
{
  "tituloPagina": "string",
  "estilos": ["string"],
  "estilosD": ["string"],
  "scripts": ["string"],
  "scriptsD": ["string"],
  "contenidos": [
    {
      "componente": "path/to/component",
      "configuracion": {"key": "value"}
    }
  ]
}
```

### Component Scanning
The `escanearComponentesDisponibles()` function scans `frontend/Html/` recursively. Directories `admin/`, `errores/`, and `Reportes/` are excluded. Components are grouped by directory (componentes, estructura, usuario, sesiones, raíz).

## Devin Secrets Needed

No secrets required — the admin interface has no authentication.
