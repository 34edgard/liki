<?php
namespace Liki;
use Liki\Routing\ControlInterfaz;
use Liki\Plantillas\Flow;
use Liki\ErrorHandler;
use Liki\Database\FlowDB;
use Liki\Validar;

class Sesion {
  public static function init() {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }
  public static function cerrar_sesion() {
    self::init();
    session_unset();
    session_destroy();
    ControlInterfaz::cambiarPagina("");
  }
  public static function iniciar_sesion($p) {
Validar::ValidarArray($p, [
  'correo' => 'isEmail',
  'contraseña' => 'isString'
]);
    extract($p);
    self::init();
    $arreglo = self::validar_datosDB($correo, $contraseña);
    if ($arreglo[0]) {
      foreach ($arreglo[1][0] as $id => $campo) {
        $_SESSION[$id] = $campo;
      }
    }

  }
  private static function validar_datosDB($correo, $contraseña) {

    $id_correo = FlowDB::conf('Correo')->campos(["email", "id_correo"])
    ->get(['email' => $correo]);

    if (!isset($id_correo[0]['id_correo'])) {
      Flow::html('sesiones/alert', [
        'mensaje' => 'el usuario o la contraseña son incorrectas ' ]);
      return [false];
    }
    $id_correo = $id_correo[0]['id_correo'];
    $arreglo = FlowDB::conf('Usuario')
    ->campos(["cedula", "contrasena", "id_rol", "nombres", "id_correo"])
    ->get(['id_correo' => $id_correo]);
    if (!isset($arreglo[0])) {
      Flow::html('sesiones/alert', [
        'mensaje' => 'el usuario o la contraseña son incorrectas ' ]);
      return [false];
    }

    if (!password_verify($contraseña, $arreglo[0]['contrasena'])) {
      Flow::html('sesiones/alert', [
        'mensaje' => 'el usuario o la contraseña son incorrectos']);
      return [false];
    }
    ControlInterfaz::cambiarPagina("inicio");

    return [true, $arreglo];
  }
}