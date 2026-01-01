<?php

use Liki\Testing\TestingRutas;
use Liki\Routing\Ruta;


use Funciones\BdSQLWeb;



return  function (){
       Ruta::prefix('/testing',function(){
           Ruta::get('/rutas',function(){
                  TestingRutas::procesar_testing();
                   
                   // Mostrar interfaz de testing
                   TestingRutas::mostrar_rutas_disponibles();
              });
           Ruta::get('/rutas/formulario',function($p){
                  //TestingRutas::procesar_testing();
                   extract($p);
                   // Mostrar interfaz de testing
                   TestingRutas::generar_formulario_ruta($ruta_index);
              },['accion','ruta_index']);
           
       }); 
     
    
    
 Ruta::prefix('/bdSQLWeb',function(){
   Ruta::get('/tablas',[BdSQLWeb::class,'bdSQLWeb']);          
                   
 });      
        
        
        
        
    
};