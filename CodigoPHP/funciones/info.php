<?php
(function (){
    global $info;

 $info = function ($pagina_html){
  $h =new componetHTML('h1', 'text-center text-primary','titulo');
$contenido[0] =$h->generarHtml('si');
$contenido[1] =$h->generarHtml('si');
$contenido[2] =$h->generarHtml('funciona');
  
echo $pagina_html[0](['titulo'=>'inicio','cuerpo'=>$contenido]);
};

})();
