<?php
use App\Controladores\Personas\Usuario;
use App\Controladores\DatosExtra\Correo;
use Liki\Plantillas\Flow;
use Liki\Database\FlowDB;
use Liki\Sesion;
use Liki\Manejadores\ManejadorBase;

return new class extends  ManejadorBase {


 public  function run($p){
    extract($p);
if(!isset($formularioEdicion)) return;
   Sesion::init();
   //formularioEdicion
   $datos = FlowDB::conf('Usuario')->campos(['cedula','nombres','apellidos','id_rol','usuario','id_correo'])
           ->get( ['cedula'=>$formularioEdicion] )[0];
      $datos['correo'] = FlowDB::conf('Correo')->campos(['email'])
      ->get(['id_correo'=>$datos['id_correo'] ])[0]['email'];
   Flow::html("EditarUsuario",$datos);
  }
};