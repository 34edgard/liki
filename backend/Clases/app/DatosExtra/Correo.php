<?php

namespace App\DatosExtra;
use Liki\Database\Tabla;
use Liki\DelegateFunction;

class Correo extends Tabla{
  
  public function __construct(){
    parent::__construct('correo');
  }
  public static function optenerEmail($id){
   // echo $id.'aqui';
     echo DelegateFunction::exec('ManejoUsuarios/ConsultarCorreo',$id);
  }
}