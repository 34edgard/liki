<?php

use App\Personas\Usuario;
use Liki\Database\Tabla;
return new class {
  
  
  public static function run($p,$f) {
    
    extract( $p);
  //  print_r($_GET);


Tabla::conf(Usuario::class)->delete(['cedula'=> $ci]);
   $f[0]();
  }
};
