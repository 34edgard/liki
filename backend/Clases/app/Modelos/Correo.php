<?php
use Liki\Modelo;
return new class extends Modelo{
  public string $tabla = 'correo';
  public array $campos = [
    'id_correo' => '',
  'email' => '' 
  ];
};