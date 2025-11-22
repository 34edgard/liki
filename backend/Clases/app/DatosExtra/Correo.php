<?php

namespace App\DatosExtra;
use Liki\Database\Tabla;
use Liki\ExecFunc;

class Correo extends Tabla{
  
  public function __construct(){
    parent::__construct('correo');
  }
  public static function optenerEmail($id){
   // echo $id.'aqui';
     echo ExecFunc::exec('ManejoUsuarios/ConsultarCorreo',$id);
  }
}