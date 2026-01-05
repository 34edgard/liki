<?php

use App\DatosExtra\Rol;
use Liki\Plantillas\Flow;
use Liki\Database\Tabla;

return new class {
  public static function run($p,$f){
    
    
    
    
    $roles = Tabla::conf(Rol::class)->campos(['id_rol','nombre_rol'])->get();
     foreach ($roles as $dato){
        Flow::html("componentes/option",[
            "value"=>$dato['id_rol'],
            "contenido"=>$dato['nombre_rol']
        ]);
    }
    
  }
  
};