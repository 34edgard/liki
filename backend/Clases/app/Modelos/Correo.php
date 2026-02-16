<?php
use Liki\Modelo;
return new class extends Modelo{
  public $tabla = 'correo';
  public $campos = [
    'id_correo' => '',
  'email' => '' 
  ];
};