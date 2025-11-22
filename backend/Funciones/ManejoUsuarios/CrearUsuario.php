<?php

//namespace Funciones\ManejoUsuarios;
use App\DatosExtra\Correo;
use App\Personas\Usuario;
use Liki\Plantillas\Plantilla;


return new class {
  

  public static function run($p) {
    
    extract($p);
    
    (new Correo())->registrar([
      "campos" => ["email"],
      "valores" => [$correo],
    ]);
    $id_correo = (new Correo())->consultarId(["campos" => ["id_correo"]])[0][
      "id_correo"
    ];
    $usuarios = new Usuario();
    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
    $usuarios->registrar([
      "campos" => [
        "cedula",
        "nombres",
        "apellidos",
        "usuario",
        "id_rol",
        "id_correo",
        "contrasena",
      ],
      "valores" => [
        $cedula,
        $nombre,
        $apellido,
        $usuario,
        $rol,
        $id_correo,
        $contraseña_hash,
      ],
    ]);
 
    
    Plantilla::HTML('componentes/h1',[
        "contenido"=>'el usuario fue creado correctamente'
    ]);
    
    Plantilla::HTML('componentes/htmx',[
        "hx"=>['trigger'=>'load',"target"=>'#usuarios',"url"=>'/usuario/list']
    ]);
    
    
   
    
  }
};