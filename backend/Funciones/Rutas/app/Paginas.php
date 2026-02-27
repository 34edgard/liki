<?php
use Liki\Routing\Ruta;
use Liki\Plantillas\Flow;



return  function (){
           
   Ruta::get('/',function(){
      
     Flow::page('Gestion_Sesion');
     
   });
   
   Ruta::get('/index.php',function(){
       
     Flow::page('Gestion_Sesion');
     
   });
   
   
   Ruta::get('/inicio',function(){
    Flow::page('inicio');
   });
   
   Ruta::get('/Administrar',function(){
     Flow::page('Administrar');
   }); 
};