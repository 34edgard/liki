<?php
//namespace Funciones\ManejoUsuarios;
use App\DatosExtra\Rol;
use Liki\Plantillas\Plantilla;
use Liki\Database\Tabla;

return new class {
  public static function run($p,$f){
    
    
    
    
    $roles = Tabla::conf(Rol::class)->campos(['id_rol','nombre_rol'])->get();
     foreach ($roles as $dato){
        Plantilla::HTML("componentes/option",[
            "value"=>$dato['id_rol'],
            "contenido"=>$dato['nombre_rol']
        ]);
    }
    
  }
  
};