<?php

namespace Liki;


class ExecFunc{
    
    
    public static function exec($name = '',$parametros=[],$funcionesExtra =[]){
        
        $class = include CONTOLLER_PATH.$name.'.php';
      return  $class::run($parametros,$funcionesExtra);
    }
    
}