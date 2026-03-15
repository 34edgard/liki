<?php

namespace Liki\Coroutines;
use Generator;

/**
 * Clase Channel - Canal de comunicación entre corrutinas
 */
class Channel {
    private $queue;
    private $capacity;
    private $waitingSenders = [];
    private $waitingReceivers = [];
    
    public function __construct(int $capacity = 0) {
        $this->capacity = $capacity;
        $this->queue = new \SplQueue();
    }
    
    /**
     * Envía un valor al canal
     */
    public function send($value): Generator {
        // Si hay capacidad o hay receptores esperando
        if ($this->capacity === 0 || count($this->queue) < $this->capacity) {
            $this->queue->enqueue($value);
            
            // Notificar a receptores en espera
            if (!empty($this->waitingReceivers)) {
                $receiver = array_shift($this->waitingReceivers);
                $receiver($this->queue->dequeue());
            }
            
            return;
        }
        
        // Esperar hasta que haya capacidad
        while (count($this->queue) >= $this->capacity) {
            yield; // Ceder control
        }
        
        $this->queue->enqueue($value);
    }
    
    /**
     * Recibe un valor del canal
     */
    public function receive(): Generator {
        if (!$this->queue->isEmpty()) {
            return $this->queue->dequeue();
        }
        
        // Esperar hasta que haya un valor
        while ($this->queue->isEmpty()) {
            yield; // Ceder control
        }
        
        return $this->queue->dequeue();
    }
    
    /**
     * Cierra el canal
     */
    public function close(): void {
        // Notificar a todos los waiters
        foreach ($this->waitingReceivers as $receiver) {
            $receiver(null); // Enviar null para indicar cierre
        }
        
        foreach ($this->waitingSenders as $sender) {
            $sender(false); // Indicar fallo en envío
        }
        
        $this->waitingReceivers = [];
        $this->waitingSenders = [];
    }
    
    /**
     * Verifica si el canal está cerrado
     */
    public function isClosed(): bool {
        return false; // Simplificado para el ejemplo
    }
}


