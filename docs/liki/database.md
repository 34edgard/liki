# Capa de Base de Datos

Esta página ofrece una visión general de la capa de abstracción de base de datos en Liki, desde la conexión PDO sin procesar hasta la interfaz de consulta de alto nivel `FlowDB`.

## Arquitectura de la Base de Datos

La capa de base de datos está organizada en tres niveles de abstracción, lo que facilita tanto las operaciones comunes como las complejas.

1.  **Capa de Conexión (`ConsultasBD`):** Es el nivel más bajo. Envuelve la conexión PDO y gestiona la ejecución directa de consultas SQL.
2.  **Capa de Constructores SQL (`Registrar`, `Consultar`, `Editar`, `Eliminar`):** Cada clase es responsable de generar una sentencia SQL parametrizada (`SELECT`, `INSERT`, `UPDATE`, `DELETE`).
3.  **Capa de Interfaz de Consulta (`FlowDB`):** Es el nivel más alto y la forma recomendada de interactuar con la base de datos. Proporciona una API fluida y encadenable que utiliza las otras capas internamente.

## `FlowDB`: La Interfaz Fluida para Consultas

`FlowDB` permite construir y ejecutar consultas de una manera muy legible y sencilla, sin necesidad de escribir SQL directamente.

El flujo de trabajo general es:

1.  **Iniciar** la consulta con `FlowDB::conf('NombreDelModelo')`.
2.  **Construir** la consulta añadiendo condiciones con métodos encadenados (`->campos()`, `->join()`, etc.).
3.  **Ejecutar** la consulta con un método terminal como `->get()`, `->post()`, `->put()` o `->delete()`.

### Iniciar una Consulta

Siempre se empieza especificando el modelo con el que se va a trabajar. Liki usará el nombre del modelo para determinar la tabla de la base de datos y los campos válidos.

```php
FlowDB::conf('Usuario'); // Inicia una consulta para el modelo 'Usuario'
```

### Métodos Principales

-   `->campos([...])`: Especifica qué columnas seleccionar en una consulta `SELECT`.
-   `->valores([...])`: Especifica los datos a insertar o actualizar en consultas `INSERT` o `UPDATE`.
-   `->get([...])`: Ejecuta una consulta `SELECT`. El array asociativo que se le pasa actúa como la cláusula `WHERE`.
-   `->post([...])`: Ejecuta una consulta `INSERT`. El array contiene los datos a insertar.
-   `->put([...])`: Ejecuta una consulta `UPDATE`. El array asociativo que se le pasa actúa como la cláusula `WHERE`.
-   `->delete([...])`: Ejecuta una consulta `DELETE`. El array asociativo que se le pasa actúa como la cláusula `WHERE`.

### Ejemplos Completos

#### Consultar (SELECT)

Obtener el nombre y el correo electrónico de un usuario con una ID específica.

```php
$usuario = FlowDB::conf('Usuario')
    ->campos(['nombre', 'email'])
    ->get(['id_usuario' => 10]);
```

#### Insertar (INSERT)

Crear un nuevo registro en la tabla de usuarios.

```php
FlowDB::conf('Usuario')->post([
    'nombre' => 'Nuevo Usuario',
    'email' => 'nuevo@example.com',
    'estado' => 1
]);
```

#### Actualizar (UPDATE)

Actualizar el nombre de un usuario basándose en su ID.

```php
FlowDB::conf('Usuario')
    ->valores(['nombre' => 'Nombre Actualizado'])
    ->put(['id_usuario' => 10]);
```

#### Eliminar (DELETE)

Eliminar un usuario basándose en su ID.

```php
FlowDB::conf('Usuario')->delete(['id_usuario' => 10]);
```

## `ConsultasBD`: La Capa de Conexión

Para usuarios avanzados o situaciones que requieran SQL personalizado, la clase `ConsultasBD` ofrece un acceso más directo. `FlowDB` la utiliza internamente.

-   **`crearConexion()`**: Crea la conexión PDO utilizando las constantes `DSN`, `usuario_BD`, y `contraceña_BD` de tu archivo `conf.php`.
-   **`consultarRegistro($sql, $params)`**: Ejecuta una consulta `SELECT` y devuelve un array de resultados asociativos.
-   **`ejecutarConsulta($sql, $params)`**: Ejecuta una consulta `INSERT`, `UPDATE` o `DELETE` y devuelve el número de filas afectadas.

Todos los errores de consulta se gestionan a través de la clase `ErrorHandler`.
