<?php


use App\Personas\Usuario;
use App\DatosExtra\Correo;
use Liki\Plantillas\Flow;
use Liki\Database\Tabla;
return new class {

 public static function run($p){
  
    extract($p);
if(!isset($formularioEdicion)) return;
   
   session_start();
   //formularioEdicion
   $datos = Tabla::conf(Usuario::class)->campos(['cedula','nombres','apellidos','id_rol','usuario','id_correo'])
           ->get( ['cedula'=>$formularioEdicion] )[0];
        
        //print_r($datos);
        
      $datos['correo'] = Tabla::conf(Correo::class)->campos(['email'])
      ->get(['id_correo'=>$datos['id_correo'] ])[0]['email'];
  // print_r($datos);
   Flow::html("EditarUsuario",$datos);
  }

  
};
