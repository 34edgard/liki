<?php
// src/Event/EventDispatcher.php

namespace Liki;

class Eventos
{
    /**
     * Lista de listeners agrupados por nombre de evento
     * @var array<string, array<callable>>
     */
    private array $listeners = [];

    /**
     * Registrar un listener para un evento
     */
    public function listen(string $eventName, callable $listener, int $priority = 0): void
    {
        $this->listeners[$eventName][$priority][] = $listener;
    }

    /**
     * Emitir/despachar un evento
     */
    public function dispatch(string $eventName, mixed $payload = null): mixed
    {
        if (empty($this->listeners[$eventName])) {
            return $payload;
        }

        // Ordenar por prioridad (mayor primero)
        krsort($this->listeners[$eventName]);

        foreach ($this->listeners[$eventName] as $listenersByPriority) {
            foreach ($listenersByPriority as $listener) {
                $result = $listener($payload);
                // Si el listener devuelve algo, actualiza el payload (útil para filtros)
                if ($result !== null) {
                    $payload = $result;
                }
            }
        }

        return $payload;
    }

    /**
     * Verificar si hay listeners
     */
    public function hasListeners(string $eventName): bool
    {
        return !empty($this->listeners[$eventName]);
    }

    /**
     * Eliminar listeners de un evento
     */
    public function forget(string $eventName): void
    {
        unset($this->listeners[$eventName]);
    }
}