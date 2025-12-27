<?php
use App\Personas\Usuario;
use App\DatosExtra\Correo;
use Liki\Database\Tabla;

return new class {
  public static function run($p,$f) {
    
    extract($p);
    
    

  $campos =["cedula", "nombres", "apellidos", "id_rol","usuario"];
  $valores =[$ci, $nombre, $apellido, $rol, $nombre_usuario];
  
$id_correo = Tabla::conf(Usuario::class)->campos(['id_correo'])
                       ->get(['cedula'=>$ci])[0]['id_correo'];


 if ($contraseña != "") {
      $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
    $campos[] = "contrasena";
    $valores[] = $contraseña_hash;
    } 
    
    Tabla::conf(Usuario::class)->campos($campos)
             ->valores($valores)
             ->put(['cedula'=>$ci]);
    Tabla::conf(Correo::class)->campos(['email'])
                  ->valores([$correo])
                  ->put(['id_correo'=>$id_correo]);
    
  
    

    $f[0]();
  }

  
};
