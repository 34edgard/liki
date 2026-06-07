<?php
namespace Liki\Consola;  
  
class Flors {  
    const FLORS_FILE = './docs/struct/flors.json';  
    const STRUCT_DIR = './docs/struct/';  
  
    public static function instalar(string $struct): void {  
        $config = json_decode(file_get_contents($struct), true);  
        // Copiar archivos a sus destinos  
        foreach ($config['archivos'] as $archivo) {  
            // crear directorios si no existen  
            // copiar archivo al destino  
        }  
        // Registrar en flors.json  
        self::registrar($config, $struct);  
    }  
  
    public static function remover(string $nombre): void {  
        $flors = self::cargarFlors();  
        $paquete = self::buscar($nombre, $flors);  
        $struct = json_decode(file_get_contents($paquete['struct']), true);  
        // Eliminar archivos listados en struct.json  
        foreach ($struct['archivos'] as $archivo) {  
            unlink($archivo['origen']);  
        }  
        // Quitar de flors.json  
        self::desregistrar($nombre);  
    }  
}