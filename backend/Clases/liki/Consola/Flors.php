<?php

namespace Liki\Consola;  
  
class Flors {  
      public static function registrar($config,$struct){
      $flors = json_decode(file_get_contents(FLORS_FILE),true);
   
     $nFlos= [
             "nombre"=> $config["nombre"],  
             "version"=>$config["version"],  
            "struct"=> $struct,  
           "instalado_en"=> date('Y-m-d')
             ];
            
       foreach($flors['instalados'] as $id => $campo){
         if($campo['nombre'] ==  $config['nombre'] ) {
        $flors['instalados'][$id] = $nFlos;
        break;
       }else{
        $flors['instalados'][] = $nFlos;
        break;
       }
       }
        
         
            
            
 file_put_contents(FLORS_FILE,json_encode($flors));


 }
    public static function instalar(string $struct): void {  
        $config = json_decode(file_get_contents(STRUCT_DIR.$struct.".json"), true);  
        // Copiar archivos a sus destinos  
        foreach ($config['archivos'] as $archivo) {  
            // crear directorios si no existen  
            // copiar archivo al destino  
        }  
        // Registrar en flors.json  
        self::registrar($config, $struct);  
    }  
    
    public static function integrar(string $nombre){
    
    }
    public static function cargarFlors(){
        
    }
    public static function buscar($nombre){
        
    }
    public static function desregistrar($nombre){
        
    }
  
    public static function remover(string $nombre): void {  
        $flors = self::cargarFlors();  
        $paquete = self::buscar($nombre, $flors);  
        $struct = json_decode(file_get_contents($paquete['struct']), true);  
        // Eliminar archivos listados en struct.json  
        foreach ($struct['archivos'] as $archivo) {  
            unlink($archivo['origen']);  
        }  
        // Quitar de flors.json  
        self::desregistrar($nombre);  
    }  
}


