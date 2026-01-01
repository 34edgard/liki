<?php
include "./conf.php";
include "./backend/autoload.php";




use Liki\Routing\Ruta;
use Liki\Plantillas\Plantilla;
use Liki\Sesion;

use Liki\ErrorHandler;

use Liki\Config\ConfigManager;

Ruta::group('toolsDep');




Ruta::get('/{html}/src',function($p){
    //echo 'fff';
    $url = str_replace('_','/',$p[0]);
   Plantilla::HTML($url);

});


Ruta::group('Paginas');






Ruta::get('/Cerrar_Sesion',[Sesion::class,'cerrar_sesion']);

Ruta::post('/iniciar/sesion',[Sesion::class,'iniciar_sesion'],['Inicio_secion','correo','contraseña']);



//Gestion_Usuario


Ruta::group('Usuario');








Ruta::get('/admin/paginas/{nombre}', function($p) {  
    $nombrePagina = $p[0];  
    $config = ConfigManager::cargarConfig($nombrePagina);  
      
    // Mostrar formulario de edición  
    Plantilla::HTML('admin/editor-pagina', [  
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



// Run the router 
Ruta::dispatch();