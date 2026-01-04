<?php

namespace Liki\WebSockets;
/**
 * Clase WebSocketServer - Implementación de servidor WebSocket en PHP
 * Compatible con RFC 6455
 */
class WebSocketServer {
    private $socket;
    private $clients = [];
    private $origins = [];
    private $handshakeHeaders = [];
    
    /**
     * Constructor del servidor WebSocket
     * @param string $host Dirección IP o hostname
     * @param int $port Puerto del servidor
     * @param array $options Opciones adicionales
     */
    public function __construct($host = '0.0.0.0', $port = 8080, $options = []) {
        $this->host = $host;
        $this->port = $port;
        $this->options = array_merge([
            'max_clients' => 100,
            'timeout' => 60,
            'buffer_size' => 4096,
            'debug' => false
        ], $options);
    }
    
    /**
     * Inicia el servidor WebSocket
     */
    public function start() {
        $this->log("Iniciando servidor WebSocket en {$this->host}:{$this->port}");
        
        // Crear socket
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        
        if ($this->socket === false) {
            $this->log("Error al crear socket: " . socket_strerror(socket_last_error()));
            return false;
        }
        
        // Configurar opciones del socket
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_option($this->socket, SOL_SOCKET, SO_KEEPALIVE, 1);
        
        // Vincular socket
        if (!socket_bind($this->socket, $this->host, $this->port)) {
            $this->log("Error al vincular socket: " . socket_strerror(socket_last_error($this->socket)));
            return false;
        }
        
        // Escuchar conexiones
        if (!socket_listen($this->socket, $this->options['max_clients'])) {
            $this->log("Error al escuchar conexiones: " . socket_strerror(socket_last_error($this->socket)));
            return false;
        }
        
        $this->log("Servidor WebSocket iniciado exitosamente");
        
        return true;
    }
    
    /**
     * Ejecuta el bucle principal del servidor
     */
    public function run() {
        $read = [$this->socket];
        $write = $except = null;
        
        while (true) {
            $changed = $read;
            
            // Esperar cambios en los sockets
            socket_select($changed, $write, $except, 0, 10);
            
            // Nuevas conexiones
            if (in_array($this->socket, $changed)) {
                $clientSocket = socket_accept($this->socket);
                
                if ($clientSocket !== false) {
                    $this->handleConnection($clientSocket);
                }
                
                unset($changed[array_search($this->socket, $changed)]);
            }
            
            // Procesar clientes existentes
            foreach ($changed as $clientSocket) {
                $clientId = $this->getClientId($clientSocket);
                
                if (!isset($this->clients[$clientId])) {
                    continue;
                }
                
                $bytes = @socket_recv($clientSocket, $buffer, $this->options['buffer_size'], 0);
                
                if ($bytes === false || $bytes === 0) {
                    $this->disconnectClient($clientId);
                    continue;
                }
                
                $client = &$this->clients[$clientId];
                
                if (!$client['handshake_done']) {
                    $this->doHandshake($clientSocket, $buffer, $clientId);
                } else {
                    $this->handleMessage($clientSocket, $buffer, $clientId);
                }
            }
            
            // Limpiar clientes desconectados
            $this->cleanupClients();
            
            usleep(1000); // Pequeña pausa para evitar uso excesivo de CPU
        }
    }
    
    /**
     * Maneja nueva conexión
     */
    private function handleConnection($socket) {
        $clientId = $this->getClientId($socket);
        
        $this->clients[$clientId] = [
            'socket' => $socket,
            'handshake_done' => false,
            'connected_at' => time(),
            'last_activity' => time(),
            'ip' => '',
            'headers' => []
        ];
        
        $this->log("Nueva conexión: {$clientId}");
        
        socket_getpeername($socket, $ip);
        $this->clients[$clientId]['ip'] = $ip;
    }
    
    /**
     * Realiza handshake WebSocket
     */
    private function doHandshake($socket, $headers, $clientId) {
        $lines = explode("\r\n", $headers);
        
        if (strpos($lines[0], 'GET') === 0) {
            $this->clients[$clientId]['headers'] = [];
            
            foreach ($lines as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $this->clients[$clientId]['headers'][trim($key)] = trim($value);
                }
            }
            
            // Extraer clave WebSocket
            $secKey = $this->clients[$clientId]['headers']['Sec-WebSocket-Key'] ?? '';
            
            if (!empty($secKey)) {
                // Calcular respuesta según RFC 6455
                $acceptKey = base64_encode(sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
                
                $response = "HTTP/1.1 101 Switching Protocols\r\n";
                $response .= "Upgrade: websocket\r\n";
                $response .= "Connection: Upgrade\r\n";
                $response .= "Sec-WebSocket-Accept: {$acceptKey}\r\n";
                
                // Headers adicionales
                $response .= "Sec-WebSocket-Version: 13\r\n";
                
                if (isset($this->clients[$clientId]['headers']['Sec-WebSocket-Protocol'])) {
                    $response .= "Sec-WebSocket-Protocol: chat\r\n";
                }
                
                $response .= "\r\n";
                
                socket_write($socket, $response, strlen($response));
                $this->clients[$clientId]['handshake_done'] = true;
                $this->clients[$clientId]['last_activity'] = time();
                
                $this->log("Handshake completado para {$clientId}");
                $this->onOpen($clientId);
                
                return true;
            }
        }
        
        $this->disconnectClient($clientId);
        return false;
    }
    
    /**
     * Maneja mensajes recibidos
     */
    private function handleMessage($socket, $buffer, $clientId) {
        $decoded = $this->decodeFrame($buffer);
        
        if ($decoded !== false) {
            $this->clients[$clientId]['last_activity'] = time();
            $this->onMessage($clientId, $decoded);
        }
    }
    
    /**
     * Decodifica frame WebSocket
     */
    private function decodeFrame($data) {
        $length = ord($data[1]) & 127;
        
        if ($length === 126) {
            $masks = substr($data, 4, 4);
            $dataOffset = 8;
        } elseif ($length === 127) {
            $masks = substr($data, 10, 4);
            $dataOffset = 14;
        } else {
            $masks = substr($data, 2, 4);
            $dataOffset = 6;
        }
        
        $message = '';
        $dataLength = strlen($data) - $dataOffset;
        
        for ($i = 0; $i < $dataLength; ++$i) {
            $message .= $data[$i + $dataOffset] ^ $masks[$i % 4];
        }
        
        return $message;
    }
    
    /**
     * Codifica mensaje para envío
     */
    private function encodeFrame($message) {
        $length = strlen($message);
        $frame = [];
        
        // Byte 1: FIN (1), RSV (0), opcode (1 = texto)
        $frame[0] = 0x81;
        
        // Byte 2: Mask (0), payload length
        if ($length <= 125) {
            $frame[1] = $length;
            $offset = 2;
        } elseif ($length <= 65535) {
            $frame[1] = 126;
            $frame[2] = ($length >> 8) & 255;
            $frame[3] = $length & 255;
            $offset = 4;
        } else {
            $frame[1] = 127;
            for ($i = 7; $i >= 0; $i--) {
                $frame[2 + (7 - $i)] = ($length >> (8 * $i)) & 255;
            }
            $offset = 10;
        }
        
        // Agregar datos
        for ($i = 0; $i < $length; $i++) {
            $frame[$offset + $i] = ord($message[$i]);
        }
        
        // Convertir a string binario
        $encoded = '';
        foreach ($frame as $byte) {
            $encoded .= chr($byte);
        }
        
        return $encoded;
    }
    
    /**
     * Envía mensaje a un cliente específico
     */
    public function send($clientId, $message) {
        if (isset($this->clients[$clientId])) {
            $encoded = $this->encodeFrame($message);
            socket_write($this->clients[$clientId]['socket'], $encoded, strlen($encoded));
            return true;
        }
        return false;
    }
    
    /**
     * Envía mensaje a todos los clientes
     */
    public function broadcast($message, $excludeClientId = null) {
        foreach ($this->clients as $id => $client) {
            if ($client['handshake_done'] && $id !== $excludeClientId) {
                $this->send($id, $message);
            }
        }
    }
    
    /**
     * Desconecta un cliente
     */
    public function disconnectClient($clientId) {
        if (isset($this->clients[$clientId])) {
            $this->onClose($clientId);
            
            socket_close($this->clients[$clientId]['socket']);
            unset($this->clients[$clientId]);
            
            $this->log("Cliente desconectado: {$clientId}");
            return true;
        }
        return false;
    }
    
    /**
     * Limpia clientes inactivos
     */
    private function cleanupClients() {
        $timeout = $this->options['timeout'];
        $now = time();
        
        foreach ($this->clients as $clientId => $client) {
            if ($client['handshake_done'] && ($now - $client['last_activity']) > $timeout) {
                $this->disconnectClient($clientId);
            }
        }
    }
    
    /**
     * Obtiene ID único del cliente
     */
    private function getClientId($socket) {
        return (int)$socket;
    }
    
    /**
     * Registra mensajes de log
     */
    private function log($message) {
        if ($this->options['debug']) {
            $timestamp = date('Y-m-d H:i:s');
            echo "[{$timestamp}] {$message}\n";
        }
    }
    
    /**
     * Método llamado cuando se abre una conexión (para sobrescribir)
     */
    protected function onOpen($clientId) {
        // Implementar en clase hija
    }
    
    /**
     * Método llamado al recibir mensaje (para sobrescribir)
     */
    protected function onMessage($clientId, $message) {
        // Implementar en clase hija
    }
    
    /**
     * Método llamado al cerrar conexión (para sobrescribir)
     */
    protected function onClose($clientId) {
        // Implementar en clase hija
    }
    
    /**
     * Obtiene información de los clientes conectados
     */
    public function getClientsInfo() {
        $info = [];
        
        foreach ($this->clients as $clientId => $client) {
            $info[$clientId] = [
                'ip' => $client['ip'],
                'connected_at' => date('Y-m-d H:i:s', $client['connected_at']),
                'last_activity' => date('Y-m-d H:i:s', $client['last_activity']),
                'handshake_done' => $client['handshake_done']
            ];
        }
        
        return $info;
    }
    
    /**
     * Cierra el servidor
     */
    public function stop() {
        $this->log("Cerrando servidor WebSocket");
        
        // Desconectar todos los clientes
        foreach (array_keys($this->clients) as $clientId) {
            $this->disconnectClient($clientId);
        }
        
        // Cerrar socket principal
        if ($this->socket) {
            socket_close($this->socket);
        }
        
        $this->log("Servidor WebSocket detenido");
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
        $this->stop();
    }
}<?php
/**
 * Clase WebSocketServer - Implementación de servidor WebSocket en PHP
 * Compatible con RFC 6455
 */
class WebSocketServer {
    private $socket;
    private $clients = [];
    private $origins = [];
    private $handshakeHeaders = [];
    
    /**
     * Constructor del servidor WebSocket
     * @param string $host Dirección IP o hostname
     * @param int $port Puerto del servidor
     * @param array $options Opciones adicionales
     */
    public function __construct($host = '0.0.0.0', $port = 8080, $options = []) {
        $this->host = $host;
        $this->port = $port;
        $this->options = array_merge([
            'max_clients' => 100,
            'timeout' => 60,
            'buffer_size' => 4096,
            'debug' => false
        ], $options);
    }
    
    /**
     * Inicia el servidor WebSocket
     */
    public function start() {
        $this->log("Iniciando servidor WebSocket en {$this->host}:{$this->port}");
        
        // Crear socket
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        
        if ($this->socket === false) {
            $this->log("Error al crear socket: " . socket_strerror(socket_last_error()));
            return false;
        }
        
        // Configurar opciones del socket
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_set_option($this->socket, SOL_SOCKET, SO_KEEPALIVE, 1);
        
        // Vincular socket
        if (!socket_bind($this->socket, $this->host, $this->port)) {
            $this->log("Error al vincular socket: " . socket_strerror(socket_last_error($this->socket)));
            return false;
        }
        
        // Escuchar conexiones
        if (!socket_listen($this->socket, $this->options['max_clients'])) {
            $this->log("Error al escuchar conexiones: " . socket_strerror(socket_last_error($this->socket)));
            return false;
        }
        
        $this->log("Servidor WebSocket iniciado exitosamente");
        
        return true;
    }
    
    /**
     * Ejecuta el bucle principal del servidor
     */
    public function run() {
        $read = [$this->socket];
        $write = $except = null;
        
        while (true) {
            $changed = $read;
            
            // Esperar cambios en los sockets
            socket_select($changed, $write, $except, 0, 10);
            
            // Nuevas conexiones
            if (in_array($this->socket, $changed)) {
                $clientSocket = socket_accept($this->socket);
                
                if ($clientSocket !== false) {
                    $this->handleConnection($clientSocket);
                }
                
                unset($changed[array_search($this->socket, $changed)]);
            }
            
            // Procesar clientes existentes
            foreach ($changed as $clientSocket) {
                $clientId = $this->getClientId($clientSocket);
                
                if (!isset($this->clients[$clientId])) {
                    continue;
                }
                
                $bytes = @socket_recv($clientSocket, $buffer, $this->options['buffer_size'], 0);
                
                if ($bytes === false || $bytes === 0) {
                    $this->disconnectClient($clientId);
                    continue;
                }
                
                $client = &$this->clients[$clientId];
                
                if (!$client['handshake_done']) {
                    $this->doHandshake($clientSocket, $buffer, $clientId);
                } else {
                    $this->handleMessage($clientSocket, $buffer, $clientId);
                }
            }
            
            // Limpiar clientes desconectados
            $this->cleanupClients();
            
            usleep(1000); // Pequeña pausa para evitar uso excesivo de CPU
        }
    }
    
    /**
     * Maneja nueva conexión
     */
    private function handleConnection($socket) {
        $clientId = $this->getClientId($socket);
        
        $this->clients[$clientId] = [
            'socket' => $socket,
            'handshake_done' => false,
            'connected_at' => time(),
            'last_activity' => time(),
            'ip' => '',
            'headers' => []
        ];
        
        $this->log("Nueva conexión: {$clientId}");
        
        socket_getpeername($socket, $ip);
        $this->clients[$clientId]['ip'] = $ip;
    }
    
    /**
     * Realiza handshake WebSocket
     */
    private function doHandshake($socket, $headers, $clientId) {
        $lines = explode("\r\n", $headers);
        
        if (strpos($lines[0], 'GET') === 0) {
            $this->clients[$clientId]['headers'] = [];
            
            foreach ($lines as $line) {
                if (strpos($line, ':') !== false) {
                    list($key, $value) = explode(':', $line, 2);
                    $this->clients[$clientId]['headers'][trim($key)] = trim($value);
                }
            }
            
            // Extraer clave WebSocket
            $secKey = $this->clients[$clientId]['headers']['Sec-WebSocket-Key'] ?? '';
            
            if (!empty($secKey)) {
                // Calcular respuesta según RFC 6455
                $acceptKey = base64_encode(sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
                
                $response = "HTTP/1.1 101 Switching Protocols\r\n";
                $response .= "Upgrade: websocket\r\n";
                $response .= "Connection: Upgrade\r\n";
                $response .= "Sec-WebSocket-Accept: {$acceptKey}\r\n";
                
                // Headers adicionales
                $response .= "Sec-WebSocket-Version: 13\r\n";
                
                if (isset($this->clients[$clientId]['headers']['Sec-WebSocket-Protocol'])) {
                    $response .= "Sec-WebSocket-Protocol: chat\r\n";
                }
                
                $response .= "\r\n";
                
                socket_write($socket, $response, strlen($response));
                $this->clients[$clientId]['handshake_done'] = true;
                $this->clients[$clientId]['last_activity'] = time();
                
                $this->log("Handshake completado para {$clientId}");
                $this->onOpen($clientId);
                
                return true;
            }
        }
        
        $this->disconnectClient($clientId);
        return false;
    }
    
    /**
     * Maneja mensajes recibidos
     */
    private function handleMessage($socket, $buffer, $clientId) {
        $decoded = $this->decodeFrame($buffer);
        
        if ($decoded !== false) {
            $this->clients[$clientId]['last_activity'] = time();
            $this->onMessage($clientId, $decoded);
        }
    }
    
    /**
     * Decodifica frame WebSocket
     */
    private function decodeFrame($data) {
        $length = ord($data[1]) & 127;
        
        if ($length === 126) {
            $masks = substr($data, 4, 4);
            $dataOffset = 8;
        } elseif ($length === 127) {
            $masks = substr($data, 10, 4);
            $dataOffset = 14;
        } else {
            $masks = substr($data, 2, 4);
            $dataOffset = 6;
        }
        
        $message = '';
        $dataLength = strlen($data) - $dataOffset;
        
        for ($i = 0; $i < $dataLength; ++$i) {
            $message .= $data[$i + $dataOffset] ^ $masks[$i % 4];
        }
        
        return $message;
    }
    
    /**
     * Codifica mensaje para envío
     */
    private function encodeFrame($message) {
        $length = strlen($message);
        $frame = [];
        
        // Byte 1: FIN (1), RSV (0), opcode (1 = texto)
        $frame[0] = 0x81;
        
        // Byte 2: Mask (0), payload length
        if ($length <= 125) {
            $frame[1] = $length;
            $offset = 2;
        } elseif ($length <= 65535) {
            $frame[1] = 126;
            $frame[2] = ($length >> 8) & 255;
            $frame[3] = $length & 255;
            $offset = 4;
        } else {
            $frame[1] = 127;
            for ($i = 7; $i >= 0; $i--) {
                $frame[2 + (7 - $i)] = ($length >> (8 * $i)) & 255;
            }
            $offset = 10;
        }
        
        // Agregar datos
        for ($i = 0; $i < $length; $i++) {
            $frame[$offset + $i] = ord($message[$i]);
        }
        
        // Convertir a string binario
        $encoded = '';
        foreach ($frame as $byte) {
            $encoded .= chr($byte);
        }
        
        return $encoded;
    }
    
    /**
     * Envía mensaje a un cliente específico
     */
    public function send($clientId, $message) {
        if (isset($this->clients[$clientId])) {
            $encoded = $this->encodeFrame($message);
            socket_write($this->clients[$clientId]['socket'], $encoded, strlen($encoded));
            return true;
        }
        return false;
    }
    
    /**
     * Envía mensaje a todos los clientes
     */
    public function broadcast($message, $excludeClientId = null) {
        foreach ($this->clients as $id => $client) {
            if ($client['handshake_done'] && $id !== $excludeClientId) {
                $this->send($id, $message);
            }
        }
    }
    
    /**
     * Desconecta un cliente
     */
    public function disconnectClient($clientId) {
        if (isset($this->clients[$clientId])) {
            $this->onClose($clientId);
            
            socket_close($this->clients[$clientId]['socket']);
            unset($this->clients[$clientId]);
            
            $this->log("Cliente desconectado: {$clientId}");
            return true;
        }
        return false;
    }
    
    /**
     * Limpia clientes inactivos
     */
    private function cleanupClients() {
        $timeout = $this->options['timeout'];
        $now = time();
        
        foreach ($this->clients as $clientId => $client) {
            if ($client['handshake_done'] && ($now - $client['last_activity']) > $timeout) {
                $this->disconnectClient($clientId);
            }
        }
    }
    
    /**
     * Obtiene ID único del cliente
     */
    private function getClientId($socket) {
        return (int)$socket;
    }
    
    /**
     * Registra mensajes de log
     */
    private function log($message) {
        if ($this->options['debug']) {
            $timestamp = date('Y-m-d H:i:s');
            echo "[{$timestamp}] {$message}\n";
        }
    }
    
    /**
     * Método llamado cuando se abre una conexión (para sobrescribir)
     */
    protected function onOpen($clientId) {
        // Implementar en clase hija
    }
    
    /**
     * Método llamado al recibir mensaje (para sobrescribir)
     */
    protected function onMessage($clientId, $message) {
        // Implementar en clase hija
    }
    
    /**
     * Método llamado al cerrar conexión (para sobrescribir)
     */
    protected function onClose($clientId) {
        // Implementar en clase hija
    }
    
    /**
     * Obtiene información de los clientes conectados
     */
    public function getClientsInfo() {
        $info = [];
        
        foreach ($this->clients as $clientId => $client) {
            $info[$clientId] = [
                'ip' => $client['ip'],
                'connected_at' => date('Y-m-d H:i:s', $client['connected_at']),
                'last_activity' => date('Y-m-d H:i:s', $client['last_activity']),
                'handshake_done' => $client['handshake_done']
            ];
        }
        
        return $info;
    }
    
    /**
     * Cierra el servidor
     */
    public function stop() {
        $this->log("Cerrando servidor WebSocket");
        
        // Desconectar todos los clientes
        foreach (array_keys($this->clients) as $clientId) {
            $this->disconnectClient($clientId);
        }
        
        // Cerrar socket principal
        if ($this->socket) {
            socket_close($this->socket);
        }
        
        $this->log("Servidor WebSocket detenido");
    }
    
    /**
     * Destructor
     */
    public function __destruct() {
        $this->stop();
    }
}