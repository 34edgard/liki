<?php
use Liki\Modelo;
return new class extends Modelo{
  public string $tabla = 'roles';
  public array $campos = [
    'id_rol' => '',
  'nombre_rol' => '' 
  ];
};