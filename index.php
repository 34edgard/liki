<?php


 $startMem = memory_get_usage();
         $inicio = microtime(true); // Guarda el tiempo actual como un número flotante
 
     
      

include "./conf.php";
include "./backend/autoload.php";
use Liki\Routing\Ruta;
use Liki\Database\FlowDB;
Ruta::group('liki/toolsDep');
Ruta::group('liki/builders');
Ruta::group('liki/admin');
Ruta::group('app/Paginas');
Ruta::group('app/sesiones');
Ruta::group('app/Usuario');
// Run the router 

Ruta::dispatch();




$fin = microtime(true); // Guarda el tiempo final
         $tiempo_total = $fin - $inicio; // Calcula la diferencia
         
         echo "El proceso tomó: " .round($tiempo_total,3) . " segundos.<br />";
         
         echo "Memoria usada: " . round((memory_get_usage() - $startMem) / 1024 / 1024, 2) . " MB\n";
