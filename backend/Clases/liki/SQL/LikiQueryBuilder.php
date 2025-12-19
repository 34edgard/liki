<?php  
namespace Liki\SQL;

class LikiQueryBuilder {  
    public static function parse(string $query): array {  
        $datos = [];  
          
        // Parsear campos  
        if (preg_match('/campos:\s*([^;]+)/', $query, $matches)) {  
            $datos['campos'] = array_map('trim', explode(' , ', $matches[1]));  
        }  
          
        // Parsear valores    
        if (preg_match('/valores:\s*([^;]+)/', $query, $matches)) {  
            $datos['valores'] = array_map('trim', explode(' , ', $matches[1]));  
        }  
          
        // Parsear where  
        if (preg_match('/where:\s*{([^}]+)}/', $query, $matches)) {  
        
        
        
            $datos['where'] = self::parseWhere($matches[1]);  
        }  
         // print_r($datos);
        return $datos;  
    }  
      
    private static function parseWhere(string $whereString): array {  
    $array = array_map('trim', explode(';', $whereString));
   // print_r($array);
    
    
         foreach($array as $a){
            $where = array_map('trim', explode(',', $a));
            
            //print_r($where);
            $e['campo'] = trim(str_replace('campo:','',$where[0]));
             $e['operador'] = trim(str_replace('operador:','',$where[1]));
              $e['valor'] = trim(str_replace('valor:','',$where[2]));
              $nwhere[] = $e;
     //   print_r($nwhere);
        
         }
    
     
    
    
    
    
    
    
    
        // Lógica para parsear las condiciones del where  
       
    
     // Retornar array en formato estándar de Liki  
    return $nwhere;
    }  
}  


/*LikiQueryBuilder::parse("
campos: c1 , c2;
where: {
    campo: c1 , operador: = , valor: 4 ;
    campo: c2 , operador: = , valor: 7
}
");
*/