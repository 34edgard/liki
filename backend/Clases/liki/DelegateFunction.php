<?php
namespace Liki;

class DelegateFunction{
    public static function exec($name = '',$parametros=[],$funcionesExtra =[]){
       $urlFlie = CONTOLLER_PATH.'backend/Clases/app/Manejadores/'.$name.'.php';
         if(!is_file($urlFlie))
                 throw new \Exception("Expection no existe el archivo $name.php en la ruta $urlFlie");
         
       $class = include $urlFlie;
      return  $class::run($parametros,$funcionesExtra);    
    } 
    public static function loadModel($name = ''): object {
       $urlFlie = CONTOLLER_PATH.'backend/Clases/app/Modelos/'.$name.'.php';
         if(!is_file($urlFlie))
                 throw new \Exception("Expection no existe el archivo $name.php en la ruta $urlFlie");
         
      return $class = include $urlFlie;
    } 
      
    public static function loadData($name = ''){
         $urlFlie = CONTOLLER_PATH.'backend/Funciones/'.$name.'.php';
             if(!is_file($urlFlie))
                     throw new \Exception("Expection no existe el archivo $name.php en la ruta $urlFlie");
         return include $urlFlie;
    }
}