<?php

namespace App\DatosExtra;
use Liki\Modelo;
use Liki\DelegateFunction;

class Correo extends Modelo{
  
  public function __construct(){
    parent::__construct('correo');
  }
  public static function optenerEmail($id){
   // echo $id.'aqui';
     echo DelegateFunction::exec('ManejoUsuarios/ConsultarCorreo',$id);
  }
}