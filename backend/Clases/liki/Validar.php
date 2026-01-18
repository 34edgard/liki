<?php
namespace Liki;


class Validar{
    public static function validacionDinamica(callable $f, $parametro){
        return $f($parametro);
    }
    public static function ValidarArray(array $parametro,array $validaviones){
        
   foreach($validaviones as $campo => $validavio){
     self::validacionDinamica( [Validar::class,$validavio],$parametro[$campo]);
   }

 }
    
    
   public static function isCallable($dato): callable{
    if(!is_callable($dato)){
        throw new \InvalidArgumentException("Expected callable"); 
    } 
     return $dato;
   }
public static function isString($dato): string{
    if(!is_string($dato)){
        throw new \InvalidArgumentException("Expected string"); 
    }
  return $dato;
}
public static function isInt($dato): int{
if(!is_int($dato)){
    throw new \InvalidArgumentException("Expected int"); 
}
  return $dato;
}
public static function isArray($dato): array{
   if(!is_array($dato)){
       throw new \InvalidArgumentException("Expected array"); 
   } 
  return $dato;
}
public static function isObject($dato): object{
    if(!is_object($dato)){
        throw new \InvalidArgumentException("Expected object"); 
    }
  return $dato;
}

public static function isBool($dato): bool{
  if(!is_bool($dato)){
      throw new \InvalidArgumentException("Expected bool"); 
  }
  return $dato;
}
public static function isFloat($dato): float{
    if(!is_float($dato)){
        throw new \InvalidArgumentException("Expected float"); 
    }
  return $dato;
}


public static function isIterable($dato): iterable{
  if(!is_iterable($dato)){
      throw new \InvalidArgumentException("Expected iterable"); 
  }

return $dato;
}

}