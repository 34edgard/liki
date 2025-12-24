<?php

use Funciones\BdSQLWeb;
use Liki\Routing\Ruta;

return new class{
    public static function run(){
        Ruta::get('/bdSQLWeb',[BdSQLWeb::class,'bdSQLWeb']);
        /*
         * resto de endpoints
         */
    }
};