<?php

namespace Liki\Coroutines;
use Generator;
/**
 * Clase CoroutineManager - Gestor avanzado de corrutinas en PHP
 * Implementa patrones de concurrencia cooperativa usando generadores
 */
class CoroutineManager {
    private $coroutines = [];
    private $scheduler;
    private $taskIdCounter = 0;
    private $events = [];
    private $running = false;
    
    /**
     * Constructor - Inicializa el gestor de corrutinas
     */
    public function __construct() {
        $this->scheduler = new Scheduler();
        $this->initEventSystem();
    }
    
    /**
     * Crea una nueva corrutina a partir de un generador
     * 
     * @param Generator $generator Función generadora
     * @param mixed $data Datos iniciales para la corrutina
     * @param string $name Nombre opcional para la corrutina
     * @return int ID de la tarea creada
     */
    public function create(Generator $generator, $data = null, $name = null): int {
        $taskId = ++$this->taskIdCounter;
        
        $coroutine = [
            'id' => $taskId,
            'name' => $name ?: "coroutine_$taskId",
            'generator' => $generator,
            'data' => $data,
            'status' => 'pending',
            'created_at' => microtime(true),
            'started_at' => null,
            'finished_at' => null,
            'result' => null,
            'exception' => null
        ];
        
        $this->coroutines[$taskId] = $coroutine;
        $this->scheduler->addTask($generator, $data);
        
        $this->emit('coroutine.created', [
            'id' => $taskId,
            'name' => $coroutine['name']
        ]);
        
        return $taskId;
    }
    
    /**
     * Ejecuta todas las corrutinas de forma cooperativa
     */
    public function run(): void {
        $this->running = true;
        $this->emit('manager.started');
        
        try {
            $this->scheduler->run();
            
            // Actualizar estado de las corrutinas completadas
            foreach ($this->scheduler->getCompletedTasks() as $taskId => $result) {
                if (isset($this->coroutines[$taskId])) {
                    $this->coroutines[$taskId]['status'] = 'completed';
                    $this->coroutines[$taskId]['finished_at'] = microtime(true);
                    $this->coroutines[$taskId]['result'] = $result;
                    
                    $this->emit('coroutine.completed', [
                        'id' => $taskId,
                        'result' => $result
                    ]);
                }
            }
            
        } catch (Exception $e) {
            $this->emit('manager.error', ['exception' => $e]);
            throw $e;
        } finally {
            $this->running = false;
            $this->emit('manager.stopped');
        }
    }
    
    /**
     * Pausa una corrutina específica
     */
    public function pause(int $taskId, $seconds = null): bool {
        if (!isset($this->coroutines[$taskId])) {
            return false;
        }
        
        $this->coroutines[$taskId]['status'] = 'paused';
        
        if ($seconds !== null) {
            // Programar reanudación automática
            $this->schedule(function() use ($taskId) {
                $this->resume($taskId);
            }, $seconds);
        }
        
        $this->emit('coroutine.paused', ['id' => $taskId]);
        return true;
    }
    
    /**
     * Reanuda una corrutina pausada
     */
    public function resume(int $taskId): bool {
        if (!isset($this->coroutines[$taskId]) || 
            $this->coroutines[$taskId]['status'] !== 'paused') {
            return false;
        }
        
        $this->coroutines[$taskId]['status'] = 'running';
        $this->scheduler->resumeTask($taskId);
        
        $this->emit('coroutine.resumed', ['id' => $taskId]);
        return true;
    }
    
    /**
     * Cancela una corrutina
     */
    public function cancel(int $taskId): bool {
        if (!isset($this->coroutines[$taskId])) {
            return false;
        }
        
        $this->coroutines[$taskId]['status'] = 'cancelled';
        $this->coroutines[$taskId]['finished_at'] = microtime(true);
        $this->scheduler->removeTask($taskId);
        
        $this->emit('coroutine.cancelled', ['id' => $taskId]);
        return true;
    }
    
    /**
     * Programa una función para ejecución diferida
     */
    public function schedule(callable $callback, $delaySeconds = 0): int {
        $taskId = ++$this->taskIdCounter;
        
        $generator = (function() use ($callback, $delaySeconds) {
            if ($delaySeconds > 0) {
                yield from $this->sleep($delaySeconds);
            }
            return $callback();
        })();
        
        return $this->create($generator, null, "scheduled_task_$taskId");
    }
    
    /**
     * Ejecuta múltiples corrutinas en paralelo (cooperativo)
     * 
     * @param array $coroutines Array de generadores
     * @return array Resultados de todas las corrutinas
     */
    public function parallel(array $coroutines): array {
        $taskIds = [];
        $results = [];
        
        foreach ($coroutines as $index => $coroutine) {
            if ($coroutine instanceof Generator) {
                $taskId = $this->create($coroutine, null, "parallel_$index");
                $taskIds[$taskId] = $index;
            }
        }
        
        $this->run();
        
        foreach ($taskIds as $taskId => $index) {
            if (isset($this->coroutines[$taskId])) {
                $results[$index] = $this->coroutines[$taskId]['result'];
            }
        }
        
        return $results;
    }
    
    /**
     * Espera a que múltiples corrutinas completen
     */
    public function wait(array $taskIds, $timeout = null): array {
        $startTime = microtime(true);
        $completed = [];
        
        while (count($completed) < count($taskIds)) {
            // Verificar timeout
            if ($timeout !== null && 
                (microtime(true) - $startTime) > $timeout) {
                break;
            }
            
            // Ejecutar una iteración del scheduler
            $this->scheduler->tick();
            
            // Verificar corrutinas completadas
            foreach ($taskIds as $taskId) {
                if (!isset($completed[$taskId]) && 
                    isset($this->coroutines[$taskId]) &&
                    $this->coroutines[$taskId]['status'] === 'completed') {
                    $completed[$taskId] = $this->coroutines[$taskId]['result'];
                }
            }
            
            // Pequeña pausa para evitar uso excesivo de CPU
            usleep(1000);
        }
        
        return $completed;
    }
    
    /**
     * Helper: Simula una operación de I/O asíncrona
     */
    public static function asyncReadFile(string $path): Generator {
        $start = microtime(true);
        
        // Simulamos I/O asíncrono
        yield from Coroutine::sleep(0.1);
        
        if (!file_exists($path)) {
            throw new Exception("Archivo no encontrado: $path");
        }
        
        $content = file_get_contents($path);
        
        return [
            'content' => $content,
            'size' => strlen($content),
            'duration' => microtime(true) - $start
        ];
    }
    
    /**
     * Helper: Simula una petición HTTP asíncrona
     */
    public static function asyncHttpRequest(string $url, array $options = []): Generator {
        $start = microtime(true);
        
        // Simulamos latencia de red
        yield from Coroutine::sleep(0.3);
        
        $ch = curl_init($url);
        curl_setopt_array($ch, $options + [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 10,
        ]);
        
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        return [
            'response' => $response,
            'info' => $info,
            'error' => $error,
            'duration' => microtime(true) - $start
        ];
    }
    
    /**
     * Helper: Procesa datos en lotes de forma asíncrona
     */
    public static function asyncBatchProcess(array $items, callable $processor, int $batchSize = 10): Generator {
        $results = [];
        $batches = array_chunk($items, $batchSize);
        
        foreach ($batches as $batchIndex => $batch) {
            $batchResults = [];
            
            foreach ($batch as $itemIndex => $item) {
                // Procesar cada item con posibilidad de pausa
                $result = $processor($item);
                $batchResults[] = $result;
                
                // Ceder control después de cada item
                yield [
                    'batch' => $batchIndex,
                    'item' => $itemIndex,
                    'progress' => ($batchIndex * $batchSize + $itemIndex + 1) / count($items)
                ];
            }
            
            $results = array_merge($results, $batchResults);
            
            // Ceder control después de cada lote
            yield [
                'batch_completed' => $batchIndex,
                'total_progress' => (($batchIndex + 1) * $batchSize) / count($items)
            ];
        }
        
        return $results;
    }
    
    /**
     * Obtiene información de una corrutina específica
     */
    public function getCoroutineInfo(int $taskId): ?array {
        return $this->coroutines[$taskId] ?? null;
    }
    
    /**
     * Obtiene información de todas las corrutinas
     */
    public function getAllCoroutines(): array {
        return $this->coroutines;
    }
    
    /**
     * Limpia corrutinas completadas
     */
    public function cleanup(): int {
        $count = 0;
        
        foreach ($this->coroutines as $taskId => $coroutine) {
            if (in_array($coroutine['status'], ['completed', 'cancelled', 'failed'])) {
                unset($this->coroutines[$taskId]);
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * Verifica si el gestor está ejecutándose
     */
    public function isRunning(): bool {
        return $this->running;
    }
    
    /**
     * Inicializa el sistema de eventos
     */
    private function initEventSystem(): void {
        $this->events = [
            'coroutine.created' => [],
            'coroutine.started' => [],
            'coroutine.paused' => [],
            'coroutine.resumed' => [],
            'coroutine.completed' => [],
            'coroutine.cancelled' => [],
            'coroutine.failed' => [],
            'manager.started' => [],
            'manager.stopped' => [],
            'manager.error' => []
        ];
    }
    
    /**
     * Registra un listener para eventos
     */
    public function on(string $event, callable $listener): void {
        if (isset($this->events[$event])) {
            $this->events[$event][] = $listener;
        }
    }
    
    /**
     * Emite un evento
     */
    private function emit(string $event, array $data = []): void {
        if (isset($this->events[$event])) {
            foreach ($this->events[$event] as $listener) {
                $listener($data);
            }
        }
    }
}

