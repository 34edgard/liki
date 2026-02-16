<?php
namespace Liki;

class Validar {


  public static function isInclude(array $campos, string $id = '') {
    if(count($campos) == 0 || $id == '') return;
     if (!isset($campos[$id])) {
        throw new \Exception("Error: El campo '$id' no está incluido en el arreglo .");
      }
  // Aquí también podrías validar el tipo de dato
  }
  public static function validacionDinamica(callable $f, $parametro, $campo) {
    return $f($parametro, $campo);
  }
  public static function ValidarArray(array $parametro, array $validaviones) {
    foreach ($validaviones as $campo => $validavio) {
      if (!isset($parametro[$campo]))
        throw new \InvalidArgumentException("Expected no existe $campo");
      self::validacionDinamica([Validar::class, $validavio], $parametro[$campo], $campo);
    }
  }
  public static function isCallable($dato, $campo): callable {
    if (!is_callable($dato))
      throw new \InvalidArgumentException("Expected callable in $campo");
    return $dato;
  }
  public static function isString($dato, $campo): string {
    if (!is_string($dato))
      throw new \InvalidArgumentException("Expected string in $campo");
    return $dato;
  }
  public static function isInt($dato, $campo): int {
    if (!is_int($dato))
      throw new \InvalidArgumentException("Expected int in $campo");
    return $dato;
  }
  public static function isArray($dato, $campo): array {
    if (!is_array($dato))
      throw new \InvalidArgumentException("Expected array in $campo");
    return $dato;
  }
  public static function isObject($dato, $campo): object {
    if (!is_object($dato))
      throw new \InvalidArgumentException("Expected object in $campo");
    return $dato;
  }

  public static function isBool($dato, $campo): bool {
    if (!is_bool($dato))
      throw new \InvalidArgumentException("Expected bool in $campo");
    return $dato;
  }
  public static function isFloat($dato, $campo): float {
    if (!is_float($dato))
      throw new \InvalidArgumentException("Expected float in $campo");
    return $dato;
  }
  public static function isIterable($dato, $campo): iterable {
    if (!is_iterable($dato))
      throw new \InvalidArgumentException("Expected iterable in $campo");
    return $dato;
  }
}