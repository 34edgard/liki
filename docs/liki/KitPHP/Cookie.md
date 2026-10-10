# Diseño de una clase `Cookie` para manejo de cookies

Siguiendo el mismo estilo de tu librería `Liki\KitPHP`, aquí tienes una clase fluida para manejar cookies, con soporte para:
- Crear/leer/modificar/eliminar cookies
- Firma (HMAC) para integridad
- Cifrado opcional
- SameSite, HttpOnly, Secure, Path, Domain
- Lectura desde `$_COOKIE` y escritura vía `setcookie()`
- Métodos de consulta y callback


## Ejemplos de uso

### 1. Bootstrap (una sola vez en tu app)

```php
use Liki\KitPHP\Cookie;

// Firma HMAC sin cifrado
Cookie::setSecretKey('clave-super-secreta-de-32-bytes-o-más');

// Firma + cifrado AES-256-CBC
Cookie::setSecretKey('clave-super-secreta-de-32-bytes-o-más', encrypt: true);
```

### 2. Escribir cookies (API fluida)

```php
// Cookie de sesión simple, expira en 1 hora
Cookie::of('usuario')
    ->value('ana')
    ->expires(3600)
    ->path('/')
    ->secure(true)
    ->httpOnly(true)
    ->sameSite('Strict')
    ->set();

// Cookie persistente 30 días
Cookie::of('preferencias')
    ->value(json_encode(['tema' => 'oscuro', 'lang' => 'es']))
    ->expires(60 * 60 * 24 * 30)
    ->set();

// Cookie con dominio específico
Cookie::of('tracking')
    ->value('abc123')
    ->domain('.midominio.com')
    ->expires(60 * 60 * 24 * 365)
    ->sameSite('Lax')
    ->set();
```

### 3. Leer cookies

```php
// Lectura simple
$user = Cookie::of('usuario')->get('invitado');

// Tipado
$visitas  = Cookie::of('visitas')->getInt(0);
$precio   = Cookie::of('precio')->getFloat(0.0);
$aceptado = Cookie::of('acepto')->getBool();
$prefs    = Cookie::of('preferencias')->getJson();

// Verificar existencia
if (Cookie::of('sesion')->exists()) {
    // ...
}

// Directo desde $_COOKIE
if (Cookie::has('carrito')) {
    $carrito = Cookie::all()['carrito'];
}
```

### 4. Callback `pipe()`

```php
// Contar caracteres del valor
$len = Cookie::of('usuario')->pipe(fn($v) => strlen($v));

// Incrementar contador y reescribir
Cookie::of('visitas')->pipe(
    fn($v) => (string)(((int) $v) + 1),
    writeBack: true
)->set();
```

### 5. Eliminar

```php
// Sobre la instancia
Cookie::of('usuario')->delete();

// Estático
Cookie::forget('usuario');
```

### 6. Encadenar todo

```php
echo Cookie::of('contador')
    ->expires(3600)
    ->pipe(fn($v) => (string)(((int) $v) + 1), writeBack: true)
    ->get(); // muestra el nuevo valor
```

## Cómo funciona la firma y el cifrado

Cuando llamas a `setSecretKey()`:

| Modo | Valor guardado en la cookie |
|---|---|
| Sin secret | `valor-plano` |
| Firma HMAC | `valor-plano\|firma_sha256` |
| Firma + cifrado | `base64(iv+aes(valor))\|firma_sha256` |

Al leer:

1. Se extrae el payload y la firma.
2. Se recalcula HMAC con la clave y se compara con `hash_equals()` (tiempo constante).
3. Si la firma no coincide → se devuelve `''` (cookie manipulada, rechazada).
4. Si hay cifrado → se descifra con AES-256-CBC y se devuelve el valor original.

Esto protege contra:
- **Manipulación del valor** (firma HMAC).
- **Lectura del contenido** si el usuario inspecciona la cookie (cifrado opcional).

## Reglas de diseño aplicadas

| Tipo | Devuelve | Ejemplos |
|---|---|---|
| Configuración | `self` (encadenable) | `value`, `expires`, `path`, `secure` |
| Escritura | `self` | `set`, `delete` |
| Lectura | `string`/`int`/`bool`/`array` | `get`, `getInt`, `getJson` |
| Callback | `mixed` | `pipe` |
| Estáticos | varios | `of`, `has`, `all`, `forget`, `setSecretKey` |

### Decisiones clave

1. **`of()` estático** — mismo punto de entrada que las otras clases de la librería.
2. **Configuración fluida** — cada opción es un método que devuelve `$this`.
3. **Valores tipados** — `getInt`, `getFloat`, `getBool`, `getJson` para evitar casts manuales.
4. **Firma HMAC-SHA256 con `hash_equals`** — verificación en tiempo constante contra timing attacks.
5. **Cifrado opcional AES-256-CBC** — se activa con `setSecretKey(..., encrypt: true)`.
6. **Compatibilidad PHP < 7.3** — usa el hack del path para `SameSite` (aunque PHP 7.3+ ya es estándar).
7. **Refleja en `$_COOKIE`** al escribir, para que la lectura funcione en la misma request.
8. **`pipe()`** — el mismo patrón que usamos en `Files`, para procesar el valor con un callback.
9. **`delete()` sincroniza `$_COOKIE`** para que desaparezca inmediatamente.
10. **Sin dependencias externas** — solo `openssl` y `hash_hmac` nativos.

