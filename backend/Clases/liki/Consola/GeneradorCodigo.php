<?php

namespace Liki\Consola;


class GeneradorCodigo{
public static function  generateModel($name) {
    $template = "<?php \n\n  return new class { \n\n  public static function run(){  // Propiedades\n\n    // Métodos}
    \n\n};";
    file_put_contents("./backend/Funciones/$name.php", $template);
    echo "Modelo '$name' creado.\n";
}

// Función para generar un controlador
public static function generateController($name) {
    $template = "<?php
    \n
    amespace App\\${name};
    
    \n\nclass ${name} {\n\n    // Métodos del controlador\n\n}";
    file_put_contents("./backend/App/${name}.php", $template);
    echo "Controlador '${name}Controller' creado.\n";
}

}