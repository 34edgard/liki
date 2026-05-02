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
    if($id <= 1) continue;
    if(str_starts_with($arg,'-') || str_starts_with($arg,'--')) {
      $opt = str_replace('-','',$arg);
       $option = '';
    $value='';
        $opts = explode('=',$opt);
    if(isset($opts[1]))
        $value = trim($opts[1]);
        
           $option = trim($opts[0]);
          
       
     define('CLI_'.$option,$value);
    continue;
    }
$extras[] = $arg;
}
//print_r($estras);

if(str_starts_with($argv[2],'-') ) $nombre = '';

function comandoExec(callable $comando,$nombre,$extras){
  if(count($extras) == 0)  $comando($nombre);
  if(count($extras) > 0) $comando($nombre,$extras);
}
if(isset($comandos[$tipo][$accion])){
 comandoExec($comandos[$tipo][$accion],$nombre,$extras);
}else{
    echo "el comando '$tipo:$accion' no existe";
}