<?php

use Liki\Consola\GeneradorCodigo;


return [
    'modelo'=>[GeneradorCodigo::class,'generateModel'],
    'controlador'=>[GeneradorCodigo::class,'generateController'],
    'likiClass'=>[GeneradorCodigo::class,'generateClassLiki'],
    'liki-grup'=>[GeneradorCodigo::class,'generateGrupoLiki'],
    'app-grup'=>[GeneradorCodigo::class,'generateGrupoApp'],
    'func-grup'=>[GeneradorCodigo::class,'generateGrupoFunc']
      ];
