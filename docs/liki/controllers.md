# Controladores y Rutas de Aplicación

Esta página describe cómo las rutas específicas de la aplicación (definidas en los grupos de rutas `app/` y `liki/`) se conectan con las clases de controladores y, finalmente, con la lógica de negocio a través del patrón de delegación `Manejador`.

## Arquitectura de Despacho en Tres Niveles

Casi todas las solicitudes a la capa de aplicación siguen un patrón de tres niveles:

1.  **Ruta (`backend/Funciones/Rutas/`):** Un archivo de rutas define la URL, el verbo HTTP y los parámetros esperados. Asocia la ruta a un método estático de un **Controlador**.
2.  **Controlador (`backend/Clases/app/Controladores/`):** El método del controlador es una capa delgada. Su única responsabilidad es llamar a `DelegateFunction::exec()` para pasar el control a un **Manejador**.
3.  **Manejador (`backend/Clases/app/Manejadores/`):** Este archivo contiene la lógica de negocio real. Realiza las consultas a la base de datos, procesa los datos y genera la respuesta final (generalmente HTML).

Este patrón separa claramente la definición de la ruta, la orquestación y la lógica de negocio.

## Grupos de Rutas de la Aplicación

Los archivos de rutas se encuentran en `backend/Funciones/Rutas/`. Los que son específicos de la aplicación están en el subdirectorio `app/`, mientras que los que pertenecen al framework están en `liki/`.

-   `app/sesiones.php`: Endpoints para iniciar y cerrar sesión.
-   `app/Usuario.php`: CRUD para la gestión de usuarios.
-   `app/Paginas.php`: Rutas que renderizan páginas HTML completas.
-   `liki/admin.php`: Rutas para el editor de configuración de páginas.
-   `liki/builders.php`: Rutas dinámicas para servir assets (JS, CSS, etc.).
-   `liki/toolsDep.php`: Herramientas para desarrolladores.

### Rutas de Sesión (`app/sesiones.php`)

Estas rutas manejan la autenticación y son la única excepción al patrón de tres niveles, ya que llaman directamente a la lógica en la clase `Liki\Sesion`.

| Método | Ruta              | Manejador                 | Parámetros Requeridos        |
| :----- | :---------------- | :------------------------ | :--------------------------- |
| `GET`  | `/Cerrar_Sesion`  | `Sesion::cerrar_sesion`   | (ninguno)                    |
| `POST` | `/iniciar/sesion` | `Sesion::iniciar_sesion`  | `Inicio_secion`, `correo`, `contraseña` |

### Rutas de Usuario (`app/Usuario.php`)

Estas rutas cubren todas las operaciones CRUD para los usuarios y siguen estrictamente el patrón de delegación a `Manejador`.

| Método | Ruta                         | Controlador                     | Parámetros Requeridos        | Acción Post-Ejecución     |
| :----- | :--------------------------- | :------------------------------ | :--------------------------- | :------------------------ |
| `POST` | `/usuario/crear`             | `Usuario::crear_usuario`        | `Crear_usuario`, `cedula`, ... | -                         |
| `GET`  | `/usuario/list`              | `Usuario::consultar_usuario`    | (ninguno)                    | -                         |
| `GET`  | `/usuario/eliminar`          | `Usuario::eliminar_usuario`     | `eliminarUsuario`, `ci`      | `Usuario::consultar_usuario` |
| `POST` | `/usuario/editar`            | `Usuario::editar_usuario`       | `EditarUsuario`, `ci`, ...     | `Usuario::consultar_usuario` |
| `GET`  | `/usuario/form/edicion`      | `Usuario::editar_usuario_form`  | `formularioEdicion`          | -                         |
| `GET`  | `/usuario/cambiarEstadoUsuario`| `Usuario::cambiarEstado`        | `cambiarEstadoUsuario`, `ci` | -                         |

*Nota: La "Acción Post-Ejecución" es una función que se invoca después del manejador principal para refrescar datos en la interfaz, como recargar una lista de usuarios después de una edición.*

## Estructura de los Controladores

Todos los controladores de la aplicación se encuentran en `backend/Clases/app/Controladores/`. Son clases ligeras que extienden `Liki\Modelo`. Sus métodos son estáticos y su única función es delegar la ejecución a un `Manejador`.

**Ejemplo de un método de controlador:**

```php
// backend/Clases/app/Controladores/Personas/Usuario.php

class Usuario extends Modelo {
    public static function crear_usuario(array $parametros, array $funcionesExtra = []) {
        // Delega la lógica al manejador 'CrearUsuario'
        return DelegateFunction::exec("ManejoUsuarios/CrearUsuario", $parametros, $funcionesExtra);
    }

    // ... otros métodos
}
```

## El Patrón de Delegación "Manejador"

El núcleo de esta arquitectura es `DelegateFunction::exec()`. Este método recibe una cadena con la ruta al archivo `Manejador` (relativa a la constante `CONTOLLER_PATH`).

`DelegateFunction` carga dinámicamente el archivo del `Manejador` y llama a su método estático `run()`, pasándole los parámetros de la solicitud.

**Mapeo de Controlador a Manejador:**

| Controlador      | Método                   | Ruta del Manejador                     |
| :--------------- | :----------------------- | :------------------------------------- |
| `Personas\Usuario` | `consultar_usuario`      | `ManejoUsuarios/ConsultarUsuario`      |
| `Personas\Usuario` | `crear_usuario`          | `ManejoUsuarios/CrearUsuario`          |
| `Personas\Usuario` | `eliminar_usuario`       | `ManejoUsuarios/EliminarUsuario`       |
| `Personas\Usuario` | `editar_usuario`         | `ManejoUsuarios/EditarUsuario`         |
| `DatosExtra\Rol` | `consultar_rol`          | `ManejoUsuarios/ConsultarRol`          |

Esto permite que la lógica de negocio esté encapsulada y sea fácil de encontrar y modificar, sin tocar las definiciones de rutas o los controladores.
