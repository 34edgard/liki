<?php
use Liki\Routing\Ruta;
use Liki\Plantillas\Flow;
use Liki\Config\ConfigManager;

return function(){
    
    Ruta::get('/admin/paginas/{nombre}', function($p) {  
        $nombrePagina = $p[0];  
        $config = ConfigManager::cargarConfig($nombrePagina);  
          
        // Mostrar formulario de edición  
        Flow::html('admin/editor-pagina', [  
            'nombrePagina' => $nombrePagina,  
            'config' => $config,  
            'componentesDisponibles' => []
        ]);  
    });  
      
    Ruta::post('/admin/paginas/{nombre}/guardar', function($p) {  
        $nombrePagina = $p[0];  
        $config = json_decode($_POST['config'], true);  
        ConfigManager::guardarConfig($nombrePagina, $config);        
        // Redirigir con mensaje de éxito  
        header('Location: /admin/paginas?success=1');  
    },['config']);    
    
    Ruta::get('/admin/paginas',function(){
        
    },['success']);
    
    Ruta::get('/pages',function(){
        
        
        
        
        $directory = './frontend/Config/Paginas/';
        
        
        
        /**
         * Versión simplificada del comando tree en PHP
         * Uso: php tree-simple.php [directorio]
         */
        
        function generateTree($path, $prefix = "", $depth = 0, $maxDepth = -1) {
            if ($maxDepth !== -1 && $depth > $maxDepth) {
                return;
            }
            
            if (!is_dir($path)) {
                return;
            }
            
            $items = scandir($path);
            $items = array_diff($items, array('.', '..'));
            sort($items);
            
            $total = count($items);
            $current = 0;
            
            foreach ($items as $item) {
                $current++;
                $isLast = ($current === $total);
                
                $connector = $isLast ? "└── " : "├── ";
                echo $prefix . $connector . "<button class='btn btn-primary' hx-get='admin/paginas/".basename($item,'.json')."' hx-trigger='click' hx-target='#pages'>".$item ."</button>";
                
                $fullPath = $path . DIRECTORY_SEPARATOR . $item;
                
                if (is_dir($fullPath)) {
                    echo "<br />";
                    $newPrefix = $prefix . ($isLast ? "    " : "│   ");
                    generateTree($fullPath, $newPrefix, $depth + 1, $maxDepth);
                } else {
                    echo "<br />";
                }
            }
        }
        
        // Obterner el directorio desde los argumentos
       // $directory = isset($argv[1]) ? $argv[1] : ".";
        
        if (!file_exists($directory)) {
            echo "Error: El directorio '$directory' no existe.\n";
            exit(1);
        }
        
        //$directory = realpath($directory);
        echo basename($directory) . "<br />";
        generateTree($directory);
        
    });
};