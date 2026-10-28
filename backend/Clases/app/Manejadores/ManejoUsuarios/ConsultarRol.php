<?php
use App\Controladores\DatosExtra\Rol;
use Liki\Plantillas\Flow;
use Liki\Database\FlowDB;

use Liki\Manejadores\ManejadorBase;

return new class extends  ManejadorBase {

  public  function run($p,$f){    
    $roles = FlowDB::conf('Rol')->campos(['id_rol','nombre_rol'])->get();
     foreach ($roles as $dato){
        Flow::html("componentes/option",[
            "value"=>$dato['id_rol'],
            "contenido"=>$dato['nombre_rol']
        ]);
    }
  }
};