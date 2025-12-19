<?php
namespace App\DatosExtra;
use Liki\Database\Tabla;
use Liki\DelegateFunction;

class Rol extends Tabla{
  
  public function __construct(){
    parent::__construct('roles');
  }
  
  public static function consultar_rol(){
      DelegateFunction::exec('ManejoUsuarios/ConsultarRol');
  }
}


 