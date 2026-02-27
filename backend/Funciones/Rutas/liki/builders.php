<?php
use Liki\Routing\Ruta;
use Liki\Plantillas\Flow;


return function(){
    Ruta::get('/{html}/html',function($p){
       //echo 'fff';
       $url = str_replace('_','/',$p[0]);
      Flow::html($url);
    
    });
    
    Ruta::get('/{js}/js',function($p){
       //echo 'fff';
       $url = str_replace('_','/',$p[0]);
      Flow::js($url);
    
    });
    
    Ruta::get('/{css}/css',function($p){
        $url = str_replace('_','/',$p[0]);
       Flow::css($url);
    
    });
};