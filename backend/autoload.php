<?php
//backend/autoload.php
spl_autoload_register(function ($class) {
// Mapeo de prefijos de namespace a directorios
 $prefixes = [
'Liki\\' => __DIR__ .'/Clases/liki/',
'App\\' => __DIR__.'/Clases/app/',
'Funciones\\' => __DIR__.'/Funciones/',
'Middleware\\'=>__DIR__.'/Funciones/Middleware/',
'Librerias\\' => __DIR__.'/Librerias/'
];

// Buscar el prefijo que coincida  
foreach($prefixes as $prefix => $base_dir) {
 $len = strlen($prefix);
//print_r($prefix);
 if(strncmp($prefix, $class, $len) !== 0){
continue;
}

// Obtener el nombre relativo de la clase  
$relative_class= substr($class,$len);

// Reemplazar namespace separators con directory separators  
$file = $base_dir. str_replace('\\','/',$relative_class).'.php';

// Si el archivo existe, cargarlo  
if(file_exists($file)){
require$file;
return true;
}
 }

return false;
});