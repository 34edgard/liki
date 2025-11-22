<?php

//namespace Funciones\ManejoUsuarios;
use App\Personas\Usuario;
use App\DatosExtra\Correo;


return new class {
  public static function run($p,$f) {
    
    extract($p);
    $usuarios = new Usuario();
    $correoUsuario = new Correo();
$datosActuales = [
  "campos" => ["cedula", "nombres", "apellidos", "id_rol","usuario"],
  "valores" => [$ci, $nombre, $apellido, $rol, $nombre_usuario],
  "where"=>[
    ["campo"=>'cedula',"operador"=>'=', "valor" => $ci]
  ]
];

$id_correo = $usuarios->consultar([
    "campos"=>['id_correo'],
       "where"=>[
           ["campo"=>'cedula',"operador"=>'=',"valor"=>$ci]
       ]
])[0]['id_correo'];

$nuevoCorreo = [
    "campos"=>['email'],
    "valores"=>[$correo],
    "where"=>[
        ["campo"=>'id_correo',"operador"=>'=',"valor"=>$id_correo]
    ]
];
 if ($contraseña != "") {
      $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
    $datosActuales["campos"][] = "contrasena";
    $datosActuales["valores"][] = $contraseña_hash;
    } 
    
    $usuarios->editar($datosActuales);
    $correoUsuario->editar($nuevoCorreo);
    
  
    

    $f[0]();
  }

  
};
