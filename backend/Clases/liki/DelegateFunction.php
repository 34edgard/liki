<?php

namespace Liki;


class DelegateFunction{
    
    
    public static function exec($name = '',$parametros=[],$funcionesExtra =[]){
       // echo CONTOLLER_PATH.$name.'.php'.'<br />';
        $class = include CONTOLLER_PATH.'Manejadores/'.$name.'.php';
      return  $class::run($parametros,$funcionesExtra);
    }
    
    public static function loadData($name = ''){
       // echo CONTOLLER_PATH.$name.'.php'.'<br />';
        return include CONTOLLER_PATH.'/'.$name.'.php';
  
    }
}