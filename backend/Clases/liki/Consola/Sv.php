<?php

class Sv
{
    /**
     * Inicia el servidor de desarrollo de PHP en segundo plano.
     *
     * @param string $host    Host o IP (por defecto 'localhost').
     * @param int    $port    Puerto (por defecto 8000).
     * @param string $docRoot Directorio raíz de documentos (por defecto '.').
     * @return bool           Devuelve true si el comando se ejecutó sin errores evidentes.
     */
    public function start(string $host = 'localhost', int $port = 8000, string $docRoot = '.'): bool
    {
        // Validación básica para evitar inyección de comandos en el host.
        if (!filter_var($host, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME) && $host !== 'localhost') {
            // Permitir IPs explícitamente.
            if (!filter_var($host, FILTER_VALIDATE_IP)) {
                throw new InvalidArgumentException("Host inválido: $host");
            }
        }

        // Validar que el puerto esté en el rango permitido.
        if ($port < 1 || $port > 65535) {
            throw new InvalidArgumentException("Puerto inválido: $port");
        }

        // Asegurar que el directorio raíz exista.
        $realDocRoot = realpath($docRoot);
        if ($realDocRoot === false || !is_dir($realDocRoot)) {
            throw new RuntimeException("El directorio raíz no existe: $docRoot");
        }

        // Construir el comando.
        // -S: Servidor integrado.
        // -t: Especifica el document root.
        // > /dev/null 2>&1 &: Redirige la salida y envía el proceso al fondo (Linux/Unix).
        // En Windows, el manejo de & es diferente, pero se asume un entorno tipo Unix para este ejemplo.
        $command = sprintf(
            'php -S %s:%d -t %s > /dev/null 2>&1 &',
            escapeshellarg($host),
            $port,
            escapeshellarg($realDocRoot)
        );

        // Ejecutar el comando.
        // shell_exec devuelve null si no hay salida (lo cual es esperado aquí debido a la redirección).
        shell_exec($command) ;

        // Una pequeña pausa para permitir que el servidor se inicie.
        usleep(500000); // 0.5 segundos

        return true;
    }
}


$sv = new Sv();
$sv->start('127.0.0.1', 8080, __DIR__ . '/');
echo "Servidor iniciado (o intentado) en http://127.0.0.1:8080\n";