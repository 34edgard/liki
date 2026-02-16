<?php
use Liki\Modelo;
return new class extends Modelo{
  public $tabla = 'roles';
  public $campos = [
    'id_rol' => '',
  'nombre_rol' => '' 
  ];
};