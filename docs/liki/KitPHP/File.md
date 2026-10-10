# Diseño y corrección de la clase `Files`

La clase está vacía, así que aquí no hay "arreglar" sino **diseñar desde cero** una API fluida para manejar archivos, con soporte para un método que reciba un callback para procesar líneas, contenido, etc.

## Consideraciones de diseño

1. **Constructor estático `of(string $path)`** — para iniciar la cadena.
2. **Separar métodos de lectura (consulta) de los de escritura (mutación)**.
3. **Un método clave que pediste**: `eachLine(callable $callback)` — procesa línea por línea sin cargar todo en memoria (usa `SplFileObject` o `fgets`).
4. **`tap()` o `pipe()`** para el callback general que menciones ("recibe un callback para hacer cosas").
5. **Validaciones**: comprobar existencia, permisos, tamaño máximo, etc.
6. **No usar `file_get_contents` para archivos grandes sin control**.

## Ejemplos de uso

### 1. Uso básico

```php
use Liki\KitPHP\Files;

$file = Files::of('/tmp/datos.txt');

if ($file->exists()) {
    echo $file->size() . " bytes\n";
    echo $file->get();
}

Files::of('/tmp/log.txt')
    ->put("Línea inicial\n")
    ->append("Segunda línea\n")
    ->append("Tercera línea\n");
```

### 2. `eachLine` — el método que pediste (procesar líneas con callback)

```php
// Contar líneas que contienen "ERROR"
$count = Files::of('/var/log/app.log')->eachLine(function (string $line, int $index) {
    if (str_contains($line, 'ERROR')) {
        // hacer algo
    }
    return true; // seguir
});

echo "Procesadas: $count líneas";
```

```php
// Detener el procesamiento tras la primera coincidencia
Files::of('/var/log/app.log')->eachLine(function ($line, $i) {
    if (str_contains($line, 'CRITICAL')) {
        echo "Primer CRITICAL en la línea $i: $line\n";
        return false; // detiene la iteración
    }
    return true;
});
```

### 3. `transformLines` — editar todas las líneas

```php
// Convertir todas las líneas a mayúsculas
Files::of('/tmp/entrada.txt')
    ->transformLines(fn($line) => strtoupper($line));
```

### 4. `filterLines` — conservar solo ciertas líneas

```php
// Eliminar líneas vacías o comentarios
Files::of('/tmp/config.txt')->filterLines(
    fn($line) => trim($line) !== '' && !str_starts_with(trim($line), '#')
);
```

### 5. `pipe` — procesar el contenido completo

```php
// Modo lectura: obtiene resultado sin escribir
$wordCount = Files::of('/tmp/novela.txt')->pipe(function (string $content) {
    return str_word_count($content);
});
echo "Palabras: $wordCount\n";

// Modo escritura: reemplaza y guarda
Files::of('/tmp/plantilla.txt')->pipe(
    fn($c) => str_replace('{{nombre}}', 'Ana', $c),
    writeBack: true
);
```

### 6. `grep` — buscar líneas por patrón

```php
foreach (Files::of('/tmp/app.log')->grep('/\d{4}-\d{2}-\d{2}/') as $m) {
    echo "Línea {$m['line']}: {$m['content']}\n";
}
```

### 7. Archivos grandes con `SplFileObject`

```php
Files::of('/var/log/huge.log')->eachLineSpl(function ($line, $n) {
    // Aquí puedes escribir a otro archivo, agregar a DB, etc.
});
```

## Comparativa de los métodos con callback

| Método | Recibe | Devuelve del callback | Uso típico |
|---|---|---|---|
| `eachLine` | `$line, $index` | `true` continuar / `false` detener | Leer sin cargar todo |
| `eachLineSpl` | `$line, $n, $file` | `true` / `false` | Archivos enormes |
| `transformLines` | `$line, $index` | nueva línea (`string`) | Editar archivo |
| `filterLines` | `$line, $index` | `bool` | Reducir líneas |
| `pipe` | `$content, $path` | `string` u otra cosa | Procesar todo el contenido |

## Reglas de diseño aplicadas

| Categoría | Mutan | Devuelven |
|---|---|---|
| Info | No | `bool`/`int`/`string` |
| Lectura | No | `string`/`array` |
| Escritura | Sí (disco) | `self` (encadenable) |
| Callbacks | Configurable | `int`/`self`/`mixed` |
| Búsqueda | No | `bool`/`array` |

### Decisiones clave

1. **`of()` estático** — punto de entrada obligatorio en todas las clases de la librería.
2. **Separación lectura/escritura** — `get()`/`lines()` no mutan; `put()`/`append()` sí.
3. **`eachLine()` eficiente en memoria** — usa `fgets()` en bucle, no carga todo el archivo.
4. **El callback puede detener** devolviendo `false` (patrón común en iteradores).
5. **`transformLines()` y `filterLines()`** son azúcar sobre `eachLine` para editar archivos.
6. **`pipe()`** permite procesar el contenido completo y elegir si se reescribe o solo se devuelve el resultado.
7. **Excepciones explícitas** (`RuntimeException`, `InvalidArgumentException`) — fallar rápido y claro.
8. **`__toString()`** devuelve la ruta, útil para `echo $file;`.
9. **Sin dependencias externas** — solo funciones nativas de PHP.

