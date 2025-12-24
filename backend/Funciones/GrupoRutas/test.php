<?php

use Liki\Testing\TestingRutas;
use Liki\Routing\Ruta;




return new class{
    public static function run(){
        
     Ruta::get('/testing/rutas',function(){
           TestingRutas::procesar_testing();
            
            // Mostrar interfaz de testing
            TestingRutas::mostrar_rutas_disponibles();
       });
        
        
        
        
    }
};