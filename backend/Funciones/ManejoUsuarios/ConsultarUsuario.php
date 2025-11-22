<?php

//namespace Funciones\ManejoUsuarios;
use Liki\Plantillas\Plantilla;
use App\Personas\Usuario;


return new class {
   public static function run() {
    session_start();
    $datos = [
      "campos" => ["cedula", "nombres", "apellidos", "id_correo", "estado"],
    ];

    if ($_SESSION["id_rol"] == 2) {
      $datos["where"] = [[
    "campo"=>'cedula',
    "operador"=>'=',
    "valor"=> $_SESSION["cedula"]
    ]];
    }

    $usuarios = new Usuario;
    $lista_usuarios = $usuarios->consultar($datos);
    foreach ($lista_usuarios as $usuario) {
        //print_r($usuario);
     Plantilla::HTML("usuario/lista-usuarios",$usuario);
    }
  }
};
