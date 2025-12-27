<?php


use Liki\Routing\Ruta;
use Liki\Plantillas\Plantilla;



return  function (){
        
 
   
   Ruta::get('/',function(){
       
     Plantilla::paginas('Gestion_Sesion');
     
   });
   
   Ruta::get('/index.php',function(){
   
   Plantilla::paginas('Gestion_Sesion');
   
   });
   
   
   Ruta::get('/inicio',function(){
    Plantilla::paginas('inicio');
   });
   
   Ruta::get('/Administrar',function(){
     Plantilla::paginas('Administrar');
   }); 
};