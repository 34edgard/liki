<?php
namespace Liki\Database;
class File
{
    /**
     * Busca nombres de ruta que coincidan con un patrón.
     *
     * @param string $pattern El patrón de búsqueda (ej. "carpetas/*.txt").
     * @param int $flags Modificadores opcionales (actualmente emula el comportamiento básico).
     * @return array|false Un array con los archivos/carpetas que coinciden, o false en caso de error.
     */
    public static function glob($pattern, $flags = 0) {
        // Separar directorio y patrón del nombre
        $dir = dirname($pattern);
        $filePattern = basename($pattern);
    
        // Si el directorio no existe, devolvemos según la bandera
        if (!is_dir($dir)) {
            if ($flags & GLOB_NOCHECK) {
                return [$pattern];
            }
            return false;
        }
    
        // Manejar GLOB_BRACE {a,b,c}
        if (($flags & GLOB_BRACE) && strpos($filePattern, '{') !== false) {
            $matches = [];
            $alternatives = globExpandBraces($filePattern);
            foreach ($alternatives as $alt) {
                $sub = glob(rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $alt, $flags & ~GLOB_BRACE);
                if (is_array($sub)) {
                    $matches = array_merge($matches, $sub);
                }
            }
            $matches = array_values(array_unique($matches));
            if (!($flags & GLOB_NOSORT)) {
                sort($matches);
            }
            return $matches;
        }
    
        // Abrir el directorio
        $handle = @opendir($dir);
        if ($handle === false) {
            if ($flags & GLOB_NOCHECK) {
                return [$pattern];
            }
            return false;
        }
    
        // Convertir el patrón glob a expresión regular
        $regex = globToRegex($filePattern);
    
        $matches = [];
        while (($entry = readdir($handle)) !== false) {
            // Ignorar . y .. excepto si el patrón los incluye explícitamente
            if (($entry === '.' || $entry === '..') && strpos($filePattern, '.') === false) {
                continue;
            }
    
            if (!preg_match($regex, $entry)) {
                continue;
            }
    
            // Ruta completa
            $fullPath = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $entry;
            if ($dir === '.' && $filePattern === $pattern) {
                // Mantener comportamiento de glob(): no añadir "./" si no se pidió
                $fullPath = $entry;
            }
    
            // Filtrar solo directorios si se pidió
            if (($flags & GLOB_ONLYDIR) && !is_dir($fullPath)) {
                continue;
            }
    
            // Añadir separador final a directorios si GLOB_MARK
            if (($flags & GLOB_MARK) && is_dir($fullPath) && substr($fullPath, -1) !== DIRECTORY_SEPARATOR) {
                $fullPath .= DIRECTORY_SEPARATOR;
            }
    
            $matches[] = $fullPath;
        }
        closedir($handle);
    
        // Si no hay coincidencias
        if (empty($matches)) {
            if ($flags & GLOB_NOCHECK) {
                return [$pattern];
            }
            return [];
        }
    
        // Ordenar (por defecto)
        if (!($flags & GLOB_NOSORT)) {
            sort($matches);
        }
    
        return $matches;
    }
    
    
}



if (!function_exists('glob')) {
    /**
     * Implementación personalizada de glob() usando opendir/readdir
     *
     * @param string $pattern  Patrón con comodines (*, ?, [abc], [!abc])
     * @param int    $flags    Banderas: GLOB_BRACE, GLOB_ONLYDIR, GLOB_MARK,
     *                         GLOB_NOSORT, GLOB_NOCHECK
     * @return array|false     Array de coincidencias o false en error
     */
    function glob($pattern, $flags = 0) {
        // Separar directorio y patrón del nombre
        $dir = dirname($pattern);
        $filePattern = basename($pattern);

        // Si el directorio no existe, devolvemos según la bandera
        if (!is_dir($dir)) {
            if ($flags & GLOB_NOCHECK) {
                return [$pattern];
            }
            return false;
        }

        // Manejar GLOB_BRACE {a,b,c}
        if (($flags & GLOB_BRACE) && strpos($filePattern, '{') !== false) {
            $matches = [];
            $alternatives = globExpandBraces($filePattern);
            foreach ($alternatives as $alt) {
                $sub = glob(rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $alt, $flags & ~GLOB_BRACE);
                if (is_array($sub)) {
                    $matches = array_merge($matches, $sub);
                }
            }
            $matches = array_values(array_unique($matches));
            if (!($flags & GLOB_NOSORT)) {
                sort($matches);
            }
            return $matches;
        }

        // Abrir el directorio
        $handle = @opendir($dir);
        if ($handle === false) {
            if ($flags & GLOB_NOCHECK) {
                return [$pattern];
            }
            return false;
        }

        // Convertir el patrón glob a expresión regular
        $regex = globToRegex($filePattern);

        $matches = [];
        while (($entry = readdir($handle)) !== false) {
            // Ignorar . y .. excepto si el patrón los incluye explícitamente
            if (($entry === '.' || $entry === '..') && strpos($filePattern, '.') === false) {
                continue;
            }

            if (!preg_match($regex, $entry)) {
                continue;
            }

            // Ruta completa
            $fullPath = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $entry;
            if ($dir === '.' && $filePattern === $pattern) {
                // Mantener comportamiento de glob(): no añadir "./" si no se pidió
                $fullPath = $entry;
            }

            // Filtrar solo directorios si se pidió
            if (($flags & GLOB_ONLYDIR) && !is_dir($fullPath)) {
                continue;
            }

            // Añadir separador final a directorios si GLOB_MARK
            if (($flags & GLOB_MARK) && is_dir($fullPath) && substr($fullPath, -1) !== DIRECTORY_SEPARATOR) {
                $fullPath .= DIRECTORY_SEPARATOR;
            }

            $matches[] = $fullPath;
        }
        closedir($handle);

        // Si no hay coincidencias
        if (empty($matches)) {
            if ($flags & GLOB_NOCHECK) {
                return [$pattern];
            }
            return [];
        }

        // Ordenar (por defecto)
        if (!($flags & GLOB_NOSORT)) {
            sort($matches);
        }

        return $matches;
    }
}

if (!function_exists('globToRegex')) {
    /**
     * Convierte un patrón glob (sin braces) a regex PCRE
     */
    function globToRegex($pattern) {
        $regex = '';
        $len = strlen($pattern);
        for ($i = 0; $i < $len; $i++) {
            $c = $pattern[$i];
            switch ($c) {
                case '*':
                    $regex .= '.*';
                    break;
                case '?':
                    $regex .= '.';
                    break;
                case '[':
                    // Buscar el cierre
                    $j = $i + 1;
                    $negate = false;
                    if ($j < $len && ($pattern[$j] === '!' || $pattern[$j] === '^')) {
                        $negate = true;
                        $j++;
                    }
                    $class = '';
                    $closed = false;
                    while ($j < $len) {
                        if ($pattern[$j] === ']' && $class !== '') {
                            $closed = true;
                            break;
                        }
                        $class .= $pattern[$j];
                        $j++;
                    }
                    if ($closed) {
                        $regex .= '[' . ($negate ? '^' : '') . $class . ']';
                        $i = $j;
                    } else {
                        // '[' literal
                        $regex .= '\[';
                    }
                    break;
                case '\\':
                    // Escape literal
                    if ($i + 1 < $len) {
                        $regex .= preg_quote($pattern[++$i], '/');
                    } else {
                        $regex .= '\\\\';
                    }
                    break;
                default:
                    $regex .= preg_quote($c, '/');
                    break;
            }
        }
        return '/^' . $regex . '$/s';
    }
}

if (!function_exists('globExpandBraces')) {
    /**
     * Expande un patrón con {a,b,c} a un array de alternativas
     */
    function globExpandBraces($pattern) {
        $start = strpos($pattern, '{');
        if ($start === false) {
            return [$pattern];
        }

        // Encontrar el cierre correspondiente (soporta anidamiento)
        $depth = 0;
        $end = -1;
        for ($i = $start; $i < strlen($pattern); $i++) {
            if ($pattern[$i] === '{') $depth++;
            elseif ($pattern[$i] === '}') {
                $depth--;
                if ($depth === 0) {
                    $end = $i;
                    break;
                }
            }
        }
        if ($end === -1) {
            return [$pattern];
        }

        $prefix = substr($pattern, 0, $start);
        $inside = substr($pattern, $start + 1, $end - $start - 1);
        $suffix = substr($pattern, $end + 1);

        // Separar alternativas respetando anidamiento
        $parts = [];
        $depth = 0;
        $current = '';
        for ($i = 0; $i < strlen($inside); $i++) {
            $c = $inside[$i];
            if ($c === '{') { $depth++; $current .= $c; }
            elseif ($c === '}') { $depth--; $current .= $c; }
            elseif ($c === ',' && $depth === 0) { $parts[] = $current; $current = ''; }
            else { $current .= $c; }
        }
        $parts[] = $current;

        $result = [];
        foreach ($parts as $part) {
            foreach (globExpandBraces($prefix . $part . $suffix) as $expanded) {
                $result[] = $expanded;
            }
        }
        return $result;
    }
}

if (!defined('GLOB_BRACE'))    define('GLOB_BRACE', 1024);
if (!defined('GLOB_ONLYDIR'))  define('GLOB_ONLYDIR', 8192);
if (!defined('GLOB_MARK'))     define('GLOB_MARK', 2);
if (!defined('GLOB_NOSORT'))   define('GLOB_NOSORT', 4);
if (!defined('GLOB_NOCHECK'))  define('GLOB_NOCHECK', 16);
if (!defined('GLOB_ERR'))      define('GLOB_ERR', 1);