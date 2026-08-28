<?php

namespace Liki\KitPHP;

class Arr{

public function map(){
 $this->value = map($this->value);   
    return $this;
}
public function filter(){
 $this->value = filter($this->value);   
    return $this;
}
public function reduce(){
 $this->value = reduce($this->value);   
    return $this;
}
public function pluck(){
 $this->value = pluck($this->value);   
    return $this;
}
public function sort(){
 $this->value = sort($this->value);   
    return $this;
}
public function reverse(){
 $this->value = reverse($this->value);   
    return $this;
}
public function unique(){
 $this->value = unique($this->value);   
    return $this;
}

public function merge(){
 $this->value = merge($this->value);   
    return $this;
}

public function first(){
 $this->value = first($this->value);   
    return $this;
}
public function last(){
 $this->value = last($this->value);   
    return $this;
}
public function chunk(){
 $this->value = chunk($this->value);   
    return $this;
}
public function groupBy(){
 $this->value = groupBy($this->value);   
    return $this;
}
 

}

