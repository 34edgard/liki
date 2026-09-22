<?php
namespace Liki\KitPHP;
class Http
{
    private array $headers = [];
    private int $timeout = 30;
    private ?string $token = null;
    
    public function setHeader(string $header): self
    {
        $this->headers[] = $header;
        return $this;
    }
    
    public function setToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }
    
    public function request(string $method, string $url, ?array $data = null): array
    {
        $ch = curl_init($url);
        
        $headers = array_merge(['Accept: application/json'], $this->headers);
        
        if ($data !== null) {
            $headers[] = 'Content-Type: application/json';
        }
        
        if ($this->token) {
            $headers[] = "Authorization: Bearer {$this->token}";
        }
        
        $opciones = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_FOLLOWLOCATION => true,
        ];
        
        if ($data !== null) {
            $opciones[CURLOPT_POSTFIELDS] = json_encode($data);
        }
        
        curl_setopt_array($ch, $opciones);
        
        $respuesta = curl_exec($ch);
        $codigo    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error     = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            return ['ok' => false, 'error' => $error, 'codigo' => 0];
        }
        
        return [
            'ok'     => $codigo >= 200 && $codigo < 300,
            'codigo' => $codigo,
            'datos'  => json_decode($respuesta, true) ?? $respuesta,
        ];
    }
    
    public function get(string $url): array    { return $this->request('GET', $url); }
    public function post(string $url, array $d): array { return $this->request('POST', $url, $d); }
    public function put(string $url, array $d): array  { return $this->request('PUT', $url, $d); }
    public function delete(string $url): array { return $this->request('DELETE', $url); }
}
