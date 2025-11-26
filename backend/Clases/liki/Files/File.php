<?php
namespace Liki\Files;

class File{
    public static function glob($patron, $banderas = 0) {
        $directorio = dirname($patron);
        $patron_archivo = basename($patron);
        
        // Convertir patrón glob a regex simple
        $regex = str_replace(
            ['*', '?', '[', ']'], 
            ['.*', '.', '\[', '\]'], 
            $patron_archivo
        );
        $regex = '/^' . $regex . '$/';
        
        $archivos = scandir($directorio);
        $resultado = [];
        
        foreach ($archivos as $archivo) {
            if ($archivo != '.' && $archivo != '..' && preg_match($regex, $archivo)) {
                $ruta_completa = $directorio . '/' . $archivo;
                $resultado[] = $ruta_completa;
            }
        }
        
        return $resultado;
    }
    
    
    
}