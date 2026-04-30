<?php
use Liki\Modelo;
return new class extends Modelo{
  protected string $tabla = 'roles';
  protected array $campos = [
    'id_rol' => '',
  'nombre_rol' => '' 
  ];
};