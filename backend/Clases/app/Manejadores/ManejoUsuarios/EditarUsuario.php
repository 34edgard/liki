<?php
use App\Controladores\Personas\Usuario;
use App\Controladores\DatosExtra\Correo;
use Liki\Database\FlowDB;

use Liki\Manejadores\ManejadorBase;

return new class extends  ManejadorBase {

  public  function run($p, $f) {

    extract($p);

    $campos = ["cedula",
      "nombres",
      "apellidos",
      "id_rol",
      "usuario"];
    $ci = (int)$ci;
    $rol = (int)$rol;
    
    
    $valores = [$ci,
      $nombre,
      $apellido,
      $rol,
      $nombre_usuario];

    $id_correo = FlowDB::conf('Usuario')->campos(['id_correo'])
    ->get(['cedula' => $ci])[0]['id_correo'];


    if ($contraseña != "") {
      $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
      $campos[] = "contrasena";
      $valores[] = $contraseña_hash;
    }

    FlowDB::conf('Usuario')->campos($campos)
    ->valores($valores)
    ->put(['cedula' => $ci]);
    FlowDB::conf('Correo')->campos(['email'])
    ->valores([$correo])
    ->put(['id_correo' => $id_correo]);

    $f[0]();
  }
};