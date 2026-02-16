<?php
namespace App\Controladores\DatosExtra;
use Liki\Modelo;
use Liki\DelegateFunction;

class Correo extends Modelo{
  public static function optenerEmail($id){
     echo DelegateFunction::exec('ManejoUsuarios/ConsultarCorreo',$id);
  }
}