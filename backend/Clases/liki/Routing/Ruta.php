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
    public static function patch(string $url_pattern, callable $funcion, array $parametros_extra = []);
    public static function options(string $url_pattern, callable $funcion, array $parametros_extra = []);
    public static function head(string $url_pattern, callable $funcion, array $parametros_extra = []);
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
     
    $pR = count($parametros_recibidos) ;
    $pE = count($parametros_esperados);
      if($pR < $pE ) throw new Exception('Error: Faltan parametros requeridos en la ruta '.$_SERVER['REQUEST_URI']);
                       
     
        if($pR > $pE ) throw new Exception('Error: Demaciados parametros en la ruta '.$_SERVER['REQUEST_URI']);
        
       
    
      foreach ($parametros_esperados as $id => $parametro) {
        
            if (is_int($id) && !isset($parametros_recibidos[$parametro])) 
                return false;
            if (is_string($id) && !isset($parametros_recibidos[$id]))
            return false;
            
            if(is_string($id) && $parametro != '')
            self::validar_reglas([$id=>$parametro],$parametros_recibidos);
        }
        return true;
    }


public static function validar_reglas(array $reglas, array $parametros_recibidos): void {  

      
    foreach ($reglas as $campo => $regla) {  
        $reglas_array = explode('|', $regla);  
          
        foreach ($reglas_array as $r) {  
            if ($r === 'required' && !isset($parametros_recibidos[$campo])) {  
                throw new Exception("El campo $campo es requerido");  
            }  
            if ($r === 'email' && isset($parametros_recibidos[$campo]) && !filter_var($parametros_recibidos[$campo], FILTER_VALIDATE_EMAIL)) {  
                 throw new Exception("El campo $campo debe ser un email válido");  
            }  
            if (strpos($r, 'min:') === 0 && isset($parametros_recibidos[$campo])) {  
                $min = substr($r, 4);  
                if (strlen($parametros_recibidos[$campo]) < $min) {  
                    throw new Exception("El campo $campo debe tener al menos $min caracteres");  
                }
            }  
        }  
    }  
      
    

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
            'parametros_esperados' => self::clacificar_parametros_esperados($parametros_esperados,$url_pattern),
            'funcion_extra' => $funcion_extra,
            'regex_pattern' => self::compile_route_pattern($url_pattern) // Compila el patrón para regex
        ];
    }

private static function clacificar_parametros_esperados(array $parametros_esperados = [], string $url_pattern = ''): array{
    $todos_parametros_esperados = [];
    $parametros_esperados = array_merge($parametros_esperados,self::separar_parametros_url($url_pattern));
  

    foreach($parametros_esperados as $id => $parametro){
        if(is_string($id)){
            $todos_parametros_esperados[$id] = $parametro;
        }else if(is_int($id)){
            $todos_parametros_esperados[] = $parametro;
        }
    }
    return $todos_parametros_esperados;
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
// Elimina todo código HTML/JS  
return htmlspecialchars(strip_tags($valor), ENT_QUOTES, 'UTF-8');

case'MODERADO':
// Solo escapa caracteres peligrosos  
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
        * Registra una ruta para solicitudes PATCH.
        */
       public static function patch(string $url_pattern, callable $funcion, array $parametros_esperados = [], array $funcion_extra = []): void {
           self::add_route('PATCH', $url_pattern, $funcion, $parametros_esperados, $funcion_extra);
       }
   /**
    * Registra una ruta para solicitudes OPTIONS.
    */
   public static function options(string $url_pattern, callable $funcion, array $parametros_esperados = [], array $funcion_extra = []): void {
       self::add_route('OPTIONS', $url_pattern, $funcion, $parametros_esperados, $funcion_extra);
   }

/**
 * Registra una ruta para solicitudes HEAD.
 */
public static function head(string $url_pattern, callable $funcion, array $parametros_esperados = [], array $funcion_extra = []): void {
    self::add_route('HEAD', $url_pattern, $funcion, $parametros_esperados, $funcion_extra);
}

public static function separar_parametros_url(string $url_pattern):array{
    $f =explode('/',$url_pattern);
    $dynamic_params = [];
    foreach($f as  $a){
    if(strpos($a,'{') === 0){
      $a=  str_replace('{','',$a);
      $a=  str_replace('}','',$a);
      $dynamic_params[] = $a;
    
    }
    }
    return $dynamic_params;
}

    /**
     * Procesa la solicitud entrante y despacha a la función de callback correspondiente.
     */
    public static function dispatch(): void {
        $request_uri = strtok($_SERVER['REQUEST_URI'], '?'); // Obtiene la URL sin la query string
        $request_method = $_SERVER['REQUEST_METHOD'];

        // Manejar datos de entrada para PUT/DELETE
        $input_data = [];
        if (in_array($request_method, ['PUT','DELETE', 'PATCH', 'OPTIONS', 'HEAD']) ){
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
                $dynamic_params = [];
                
                $parametros_url = self::separar_parametros_url($route['url_pattern']);
                
                foreach($matches as $id => $parametro){
                   $dynamic_params[$parametros_url[$id]]= $parametro;
                
                }

                // Combina los parámetros dinámicos con los datos de entrada
                $all_params = array_merge($input_data, $dynamic_params);
         // Validar parámetros esperados (si los hay)
        try {
                if (!empty($route['parametros_esperados']) && !self::validar_parametros($route['parametros_esperados'], $all_params)) {
                    // Si faltan parámetros esperados, se podría lanzar una excepción o devolver un error 400
                  
                ErrorHandler::getInstance()->handle(
                     ErrorHandler::VALIDATION_ERROR,
                     'Error: Faltan parametros requeridos en la ruta '.$route['url_pattern'],
                    ['exception' => 'Faltan parametros requeridos'],
                    400
                );
                  
                    return;
                }
                
                //sanitización
                $all_params=self::sanitizar_entrada($all_params);
                
                
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
            ['exception' => '404 Not Founden no se encontro la ruta '.$request_uri ],
            404
        );
        
        
    }

public static function modul( string $ruta, bool $condicion = true, array $middlewares = []){
        $urlFile = CONTOLLER_PATH.'backend/Funciones/Rutas/'.$ruta.'.php';
       if(!$condicion) return;
        if(!file_exists($urlFile))            
         throw new Exception('el archivo de rutas '.$urlFile.' no existe');
        
        
        foreach($middlewares as $middleware){
          if( !$middleware()) return;
        }
        
        
       $f = include $urlFile;
    is_callable($f) ? $f() : null;
}

public static function group(callable $rutas,array $parametrosCompartidos = [], array $FuncionesCompartidas = [], array $middlewares = []){
   

foreach($middlewares as $middleware){
  if( !$middleware()) return;
}


  $nRutas = count(self::$routes);
     $rutas();
  

foreach(self::$routes as $index => $rutas ){
   
   if($index < $nRutas ) continue;
   
   
//parametros_esperados

foreach($parametrosCompartidos as $parametro) {
   self::$routes[$index]['parametros_esperados'][] = $parametro;
}

      foreach($FuncionesCompartidas as $id => $Funcion) {
        if(!is_string($id)){
        self::$routes[$index]['funcion_extra'][] = $Funcion;
        }else{
        self::$routes[$index]['funcion_extra'][$id] = $Funcion;
        }
    }

}

}

public static function prefix(string $prefijo, callable $agregarRutas, array $middlewares =[], array $Funciones = []){
    foreach($middlewares as $middleware){
      if(!$middleware()) return;
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