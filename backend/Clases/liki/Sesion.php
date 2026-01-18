<?php

namespace Liki;

use App\Controladores\Personas\Usuario;
use App\Controladores\DatosExtra\Correo;
use Liki\Routing\ControlInterfaz;
use Liki\Plantillas\Flow;
use Liki\ErrorHandler;
use Liki\Database\FlowDB;

class Sesion{
    public static function cerrar_sesion(){
        session_start();
        session_unset();
        session_destroy();
        ControlInterfaz::cambiarPagina("");
    }
    
    public static function  iniciar_sesion() {
      $extras = func_get_args();
      extract( $extras[0]);
      // var_dump($extras);
      session_start();
      $arreglo = self::validar_datosDB($correo, $contraseña);
      //return;
      if ($arreglo[0]) {
          
          foreach($arreglo[1][0] as $id => $campo){
              $_SESSION[$id] = $campo;       
          }
          
         }
      
    }  
    
    private static function validar_datosDB($correo, $contraseña) {
      
     $id_correo = FlowDB::conf(Correo::class)->campos(["email","id_correo"])
            ->get(['email'=>$correo]);
  
    $id_correo = $id_correo[0]['id_correo'];
  
      if (!isset($id_correo) ) {
            
        ErrorHandler::getInstance()->handle(
       ErrorHandler::AUTH_EMAIL_NOT_FOUND,
        'Credenciales inválidas',
        ['email'=>$correo],
       401
        );
          
      }
     
      $arreglo = FlowDB::conf(Usuario::class)
             ->campos( ["cedula", "contrasena", "id_rol", "nombres","id_correo"])
             ->get(['id_correo'=>$id_correo]);
          
    if (!isset($arreglo[0]) ) {
    
        Flow::html('sesiones/alert',[
            'mensaje'=>'el usuario o la contraseña son incorrectas '
        ]);
        return [false];
    }
    
    
      if ( !password_verify($contraseña, $arreglo[0]['contrasena']) ) {
       
     Flow::html('sesiones/alert',[
            'mensaje'=>'el usuario o la contraseña son incorrectos'
        ]);
       return [false];
      }
       
      ControlInterfaz::cambiarPagina("inicio");
      
      return [true, $arreglo];
    }
}
