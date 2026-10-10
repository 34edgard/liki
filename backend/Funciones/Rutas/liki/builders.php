<?php
use Liki\Routing\Ruta;
use Liki\Plantillas\Flow;


return function(){
    Ruta::get('/{html}/html',function($p){
       $url = str_replace('_','/',$p['html']);
      Flow::html($url);
    
    });
    
    Ruta::get('/{js}/js',function($p){
       $url = str_replace('_','/',$p['js']);
      Flow::js($url);
    
    });
    
    Ruta::get('/{css}/css',function($p){
      header('Content-Type: text/css');
      $url = str_replace('_','/',$p['css']);
       Flow::css($url);
    
    });
};