

## Ejemplos de uso

```php
// Conversión a entero
Caster::toInt("123");        // 123
Caster::toInt("  45  ");     // 45
Caster::toInt(true);         // 1
Caster::toInt(3.99);         // 3
Caster::toInt(null);         // 0

// Conversión a float
Caster::toFloat("3.14");     // 3.14
Caster::toFloat("3,14");     // 3.14 (soporta coma)
Caster::toFloat("abc");      // 0.0

// Conversión a string
Caster::toString(123);       // "123"
Caster::toString(true);      // "true"
Caster::toString([1, 2, 3]); // "1,2,3"
Caster::toString(null);      // ""

// Conversión a booleano
Caster::toBool("true");      // true
Caster::toBool("false");     // false
Caster::toBool("no");        // false
Caster::toBool("0");         // false
Caster::toBool([]);          // false
Caster::toBool([1]);         // true

// Conversión a array
Caster::toArray("a,b,c");           // ['a', 'b', 'c']
Caster::toArray('{"a":1,"b":2}');   // ['a' => 1, 'b' => 2]
Caster::toArray(42);                // [42]

// Conversión a objeto
Caster::toObject(['a' => 1]);       // stdClass con propiedad a

// Conversión genérica por nombre
Caster::cast("42", "int");          // 42
Caster::cast("3.14", "float");      // 3.14
Caster::cast("true", "bool");       // true

// Conversión de arrays completos
Caster::castArray(["1", "2", "3"], "int");  // [1, 2, 3]
```

## Características destacadas

- ✅ **Métodos estáticos** — no necesitas instanciar la clase
- ✅ **`declare(strict_types=1)`** — tipado estricto en las firmas
- ✅ **Manejo de errores** — lanza `InvalidArgumentException` en casos inválidos
- ✅ **Soporte para coma decimal** en floats (`"3,14"` → `3.14`)
- ✅ **Parseo de JSON** automático en `toArray()`
- ✅ **Conversión inteligente de strings** a booleanos (`"no"`, `"off"`, `"false"` → `false`)
- ✅ **Método `cast()`** — despacho dinámico por nombre de tipo
- ✅ **Método `castArray()`** — convierte arrays completos de una vez

