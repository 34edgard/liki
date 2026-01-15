<?php

//$startMem = memory_get_usage();
//$inicio = microtime(true); // Guarda el tiempo actual como un número flotante

include "./conf.php";
include "./backend/autoload.php";



use Liki\Routing\Ruta;
use Liki\Plantillas\Flow;
use Liki\Sesion;

use Liki\ErrorHandler;

use Liki\Config\ConfigManager;



Ruta::group('liki/toolsDep');




Ruta::get('/{html}/html',function($p){
    //echo 'fff';
    $url = str_replace('_','/',$p[0]);
   Flow::html($url);

});

Ruta::get('/{js}/js',function($p){
    //echo 'fff';
    $url = str_replace('_','/',$p[0]);
   Flow::js($url);

});





Ruta::group('app/Paginas');




Ruta::get('/Cerrar_Sesion',[Sesion::class,'cerrar_sesion']);

Ruta::post('/iniciar/sesion',[Sesion::class,'iniciar_sesion'],['Inicio_secion','correo','contraseña']);



//Gestion_Usuario


Ruta::group('app/Usuario');








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



// Run the router 
Ruta::dispatch();
//$fin = microtime(true); // Guarda el tiempo final
///$tiempo_total = $fin - $inicio; // Calcula la diferencia

//echo "El proceso tomó: " .round($tiempo_total,3) . " segundos.<br />";

//echo "Memoria usada: " . round((memory_get_usage() - $startMem) / 1024 / 1024, 2) . " MB\n";