<?php
//namespace Funciones\ManejoUsuarios;
use App\Personas\Usuario;
use Liki\Plantillas\Plantilla;


return new class {
  public static function run($p) {
    session_start();
   
    extract($p);
    $datos = [
      "campos" => ["cedula", "nombres", "apellidos", "id_correo", "estado"],
    ];

    if ($_SESSION["id_rol"] == 2) {
      $datos["valor"] = $_SESSION["ci"];
    }

    $usuarios = new Usuario();
    $lista_usuarios = $usuarios->consultar($datos);

    foreach ($lista_usuarios as $usuario) {
      
  Plantilla::HTML("usuario/lista-usuarios",$usuario);
    }
  }
};
