<?php

namespace Liki\Coroutines;
use Generator;


/**
 * Clase Coroutine - Utilidades para trabajar con corrutinas
 */
class Coroutine {
    /**
     * Pausa la ejecución por N segundos (simulada)
     */
    public static function sleep(float $seconds): Generator {
        $start = microtime(true);
        while (microtime(true) - $start < $seconds) {
            yield; // Ceder control
        }
    }
    
    /**
     * Espera a que múltiples corrutinas completen
     */
    public static function waitAll(array $generators): Generator {
        $results = [];
        $active = [];
        
        // Inicializar todos los generadores
        foreach ($generators as $key => $generator) {
            if ($generator instanceof Generator) {
                $generator->rewind();
                $active[$key] = $generator;
            }
        }
        
        // Ejecutar cooperativamente
        while (!empty($active)) {
            foreach ($active as $key => $generator) {
                if ($generator->valid()) {
                    $generator->next();
                } else {
                    $results[$key] = $generator->getReturn();
                    unset($active[$key]);
                }
                yield; // Ceder control entre iteraciones
            }
        }
        
        return $results;
    }
    
    /**
     * Canal de comunicación entre corrutinas
     */
    public static function channel(int $capacity = 0): Channel {
        return new Channel($capacity);
    }
    
    /**
     * Crea una corrutina que se repite cada intervalo
     */
    public static function interval(float $seconds, callable $callback): Generator {
        while (true) {
            yield from self::sleep($seconds);
            $result = $callback();
            if ($result === false) { // Permite detener el intervalo
                break;
            }
            yield $result;
        }
    }
    
    /**
     * Timeout para una corrutina
     */
    public static function timeout(Generator $generator, float $seconds): Generator {
        $start = microtime(true);
        
        while ($generator->valid()) {
            if (microtime(true) - $start > $seconds) {
                throw new Exception("Timeout after {$seconds} seconds");
            }
            
            $generator->next();
            yield $generator->current();
        }
        
        return $generator->getReturn();
    }
}

