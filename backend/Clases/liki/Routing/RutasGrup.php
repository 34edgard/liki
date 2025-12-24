<?php

namespace Liki\Routing;
use Liki\DelegateFunction;

class RutasGrup{
    
    public static $rutas = [
        "GrupoRutas/test"
    ];
    public static function init(){
        $rutas = self::$rutas;
        foreach($rutas as $ruta){
            
            DelegateFunction::exec($ruta);
        }
    }
}