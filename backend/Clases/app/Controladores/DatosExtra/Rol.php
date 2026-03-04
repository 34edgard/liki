<?php
namespace App\Controladores\DatosExtra;
use Liki\DelegateFunction;

class Rol {
  
  public static function consultar_rol(){
      DelegateFunction::exec('ManejoUsuarios/ConsultarRol');
  }
}