<?php
namespace App\Controladores\DatosExtra;
use Liki\Controladores\ControladorBase;

class Correo extends ControladorBase{
  public static function optenerEmail($id){
     echo self::manejador('ManejoUsuarios/ConsultarCorreo',[$id]);
  }
}