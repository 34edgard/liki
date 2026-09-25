<?php

namespace Liki\KitPHP;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use ArrayIterator;
use Stringable;
use Traversable;

class Arr implements ArrayAccess, Countable, IteratorAggregate, Stringable
{
    /** @var array<mixed> */
    private array $value;

    public function __construct(array $value = [])
    {
        $this->value = $value;
    }

    /* ---------- Constructor estático ---------- */

    public static function of(array $value = []): self
    {
        return new self($value);
    }

    /* ---------- Transformaciones (mutan y devuelven $this) ---------- */

    public function map(callable $callback): self
    {
        $this->value = array_map($callback, $this->value);
        return $this;
    }

    public function filter(?callable $callback = null): self
    {
        $this->value = $callback === null
            ? array_filter($this->value)
            : array_filter($this->value, $callback);
        return $this;
    }

    public function reject(callable $callback): self
    {
        $this->value = array_filter(
            $this->value,
            fn($v, $k) => !$callback($v, $k),
            ARRAY_FILTER_USE_BOTH
        );
        return $this;
    }

    public function reduce(callable $callback, mixed $initial = null): mixed
    {
        return array_reduce($this->value, $callback, $initial);
    }

    public function pluck(string $key): self
    {
        $this->value = array_map(
            fn($item) => is_array($item)
                ? ($item[$key] ?? null)
                : (is_object($item) ? ($item->$key ?? null) : null),
            $this->value
        );
        return $this;
    }

    public function sort(?callable $callback = null): self
    {
        if ($callback === null) {
            sort($this->value);
        } else {
            usort($this->value, $callback);
        }
        return $this;
    }

    public function sortDesc(?callable $callback = null): self
    {
        if ($callback === null) {
            rsort($this->value);
        } else {
            usort($this->value, fn($a, $b) => -$callback($a, $b));
        }
        return $this;
    }

    public function sortKeys(): self
    {
        ksort($this->value);
        return $this;
    }

    public function reverse(): self
    {
        $this->value = array_reverse($this->value);
        return $this;
    }

    public function unique(): self
    {
        $this->value = array_values(array_unique($this->value, SORT_REGULAR));
        return $this;
    }

    public function merge(array ...$arrays): self
    {
        $this->value = array_merge($this->value, ...$arrays);
        return $this;
    }

    public function chunk(int $size, bool $preserveKeys = false): self
    {
        $this->value = array_chunk($this->value, $size, $preserveKeys);
        return $this;
    }

    public function groupBy(callable|string $key): self
    {
        $result = [];
        foreach ($this->value as $item) {
            if (is_string($key)) {
                $groupKey = is_array($item) ? ($item[$key] ?? null) : ($item->$key ?? null);
            } else {
                $groupKey = $key($item);
            }
            $result[$groupKey][] = $item;
        }
        $this->value = $result;
        return $this;
    }

    public function flatten(int $depth = PHP_INT_MAX): self
    {
        $this->value = $this->flattenArray($this->value, $depth);
        return $this;
    }

    private function flattenArray(array $array, int $depth): array
    {
        $result = [];
        foreach ($array as $item) {
            if (is_array($item) && $depth > 0) {
                $result = array_merge($result, $this->flattenArray($item, $depth - 1));
            } else {
                $result[] = $item;
            }
        }
        return $result;
    }

    public function push(mixed ...$values): self
    {
        foreach ($values as $v) {
            $this->value[] = $v;
        }
        return $this;
    }

    public function slice(int $offset, ?int $length = null): self
    {
        $this->value = array_slice($this->value, $offset, $length);
        return $this;
    }

    /* ---------- Consultas (NO mutan, devuelven valor) ---------- */

    public function first(mixed $default = null): mixed
    {
        return $this->value === [] ? $default : reset($this->value);
    }

    public function last(mixed $default = null): mixed
    {
        return $this->value === [] ? $default : end($this->value);
    }

    public function count(): int
    {
        return count($this->value);
    }

    public function isEmpty(): bool
    {
        return $this->value === [];
    }

    public function isNotEmpty(): bool
    {
        return $this->value !== [];
    }

    public function contains(mixed $needle): bool
    {
        return in_array($needle, $this->value, true);
    }

    public function keys(): array
    {
        return array_keys($this->value);
    }

    public function values(): array
    {
        return array_values($this->value);
    }

    public function sum(): int|float
    {
        return array_sum($this->value);
    }

    public function implode(string $separator = ','): string
    {
        return implode($separator, $this->value);
    }

    /* ---------- Salidas ---------- */

    public function toArray(): array
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return json_encode($this->value, JSON_UNESCAPED_UNICODE);
    }

    /* ---------- Interfaces ---------- */

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->value[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->value[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->value[] = $value;
        } else {
            $this->value[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->value[$offset]);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->value);
    }
}


/*

// Map + filter + sort
$result = Arr::of([3, 1, 4, 1, 5, 9, 2, 6])
    ->filter(fn($n) => $n > 2)
    ->map(fn($n) => $n * 10)
    ->sort()
    ->toArray();
// [30, 40, 50, 60, 90]

// Pluck sobre array de arrays
$users = [
    ['id' => 1, 'name' => 'Ana', 'role' => 'admin'],
    ['id' => 2, 'name' => 'Luis', 'role' => 'user'],
    ['id' => 3, 'name' => 'Eva', 'role' => 'admin'],
];
echo Arr::of($users)->pluck('name')->implode(', '); // "Ana, Luis, Eva"

// groupBy
$byRole = Arr::of($users)->groupBy('role')->toArray();
// ['admin' => [...], 'user' => [...]]

// Consultas
$arr = Arr::of([1, 2, 3]);
$arr->first();        // 1
$arr->last();         // 3
$arr->count();        // 3
$arr->contains(2);    // true
$arr->isEmpty();      // false

// Interfaces: count(), foreach, $arr[0], echo
count($arr);          // 3
foreach ($arr as $v) { /.....
 }
echo $arr[0];         // 1
echo $arr;            // "[1,2,3]"

*/