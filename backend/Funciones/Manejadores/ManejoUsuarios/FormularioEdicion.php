<?php
use App\Controladores\Personas\Usuario;
use App\Controladores\DatosExtra\Correo;
use Liki\Plantillas\Flow;
use Liki\Database\FlowDB;
use Liki\Sesion;
return new class {

 public static function run($p){
  
    extract($p);
if(!isset($formularioEdicion)) return;
   
   Sesion::init();
   //formularioEdicion
   $datos = FlowDB::conf(Usuario::class)->campos(['cedula','nombres','apellidos','id_rol','usuario','id_correo'])
           ->get( ['cedula'=>$formularioEdicion] )[0];
        

      $datos['correo'] = FlowDB::conf(Correo::class)->campos(['email'])
      ->get(['id_correo'=>$datos['id_correo'] ])[0]['email'];

   Flow::html("EditarUsuario",$datos);
  }
};