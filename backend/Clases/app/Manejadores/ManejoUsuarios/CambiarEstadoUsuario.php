<?php
use App\Controladores\Personas\Usuario;
use Liki\Manejadores\ManejadorBase;

return new class extends  ManejadorBase {
  public  function run($p) {  
    extract($p);
    
    
    
 $ci = (int)$ci;
    $estadoActual = $this->modelo('Usuario')->campos(["cedula", "estado"])
                              ->get(['cedula'=>$ci]);
   
   $estado = "activo";
   $estilo = "success";
 if ($estadoActual[0]["estado"] == "activo") {
      $estado = "inactivo";
      $estilo = "secondary";
    } 

    
     $this->modelo('Usuario')->campos(["cedula", "estado"])
             ->valores([$ci, $estado])
             ->put(['cedula'=>$ci]);

 $this->render('componentes/button',[
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
