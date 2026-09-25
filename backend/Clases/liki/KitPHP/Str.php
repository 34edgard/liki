<?php

namespace Liki\KitPHP;

use Stringable;

class Str implements Stringable
{
    private string $value;

    public function __construct(string $value = '')
    {
        $this->value = $value;
    }

    /* ---------- Constructores ---------- */

    public static function of(string|Stringable $value): self
    {
        return new self((string) $value);
    }

    /* ---------- Transformaciones (mutan y devuelven $this) ---------- */

    public function trim(string $chars = " \t\n\r\0\x0B"): self
    {
        $this->value = trim($this->value, $chars);
        return $this;
    }

    public function ltrim(string $chars = " \t\n\r\0\x0B"): self
    {
        $this->value = ltrim($this->value, $chars);
        return $this;
    }

    public function rtrim(string $chars = " \t\n\r\0\x0B"): self
    {
        $this->value = rtrim($this->value, $chars);
        return $this;
    }

    public function replace(string|array $search, string|array $replace): self
    {
        $this->value = str_replace($search, $replace, $this->value);
        return $this;
    }

    public function upper(): self
    {
        $this->value = mb_strtoupper($this->value, 'UTF-8');
        return $this;
    }

    public function lower(): self
    {
        $this->value = mb_strtolower($this->value, 'UTF-8');
        return $this;
    }

    public function ucfirst(): self
    {
        $this->value = mb_strtoupper(mb_substr($this->value, 0, 1, 'UTF-8'), 'UTF-8')
            . mb_substr($this->value, 1, null, 'UTF-8');
        return $this;
    }

    public function slug(string $separator = '-'): self
    {
        // Transliterar acentos a ASCII
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $this->value) ?: $this->value;
        $value = strtolower($value);
        $value = preg_replace('/[^a-z0-9]+/', $separator, $value);
        $value = trim($value, $separator);
        $this->value = $value;
        return $this;
    }

    public function toCamel(): self
    {
        $value = str_replace(['-', '_'], ' ', $this->value);
        $value = ucwords(strtolower($value));
        $value = str_replace(' ', '', $value);
        $this->value = lcfirst($value);
        return $this;
    }

    public function toSnake(): self
    {
        $value = preg_replace('/([a-z])([A-Z])/', '$1_$2', $this->value);
        $value = str_replace(['-', ' '], '_', $value);
        $this->value = strtolower($value);
        return $this;
    }

    public function toKebab(): self
    {
        return $this->toSnake()->replace('_', '-');
    }

    public function padLeft(int $length, string $pad = ' '): self
    {
        $this->value = str_pad($this->value, $length, $pad, STR_PAD_LEFT);
        return $this;
    }

    public function padRight(int $length, string $pad = ' '): self
    {
        $this->value = str_pad($this->value, $length, $pad, STR_PAD_RIGHT);
        return $this;
    }

    public function substr(int $start, ?int $length = null): self
    {
        $this->value = mb_substr($this->value, $start, $length, 'UTF-8');
        return $this;
    }

    public function limit(int $length, string $end = '...'): self
    {
        if (mb_strlen($this->value, 'UTF-8') > $length) {
            $this->value = mb_substr($this->value, 0, $length, 'UTF-8') . $end;
        }
        return $this;
    }

    public function repeat(int $times): self
    {
        $this->value = str_repeat($this->value, $times);
        return $this;
    }

    public function reverse(): self
    {
        $this->value = implode('', array_reverse(
            mb_str_split($this->value, 1, 'UTF-8')
        ));
        return $this;
    }

    /* ---------- Consultas (devuelven bool / array / int) ---------- */

    public function contains(string $needle): bool
    {
        return $needle === '' || str_contains($this->value, $needle);
    }

    public function startsWith(string $needle): bool
    {
        return str_starts_with($this->value, $needle);
    }

    public function endsWith(string $needle): bool
    {
        return str_ends_with($this->value, $needle);
    }

    public function equals(string $other): bool
    {
        return $this->value === $other;
    }

    public function isEmpty(): bool
    {
        return $this->value === '';
    }

    public function len(): int
    {
        return mb_strlen($this->value, 'UTF-8');
    }

    public function split(string $separator = ''): array
    {
        if ($separator === '') {
            return mb_str_split($this->value, 1, 'UTF-8');
        }
        return explode($separator, $this->value);
    }

    /* ---------- Salidas ---------- */

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}


/*

$slug = Str::of(" Hola MUuuuUUUndo ")
    ->trim()
    ->lower()
    ->slug('-')
    ->toString();

echo $slug; // "hola-muuuuuuundo"

// Consultas
Str::of('Hola Mundo')->contains('Mundo');    // true
Str::of('Hola Mundo')->startsWith('Hola');   // true
Str::of('Hola Mundo')->endsWith('Mundo');    // true

// Conversiones
echo Str::of('hola_mundo_test')->toCamel();  // "holaMundoTest"
echo Str::of('holaMundoTest')->toSnake();    // "hola_mundo_test"
echo Str::of('Mi Título Ñoño')->slug();      // "mi-titulo-nono"


*/