<?php

$startMem = memory_get_usage();
 $inicio = microtime(true); // Guarda el tiempo actual como un número flotante    
  
//declare(strict_types=1);
include "./conf.php";
include "./backend/autoload.php";
use Liki\Routing\Ruta;
use Middleware\AuthMiddleware;



Ruta::modul('liki/toolsDep',true,[
    [AuthMiddleware::class,'login'],
    [AuthMiddleware::class,'isAdmin']
    
 ]);
Ruta::modul('liki/builders');
Ruta::modul('liki/admin',true,[
   [AuthMiddleware::class,'login'],
    [AuthMiddleware::class,'isAdmin']
]);
Ruta::modul('app/Paginas');
Ruta::modul('app/sesiones');
Ruta::modul('app/Usuario');
// Run the router 


 
Ruta::dispatch();


$fin = microtime(true); // Guarda el tiempo final
$tiempo_total = $fin - $inicio; // Calcula la diferencia     
$sm = file_get_contents('./logs/rendimiento.log') ;
$sm .= "------------------------------------\n";
$sm .= 'url: '. $_SERVER['REQUEST_URI'] ."\n";
$sm .= "El proceso tomó: " .round($tiempo_total,6) . " segundos.\n";
$sm .= "Memoria usada: " . round((memory_get_usage() - $startMem) / 1024 / 1024, 2) . " MB\n\n";

file_put_contents('./logs/rendimiento.log',$sm);

