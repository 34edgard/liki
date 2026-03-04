<?php
namespace App\Controladores\DatosExtra;
use Liki\DelegateFunction;

class Correo {
  public static function optenerEmail($id){
     echo DelegateFunction::exec('ManejoUsuarios/ConsultarCorreo',$id);
  }
}