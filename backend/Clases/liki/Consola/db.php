<?php

namespace Liki\Consola;
use Liki\Database\ConsultasBD;
class db{
 
public static function exportDatabase() {
   
}




public static function import(){
        
            
            
        
        $tablas = file_get_contents("./DataBase/sql/sqlite/tablas.sql");
        $registros = file_get_contents("./DataBase/sql/sqlite/registros.sql");
        
        
        $con = new ConsultasBD();
        
        //print($tablas);
        
        $tables = explode(';',$tablas);
        foreach($tables as $t){
            
        
        $con->ejecutarConsulta($t);
        }
        
        
        $rs = explode(';',$registros);
        foreach($rs as $rds){
            
        
        $con->ejecutarConsulta($rds);
        }
        
        
}



}

