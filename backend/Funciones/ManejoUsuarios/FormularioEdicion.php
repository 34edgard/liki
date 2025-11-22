<?php

//namespace Funciones\ManejoUsuarios;
use App\Personas\Usuario;
use App\DatosExtra\Correo;
use Liki\Plantillas\Plantilla;
      
return new class {

 public static function run($p){
  
    extract($p);
if(!isset($formularioEdicion)) return;
   
   session_start();
   //formularioEdicion
   $campos =["campos"=>['cedula','nombres','apellidos','id_rol','usuario','id_correo'],
     "where"=>[
       ["campo"=>'cedula',"operador"=>'=',
   "valor"=>$formularioEdicion]
     ]
   ];
   $datos = (new Usuario)->consultar($campos)[0];
   $datos['correo'] = (new Correo)->consultar([
    "campos"=>['email'],
    "where"=>[
        ['campo'=>'id_correo','operador'=>'=','valor'=>$datos['id_correo']]
    ]
])[0]['email'];
  // print_r($datos);
   Plantilla::HTML("EditarUsuario",$datos);
  }

  
};
