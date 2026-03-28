<?php
use Liki\Modelo;
return new class extends Modelo{
  public string $tabla = 'usuario';
  public array $campos = [
    'id_usuario' => 'isInt',
  'cedula' => 'isString' ,
  'nombres' => 'isString',
  'apellidos' => 'isString' ,
  'id_rol'  => 'isInt'  ,
  'usuario' => 'isString'  ,
  'id_correo'  => 'isInt' ,
  'contrasena'  => 'isString' ,
  'estado'  => 'isArray' 
  ];
};


  
  