<?php
namespace Liki\Controladores;
use Liki\DelegateFunction;
abstract class ControladorBase{
    public static function manejador(string $name,array $parametros =[], array $funciones =[]){
       return DelegateFunction::exec($name,$parametros,$funciones);
    }
}