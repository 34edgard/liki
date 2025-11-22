<?php

//namespace Funciones\ManejoUsuarios;
use App\DatosExtra\Correo;

return new class {
public static function run($id_correo){
    
    //echo $id_correo.'____ qwws';
  return  (new Correo())->consultar([
      "campos" => ["id_correo", "email"],
      "where"=>[
        ["campo"=>'id_correo',"operador"=>'=',"valor"=>$id_correo]
      ]  ])[0]["email"];
}
};