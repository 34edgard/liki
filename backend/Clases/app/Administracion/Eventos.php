<?php

namespace App\Administracion;
use Liki\Modelo;
use Liki\DelegateFunction;

class Eventos extends Modelo{
  
  public function __construct(){
    parent::__construct('eventos');
  }
  
}