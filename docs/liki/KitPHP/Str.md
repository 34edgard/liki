
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


