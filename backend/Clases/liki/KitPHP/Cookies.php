<?php

namespace Liki\KitPHP;

use RuntimeException;
use InvalidArgumentException;
use Stringable;

class Cookie implements Stringable
{
    private string $name;
    private string $value = '';
    private int $expires = 0;
    private string $path = '/';
    private string $domain = '';
    private bool $secure = false;
    private bool $httpOnly = true;
    private string $sameSite = 'Lax'; // Lax | Strict | None

    /** Clave secreta para firmar/cifrar. Configúrala una vez. */
    private static ?string $secretKey = null;
    private static bool $encrypt = false;

    public function __construct(string $name)
    {
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $name)) {
            throw new InvalidArgumentException("Nombre de cookie inválido: {$name}");
        }
        $this->name = $name;
    }

    /* ---------- Configuración global ---------- */

    /**
     * Define la clave secreta usada para firmar/cifrar cookies.
     * Debe llamarse al inicio de la app (ej. en el bootstrap).
     */
    public static function setSecretKey(string $key, bool $encrypt = false): void
    {
        self::$secretKey = $key;
        self::$encrypt = $encrypt;
    }

    /* ---------- Constructor estático ---------- */

    /**
     * Crea una instancia para una cookie concreta.
     */
    public static function of(string $name): self
    {
        return new self($name);
    }

    /* ---------- Configuración fluida ---------- */

    public function value(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function expires(int $seconds): self
    {
        $this->expires = $seconds === 0 ? 0 : time() + $seconds;
        return $this;
    }

    public function expiresAt(int $timestamp): self
    {
        $this->expires = $timestamp;
        return $this;
    }

    public function path(string $path): self
    {
        $this->path = $path;
        return $this;
    }

    public function domain(string $domain): self
    {
        $this->domain = $domain;
        return $this;
    }

    public function secure(bool $secure = true): self
    {
        $this->secure = $secure;
        return $this;
    }

    public function httpOnly(bool $httpOnly = true): self
    {
        $this->httpOnly = $httpOnly;
        return $this;
    }

    public function sameSite(string $sameSite): self
    {
        $allowed = ['Lax', 'Strict', 'None'];
        if (!in_array($sameSite, $allowed, true)) {
            throw new InvalidArgumentException("SameSite inválido: {$sameSite}");
        }
        $this->sameSite = $sameSite;
        return $this;
    }

    /* ---------- Escritura / borrado ---------- */

    /**
     * Envía la cookie al navegador. Devuelve $this para encadenar.
     */
    public function set(): self
    {
        $value = $this->prepareValueForWrite($this->value);

        $options = [
            'expires'  => $this->expires,
            'path'     => $this->path,
            'domain'   => $this->domain,
            'secure'   => $this->secure,
            'httponly' => $this->httpOnly,
            'samesite' => $this->sameSite,
        ];

        if (PHP_VERSION_ID >= 70300) {
            setcookie($this->name, $value, $options);
        } else {
            // Compatibilidad con PHP < 7.3 (sameSite vía hack del path)
            setcookie(
                $this->name,
                $value,
                $this->expires,
                $this->path . '; samesite=' . $this->sameSite,
                $this->domain,
                $this->secure,
                $this->httpOnly
            );
        }

        // Refleja en $_COOKIE para que la lectura funcione en la misma request
        $_COOKIE[$this->name] = $value;

        return $this;
    }

    /**
     * Elimina la cookie (valor vacío + expiración en el pasado).
     */
    public function delete(): self
    {
        unset($_COOKIE[$this->name]);

        if (PHP_VERSION_ID >= 70300) {
            setcookie($this->name, '', [
                'expires'  => time() - 3600,
                'path'     => $this->path,
                'domain'   => $this->domain,
                'secure'   => $this->secure,
                'httponly' => $this->httpOnly,
                'samesite' => $this->sameSite,
            ]);
        } else {
            setcookie(
                $this->name,
                '',
                time() - 3600,
                $this->path,
                $this->domain,
                $this->secure,
                $this->httpOnly
            );
        }

        return $this;
    }

    /* ---------- Lectura ---------- */

    public function exists(): bool
    {
        return isset($_COOKIE[$this->name]);
    }

    public function get(string $default = ''): string
    {
        if (!$this->exists()) {
            return $default;
        }
        return $this->extractValueFromRead((string) $_COOKIE[$this->name]);
    }

    public function getInt(int $default = 0): int
    {
        $v = $this->get((string) $default);
        return is_numeric($v) ? (int) $v : $default;
    }

    public function getFloat(float $default = 0.0): float
    {
        $v = $this->get((string) $default);
        return is_numeric($v) ? (float) $v : $default;
    }

    public function getBool(bool $default = false): bool
    {
        if (!$this->exists()) {
            return $default;
        }
        return filter_var($this->get(), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    /**
     * @return array<mixed>
     */
    public function getJson(array $default = []): array
    {
        $raw = $this->get('');
        if ($raw === '') {
            return $default;
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : $default;
    }

    /* ---------- Callback (estilo pipe) ---------- */

    /**
     * Procesa el valor de la cookie con un callback.
     * Si $writeBack es true y el callback devuelve un string, se reescribe la cookie.
     */
    public function pipe(callable $callback, bool $writeBack = false): mixed
    {
        $value = $this->get();
        $result = $callback($value, $this->name);

        if ($writeBack && is_string($result)) {
            return $this->value($result)->set();
        }
        return $result;
    }

    /* ---------- Internos: firma y cifrado ---------- */

    /**
     * Prepara el valor para escritura: firma + cifrado (si aplica).
     */
    private function prepareValueForWrite(string $value): string
    {
        if (self::$secretKey !== null) {
            if (self::$encrypt) {
                $value = $this->encryptValue($value, self::$secretKey);
            }
            $value .= '|' . $this->sign($value, self::$secretKey);
        }
        return $value;
    }

    /**
     * Extrae el valor de una cookie leída: verifica firma y descifra (si aplica).
     * Devuelve '' si la firma no es válida.
     */
    private function extractValueFromRead(string $raw): string
    {
        if (self::$secretKey === null) {
            return $raw;
        }

        if (!str_contains($raw, '|')) {
            return ''; // Cookie sin firma cuando se esperaba una firmada
        }

        [$payload, $signature] = explode('|', $raw, 2);

        if (!hash_equals($this->sign($payload, self::$secretKey), $signature)) {
            return ''; // Firma inválida → cookie manipulada
        }

        if (self::$encrypt) {
            return $this->decryptValue($payload, self::$secretKey);
        }

        return $payload;
    }

    private function sign(string $data, string $key): string
    {
        return hash_hmac('sha256', $data, $key);
    }

    private function encryptValue(string $plain, string $key): string
    {
        $cipher = 'aes-256-cbc';
        $ivLen = openssl_cipher_iv_length($cipher);
        $iv = random_bytes($ivLen);
        $encrypted = openssl_encrypt($plain, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        if ($encrypted === false) {
            throw new RuntimeException('Error al cifrar la cookie.');
        }
        return base64_encode($iv . $encrypted);
    }

    private function decryptValue(string $encoded, string $key): string
    {
        $cipher = 'aes-256-cbc';
        $data = base64_decode($encoded, true);
        if ($data === false) {
            return '';
        }
        $ivLen = openssl_cipher_iv_length($cipher);
        $iv = substr($data, 0, $ivLen);
        $cipherText = substr($data, $ivLen);
        $plain = openssl_decrypt($cipherText, $cipher, $key, OPENSSL_RAW_DATA, $iv);
        return $plain === false ? '' : $plain;
    }

    /* ---------- Salidas ---------- */

    public function name(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->get();
    }

    /* ---------- Utilidades estáticas ---------- */

    /**
     * Devuelve todas las cookies actuales (solo las enviadas por el cliente).
     * @return array<string, string>
     */
    public static function all(): array
    {
        return $_COOKIE;
    }

    public static function has(string $name): bool
    {
        return isset($_COOKIE[$name]);
    }

    public static function forget(string $name, string $path = '/', string $domain = ''): void
    {
        (new self($name))->path($path)->domain($domain)->delete();
    }
}




/*
 
use Liki\KitPHP\Cookie;

// Firma HMAC sin cifrado
Cookie::setSecretKey('clave-super-secreta-de-32-bytes-o-más');

// Firma + cifrado AES-256-CBC
Cookie::setSecretKey('clave-super-secreta-de-32-bytes-o-más', encrypt: true);



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


// Contar caracteres del valor
$len = Cookie::of('usuario')->pipe(fn($v) => strlen($v));

// Incrementar contador y reescribir
Cookie::of('visitas')->pipe(
    fn($v) => (string)(((int) $v) + 1),
    writeBack: true
)->set();




// Sobre la instancia
Cookie::of('usuario')->delete();

// Estático
Cookie::forget('usuario');



echo Cookie::of('contador')
->expires(3600)
->pipe(fn($v) => (string)(((int) $v) + 1), writeBack: true)
->get(); // muestra el nuevo valor






 */