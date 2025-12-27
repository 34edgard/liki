<?php

use Funciones\BdSQLWeb;
use Liki\Routing\Ruta;

return  function (){
        Ruta::get('/bdSQLWeb',[BdSQLWeb::class,'bdSQLWeb']);
        /*
         * resto de endpoints
         */
    
};