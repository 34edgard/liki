<?php
// Asegúrate de que el archivo sea ejecutable desde la consola
///siii 
if (php_sapi_name() !== 'cli' ) {
   die("Este script debe ejecutarse desde la línea de comandos.\n");
}
// Función para generar un modelo
include "./conf.php";
include "./backend/autoload.php";

use Liki\DelegateFunction;

$comandos = DelegateFunction::loadData('Tools/Terminal');
// Comprobación de los argumentos de la línea de comandos
if ($argc < 2) {
    echo "Uso: php consol.php [comando] [nombre] [extras]\n";
    echo "comandos: \n\n ";
    foreach($comandos as $nombre => $comando){
        foreach($comando as $accion => $accionExec ){
        echo ' -'.$nombre.':'.$accion."\n";
        }
    }
    exit(1);
}

$comando = explode(':',$argv[1]);
$tipo = $comando[0];
$accion = $comando[1];
$nombre = $argv[2] ?? '';
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
if(isset($comandos[$tipo][$accion])){
 comandoExec($comandos[$tipo][$accion],$nombre,$extras);
}else{
    echo "el comando '$tipo:$accion' no existe";
}