<?php
namespace App\Controladores\Administracion;
use Liki\Modelo;
use Liki\DelegateFunction;

class Notificaciones extends Modelo{
  public function __construct(){
    parent::__construct('notificaciones');
  }
}