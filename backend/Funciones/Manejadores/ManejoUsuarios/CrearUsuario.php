<?php


use App\DatosExtra\Correo;
use App\Personas\Usuario;
use Liki\Plantillas\Flow;
use Liki\Database\FlowDB;

return new class {
  

  public static function run($p) {
    
    extract($p);
    
    FlowDB::conf(Correo::class)->campos(["email"])
                  ->post([$correo]);
    
    
    
    
    $id_correo = FlowDB::conf(Correo::class)->consultarId(["campos" => ["id_correo"]])[0][
      "id_correo"
    ];
    
    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
    FlowDB::conf(Usuario::class)->campos(["cedula","nombres","apellidos",
        "usuario","id_rol","id_correo","contrasena",
      ])
    ->post([$cedula, $nombre, $apellido,$usuario, $rol,$id_correo,$contraseña_hash ]);
 
    
    Flow::html('componentes/h1',[
        "contenido"=>'el usuario fue creado correctamente'
    ]);
    
    Flow::html('componentes/htmx',[
        "hx"=>['trigger'=>'load',"target"=>'#usuarios',"url"=>'/usuario/list']
    ]);
    
    
   
    
  }
};