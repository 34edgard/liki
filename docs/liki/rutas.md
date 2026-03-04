# Sistema de Enrutamiento (Routing)

Esta página cubre cómo las solicitudes HTTP se asocian a funciones manejadoras en Liki, incluyendo el patrón front-controller, el registro de rutas, la carga de grupos, el alcance de prefijos y el despacho.

## Visión General

Liki utiliza un patrón de **controlador frontal (front-controller)**: todas las solicitudes HTTP entran a través de `index.php`, que registra las rutas cargando archivos de grupo y luego llama a `Ruta::dispatch()` para encontrar y ejecutar el manejador apropiado.

La clase central es `Liki\Routing\Ruta`, que mantiene un registro estático `$routes`, proporciona métodos de registro (`get`, `post`, etc.) y contiene la lógica de despacho.

### Ciclo de Vida de una Solicitud

1.  Toda solicitud web llega a `index.php`.
2.  `index.php` carga los archivos de configuración y el autoloader.
3.  Se llama a `Ruta::group()` una vez por cada archivo de grupo de rutas para registrar todas las rutas en memoria.
4.  Se llama a `Ruta::dispatch()` para procesar la solicitud actual, encontrar la ruta coincidente y ejecutar su manejador.
5.  Si no se encuentra ninguna ruta, se emite un error 404.

## Registro de Rutas

Las rutas se definen dentro de archivos de grupo en `backend/Funciones/Rutas/`. Cada archivo debe devolver una función anónima que, a su vez, llama a los métodos de registro de la clase `Ruta`.

### Métodos de Registro

La clase `Ruta` proporciona métodos para los verbos HTTP más comunes:

-   `Ruta::get()`
-   `Ruta::post()`
-   `Ruta::put()`
-   `Ruta::delete()`
-   `Ruta::patch()`
-   `Ruta::options()`
-   `Ruta::head()`

### Definir una Ruta Básica

Para registrar una ruta, se utiliza el método correspondiente al verbo HTTP deseado.

**Sintaxis:**

```php
Ruta::get(string $patron_url, callable $manejador, array $parametros_esperados = [], array $funcion_extra = []);
```

**Ejemplo:**

```php
// backend/Funciones/Rutas/app/Paginas.php

return function() {
    // Asocia la URL '/' a una función que renderiza la página 'Gestion_Sesion'
    Ruta::get('/', function() {
        Flow::page('Gestion_Sesion');
    });

    // Asocia la URL '/inicio' a otra función
    Ruta::get('/inicio', function() {
        Flow::page('inicio');
    });
};
```

### Parámetros Dinámicos en Rutas

Puedes definir segmentos dinámicos en tus rutas encerrando un nombre de parámetro entre llaves `{}`. Estos segmentos se capturarán y pasarán al manejador.

**Ejemplo:**

```php
Ruta::get('/usuarios/{id}', function($params) {
    $userId = $params['id'];
    echo "Mostrando el usuario con ID: " . $userId;
});
```

El motor de enrutamiento convierte automáticamente `{param}` en una expresión regular de captura `([a-zA-Z0-9_]+)`.

## Agrupación y Carga de Rutas

Para una mejor organización, las rutas se agrupan en archivos y se pueden anidar bajo prefijos comunes.

### Carga de Archivos de Grupo: `Ruta::group()`

Este método carga un archivo PHP desde el directorio `backend/Funciones/Rutas/` y ejecuta la función anónima que este retorna para registrar sus rutas.

**Ejemplo en `index.php`:**

```php
// Carga las rutas definidas en backend/Funciones/Rutas/app/Paginas.php
Ruta::group('app/Paginas');

// Carga las rutas de sesión
Ruta::group('app/sesiones');
```

Las rutas se evalúan en el orden en que se registran. La primera coincidencia es la que se ejecuta.

### Prefijos de URL: `Ruta::prefix()`

Permite agrupar varias rutas bajo un prefijo de URL compartido sin tener que repetirlo en cada definición.

**Ejemplo de `liki/toolsDep.php`:**

```php
// backend/Funciones/Rutas/liki/toolsDep.php

return function() {
    Ruta::prefix('/testing', function() {
        // Esta ruta se registrará como /testing/rutas
        Ruta::get('/rutas', function() { 
            // ... 
        });

        // Esta ruta se registrará como /testing/rutas/formulario
        Ruta::get('/rutas/formulario', function($p) { 
            // ... 
        }, ['accion', 'ruta_index']);
    });
};
```

## Despacho y Manejo de Parámetros

### Lógica de Despacho: `Ruta::dispatch()`

Una vez que todas las rutas han sido registradas, `Ruta::dispatch()` se encarga de:

1.  Extraer la URI y el método de la solicitud actual.
2.  Leer los datos de entrada (`$_GET`, `$_POST`, o `php://input` para otros verbos).
3.  Iterar sobre las rutas registradas y encontrar la primera que coincida con el método y el patrón de la URL.
4.  Al encontrar una coincidencia:
    a. Fusionar los parámetros dinámicos de la URL con los datos de entrada.
    b. Validar que los `parametros_esperados` (si se definieron) estén presentes. Si no, devuelve un error 400.
    c. Sanitizar todos los parámetros de entrada.
    d. Llamar a la función manejadora (`handler`) pasando los parámetros.
5.  Si ninguna ruta coincide, se delega a `ErrorHandler` para emitir una respuesta 404.

### Sanitización de Entradas

Antes de que los parámetros lleguen al manejador, `sanitizar_entrada()` los procesa según la constante `MODO_SANITIZACION` definida en `conf.php`:

-   `NINGUNO`: Los datos se pasan sin modificar.
-   `MODERADO`: Aplica `htmlspecialchars()`.
-   `ESTRICTO`: Aplica `htmlspecialchars(strip_tags())`.

Esta protección ayuda a prevenir ataques XSS.
