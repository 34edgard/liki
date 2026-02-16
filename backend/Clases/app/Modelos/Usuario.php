<?php
use Liki\Modelo;
return new class extends Modelo{
  public $tabla = 'usuario';
  public $campos = [
    'id_usuario' => '',
  'cedula' => '' ,
  'nombres' => '',
  'apellidos' => '' ,
  'id_rol'  => ''  ,
  'usuario' => ''  ,
  'id_correo'  => '' ,
  'contrasena'  => '' ,
  'estado'  => '' 
  ];
};