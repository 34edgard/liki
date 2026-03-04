# Migraciones de Base de Datos

Esta página cubre el subsistema de migraciones en Liki: la clase `MigrationRunner` que ejecuta y rastrea los archivos de migración, la clase `SchemaBuilder` utilizada dentro de los archivos de migración para generar DDL (Lenguaje de Definición de Datos), y las constantes de configuración que gobiernan su comportamiento.

## ¿Qué es una Migración?

Una migración es un archivo PHP que describe un cambio en la estructura de tu base de datos, como crear una nueva tabla, añadir una columna o modificar una existente. En lugar de aplicar estos cambios manualmente en el gestor de base de datos, los escribes en código. Esto permite que los cambios en el esquema de la base de datos se versionen junto con el resto de tu código, asegurando consistencia entre diferentes entornos de desarrollo y producción.

## Configuración

El sistema de migraciones requiere que se definan las siguientes constantes en tu archivo `conf.php`:

-   `MIGRATION_TABLE`: El nombre de la tabla que Liki usará para llevar un registro de las migraciones que ya se han ejecutado. (Ej: `"migrations"`).
-   `MIGRATION_PATH`: La ruta al directorio donde guardas tus archivos de migración. (Ej: `"./DataBase/migrations/"`).
-   `DB_DRIVER`: El tipo de base de datos que estás utilizando (Ej: `"sqlite"`), para que el `SchemaBuilder` genere SQL compatible.
-   `MIGRATION_AUTO_RUN`: Si se establece en `true`, Liki intentará ejecutar las migraciones pendientes automáticamente en cada carga de la aplicación. **Se recomienda mantenerlo en `false` en producción** y ejecutar las migraciones manualmente.

## Formato de un Archivo de Migración

Cada migración es un archivo PHP en el directorio definido en `MIGRATION_PATH`. El nombre del archivo suele incluir una marca de tiempo para asegurar que se ejecuten en el orden correcto.

El archivo debe devolver un objeto anónimo que contenga dos métodos:

-   `up()`: Contiene el código para **aplicar** el cambio en la base de datos.
-   `down()`: Contiene el código para **revertir** el cambio realizado por `up()`.

**Ejemplo: `2024_01_01_000001_create_estudiante_table.php`**

```php
<?php

return new class {
    public function up(): void {
        $schema = new \Liki\Database\SchemaBuilder();
        // Define la estructura de la nueva tabla 'estudiante'
        $sql = $schema->createTable('estudiante', function($table) {
            $table->id(); // Crea una columna ID autoincremental
            $table->string('nombre');
            $table->string('apellido');
            $table->timestamps(); // Crea las columnas created_at y updated_at
        });

        // Ejecuta el SQL generado
        (new \Liki\Database\ConsultasBD())->ejecutarConsulta($sql);
    }

    public function down(): void {
        $schema = new \Liki\Database\SchemaBuilder();
        // Genera la sentencia para eliminar la tabla
        $sql = $schema->dropTable('estudiante');

        // Ejecuta el SQL
        (new \Liki\Database\ConsultasBD())->ejecutarConsulta($sql);
    }
};

```

## `SchemaBuilder`: El Constructor de Esquemas

En lugar de escribir SQL a mano, Liki proporciona la clase `SchemaBuilder` para generar las sentencias DDL de forma programática y compatible con diferentes motores de bases de datos.

-   `$schema->createTable('nombre', ...)`: Genera una sentencia `CREATE TABLE`.
-   `$schema->dropTable('nombre')`: Genera una sentencia `DROP TABLE`.

Dentro de `createTable`, se utiliza un `TableBuilder` (`$table`) para definir las columnas:

-   `$table->id()`: Columna `id` autoincremental y clave primaria.
-   `$table->string('nombre', longitud)`: Columna `VARCHAR`.
-   `$table->integer('nombre')`: Columna `INTEGER`.
-   `$table->text('nombre')`: Columna `TEXT`.
-   `$table->timestamps()`: Crea las columnas `created_at` y `updated_at`.

## Ejecutar y Revertir Migraciones

Las migraciones se gestionan a través de la clase `MigrationRunner`, generalmente invocada desde la línea de comandos.

-   **Ejecutar Migraciones:** El comando `db:migracion-run` busca todos los archivos en `MIGRATION_PATH` que no se hayan ejecutado (comparando con la tabla `MIGRATION_TABLE`) y ejecuta su método `up()`.

-   **Revertir Migraciones (`Rollback`):** El sistema agrupa las migraciones que se ejecutan juntas en un "lote". El comando de `rollback` revierte el último lote ejecutado, llamando al método `down()` de cada migración en ese lote en orden inverso.

El uso de la línea de comandos para gestionar las migraciones es la práctica recomendada, especialmente en entornos de producción.
