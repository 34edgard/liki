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
    });
    
    
};