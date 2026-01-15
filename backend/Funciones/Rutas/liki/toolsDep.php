<?php

use Liki\Testing\TestingRutas;
use Liki\Routing\Ruta;

use Liki\Plantillas\Flow;
use Funciones\BdSQLWeb;

use Liki\Consola\GeneradorCodigo;
use Liki\Database\MigrationRunner;
use Liki\Consola\db;

function comandoExec(callable $comando,$nombre,$extras){
   
 if(count($extras) == 0)  $comando($nombre);
 if(count($extras) > 0) $comando($nombre,$extras);
}


return  function (){
       Ruta::prefix('/testing',function(){
           Ruta::get('/rutas',function(){
                  TestingRutas::procesar_testing();
                   
                   // Mostrar interfaz de testing
                   TestingRutas::mostrar_rutas_disponibles();
              });
           Ruta::get('/rutas/formulario',function($p){
                  //TestingRutas::procesar_testing();
                   extract($p);
                   // Mostrar interfaz de testing
                   TestingRutas::generar_formulario_ruta($ruta_index);
              },['accion','ruta_index']);
           
       }); 
     
    
    
 Ruta::prefix('/bdSQLWeb',function(){
   Ruta::get('/tablas',[BdSQLWeb::class,'bdSQLWeb']);          
                   
 });      
        
        Ruta::prefix('/Terminal',function(){
          Ruta::get('/interfaz',function(){
              Flow::html('componentes/Terminal');
          });          
          Ruta::get('/exec',function($p){
          // echo
        extract($p);
        
        if (strpos($comando, '\\') === 0) {
            // Comando de sistema: remover \ y ejecutar con shell_exec
            $comando = substr($comando, 1);
            $output = shell_exec($comando);
        } else {  
        
        
        $comandos = [
        'modelo'=>[GeneradorCodigo::class,'generateModel'],
        'controlador'=>[GeneradorCodigo::class,'generateController'],
        'likiClass'=>[GeneradorCodigo::class,'generateClassLiki'],
        'migracion-run'=>[MigrationRunner::class,'run'],
        'liki-grup'=>[GeneradorCodigo::class,'generateGrupoLiki'],
        'app-grup'=>[GeneradorCodigo::class,'generateGrupoApp'],
        'func-grup'=>[GeneradorCodigo::class,'generateGrupoFunc'],
        'db:import'=>[db::class,'import'],
        'db:export'=>[db::class,'exportDatabase']
        ];
        
        
            // Comando de Liki: parsear y ejecutar desde $comandos  
            $parts = explode(' ', $comando);  
            $tipo = $parts[0];  
            if (isset($comandos[$tipo])) {  
                comandoExec($comandos[$tipo], $parts[1] ?? '', array_slice($parts, 2));  
            }  
        }
          },['comando']);                    
        });   
        
        
        
        
    
};