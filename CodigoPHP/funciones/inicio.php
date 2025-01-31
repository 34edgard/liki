<?php
( function (){
    global $inicio;
 
   class ConexionesSQLite extends ConexionesBDPDO {
    protected $dsn = "sqlite:CodigoPHP/SqliteBD/likiDB.db"; // Cambia la ruta según sea necesario

    public function crearConexion(): ?PDO {
        return parent::crearConexion(); // Utiliza el método de la clase padre
    }
}

// Ejemplo de uso
try {
    echo DSN;
    $consultas = new ConsultasBDPDO();
    // Ejemplo de inyección SQL (vulnerabilidad)
    // Supongamos que $userInput proviene de una entrada del usuario sin sanitizar.
  //  $userInput = "'; DROP TABLE users; --"; // Esto es una inyección SQL
    //$sql = "SELECT * FROM users WHERE username = '$userInput'"; // Vulnerable a inyección SQL
    $sql = "SELECT * FROM `po`";
    $resultado = $consultas->consultarRegistro($sql);
    print_r($resultado);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} 
 
$inicio = function ($pagina_html){

 $cont ='';
 $h = new componetHTML('h1', 'text-center text-primary','titulo');
 $h2 = new componetHTML('h2','text-center text-primary');
 $p = new componetHTML('p', 'text-center');
 $img = new ComponenteImagen('/src/img/logo.jpg','rounded-circle w-25 h-25');
 $con = new componetHTML('div', 'container d-flex flex-column justify-content-center align-items-center border border-dark rounded bg-gradient mt-5 p-4','contenedor');
 $link = new links('/inicio');
 $cont .= $h->generarHtml('Bienvenido a liki');
 $cont .= $img->generarHtml();
 $cont .= $h2->generarHtml('Que es liki');
 $cont .= $p->generarHtml('liki es una nueva forma de crear aplicaciones web de forma rapida ');
 $cont .= $p->generarHtml('liki ofrece una coleccion de clases y una estructura facil de entender ');
 $cont .=$link->generarHtml('explorar');

$contenido[] =$con->generarHtml($cont);

  
echo $pagina_html[0](['titulo'=>'inicio','cuerpo'=>$contenido]);
};
 })();

 
