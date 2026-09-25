<?php

namespace Liki\KitPHP;

use RuntimeException;
use InvalidArgumentException;
use SplFileObject;
use Stringable;

class Files implements Stringable
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /* ---------- Constructor estático ---------- */

    public static function of(string $path): self
    {
        return new self($path);
    }

    /* ---------- Información del archivo (consultas) ---------- */

    public function exists(): bool
    {
        return file_exists($this->path);
    }

    public function isFile(): bool
    {
        return is_file($this->path);
    }

    public function isDir(): bool
    {
        return is_dir($this->path);
    }

    public function isReadable(): bool
    {
        return is_readable($this->path);
    }

    public function isWritable(): bool
    {
        return is_writable($this->path);
    }

    public function size(): int
    {
        $size = filesize($this->path);
        return $size === false ? 0 : $size;
    }

    public function extension(): string
    {
        return pathinfo($this->path, PATHINFO_EXTENSION);
    }

    public function filename(): string
    {
        return pathinfo($this->path, PATHINFO_FILENAME);
    }

    public function dirname(): string
    {
        return pathinfo($this->path, PATHINFO_DIRNAME);
    }

    public function mimeType(): string
    {
        if (!$this->exists()) {
            return '';
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($this->path) ?: '';
    }

    public function lastModified(): int
    {
        $t = filemtime($this->path);
        return $t === false ? 0 : $t;
    }

    public function path(): string
    {
        return $this->path;
    }

    /* ---------- Lectura ---------- */

    /**
     * Lee todo el contenido. Lanza excepción si no existe o no es legible.
     */
    public function get(): string
    {
        if (!$this->exists()) {
            throw new RuntimeException("El archivo no existe: {$this->path}");
        }
        if (!$this->isReadable()) {
            throw new RuntimeException("El archivo no es legible: {$this->path}");
        }
        $content = file_get_contents($this->path);
        if ($content === false) {
            throw new RuntimeException("No se pudo leer: {$this->path}");
        }
        return $content;
    }

    /**
     * Lee el contenido como array de líneas (sin saltos de línea).
     */
    public function lines(): array
    {
        if (!$this->exists()) {
            throw new RuntimeException("El archivo no existe: {$this->path}");
        }
        $lines = file($this->path, FILE_IGNORE_NEW_LINES);
        return $lines === false ? [] : $lines;
    }

    /**
     * Devuelve las primeras $n líneas.
     */
    public function head(int $n = 10): array
    {
        return array_slice($this->lines(), 0, $n);
    }

    /**
     * Devuelve las últimas $n líneas.
     */
    public function tail(int $n = 10): array
    {
        return array_slice($this->lines(), -$n);
    }

    /* ---------- Escritura / mutación ---------- */

    public function put(string $content): self
    {
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            throw new RuntimeException("El directorio no existe: {$dir}");
        }
        if (file_put_contents($this->path, $content) === false) {
            throw new RuntimeException("No se pudo escribir: {$this->path}");
        }
        return $this;
    }

    public function append(string $content): self
    {
        if (file_put_contents($this->path, $content, FILE_APPEND) === false) {
            throw new RuntimeException("No se pudo añadir a: {$this->path}");
        }
        return $this;
    }

    public function prepend(string $content): self
    {
        $current = $this->exists() ? $this->get() : '';
        return $this->put($content . $current);
    }

    public function delete(): bool
    {
        if (!$this->exists()) {
            return false;
        }
        return unlink($this->path);
    }

    public function copy(string $destination): self
    {
        if (!copy($this->path, $destination)) {
            throw new RuntimeException("No se pudo copiar a: {$destination}");
        }
        return new self($destination);
    }

    public function move(string $destination): self
    {
        if (!rename($this->path, $destination)) {
            throw new RuntimeException("No se pudo mover a: {$destination}");
        }
        $this->path = $destination;
        return $this;
    }

    public function truncate(): self
    {
        return $this->put('');
    }

    /* ---------- Directorios ---------- */

    public static function makeDir(string $path, int $mode = 0777, bool $recursive = true): bool
    {
        if (is_dir($path)) {
            return true;
        }
        return mkdir($path, $mode, $recursive);
    }

    public function files(): array
    {
        if (!$this->isDir()) {
            return [];
        }
        $items = scandir($this->path);
        if ($items === false) {
            return [];
        }
        return array_values(array_filter(
            $items,
            fn($i) => $i !== '.' && $i !== '..'
        ));
    }

    /* ---------- Callbacks / iteración ---------- */

    /**
     * Procesa cada línea del archivo mediante un callback.
     * El callback recibe ($line, $index). Si devuelve false, se detiene.
     * Si devuelve un string, se usa como reemplazo de la línea (útil para editar).
     *
     * Uso eficiente de memoria: NO carga todo el archivo si es grande.
     *
     * @return int Número de líneas procesadas
     */
    public function eachLine(callable $callback): int
    {
        if (!$this->exists()) {
            throw new RuntimeException("El archivo no existe: {$this->path}");
        }

        $handle = fopen($this->path, 'r');
        if ($handle === false) {
            throw new RuntimeException("No se pudo abrir: {$this->path}");
        }

        $index = 0;
        $processed = 0;

        try {
            while (($line = fgets($handle)) !== false) {
                $trimmed = rtrim($line, "\r\n");
                $result = $callback($trimmed, $index);

                if ($result === false) {
                    break;
                }
                $index++;
                $processed++;
            }
        } finally {
            fclose($handle);
        }

        return $processed;
    }

    /**
     * Transforma el archivo línea por línea y guarda el resultado.
     * El callback recibe ($line, $index) y debe devolver la nueva línea.
     */
    public function transformLines(callable $callback): self
    {
        $lines = $this->lines();
        $out = [];
        foreach ($lines as $i => $line) {
            $out[] = $callback($line, $i);
        }
        return $this->put(implode(PHP_EOL, $out));
    }

    /**
     * Filtra líneas según un callback (true = conservar).
     */
    public function filterLines(callable $callback): self
    {
        $lines = $this->lines();
        $out = [];
        foreach ($lines as $i => $line) {
            if ($callback($line, $i)) {
                $out[] = $line;
            }
        }
        return $this->put(implode(PHP_EOL, $out));
    }

    /**
     * Procesa el contenido completo con un callback.
     * El callback recibe el string y debe devolver el string transformado.
     * Si el callback devuelve algo distinto de string, se usa como valor de retorno
     * sin escribir el archivo (modo lectura).
     *
     * @param callable $callback
     * @param bool $writeBack Si true, escribe el resultado al archivo
     */
    public function pipe(callable $callback, bool $writeBack = false): mixed
    {
        $content = $this->get();
        $result = $callback($content, $this->path);

        if ($writeBack && is_string($result)) {
            $this->put($result);
            return $this;
        }

        return $result;
    }

    /**
     * Itera el archivo con SplFileObject (útil para archivos muy grandes).
     * El callback recibe ($line, $lineNumber, $fileObject). Si devuelve false, se detiene.
     */
    public function eachLineSpl(callable $callback): self
    {
        if (!$this->exists()) {
            throw new RuntimeException("El archivo no existe: {$this->path}");
        }

        $file = new SplFileObject($this->path, 'r');
        $file->setFlags(SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);

        foreach ($file as $lineNumber => $line) {
            $result = $callback(rtrim($line, "\r\n"), $lineNumber, $file);
            if ($result === false) {
                break;
            }
        }
        return $this;
    }

    /* ---------- Búsqueda ---------- */

    public function contains(string $needle): bool
    {
        return $this->exists() && str_contains($this->get(), $needle);
    }

    public function grep(string $pattern): array
    {
        $matches = [];
        foreach ($this->lines() as $i => $line) {
            if (preg_match($pattern, $line)) {
                $matches[] = ['line' => $i + 1, 'content' => $line];
            }
        }
        return $matches;
    }

    /* ---------- Salidas ---------- */

    public function __toString(): string
    {
        return $this->path;
    }
}


/*
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




// Contar líneas que contienen "ERROR"
$count = Files::of('/var/log/app.log')->eachLine(function (string $line, int $index) {
    if (str_contains($line, 'ERROR')) {
        // hacer algo
    }
    return true; // seguir
});

echo "Procesadas: $count líneas";



// Detener el procesamiento tras la primera coincidencia
Files::of('/var/log/app.log')->eachLine(function ($line, $i) {
    if (str_contains($line, 'CRITICAL')) {
        echo "Primer CRITICAL en la línea $i: $line\n";
        return false; // detiene la iteración
    }
    return true;
});


// Convertir todas las líneas a mayúsculas
Files::of('/tmp/entrada.txt')
->transformLines(fn($line) => strtoupper($line));



// Eliminar líneas vacías o comentarios
Files::of('/tmp/config.txt')->filterLines(
    fn($line) => trim($line) !== '' && !str_starts_with(trim($line), '#')
);



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




foreach (Files::of('/tmp/app.log')->grep('/\d{4}-\d{2}-\d{2}/') as $m) {
    echo "Línea {$m['line']}: {$m['content']}\n";
}



Files::of('/var/log/huge.log')->eachLineSpl(function ($line, $n) {
    // Aquí puedes escribir a otro archivo, agregar a DB, etc.
});



*/