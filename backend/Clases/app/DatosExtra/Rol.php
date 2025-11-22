<?php
namespace App\DatosExtra;
use Liki\Database\Tabla;
use Liki\ExecFunc;

class Rol extends Tabla{
  
  public function __construct(){
    parent::__construct('roles');
  }
  
  public static function consultar_rol(){
      ExecFunc::exec('ManejoUsuarios/ConsultarRol');
  }
}


 