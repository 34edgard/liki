<?php

namespace Liki\KitPHP;

class Num{
    private $value;
    
    public function of( $value){
        $this->value = $value;
    }
    public function toNum(){
        return $this->value;
    }
  public function add(){
     $this->value = add($this->value);   
        return $this;
    }
    
    public function subtract(){
     $this->value = subtract($this->value);   
        return $this;
    }
    
    public function multiply(){
     $this->value = multiply($this->value);   
        return $this;
    }
    public function divide(){
     $this->value = divide($this->value);   
        return $this;
    }
    public function round(){
     $this->value = round($this->value);   
        return $this;
    }
    public function format(){
     $this->value = format($this->value);   
        return $this;
    }
    public function percentage(){
     $this->value = percentage($this->value);   
        return $this;
    }
    
    
}


