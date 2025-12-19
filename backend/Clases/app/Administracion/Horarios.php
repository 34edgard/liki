<?php

namespace App\Administracion;
use Liki\Database\Tabla;
use Liki\DelegateFunction;

class Horarios extends Tabla{
  
  public function __construct(){
    parent::__construct('horarios');
  }
  
}