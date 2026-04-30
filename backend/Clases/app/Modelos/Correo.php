<?php
use Liki\Modelo;
return new class extends Modelo{
  protected string $tabla = 'correo';
  protected array $campos = [
    'id_correo' => '',
  'email' => '' 
  ];
};