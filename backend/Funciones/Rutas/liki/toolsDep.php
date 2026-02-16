<?php

use Liki\Testing\TestingRutas;
use Liki\Routing\Ruta;

use Liki\Plantillas\Flow;
use Funciones\BdSQLWeb;

use Liki\DelegateFunction;



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
      
    
    
    Ruta::prefix('/errors',function(){
      Ruta::get('/',function(){
        
        $logs = file_get_contents('./logs/errors.log');
        if ($logs === '') {
        echo "no hay logs";
        return;
        }
        $logs = explode("\n",$logs);
        foreach($logs as $log){
            if($log === '') continue;
            echo $log.'<hr />';
        }
        
      });          
         Ruta::post('/borrar',function(){
           file_put_contents('./logs/errors.log','');
            $logs = file_get_contents('./logs/errors.log');
            
           echo $logs."no hay logs";
         });                 
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
            echo $output;
        } else {  
        
        
        $comandos = DelegateFunction::loadData('Tools/Terminal');
        
        
        
            // Comando de Liki: parsear y ejecutar desde $comandos  
            $parts = explode(' ', $comando);  
            $tipoAccion = explode(':',$parts[0]);  
            $tipo = $tipoAccion[0];
             $accion = $tipoAccion[1] ?? '';
            if (isset($comandos[$tipo][$accion])) {  
                comandoExec($comandos[$tipo][$accion], $parts[1] ?? '', array_slice($parts, 2));  
            }  
        }
          },['comando']);                    
        });   
};