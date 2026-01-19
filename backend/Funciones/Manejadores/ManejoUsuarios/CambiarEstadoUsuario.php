<?php
use App\Controladores\Personas\Usuario;
use Liki\Plantillas\Flow;
use Liki\Database\FlowDB;


return new class {
  public static function run($p) {  
    extract($p);
 
    $estadoActual = FlowDB::conf(Usuario::class)->campos(["cedula", "estado"])
                              ->get(['cedula'=>$ci]);
   
   $estado = "activo";
   $estilo = "success";
 if ($estadoActual[0]["estado"] == "activo") {
      $estado = "inactivo";
      $estilo = "secondary";
    } 

    
    FlowDB::conf(Usuario::class)->campos(["cedula", "estado"])
             ->valores([$ci, $estado])
             ->put(['cedula'=>$ci]);

Flow::html('componentes/button',[
    "contenido"=>$estado,
    "estilo"=>$estilo,
    "hx"=>[
        "url"=>"/usuario/cambiarEstadoUsuario?cambiarEstadoUsuario&ci={$estadoActual[0]['cedula']}",
        "target"=>"#estado{$estadoActual[0]['cedula']}",
        "trigger"=>'click'
        ]
]);

     }
};
