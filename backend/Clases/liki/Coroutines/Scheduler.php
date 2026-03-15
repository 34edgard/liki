<?php

namespace Liki\Coroutines;
use Generator;


/**
 * Clase Scheduler - Planificador de tareas cooperativas
 */
class Scheduler {
    private $tasks = [];
    private $taskIdCounter = 0;
    private $completedTasks = [];
    private $taskMap = [];
    
    /**
     * Agrega una tarea al planificador
     */
    public function addTask(Generator $coroutine, $data = null): int {
        $taskId = ++$this->taskIdCounter;
        
        $this->tasks[$taskId] = [
            'coroutine' => $coroutine,
            'data' => $data,
            'started' => false,
            'completed' => false,
            'exception' => null
        ];
        
        $this->taskMap[$taskId] = $taskId;
        
        return $taskId;
    }
/**
 * Ejecuta todas las tareas de forma cooperativa e intercalada
 */
public function run(): void {
    while (!empty($this->tasks)) {
        $this->tick();
    }
}

/**
 * Ejecuta un solo paso de la tarea actual y la rota
 */
public function tick(): bool {
    if (empty($this->tasks)) {
        return false;
    }

    // 1. Tomamos la primera tarea de la lista
    $taskId = array_key_first($this->tasks);
    $task = $this->tasks[$taskId];
    // 2. La quitamos temporalmente de la lista para moverla al final
    unset($this->tasks[$taskId]);

    try {
        if (!$task['started']) {
            $task['coroutine']->rewind();
            $task['started'] = true;
        }

        if ($task['coroutine']->valid()) {
            // Ejecutamos solo UN paso (hasta el siguiente yield)
            $task['coroutine']->next();
        }

        // 3. Si la tarea todavía tiene más pasos, la volvemos a meter al FINAL
        if ($task['coroutine']->valid()) {
            $this->tasks[$taskId] = $task;
        } else {
            // Si terminó, guardamos el resultado
            $this->completedTasks[$taskId] = $task['coroutine']->getReturn();
        }

    } catch (Exception $e) {
        $this->completedTasks[$taskId] = null;
    }

    return !empty($this->tasks);
}  
    /**
     * Reanuda una tarea específica
     */
    public function resumeTask(int $taskId): bool {
        if (!isset($this->tasks[$taskId])) {
            return false;
        }
        
        $this->tasks[$taskId]['paused'] = false;
        return true;
    }
    
    /**
     * Elimina una tarea
     */
    public function removeTask(int $taskId): bool {
        if (isset($this->tasks[$taskId])) {
            unset($this->tasks[$taskId]);
            return true;
        }
        return false;
    }
    
    /**
     * Obtiene tareas completadas
     */
    public function getCompletedTasks(): array {
        return $this->completedTasks;
    }
    
    /**
     * Obtiene tareas pendientes
     */
    public function getPendingTasks(): array {
        return $this->tasks;
    }
}

