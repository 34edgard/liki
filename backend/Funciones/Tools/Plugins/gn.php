<?php

use Liki\Consola\Gn;


return [
    'modelo'=>[Gn::class,'generateModel'],
     'middleware'=>[Gn::class,'generateMiddleware'],
    'controlador'=>[Gn::class,'generateController'],
    'manejador'=>[Gn::class,'generateManejador'],
    'likiClass'=>[Gn::class,'generateClassLiki'],
    'liki-grup'=>[Gn::class,'generateGrupoLiki'],
    'app-grup'=>[Gn::class,'generateGrupoApp'],
    'func-grup'=>[Gn::class,'generateGrupoFunc'],
    'model-grup-db'=>[Gn::class,'gnModelosDB']
     
    
     ];
