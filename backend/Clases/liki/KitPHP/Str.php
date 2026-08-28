<?php

namespace Liki\KitPHP;

class Str{
    private string $value;
    public static function of(string $value):self {
        $instance = new self(); 
        $instance->value = $value; 
        return $instance;
    }
    public function trim(): self{
        $this->value = trim($this->value);
        return $this;
    }
    
    public function replace($text,$text2): self{
       $this->value = str_replace($text,$text2,$this->value);
    
      return $this;
    }
    
    public function contains(string $needle): bool {
         return str_contains($this->value, $needle); 
        } 
    public function upper(): self { 
    $this->value = mb_strtoupper($this->value);
     return $this; 
    }
    
    public function slug():self{ 
         $this->value = slug($this->value);
        return $this;
    }
    
    public function toString():string {
        return $this->value;
    }
    
    public function len(){
       return strlen($this->value);
    }
    
    public function lower(){
        
        $this->value =  strtolower($this->value);
        return $this;
    }
    public function startsWith(){
            $this->value =  strtostartsWith($this->value);
            return $this;
    }
}




/*
 * 
 * 
 * Día 1-2: Clase Str con 
 * 20 métodos 
 * (   , endsWith, replace, split, substr, pad, slug, toCamel, toSnake, etc.)
 * 
 * 
 * 
 */

$text = Str::of(" Hola MUuuuUUUndo ") 


 ->startsWith(' ');
 

 //->toString(); // "hola-mundo" 
echo $text;
