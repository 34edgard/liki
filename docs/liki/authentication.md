# Autenticación y Sesiones

Esta página documenta el ciclo de vida de la sesión en Liki: cómo se inician las sesiones, cómo se validan las credenciales de inicio de sesión, cómo se almacena el estado en `$_SESSION` y cómo se protegen las rutas usando el middleware `ValidarSesion`.

## Componentes Clave

La autenticación en Liki se gestiona mediante la cooperación de tres componentes principales:

1.  **`Sesion` (`backend/Clases/liki/Sesion.php`):** La clase central que se encarga de iniciar, popular con datos del usuario y destruir la sesión de PHP.
2.  **`ValidarSesion` (`backend/Clases/liki/Routing/ValidarSesion.php`):** Un middleware a nivel de ruta que exige una sesión activa para poder acceder a ciertos endpoints.
3.  **Rutas de Sesión (`backend/Funciones/Rutas/app/sesiones.php`):** El archivo que enlaza las URL `/iniciar/sesion` y `/Cerrar_Sesion` con los métodos de la clase `Sesion`.

## El Ciclo de Vida de la Sesión

### 1. Inicio de Sesión (`iniciar_sesion`)

El proceso comienza cuando un usuario envía sus credenciales (generalmente desde un formulario) al endpoint `POST /iniciar/sesion`.

El método `Sesion::iniciar_sesion()` se encarga de:

1.  Asegurarse de que una sesión PHP esté iniciada llamando a `Sesion::init()`.
2.  Validar las credenciales (`correo` y `contraseña`) contra la base de datos a través del método privado `validar_datosDB()`.
3.  Si la validación es exitosa:
    a. Se realiza una consulta para obtener todos los datos del usuario.
    b. **Todos los campos del registro del usuario en la base de datos se copian directamente en la variable global `$_SESSION`**.
    c. Se redirige al usuario a la página de inicio con `ControlInterfaz::cambiarPagina('inicio')`.
4.  Si la validación falla (correo no encontrado o contraseña incorrecta), se devuelve un error HTTP 401.

### 2. Contenido de `$_SESSION`

Después de un inicio de sesión exitoso, `$_SESSION` contendrá una copia de las columnas de la tabla `usuario`, incluyendo como mínimo:
-   `cedula`
-   `contrasena` (el hash, no la contraseña en texto plano)
-   `id_rol`
-   `nombres`
-   `id_correo`

### 3. Cierre de Sesión (`cerrar_sesion`)

Al llamar al endpoint `GET /Cerrar_Sesion`, se invoca a `Sesion::cerrar_sesion()`. Este método:

1.  Asegura que la sesión esté iniciada.
2.  Limpia todas las variables de sesión con `session_unset()`.
3.  Destruye la sesión con `session_destroy()`.
4.  Redirige al usuario a la página principal.

## Protección de Rutas con `ValidarSesion`

Para restringir el acceso a ciertas páginas o endpoints solo a usuarios autenticados, Liki utiliza el middleware `ValidarSesion`.

`ValidarSesion` es una clase estática que proporciona un método `validar_sesion()`.

**¿Cómo funciona?**

-   Se llama a `ValidarSesion::validar_sesion()` al principio de un punto de entrada de página (en `frontend/Paginas/`) o dentro de un grupo de rutas.
-   El método comprueba si la variable `$_SESSION['cedula']` está definida. (Se puede pasar un argumento para comprobar otra clave, pero `cedula` es el predeterminado).
-   Si la clave no existe, significa que el usuario no ha iniciado sesión. El middleware inmediatamente redirige al usuario a la página de inicio (`/`) y detiene la ejecución del script con `exit()`.

**Ejemplo de uso en una página protegida:**

```php
// frontend/Paginas/inicio.php

// Primero, se valida la sesión.
// Si el usuario no ha iniciado sesión, será redirigido y el resto del código no se ejecutará.
Liki\Routing\ValidarSesion::validar_sesion();

// Si la sesión es válida, se procede a cargar la configuración y renderizar la página.
$config = ConfigManager::cargarConfig('Inicio');
Flow::html('estructura/pagina', $config);
```
