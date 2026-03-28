<?php
namespace Liki;

class Validar {

    public static function isInclude(array $campos, string $id = '') {
        if(count($campos) == 0 || $id == '') return;
        if (!isset($campos[$id])) {
            throw new \Exception("Error: El campo '$id' no está incluido en el arreglo .");
        }
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

 public static function ValidarArrayExistente(array $parametro, array $validaviones) {
     foreach ($validaviones as $campo => $validavio) {
         if (!isset($parametro[$campo]))
         continue;
            // throw new \InvalidArgumentException("Expected no existe $campo");
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

    /**
     * Valida que el dato sea un email válido.
     * Convierte el dato a string (con trim) antes de validar.
     *
     * @param mixed $dato
     * @param string $campo
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function isEmail($dato, $campo): string {
        // Conversión: intentar convertir a string escalar
        if (!is_string($dato) && !is_numeric($dato) && !is_bool($dato)) {
            throw new \InvalidArgumentException("Expected scalar for email in $campo");
        }
        $dato = trim((string)$dato);
        if (!filter_var($dato, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Expected valid email in $campo");
        }
        return $dato;
    }

    /**
     * Valida que el dato sea una URL válida.
     * Convierte el dato a string (con trim) antes de validar.
     *
     * @param mixed $dato
     * @param string $campo
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function isUrl($dato, $campo): string {
        if (!is_string($dato) && !is_numeric($dato) && !is_bool($dato)) {
            throw new \InvalidArgumentException("Expected scalar for URL in $campo");
        }
        $dato = trim((string)$dato);
        if (!filter_var($dato, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException("Expected valid URL in $campo");
        }
        return $dato;
    }

    /**
     * Valida que el dato sea un número mayor o igual a un mínimo.
     * Intenta convertir el dato a número (int/float) si es posible.
     *
     * @param mixed $dato
     * @param string $campo
     * @param int|float $min
     * @return int|float
     * @throws \InvalidArgumentException
     */
    public static function isMin($dato, $campo, $min): int|float {
        // Conversión: si es una cadena numérica, convertir a número
        if (!is_numeric($dato)) {
            if (is_string($dato) && is_numeric($dato)) {
                $dato = $dato + 0; // convierte a int o float según corresponda
            } else {
                throw new \InvalidArgumentException("Expected numeric value for min validation in $campo");
            }
        }
        if ($dato < $min) {
            throw new \InvalidArgumentException("Value in $campo must be at least $min");
        }
        return $dato;
    }

    /**
     * Valida que el dato sea un número menor o igual a un máximo.
     * Intenta convertir el dato a número (int/float) si es posible.
     *
     * @param mixed $dato
     * @param string $campo
     * @param int|float $max
     * @return int|float
     * @throws \InvalidArgumentException
     */
    public static function isMax($dato, $campo, $max): int|float {
        if (!is_numeric($dato)) {
            if (is_string($dato) && is_numeric($dato)) {
                $dato = $dato + 0;
            } else {
                throw new \InvalidArgumentException("Expected numeric value for max validation in $campo");
            }
        }
        if ($dato > $max) {
            throw new \InvalidArgumentException("Value in $campo must be at most $max");
        }
        return $dato;
    }
}