<?php

use App\DatosExtra\Correo;
use Liki\Database\Tabla;
return new class {
public static function run($id_correo){
    
    
  return  Tabla::conf(Correo::class)->campos(["id_correo", "email"])
          ->get(['id_correo'=>$id_correo])[0]["email"];
}
};