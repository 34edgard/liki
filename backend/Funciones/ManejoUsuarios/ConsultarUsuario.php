<?php


use Liki\Plantillas\Plantilla;
use App\Personas\Usuario;
use Liki\Database\Tabla;

return new class {
   public static function run() {
    session_start();
  
    
   Tabla::conf(Usuario::class)->campos(["cedula", 
    "nombres", 
    "apellidos", 
    "id_correo", 
    "estado"]);
    
    $where = [];

    if ($_SESSION["id_rol"] == 2) {
      $where = ['cedula'=> $_SESSION["cedula"] ];
    }
$lista_usuarios = Tabla::conf(Usuario::class)->get($where);
    

    
  foreach ($lista_usuarios as $usuario) {
        //print_r($usuario);
     Plantilla::HTML("usuario/lista-usuarios",$usuario);
    }
  }
};
