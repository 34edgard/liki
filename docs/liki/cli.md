# Consola de Comandos (CLI)

Esta página documenta `consol.php`, el punto de entrada de la línea de comandos para el framework Liki, cubriendo el análisis de argumentos, el despacho de comandos y el sistema de registro de plugins.

## Propósito

`consol.php` proporciona un ejecutor de CLI unificado que delega comandos a un registro de plugins. Esto permite extender fácilmente las capacidades del framework con nuevas herramientas de desarrollo, como generadores de código o gestores de tareas.

## Uso

Todos los comandos se ejecutan a través del script `consol.php` desde la raíz de tu proyecto. La estructura del comando es la siguiente:

```bash
php consol.php tipo:accion [argumento_principal] [argumento_extra_1] ...
```

-   **`tipo:accion`**: El comando a ejecutar, compuesto por el nombre del plugin (`tipo`) y la función a llamar (`accion`).
-   **`[argumento_principal]`**: Un argumento opcional que se pasa como primer parámetro a la función del comando.
-   **`[argumentos_extras]`**: Cualquier argumento adicional se agrupa en un array y se pasa como segundo parámetro.

Si ejecutas el script sin un comando, se mostrará una lista de todos los comandos disponibles.

```bash
php consol.php

# Salida de ejemplo:
# Uso: php consol.php [comando] [nombre] [extras]
# comandos:
#
#  -gn:modelo
#  -gn:controlador
#  -db:migracion-run
#  -db:import
#  -db:export
```

## Arquitectura de Plugins

El sistema de CLI es modular. Los comandos se agrupan en "plugins" que se registran en un archivo central.

1.  **Registro de Plugins (`backend/Funciones/Tools/Terminal.php`):** Este archivo actúa como un registro central. Devuelve un array asociativo donde cada clave es el `tipo` (nombre del plugin) y su valor es la carga de otro archivo que contiene las acciones de ese plugin.

    ```php
    // backend/Funciones/Tools/Terminal.php
    return [
       "gn"=> DelegateFunction::loadData('Tools/Plugins/gn'),
       "db"=> DelegateFunction::loadData('Tools/Plugins/db')
    ];
    ```

2.  **Archivo de Plugin (`backend/Funciones/Tools/Plugins/`):** Cada archivo de plugin (ej. `db.php`) devuelve un array asociativo donde la clave es el nombre de la `accion` y el valor es una función `callable` (una función anónima o una referencia a un método estático).

    ```php
    // backend/Funciones/Tools/Plugins/db.php
    use Liki\Database\MigrationRunner;
    use Liki\Consola\db as ConsolaDB;

    return [
        'migracion-run' => [MigrationRunner::class, 'run'],
        'import' => [ConsolaDB::class, 'import'],
        'export' => [ConsolaDB::class, 'exportDatabase']
    ];
    ```

## Comandos Incluidos

Liki viene con un conjunto de comandos útiles para tareas comunes.

### Comandos de Base de Datos (`db`)

Estos comandos te ayudan a gestionar el esquema y los datos de tu base de datos.

-   **`php consol.php db:migracion-run`**
    Ejecuta todas las migraciones de base de datos pendientes que aún no se han aplicado. Es la forma estándar de actualizar el esquema de tu base de datos. (Ver [Migraciones de Base de Datos](./migrations.md) para más detalles).

-   **`php consol.php db:import`**
    Importa un volcado de la base de datos.

-   **`php consol.php db:export`**
    Exporta el estado actual de la base de datos a un archivo.

### Comandos de Generación de Código (`gn`)

Estos comandos (definidos en el plugin `gn`) se utilizan para generar archivos boilerplate (modelos, controladores, etc.), acelerando el proceso de desarrollo. *La documentación detallada para este plugin se puede añadir por separado.*

## Cómo Añadir un Nuevo Comando

Extender la CLI es sencillo:

1.  **Crea un archivo de plugin:** Añade un nuevo archivo PHP en `backend/Funciones/Tools/Plugins/`, por ejemplo, `miplugin.php`.

2.  **Define tus acciones:** En `miplugin.php`, devuelve un array con las acciones y sus funciones correspondientes.

    ```php
    // backend/Funciones/Tools/Plugins/miplugin.php
    return [
        'hola' => function($nombre = 'mundo') {
            echo "¡Hola, $nombre!\n";
        }
    ];
    ```

3.  **Registra tu plugin:** Abre `backend/Funciones/Tools/Terminal.php` y añade tu nuevo plugin al array.

    ```php
    return [
       "gn"=> DelegateFunction::loadData('Tools/Plugins/gn'),
       "db"=> DelegateFunction::loadData('Tools/Plugins/db'),
       "miplugin"=> DelegateFunction::loadData('Tools/Plugins/miplugin') // <-- Añade esta línea
    ];
    ```

¡Y ya está! Ahora puedes ejecutar tu nuevo comando desde la terminal:

```bash
php consol.php miplugin:hola Liki
# Salida: ¡Hola, Liki!
```
