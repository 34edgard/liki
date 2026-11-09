<?php
use App\Controladores\Personas\Usuario;
use Liki\Database\FlowDB;

use Liki\Manejadores\ManejadorBase;

return new class extends  ManejadorBase {

  public  function run(array $p =[], array $f = []) {
    extract($p);
    FlowDB::conf('Usuario')->delete(['cedula' => $ci]);
    $f[0]();
  }
};