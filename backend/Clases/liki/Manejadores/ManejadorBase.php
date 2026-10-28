<?php
namespace Liki\Manejadores;
use Liki\Database\FlowDB;
use Liki\Plantillas\Flow;
use Liki\Validar;
use Liki\Routing\CSRF;

abstract class ManejadorBase{
    private array $parametros;
    private array $funciones;
    public function __invoke(array $parametros =[],array $funciones =[]):void{
        $this->parametros = $parametros;
        $this->funciones =$funciones;
    }
    public function validar(array $reglas):array {
      return  Validar::ValidarArray($this->parametros,$reglas);
    }
    public function castear(array $mapa):array {
        
    }
    public function input(string $clave, $defaul = null){
       return $this->parametros[$clave] ?? $defaul;
    }
    public function render(string $componente,array $datos ):void {
        Flow::html($componente,$datos);
    }
    public function json(array $datos):void{
        Flow::json($datos);
    }
    public function redirigir(string $url):void{
        header('Location: '.$url);
    }
    public function requerir(array $datos){
        
    }
    public function continuar(int|string $campo,array $datos =[]){
       return $this->funciones[$campo](...$datos);
    }
    public function modelo(string $modelo):FlowDB {
       return new FlowDB($modelo);
    }
    public function transaccion(callable $fn){
        
    }
    public function csrf(){
       CSRF::validarToken($this->parametros['csrf_token']);
    }
  //  public function run(array $p =[],array $f =[]){
        
  //  }
    public function antes(){
        
    }
    public function despues(){
        
    }
    
}