<?php

// Asegúrate de que el archivo sea ejecutable desde la consola
if (php_sapi_name() !== 'cli') {
    die("Este script debe ejecutarse desde la línea de comandos.\n");
}

// Función para generar un modelo
include "./CodigoPHP/.consola/index.php";

// Comprobación de los argumentos de la línea de comandos
if ($argc < 3) {
    echo "Uso: php script.php [modelo|controlador] [nombre]\n";
    exit(1);
}

$tipo = $argv[1];
$nombre = $argv[2];

switch ($tipo) {
    case 'modelo':
        generateModel($nombre);
        break;

    case 'controlador':
        generateController($nombre);
        break;

    default:
        echo "Tipo inválido. Usa 'modelo' o 'controlador'.\n";
        exit(1);
}

?>