<?php
namespace Liki;
use Liki\Validar;

abstract class Modelo {  
    public string $tabla ;  
    public array $campos ; // ['cedula' => 'isInt', 'nombres' => 'isString', ...]  
  
    public function validarCampos(array $campos): void {  
        foreach ($campos as $campo) {  
            Validar::isInclude($this->campos ?? [], $campo);  
        }  
    }  
  
    public function validarValores(array $valores): void {  
        Validar::ValidarArrayExistente($valores, $this->campos ?? []);  
    }  
  
    public function getTabla(): string { return $this->tabla ?? ''; }  
    public function getCampos(): array { return $this->campos ?? []; }  
}