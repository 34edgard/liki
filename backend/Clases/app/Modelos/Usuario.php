<?php
use Liki\Modelo;
return new class extends Modelo{
  protected string $tabla = 'usuario';
  protected array $campos = [
    'id_usuario' => 'isInt',
  'cedula' => 'isInt' ,
  'nombres' => 'isString',
  'apellidos' => 'isString' ,
  'id_rol'  => 'isInt'  ,
  'usuario' => 'isString'  ,
  'id_correo'  => 'isInt' ,
  'contrasena'  => 'isString' ,
  'estado'  => 'isString' 
  ];
};


  
  