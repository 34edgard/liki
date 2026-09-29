<?php
/*$startMem = memory_get_usage();
 $inicio = microtime(true); // Guarda el tiempo actual como un número flotante    
  */   

include "./conf.php";
include "./backend/autoload.php";
use Liki\Routing\Ruta;
use Middleware\AuthMiddleware;

Ruta::modul('liki/toolsDep',false,[
    [AuthMiddleware::class,'login'],
    [AuthMiddleware::class,'isAdmin']
    
 ]);
Ruta::modul('liki/builders');
Ruta::modul('liki/admin',false,[
   [AuthMiddleware::class,'login']
]);
Ruta::modul('app/Paginas');
Ruta::modul('app/sesiones');
Ruta::modul('app/Usuario');
// Run the router 

Ruta::group(function(){
Ruta::get('/golo/{h}/{jk}',function($p){
    
    print_r($p);
    
    
},[
    'app'=>'x',
    'j'
]);
},[
    'jj'
]);




Ruta::dispatch();

/*
$fin = microtime(true); // Guarda el tiempo final
$tiempo_total = $fin - $inicio; // Calcula la diferencia     
$sm = file_get_contents('./logs/rendimiento.log') ;
$sm .= "------------------------------------\n";
$sm .= 'url: '. $_SERVER['REQUEST_URI']."\n";
$sm .= "El proceso tomó: " .round($tiempo_total,6) . " segundos.\n";
$sm .= "Memoria usada: " . round((memory_get_usage() - $startMem) / 1024 / 1024, 2) . " MB\n\n";

file_put_contents('./logs/rendimiento.log',$sm);

*/