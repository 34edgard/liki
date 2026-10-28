<?php
namespace Liki;
use Exception;

class DelegateFunction{
    public static function exec(string $name = '', array $parametros=[], array $funcionesExtra =[]){
       $urlFlie = CONTOLLER_PATH.'backend/Clases/app/Manejadores/'.$name.'.php';
         if(!is_file($urlFlie))
                 throw new Exception("Expection no existe el archivo $name.php en la ruta $urlFlie");
       $class = include $urlFlie;
       $class($parametros,$funcionesExtra);
         $class->antes($parametros,$funcionesExtra);
         $resultado = $class->run($parametros,$funcionesExtra);    
        $class->despues($parametros,$funcionesExtra);
        return $resultado;
    } 
    public static function loadModel(string $name = ''): object {
       $urlFlie = CONTOLLER_PATH.'backend/Clases/app/Modelos/'.$name.'.php';
         if(!is_file($urlFlie))
                 throw new Exception("Expection no existe el archivo $name.php en la ruta $urlFlie");
      return $class = include $urlFlie;
    } 
    public static function loadData(string $name = ''): array {
         $urlFlie = CONTOLLER_PATH.'backend/Funciones/'.$name.'.php';
             if(!is_file($urlFlie))
                     throw new Exception("Expection no existe el archivo $name.php en la ruta $urlFlie");
         return include $urlFlie;
    }
}