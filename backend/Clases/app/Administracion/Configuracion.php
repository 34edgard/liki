<?php

namespace App\Administracion;
use Liki\Modelo;
use Liki\DelegateFunction;

class Configuracion extends Modelo{
  
  public function __construct(){
    parent::__construct('configuracion');
  }
  
}