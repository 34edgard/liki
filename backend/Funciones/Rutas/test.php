<?php

use Liki\Testing\TestingRutas;
use Liki\Routing\Ruta;




return  function (){
        
     Ruta::get('/testing/rutas',function(){
           TestingRutas::procesar_testing();
            
            // Mostrar interfaz de testing
            TestingRutas::mostrar_rutas_disponibles();
       });
        
        
        
        
    
};