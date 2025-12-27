<?php
namespace Liki;

abstract class Modelo {
    public $tablaNombre;
    
  public function __construct(string $tablaNombre){
      $this->tablaNombre = $tablaNombre;;
  }
  
  public function setTableName(string $tablaNombre){
      $this->tablaNombre = $tablaNombre;
  }
  
  public function getTableName():string {
      return $this->tablaNombre;
  }
}



