<?php
(function (){
    global $pagina_html;

 $pagina_html = function ($propiedades=['titulo'=>'inicio','cuerpo'=>[]]){
  $página = new Pagina($propiedades['titulo']);
  $página->agregarElemento($propiedades['cuerpo']);
return $página->generarHtml([]);
};


})();

