<?php

namespace Liki\Consola;

use Liki\Database\ConsultasBD;
class Gn{
    public static function traducirTipos(string $tipo =''):string{
        
        $sqlite_types = [
            'isString' => ['TEXT', 'CHARACTER', 'VARCHAR', 'NVARCHAR', 'CLOB', 'STRING'],
            'isInt' => ['INTEGER', 'INT', 'INT2', 'INT8', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'BIGINT'],
            'isFloat' => ['REAL', 'FLOAT', 'DOUBLE', 'DOUBLE PRECISION'],
            'isBool' => ['BOOLEAN', 'BOOL'],
            'isBlob' => ['BLOB', 'BYTEA', 'BINARY', 'VARBINARY'],
            'isDate' => ['DATE', 'DATETIME', 'TIMESTAMP', 'TIME'],
            'isJson' => ['JSON', 'JSONB'],
            'isNumeric' => ['NUMERIC', 'DECIMAL', 'DEC'],
            'isNull' => ['NULL']
        ];
        foreach($sqlite_types as $id => $types){
            foreach($types as $type){
            if($type == $tipo) return $id;
            }
        }
    return 'isString';
    }
    
public static function  generateModel($name,$campos = []) {
  //  print_r($name);
    $tablaName = strtolower($name);
    $template = "<?php\n  use Liki\Modelo;\n return new class extends Modelo{\n
      protected string \$tabla = '$tablaName';\n
      protected array \$campos = [\n";
   $i = 1;
$ncampos = count($campos);
//print_r($campos[0]);
//return;
 foreach($campos as $id => $campo){
    // print_r($campo);
    //continue;
       $template .=  " '".$campo['name']."' => '".self::traducirTipos($campo['type'])."'";
   if($i < $ncampos)  $template .=",";
$i++;
     $template .="\n";
    }
   $template .= "];\n
    };";
   file_put_contents("./backend/Clases/app/Modelos/$name.php", $template);
   echo "Modelo '$name' creado.\n";
}
public static function  generateManejador($name,$file='') {
    print_r($name);
    $template = "<?php \n\n  return new class { \n\n  public static function run(){  // Propiedades\n\n    // Métodos}
    \n\n};";
    file_put_contents("./backend/Clases/app/Manejadores/$file$name.php", $template);
    echo "Modelo '$name' creado.\n";
}


public static function gnModelosDB(){
$sql['sqlite']= "SELECT name FROM sqlite_master WHERE type='table' ";
    $sql['mysql']='SHOW TABLES';
    
$con = new ConsultasBD();
$res = $con->consultarRegistro($sql[DB_DRIVER],[]);

foreach($res as   $tabla){
    
 foreach($tabla as $t){
    
    $td['sqlite']= "PRAGMA table_info($t) ";
    $td['mysql']='SHOW TABLES';
    
    
   $Tablas = $con->consultarRegistro($td[DB_DRIVER]);
//print_r($Tablas);
//echo "--------------------";
//continue;
  

 self::generateModel($t,$Tablas);
  
 }
}

//
    
}


// Función para generar un controlador
public static function generateController($name,$file ='') {
    $template = "<?php
    \n
 namespace App\\$name;
 use Liki\DelegateFunction;   
    \n\nclass $name {\n\n    // Métodos del controlador\n\n}";
    file_put_contents("./backend/Clases/app/Controladores/$file$name.php", $template);
    echo "Controlador '${name}Controller' creado.\n";
}

public static function generateClassLiki($name,$file ='') {
    $template = "<?php
    \n
 namespace Liki\\$name;
    
    \n\nclass $name {\n\n    // Métodos del controlador\n\n}";
    file_put_contents("./backend/Clases/liki/$file$name.php", $template);
    echo "Controlador '${name}Controller' creado.\n";
}

public static function generateGrupoApp($name,$extras) {

   mkdir("./backend/Clases/app/Controladores/$name");
   foreach($extras as $extra){
    self::generateController($extra,$name.'/');
   }
   echo "grupo de controladores creados en '${name}' creado.\n";
}

public static function generateGrupoFunc($name,$extras) {

   mkdir("./backend/Clases/app/Manejadores/$name");
   foreach($extras as $extra){
    self::generateModel($extra,$name.'/');
   }
   echo "grupo de funciones creados en '${name}' creado.\n";
}

public static function generateGrupoLiki($name,$extras) {

   mkdir("./backend/Clases/liki/$name");
   foreach($extras as $extra){
    self::generateClassLiki($extra,$name.'/');
   }
   echo "grupo de clases liki creados en '${name}' creado.\n";
}





public static function generateMiddleware($name,$file = ''){
    $template = "<?php\n\nnamespace Middleware;\n\nclass {$name} {\n public function handle() {\n // Lógica del middleware \n return false; // true para continuar, false para detener\n }\n}";
file_put_contents("./backend/Funciones/Middleware/$file$name.php", $template);
echo "Middleware '$name' creado.\n";
}

}