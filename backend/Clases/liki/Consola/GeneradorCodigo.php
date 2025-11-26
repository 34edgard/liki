<?php

namespace Liki\Consola;


class GeneradorCodigo{
    
public static function  generateModel($name,$file='') {
    print_r($name);
    $template = "<?php \n\n  return new class { \n\n  public static function run(){  // Propiedades\n\n    // Métodos}
    \n\n};";
    file_put_contents("./backend/Funciones/$file$name.php", $template);
    echo "Modelo '$name' creado.\n";
}

// Función para generar un controlador
public static function generateController($name,$file ='') {
    $template = "<?php
    \n
 namespace App\\$name;
    
    \n\nclass $name {\n\n    // Métodos del controlador\n\n}";
    file_put_contents("./backend/Clases/app/$file$name.php", $template);
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

   mkdir("./backend/Clases/app/$name");
   foreach($extras as $extra){
    self::generateController($extra,$name.'/');
   }
   echo "grupo de controladores creados en '${name}' creado.\n";
}

public static function generateGrupoFunc($name,$extras) {

   mkdir("./backend/Funciones/$name");
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


}