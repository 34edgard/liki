<?php


use Liki\Plantillas\Flow;
use App\Personas\Usuario;
use Liki\Database\FlowDB;

return new class {
   public static function run() {
    session_start();
  
    
   FlowDB::conf(Usuario::class)->campos(["cedula", 
    "nombres", 
    "apellidos", 
    "id_correo", 
    "estado"]);
    
    $where = [];

    if ($_SESSION["id_rol"] == 2) {
      $where = ['cedula'=> $_SESSION["cedula"] ];
    }
$lista_usuarios = FlowDB::conf(Usuario::class)->get($where);
    

    
  foreach ($lista_usuarios as $usuario) {
        //print_r($usuario);
     Flow::html("usuario/lista-usuarios",$usuario);
    }
  }
};
