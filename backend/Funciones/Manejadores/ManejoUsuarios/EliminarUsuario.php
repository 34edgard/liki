<?php
use App\Controladores\Personas\Usuario;
use Liki\Database\FlowDB;


return new class {
  public static function run($p,$f) {
    
 extract($p); 

FlowDB::conf(Usuario::class)->delete(['cedula'=> $ci]);
   $f[0]();
  }
};