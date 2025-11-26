<?php

//namespace Funciones\ManejoUsuarios;
use App\DatosExtra\Correo;

return new class {
public static function run($id_correo){
   // print_r((new Correo())->consultar([
      //  "campos" => ["id_correo", "email"]]
   // ));
    //echo $id_correo.'____ qwws';
  $email = (new Correo())->consultar([
      "campos" => ["id_correo", "email"],
      "where"=>[
        ["campo"=>'id_correo',"operador"=>'=',"valor"=>$id_correo]
      ]  ]);
  //  print_r($email);
    return $email[0]['email'];
    
}
};