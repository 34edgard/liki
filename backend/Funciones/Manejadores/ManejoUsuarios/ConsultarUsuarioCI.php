<?php
use App\Controladores\Personas\Usuario;
use Liki\Plantillas\Flow;
use Liki\Database\FlowDB;


return new class {
  public static function run($p) {
    session_start();   
    extract($p);
     
    FlowDB::conf(Usuario::class)->campos(["cedula", "nombres", "apellidos", "id_correo", "estado"]);
    $where =[];
    if ($_SESSION["id_rol"] == 2) {
      $where = ['cedula'=>$_SESSION["ci"]];
    }
  
    $lista_usuarios = FlowDB::conf(Usuario::class)->get($where);

    foreach ($lista_usuarios as $usuario) {
      
  Flow::html("usuario/lista-usuarios",$usuario);
    }
  }
};
