<?php
namespace App\Controladores\DatosExtra;
use Liki\Modelo;

use Liki\DelegateFunction;

class Rol extends Modelo{
  
  public function __construct(){
    parent::__construct('roles');
  }
  
  public static function consultar_rol(){
      DelegateFunction::exec('ManejoUsuarios/ConsultarRol');
  }
}


 