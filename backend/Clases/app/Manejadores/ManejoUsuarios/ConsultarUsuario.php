<?php
use Liki\Sesion;
use Liki\Plantillas\Flow;
use App\Controladores\Personas\Usuario;
use Liki\Database\FlowDB;
use Middleware\AuthMiddleware;
use Liki\Manejadores\ManejadorBase;

return new class extends  ManejadorBase {

  public  function run() {
    Sesion::init();

    $where = [];

if(AuthMiddleware::isAdmin()) $where = ['cedula'=>$_SESSION["cedula"]];
  
  $lista_usuarios = FlowDB::conf('Usuario')
    ->campos(['cedula','nombres','apellidos','id_correo','estado'])
    ->get($where);

    foreach ($lista_usuarios as $usuario) {
      Flow::html("usuario/lista-usuarios", $usuario);
    }

  }
};