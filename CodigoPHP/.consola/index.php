<?php
function generateModel($name) {
    $template = "<?php \n\n (function (){
    global $$name; \n\n  $$  // Propiedades\n\n    // Métodos\n\n})();";
    file_put_contents("./CodigoPHP/funciones/$name.php", $template);
    echo "Modelo '$name' creado.\n";
}

// Función para generar un controlador
function generateController($name) {
    $template = "<?php\n\nclass ${name}Controller {\n\n    // Métodos del controlador\n\n}";
    file_put_contents("${name}Controller.php", $template);
    echo "Controlador '${name}Controller' creado.\n";
}