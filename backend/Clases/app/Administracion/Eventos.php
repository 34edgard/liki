<?php

namespace App\Administracion;
use Liki\Database\Tabla;
use Liki\DelegateFunction;

class Eventos extends Tabla{
  
  public function __construct(){
    parent::__construct('eventos');
  }
  
}