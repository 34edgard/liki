
## Ejemplo de uso (API fluida)

```php
use Liki\KitPHP\Arr;

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
foreach ($arr as $v) { /* ... */ }
echo $arr[0];         // 1
echo $arr;            // "[1,2,3]"
```

## Reglas de diseño aplicadas

| Tipo de método | Devuelve | Ejemplos |
|---|---|---|
| Transformación | `self` (encadenable) | `map`, `filter`, `sort`, `unique` |
| Reducción | valor escalar | `reduce`, `sum`, `implode` |
| Consulta | `bool`/`int`/`array`/`mixed` | `first`, `last`, `count`, `contains` |
| Salida | `array`/`string` | `toArray`, `__toString` |

