<?php

use App\Personas\Usuario;
use Liki\Plantillas\Plantilla;

use Liki\Database\Tabla;


return new class {
  public static function run($p) {
    session_start();
   
    extract($p);
   
    
    Tabla::conf(Usuario::class)->campos(["cedula", "nombres", "apellidos", "id_correo", "estado"]);
    $where =[];
    if ($_SESSION["id_rol"] == 2) {
      $where = ['cedula'=>$_SESSION["ci"]];
    }

   
    $lista_usuarios = Tabla::conf(Usuario::class)->get($where);

    foreach ($lista_usuarios as $usuario) {
      
  Plantilla::HTML("usuario/lista-usuarios",$usuario);
    }
  }
};
