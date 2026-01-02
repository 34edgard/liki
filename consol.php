<?php

// Asegúrate de que el archivo sea ejecutable desde la consola
if (php_sapi_name() !== 'cli' || true) {
   die("Este script debe ejecutarse desde la línea de comandos.\n");
}

// Función para generar un modelo
include "./conf.php";
include "./backend/autoload.php";

use Liki\Consola\GeneradorCodigo;
use Liki\Database\MigrationRunner;
  

$comandos = [
    'modelo'=>[GeneradorCodigo::class,'generateModel'],
    'controlador'=>[GeneradorCodigo::class,'generateController'],
    'likiClass'=>[GeneradorCodigo::class,'generateClassLiki'],
    'migracion-run'=>[MigrationRunner::class,'run'],
    'liki-grup'=>[GeneradorCodigo::class,'generateGrupoLiki'],
    'app-grup'=>[GeneradorCodigo::class,'generateGrupoApp'],
    'func-grup'=>[GeneradorCodigo::class,'generateGrupoFunc'],
    ];



// Comprobación de los argumentos de la línea de comandos
if ($argc < 2) {
  
    echo "Uso: php consol.php [comando] [nombre] [extras]\n";
    
    echo "comandos: \n\n ";
    foreach($comandos as $nombre => $comando){
        echo ' -'.$nombre."\n";
    }
    exit(1);
}

$tipo = $argv[1];
$nombre = $argv[2];
$extras = [];
foreach($argv as $id => $arg){
    if($id <= 2) continue;
$extras[] = $arg;
}

//print_r($estras);
function comandoExec(callable $comando,$nombre,$extras){
    
  if(count($extras) == 0)  $comando($nombre);
  if(count($extras) > 0) $comando($nombre,$extras);
}
 
if(isset($comandos[$tipo])){
 comandoExec($comandos[$tipo],$nombre,$extras);
    
}else{
    echo "el comando '$tipo' no existe";
}


?>