<?php
use Liki\Sesion;
use Liki\Plantillas\Flow;
use App\Controladores\Personas\Usuario;
use Liki\Database\FlowDB;
use Middleware\AuthMiddleware;
use Liki\Manejadores\ManejadorBase;

return new class extends  ManejadorBase {

  public  function run(array $p =[], array $f = []) {
    Sesion::init();

    $where = [];

if(!AuthMiddleware::isAdmin()) $where = ['cedula'=>$_SESSION["cedula"]];

  $db = new FlowDB('Usuario');
  $lista_usuarios =  $db //->tabla('usuario')
    ->campos(['usuario.cedula','usuario.nombres','usuario.apellidos','correo.email as correo','usuario.estado'])
    ->innerJoin('correo','usuario.id_correo = correo.id_correo')
    ->get($where);

    foreach ($lista_usuarios as $usuario) {
      Flow::html("usuario/lista-usuarios", $usuario);
    }

  }
};