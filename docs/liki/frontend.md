# Arquitectura Frontend

Esta página ofrece una visión general de la arquitectura frontend de Liki: las convenciones utilizadas para definir páginas, el sistema de configuración basado en JSON, la plantilla de página compartida y el modelo de componentes que ensambla la salida HTML final.

## Estructura de Directorios

El frontend se divide en tres directorios principales, cada uno con un rol distinto:

-   `frontend/Paginas/`: Puntos de entrada de la página. Son archivos PHP delgados que cargan la configuración y desencadenan el renderizado.
-   `frontend/Config/Paginas/`: Archivos JSON que definen el título, los assets (CSS/JS) y la lista de componentes de cada página.
-   `frontend/Html/`: Contiene todos los componentes y plantillas parciales de PHP reutilizables que son renderizados por el motor de plantillas `Flow`.

La plantilla principal que sirve como esqueleto para todas las páginas se encuentra en `frontend/Html/estructura/pagina.php`.

## Flujo de Renderizado de una Página

El proceso para mostrar una página al usuario sigue un flujo de datos claro:

1.  **Punto de Entrada (`frontend/Paginas/`):** Una ruta dirige la solicitud a un archivo en este directorio (por ejemplo, `Gestion_Sesion.php`).
2.  **Carga de Configuración:** Este archivo utiliza `ConfigManager::cargarConfig('NombreDeConfig')` para cargar el archivo JSON correspondiente desde `frontend/Config/Paginas/`.
3.  **Renderizado de Plantilla:** Luego, se invoca a `Flow::html('estructura/pagina', $config)`, pasando los datos del JSON a la plantilla principal para que esta construya la página.

```php
// Ejemplo: frontend/Paginas/Gestion_Sesion.php

// Carga el archivo frontend/Config/Paginas/Gestion_Sesion.json
$config = ConfigManager::cargarConfig('Gestion_Sesion');

// Renderiza la plantilla principal con esa configuración
Flow::html('estructura/pagina', $config);
```

## Configuración de Páginas (Archivos JSON)

La estructura y contenido de cada página se declaran en un archivo JSON en `frontend/Config/Paginas/`. Esto permite definir la composición de una página sin escribir lógica de PHP.

**Ejemplo: `Gestion_Sesion.json`**

```json
{
    "tituloPagina": "APP Liki",
    "estilos": ["bootstrap.min", "estilos"],
    "scriptsD": ["LoadComponets", "htmx", "repuestas"],
    "scripts": ["color-modes", "script", "bootstrap.bundle.min"],
    "contenidos": [
        {"componente": "estructura/Header", "configuracion": {"op": "APP Liki"}},
        {"componente": "sesiones/Inicio_sesion", "configuracion": {"op": "APP Liki"}}
    ]
}
```

**Campos Principales:**

-   `tituloPagina` (string): El valor para la etiqueta `<title>` de la página.
-   `estilos` (array): Nombres de archivos CSS que se cargarán desde el directorio `frontend/css/`.
-   `scripts` (array): Nombres de archivos JS que se cargarán desde el directorio `frontend/js/`.
-   `estilosD` / `scriptsD` (array): Assets dinámicos que se sirven a través de rutas especiales, no como archivos estáticos.
-   `contenidos` (array): Una lista ordenada de los componentes que se renderizarán en el `<body>` de la página.

## El Modelo de Componentes

El corazón del renderizado son los componentes. La clave `contenidos` en el JSON define qué componentes se mostrarán y en qué orden.

Cada objeto en el array `contenidos` tiene dos claves:

1.  `componente`: Un string que representa la ruta a un archivo PHP dentro de `frontend/Html/` (sin la extensión `.php`).
2.  `configuracion`: Un objeto JSON libre que se convierte en una variable PHP (`$configuracion`) disponible dentro del archivo del componente. Esto permite pasar datos a los componentes.

**Ejemplo de resolución de componente:**

Una entrada `{"componente": "sesiones/Inicio_sesion", ...}` en el JSON hará que el sistema renderice el archivo `frontend/Html/sesiones/Inicio_sesion.php`.

Los componentes son parciales de PHP simples: no devuelven ningún valor, simplemente imprimen HTML directamente en la salida.

## La Plantilla Maestra (`pagina.php`)

El archivo `frontend/Html/estructura/pagina.php` es el esqueleto HTML compartido por todas las páginas. Sus responsabilidades clave son:

-   Generar el `<head>`, incluyendo el título y los enlaces a los CSS (`estilos`, `estilosD`).
-   Iterar sobre la lista `contenidos` del JSON y renderizar cada `componente` en orden dentro del `<body>` usando `Flow::html()`.
-   Incluir los scripts JS (`scripts`, `scriptsD`) al final del `<body>`.

## Integración con HTMX

El frontend de Liki está diseñado para ser dinámico usando [HTMX](https://htmx.org/) en lugar de un framework de JavaScript pesado. La librería de HTMX se carga a través de los `scriptsD` y se habilitan extensiones importantes (como `response-targets`) en la etiqueta `<body>` de `pagina.php`, dejándolo disponible para todos los componentes.
