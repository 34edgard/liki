<?php
use App\Controladores\DatosExtra\Correo;
use Liki\Database\FlowDB;

return new class {
public static function run($id_correo){
       
  return  FlowDB::conf('Correo')->campos(["id_correo", "email"])
          ->get(['id_correo'=>$id_correo])[0]["email"];
}
};