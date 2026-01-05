<?php

namespace Liki;


class DelegateFunction{
    
    
    public static function exec($name = '',$parametros=[],$funcionesExtra =[]){
       // echo CONTOLLER_PATH.$name.'.php'.'<br />';
        $class = include CONTOLLER_PATH.'Manejadores/'.$name.'.php';
      return  $class::run($parametros,$funcionesExtra);
    }
    
}