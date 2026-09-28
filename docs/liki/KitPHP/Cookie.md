

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






