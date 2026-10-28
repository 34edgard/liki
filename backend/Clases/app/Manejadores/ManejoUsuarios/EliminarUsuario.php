<?php
use App\Controladores\Personas\Usuario;
use Liki\Database\FlowDB;

use Liki\Manejadores\ManejadorBase;

return new class extends  ManejadorBase {

  public  function run($p, $f) {
    extract($p);
    FlowDB::conf('Usuario')->delete(['cedula' => $ci]);
    $f[0]();
  }
};