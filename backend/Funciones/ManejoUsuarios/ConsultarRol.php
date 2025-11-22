<?php
//namespace Funciones\ManejoUsuarios;
use App\DatosExtra\Rol;
use Liki\Plantillas\Plantilla;

return new class {
  public static function run($p,$f){
    $roles = (new Rol)->consultar(["campos"=>['id_rol','nombre_rol']]);
    foreach ($roles as $dato){
        Plantilla::HTML("componentes/option",[
            "value"=>$dato['id_rol'],
            "contenido"=>$dato['nombre_rol']
        ]);
    }
    
  }
  
};