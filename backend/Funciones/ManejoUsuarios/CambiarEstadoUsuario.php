<?php

use App\Personas\Usuario;
use Liki\Plantillas\Plantilla;
use Liki\Database\Tabla;


return new class {
  public static function run($p) {
    
    extract($p);

    
 
    $estadoActual = Tabla::conf(Usuario::class)->campos(["cedula", "estado"])
                              ->get(['cedula'=>$ci]);
   
   $estado = "activo";
   $estilo = "success";
 if ($estadoActual[0]["estado"] == "activo") {
      $estado = "inactivo";
      $estilo = "secondary";
    } 

    
    Tabla::conf(Usuario::class)->campos(["cedula", "estado"])
             ->valores([$ci, $estado])
             ->put(['cedula'=>$ci]);

Plantilla::HTML('componentes/button',[
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
