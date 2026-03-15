<?php

namespace Liki\Coroutines;
use Generator;


/**
 * Clase Promise - Implementación simple de promesas para corrutinas
 */
class Promise {
    private $value = null;
    private $error = null;
    private $callbacks = [];
    private $errorCallbacks = [];
    private $state = 'pending'; // pending, fulfilled, rejected
    
    public function then(callable $onFulfilled = null, callable $onRejected = null): self {
        if ($this->state === 'fulfilled' && $onFulfilled) {
            try {
                $result = $onFulfilled($this->value);
                if ($result instanceof self) {
                    return $result;
                }
            } catch (Exception $e) {
                return self::reject($e);
            }
        } elseif ($this->state === 'rejected' && $onRejected) {
            try {
                $result = $onRejected($this->error);
                if ($result instanceof self) {
                    return $result;
                }
            } catch (Exception $e) {
                return self::reject($e);
            }
        } else {
            if ($onFulfilled) {
                $this->callbacks[] = $onFulfilled;
            }
            if ($onRejected) {
                $this->errorCallbacks[] = $onRejected;
            }
        }
        
        return $this;
    }
    
    public static function resolve($value = null): self {
        $promise = new self();
        $promise->fulfill($value); // Antes decía ->resolve() y causaba error
        return $promise;
    }
    
    public static function reject($error): self {
        $promise = new self();
        $promise->fail($error); // Antes decía ->reject() y causaba error
        return $promise;
    }
    
    public static function all(array $promises): self {
        return new self(function($resolve, $reject) use ($promises) {
            $results = [];
            $remaining = count($promises);
            
            foreach ($promises as $index => $promise) {
                $promise->then(
                    function($value) use ($index, &$results, &$remaining, $resolve) {
                        $results[$index] = $value;
                        $remaining--;
                        
                        if ($remaining === 0) {
                            $resolve($results);
                        }
                    },
                    $reject
                );
            }
        });
    }
    
    public function fulfill($value = null): void {
        if ($this->state !== 'pending') {
            return;
        }
        
        $this->state = 'fulfilled';
        $this->value = $value;
        
        foreach ($this->callbacks as $callback) {
            $callback($value);
        }
        
        $this->callbacks = [];
    }
    
    public function fail($error): void {
        if ($this->state !== 'pending') {
            return;
        }
        
        $this->state = 'rejected';
        $this->error = $error;
        
        foreach ($this->errorCallbacks as $callback) {
            $callback($error);
        }
        
        $this->errorCallbacks = [];
    }
}