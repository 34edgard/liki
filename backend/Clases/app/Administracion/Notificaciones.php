<?php

namespace App\Admini;
use Liki\Database\Tabla;
use Liki\ExecFunc;

class Notificaciones extends Tabla{
  
  public function __construct(){
    parent::__construct('notificaciones');
  }
  
}