<?php
// namespace Funciones\ManejoUsuarios;
use App\Personas\Usuario;

return new class {
  
  
  public static function run($p,$f) {
    
    extract( $p);
  //  print_r($_GET);
    $usuarios = new Usuario();

    $usuarios->eliminar(["campos" => ["cedula"], 
      "where"=>[
        ["campo"=>'cedula',"operador"=>'=',
      "valor" => $ci]
      ]
    ]);
   $f[0]();
  }
};
