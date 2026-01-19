<?php
use Liki\Sesion;
use Liki\Plantillas\Flow;
use App\Controladores\Personas\Usuario;
use Liki\Database\FlowDB;

return new class {
   public static function run() {
    Sesion::init();
      
    $where = [];

    if ($_SESSION["id_rol"] == 2) {
      $where = ['cedula'=> $_SESSION["cedula"] ];
    }
$lista_usuarios = FlowDB::conf(Usuario::class)->campos(["cedula", 
"nombres", 
"apellidos", 
"id_correo", 
"estado"])->get($where);
 
    
  foreach ($lista_usuarios as $usuario) {
     //   print_r($usuario);
    Flow::html("usuario/lista-usuarios",$usuario);
    }
  }
};
