<?php

namespace Liki\Routing;
use Liki\ErrorHandler;
use Exception;

set_exception_handler(function($exception){
      ErrorHandler::getInstance()->handle(
      ErrorHandler::SERVER_ERROR,
      'Error interno del servidor',
     ['exception' => $exception->getMessage()],
     500
 );
});


interface Rutas_Server {
    public static function validar_metodo(string $metodo): bool;
    public static function validar_parametros(array $parametros_esperados, array $parametros_recibidos): bool;
    public static function get(string $url_pattern, callable $funcion, array $parametros_extra = []);
    public static function post(string $url_pattern, callable $funcion, array $parametros_extra = []);
    public static function put(string $url_pattern, callable $funcion, array $parametros_extra = []);
    public static function delete(string $url_pattern, callable $funcion, array $parametros_extra = []);
    public static function dispatch(): void;
}

class Ruta implements Rutas_Server {

    private static  $routes = [];

    /**
     * Valida si los parámetros esperados están presentes en los parámetros recibidos.
     *
     * @param array $parametros_esperados Nombres de los parámetros que se esperan.
     * @param array $parametros_recibidos Parámetros recibidos (e.g., $_GET, $_POST).
     * @return bool True si todos los parámetros esperados están presentes, false de lo contrario.
     */
    public static function validar_parametros(array $parametros_esperados, array $parametros_recibidos): bool {
        foreach ($parametros_esperados as $parametro) {
            if (!isset($parametros_recibidos[$parametro])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Valida si el método de la solicitud actual coincide con el método dado.
     *
     * @param string $metodo El método HTTP a validar (e.g., 'GET', 'POST').
     * @return bool True si los métodos coinciden, false de lo contrario.
     */
    public static function validar_metodo(string $metodo): bool {
        return $_SERVER['REQUEST_METHOD'] === $metodo;
    }

    /**
     * Convierte un patrón de URL con segmentos dinámicos a una expresión regular.
     * Ejemplo: /users/{id} -> #^/users/(\d+)$#
     *
     * @param string $url_pattern El patrón de URL con segmentos dinámicos.
     * @return string La expresión regular resultante.
     */
    private static function compile_route_pattern(string $url_pattern): string {
        // Escapa caracteres especiales de regex y reemplaza {param} con grupos de captura.
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([a-zA-Z0-9_]+)', $url_pattern);
        return '#^' . $pattern . '$#';
    }

    /**
     * Registra una ruta para un método HTTP específico.
     *
     * @param string $method El método HTTP (GET, POST, PUT, DELETE).
     * @param string $url_pattern El patrón de URL (puede incluir segmentos dinámicos como /users/{id}).
     * @param callable $handler La función de callback a ejecutar cuando la ruta coincide.
     * @param array $parametros_esperados Parámetros esperados en la data global (GET, POST, etc.).
     * @param array $funcion_extra Parámetros adicionales para la función de callback.
     */
    private static function add_route(string $method, string $url_pattern, callable $handler, array $parametros_esperados = [], array $funcion_extra = []): void {
        self::$routes[] = [
            'method' => $method,
            'url_pattern' => $url_pattern,
            'handler' => $handler,
            'parametros_esperados' => $parametros_esperados,
            'funcion_extra' => $funcion_extra,
            'regex_pattern' => self::compile_route_pattern($url_pattern) // Compila el patrón para regex
        ];
    }



private static function sanitizar_entrada( array $datos =[]):array {
if(MODO_SANITIZACION === 'NINGUNO'){
return $datos;
}

 $datos_limpios = [];
foreach($datos as $clave => $valor){ 
if(is_array($valor)){
 $datos_limpios[$clave]=self::sanitizar_entrada($valor);
}else{
$datos_limpios[$clave]=self::aplicar_sanitizacion($valor);
}
}
return $datos_limpios;
}

private static function aplicar_sanitizacion(string $valor): string {
switch (MODO_SANITIZACION){
case 'ESTRICTO':
// Elimina todo código HTML/JS  
return htmlspecialchars(strip_tags($valor), ENT_QUOTES, 'UTF-8');

case'MODERADO':
// Solo escapa caracteres peligrosos  
 return htmlspecialchars($valor,ENT_QUOTES,'UTF-8');

default:
return $valor;
}
}








    /**
     * Registra una ruta para solicitudes GET.
     */
    public static function get(string $url_pattern, callable $funcion, array $parametros_esperados = [], array $funcion_extra = []): void {
        self::add_route('GET', $url_pattern, $funcion, $parametros_esperados, $funcion_extra);
    }

    /**
     * Registra una ruta para solicitudes POST.
     */
    public static function post(string $url_pattern, callable $funcion, array $parametros_esperados = [], array $funcion_extra = []): void {
        self::add_route('POST', $url_pattern, $funcion, $parametros_esperados, $funcion_extra);
    }

    /**
     * Registra una ruta para solicitudes PUT.
     */
    public static function put(string $url_pattern, callable $funcion, array $parametros_esperados = [], array $funcion_extra = []): void {
        self::add_route('PUT', $url_pattern, $funcion, $parametros_esperados, $funcion_extra);
    }

    /**
     * Registra una ruta para solicitudes DELETE.
     */
    public static function delete(string $url_pattern, callable $funcion, array $parametros_esperados = [], array $funcion_extra = []): void {
        self::add_route('DELETE', $url_pattern, $funcion, $parametros_esperados, $funcion_extra);
    }

    /**
     * Procesa la solicitud entrante y despacha a la función de callback correspondiente.
     */
    public static function dispatch(): void {
        $request_uri = strtok($_SERVER['REQUEST_URI'], '?'); // Obtiene la URL sin la query string
        $request_method = $_SERVER['REQUEST_METHOD'];

        // Manejar datos de entrada para PUT/DELETE
        $input_data = [];
        if (in_array($request_method, ['PUT', 'DELETE'])) {
            parse_str(file_get_contents("php://input"), $input_data);
        } elseif ($request_method === 'POST') {
            $input_data = $_POST;
        } elseif ($request_method === 'GET') {
            $input_data = $_GET;
        }

        foreach (self::$routes as $route) {
            if ($route['method'] === $request_method && preg_match($route['regex_pattern'], $request_uri, $matches)) {
                // Elimina el primer elemento de $matches (la cadena completa)
                array_shift($matches);
                $dynamic_params = $matches; // Parámetros extraídos de la URL dinámica

                // Combina los parámetros dinámicos con los datos de entrada
                $all_params = array_merge($input_data, $dynamic_params);

                // Validar parámetros esperados (si los hay)
                if (!empty($route['parametros_esperados']) && !self::validar_parametros($route['parametros_esperados'], $all_params)) {
                    // Si faltan parámetros esperados, se podría lanzar una excepción o devolver un error 400
                  
                ErrorHandler::getInstance()->handle(
                     ErrorHandler::VALIDATION_ERROR,
                     'Error: Faltan parámetros requeridos',
                    ['exception' => 'Faltan parámetros requeridos'],
                    400
                );
                  
                    return;
                }
                
                //sanitización
                $all_params=self::sanitizar_entrada($all_params);

                try {
                    // Prepara los argumentos para la función de callback
                    $args = [];
                    if (!empty($all_params)) {
                        $args[] = $all_params; // Pasa todos los parámetros (GET/POST/PUT/DELETE + dinámicos)
                    }
                    if (!empty($route['funcion_extra'])) {
                        $args[] = $route['funcion_extra']; // Pasa parámetros extra definidos en la ruta
                    }

                    // Llama a la función de callback con los argumentos preparados
                    call_user_func_array($route['handler'], $args);
                    return; // Se encontró y ejecutó la ruta, salir.
                } catch (\Exception $e) {
                    
                    ErrorHandler::getInstance()->handle(
                         ErrorHandler::SERVER_ERROR,
                         'Error: interno del servidor',
                        ['exception' => 'Error interno del servidor.'.$e->getMessage()],
                        500
                    );
                    
                    return;
                }
            }
        }

        // Si ninguna ruta coincide
        
        ErrorHandler::getInstance()->handle(
             ErrorHandler::ROUTE_NOT_FOUND,
             'Error: 404 Not Found',
            ['exception' => '404 Not Found'],
            404
        );
        
        
    }
    
    

public static function group( string $ruta, bool $condicion = false, array $middlewares = []){
        $urlFile = CONTOLLER_PATH.'Rutas/'.$ruta.'.php';
       if($condicion) return;
        if(!file_exists($urlFile))            
         throw new Exception('el archivo de rutas '.$urlFile.' no existe');
        
        
        foreach($middlewares as $middleware){
          if( $middleware()) return;
        }
        
        
       $f = include $urlFile;
    $f();
}



public static function prefix(string $prefijo, callable $agregarRutas, array $middlewares =[], array $Funciones = []){
    foreach($middlewares as $middleware){
      if( $middleware()) return;
    }
    
   $nRutas = count(self::$routes);
   $agregarRutas();

foreach(self::$routes as $index => $rutas ){
    
    if($index < $nRutas ) continue;
    
    
    self::$routes[$index]['url_pattern'] = $prefijo.$rutas['url_pattern'];
    self::$routes[$index]['regex_pattern'] = self::compile_route_pattern($prefijo.$rutas['url_pattern']);// Compila el patrón para regex
     foreach($Funciones as $Funcion) {
    self::$routes[$index]['funcion_extra'][] = $Funcion;
     }
 

}
}

// Agregar este método a la clase Ruta
public static function obtener_rutas(): array {
    return self::$routes;
}
    
    
    
}

