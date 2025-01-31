<?php

interface Peticion_Server {
    public static function validar_metodo(string $metodo): bool;
    public static function validar_parametros(array $parametros, array $variable_global): bool;
    public static function metodo_get(string $url, callable $funcion, array $parametros = [], array $funcion_extra = []);
    public static function metodo_post(string $url, callable $funcion, array $parametros = [], array $funcion_extra = []);
    public static function metodo_put(string $url, callable $funcion, array $parametros = [], array $funcion_extra = []);
    public static function metodo_delete(string $url, callable $funcion, array $parametros = [], array $funcion_extra = []);
}

class Peticion implements Peticion_Server {

    public static function validar_parametros(array $parametros, array $variable_global): bool {
        foreach ($parametros as $parametro) {
            if (!isset($variable_global[$parametro])) {
                return false;
            }
        }
        return true;
    }

    public static function validar_metodo(string $metodo): bool {
        return $_SERVER['REQUEST_METHOD'] === $metodo;
    }
    
    private static function extraer_querys_url(string $url):string{
        $nueva_url= '';
        
         for($i = 0; $i < strlen($url);$i++){
           if($url[$i] == '?') break;
   
              $nueva_url .= $url[$i];
 
           }
         return $nueva_url;
    }
    
    private static function manejar_metodo(string $url, string $metodo, callable $funcion, array $parametros, array $funcion_extra, array $data_global) {
        if (self::extraer_querys_url($_SERVER['REQUEST_URI']) !== self::extraer_querys_url($url)) return;

        if (self::validar_metodo($metodo)) {
            $numero_parametros = count($parametros);
            $numero_data = count($data_global);

            if ($numero_parametros === 0 && $numero_data === 0) {
                // No hay parámetros esperados y tampoco se recibieron
                $funcion($funcion_extra);
            } elseif ($numero_parametros === $numero_data && self::validar_parametros($parametros, $data_global)) {
                // Se recibieron los parámetros esperados
                $funcion($data_global, $funcion_extra);
            }
        }
    }

    public static function metodo_get(string $url, callable $funcion, array $parametros = [], array $funcion_extra = []) {
        try{
        self::manejar_metodo($url, 'GET', $funcion, $parametros, $funcion_extra, $_GET);
        }catch(\Exception $err){
            echo $err->getMessage();
        }
    }

    public static function metodo_post(string $url, callable $funcion, array $parametros = [], array $funcion_extra = []) {
       try{
        self::manejar_metodo($url, 'POST', $funcion, $parametros, $funcion_extra, $_POST);
        }catch(\Exception $err){
            echo $err->getMessage();
        }
    }

    public static function metodo_put(string $url, callable $funcion, array $parametros = [], array $funcion_extra = []) {
        parse_str(file_get_contents("php://input"), $_PUT); // Obtener datos PUT
        try{
        self::manejar_metodo($url, 'PUT', $funcion, $parametros, $funcion_extra, $_PUT);
        }catch(\Exception $err){
                 echo $err->getMessage();
        }
    }

    public static function metodo_delete(string $url, callable $funcion, array $parametros = [], array $funcion_extra = []) {
        parse_str(file_get_contents("php://input"), $_DELETE); // Obtener datos DELETE
        try{
        
        self::manejar_metodo($url, 'DELETE', $funcion, $parametros, $funcion_extra, $_DELETE);
         }catch(\Exception $err){
                 echo $err->getMessage();
         }
    }
}