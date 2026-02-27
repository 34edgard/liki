<?php
 namespace Liki\Config; 
class ConfigManager {  
    private static $configPath = './frontend/Config/Paginas/';        
    public static function cargarConfig(string $pagina): array {  
        $archivo = self::$configPath . $pagina . '.json';          
        if (!file_exists($archivo)) {            
            throw new \Exception("Configuración no encontrada: {$pagina}");  
        }  
        return json_decode(file_get_contents($archivo), true);          
    }        
    public static function guardarConfig(string $pagina, array $config): void {  
        $archivo = self::$configPath . $pagina . '.json';  
        file_put_contents($archivo, json_encode($config, JSON_PRETTY_PRINT));  
    }  
}