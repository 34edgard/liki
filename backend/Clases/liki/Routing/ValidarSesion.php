<?php
namespace Liki\Routing;
use Liki\Routing\ControlInterfaz;
use Liki\Sesion;
class ValidarSesion{
public static function validar_sesion($name = 'cedula'){
if (session_id() == "") {
  Sesion::init();
  if (!isset($_SESSION[$name])) {
ControlInterfaz::cambiarPagina('');
   exit();
  }
}
}
}