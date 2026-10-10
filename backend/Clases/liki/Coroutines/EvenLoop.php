<?php
namespace Liki\Coroutines;

class EventLoop
{
    /** @var array<int, resource> Streams a monitorear para lectura */
    private array $readStreams = [];

    /** @var array<int, callable> Callbacks asociados a cada stream */
    private array $readCallbacks = [];

    /** @var array<int, array{0: callable, 1: array}> Tareas diferidas (timers, microtasks) */
    private array $timers = [];

    private bool $running = false;

    /**
     * Registra un stream para ser monitoreado en modo lectura.
     */
    public function addReadStream($stream, callable $callback): void
    {
        $id = (int) $stream;
        $this->readStreams[$id] = $stream;
        $this->readCallbacks[$id] = $callback;
    }

    public function removeReadStream($stream): void
    {
        $id = (int) $stream;
        unset($this->readStreams[$id], $this->readCallbacks[$id]);
    }

    /**
     * Agenda una tarea para ejecutarse después de $delay segundos.
     */
    public function addTimer(float $delay, callable $callback): void
    {
        $this->timers[] = [
            'runAt' => microtime(true) + $delay,
            'callback' => $callback,
        ];
    }

    /**
     * Detiene el loop.
     */
    public function stop(): void
    {
        $this->running = false;
    }

    /**
     * El corazón del event loop.
     */
    public function run(): void
    {
        $this->running = true;

        while ($this->running) {
            // 1. Calcular timeout basado en el próximo timer
            $timeout = $this->nextTimerTimeout();

            // 2. Esperar por actividad en streams o hasta el próximo timer
            $read = array_values($this->readStreams);
            $write = null;
            $except = null;

            if (empty($read)) {
                // Sin streams: dormimos hasta el próximo timer
                if ($timeout !== null) {
                    usleep((int) ($timeout * 1_000_000));
                }
            } else {
                $sec = $timeout !== null ? (int) $timeout : 1;
                $usec = $timeout !== null ? (int) (($timeout - $sec) * 1_000_000) : 0;

                $changed = @stream_select($read, $write, $except, $sec, $usec);

                if ($changed > 0) {
                    foreach ($read as $stream) {
                        $id = (int) $stream;
                        if (isset($this->readCallbacks[$id])) {
                            ($this->readCallbacks[$id])($stream, $this);
                        }
                    }
                }
            }

            // 3. Ejecutar timers vencidos
            $this->runDueTimers();
        }
    }

    private function nextTimerTimeout(): ?float
    {
        if (empty($this->timers)) {
            return null;
        }

        $now = microtime(true);
        $min = null;

        foreach ($this->timers as $timer) {
            $remaining = $timer['runAt'] - $now;
            if ($remaining < 0) {
                $remaining = 0;
            }
            if ($min === null || $remaining < $min) {
                $min = $remaining;
            }
        }

        return $min;
    }

    private function runDueTimers(): void
    {
        $now = microtime(true);

        foreach ($this->timers as $key => $timer) {
            if ($timer['runAt'] <= $now) {
                unset($this->timers[$key]);
                ($timer['callback'])($this);
            }
        }

        // Reindexar
        $this->timers = array_values($this->timers);
    }
}









