<?php
namespace Liki;
use Liki\Validar;

abstract class Modelo {  
    protected string $tabla ;  
    protected array $campos ; // ['cedula' => 'isInt', 'nombres' => 'isString', ...]  
  
    public function validarCampos(array $campos): void {  
        foreach ($campos as $campo) {  
            Validar::isInclude($this->campos ?? [], $campo);  
        }  
    }  
  
    public function validarValores(array $valores, array $camposValidar =[]): void {  
     $campos = [];
    $valoresArray=[];
    foreach($camposValidar as $i => $campo){
        if($this->campos[$campo] == '') continue;
     $campos[$campo] = $this->campos[$campo];
     $valoresArray[$campo] = $valores[$i];
    }
   // print_r($valoresArray);
       Validar::ValidarArray($valoresArray, $campos);  
    }  
  
    public function getTabla(): string { return $this->tabla ?? ''; }  
    public function getCampos(): array { return $this->campos ?? []; }  
}