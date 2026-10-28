<?php
use App\Controladores\DatosExtra\Correo;
use Liki\Database\FlowDB;

use Liki\Manejadores\ManejadorBase;

return new class extends  ManejadorBase {

public  function run(array $id_correo){
       
  return  FlowDB::conf('Correo')->campos(["id_correo", "email"])
          ->get(['id_correo'=>$id_correo[0]])[0]["email"];
}
};