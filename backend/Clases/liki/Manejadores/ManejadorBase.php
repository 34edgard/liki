<?php
namespace Liki\Manejadores;
use Liki\Database\FlowDB;
use Liki\Plantillas\Flow;
use Liki\Validar;
use Liki\Routing\CSRF;
use Liki\ErrorHandler;

abstract class ManejadorBase{
    private array $parametros;
    private array $funciones;
    protected bool $requireCsrf = false;
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
    public function csrf():void {
        $token = $this->parametros['csrf_token']
        ?? $_SERVER['HTTP_X_CSRF_TOKEN']
        ?? null ;
        
       if(!CSRF::validarToken($token)){
           http_response_code(403);
        ErrorHandler::getInstance()->halder(
            ErrorHandler::VALIDATION_ERROR,
            'token CSRF invalido',
            [],
            403
        );
        exit;
       }
    }

    abstract public function run(array $p = [], array $f = []);
        
    
    
    
    public function antes(array $p,array $f){
        if($this->requireCsrf) $this->csrf();
        
    }
    public function despues($res,array $p,array $f){
        
    }
}