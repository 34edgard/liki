<?php

namespace Liki;

use App\Personas\Usuario;
use App\DatosExtra\Correo;
use Liki\Routing\ControlInterfaz;
use Liki\Plantillas\Plantilla;
use Liki\ErrorHandler;

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
      $PA = new Usuario();
      $correoElectronico = new Correo();
     
     $id_correo = $correoElectronico->consultar(  [
          "campos" => ["email","id_correo"],
            "where"=>[
      ["campo"=>'email',"operador"=>'=',"valor"=>$correo]
      ]
      ]);
    
    
    $id_correo = $id_correo[0]['id_correo'];
     // print_r($id_correo);
      
      if (!isset($id_correo) ) {
        
        
        ErrorHandler::getInstance()->handle(
       ErrorHandler::AUTH_EMAIL_NOT_FOUND,
        'Credenciales inválidas',
        ['email'=>$correo],
       401
        );
          
      }
    
    
      
      $arreglo = $PA->consultar([
        "campos" => ["cedula", "contrasena", "id_rol", "nombres","id_correo"],
        "where"=>[
          ["campo"=>'id_correo',"operador"=>'=',"valor"=>$id_correo]
        ]
      ]);
    
      
    if (!isset($arreglo[0]) ) {
        
        
        
        Plantilla::HTML('sesiones/alert',[
            'mensaje'=>'el usuario o la contraseña son incorrectas '
        ]);
        return [false];
    }
    
    
      if ( !password_verify($contraseña, $arreglo[0]['contrasena']) ) {
      
    
    
    
     Plantilla::HTML('sesiones/alert',[
            'mensaje'=>'el usuario o la contraseña son incorrectos'
        ]);
       return [false];
      }
      
      
      
      ControlInterfaz::cambiarPagina("inicio");
      
      return [true, $arreglo];
    }

}


