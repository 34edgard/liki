<?php

namespace Liki\Consola;
use Liki\Database\ConsultasBD;
class db{
 
public static function exportDatabase() {
    $tables = [];
   $conn =  new ConsultasBD();
    $sql['sqlite']= "SELECT name FROM sqlite_master WHERE type='table' ";
    $sql['mysql']='SHOW TABLES';
    
    $result = $conn->consultarRegistro($sql[DB_DRIVER],[]);


print_r($result);
return;

    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }

    $sqlScript = "";
    
    foreach ($tables as $table) {
        // Obtener la estructura de la tabla
        $result = $conn->query("SHOW CREATE TABLE $table");
        $row = $result->fetch_row();
        $sqlScript .= "\n\n" . $row[1] . ";\n\n";

        // Obtener los datos de la tabla
        $result = $conn->query("SELECT * FROM $table");
        while ($row = $result->fetch_assoc()) {
            $sqlScript .= "INSERT INTO $table (";
            $sqlScript .= implode(", ", array_keys($row)) . ") VALUES ('";
            $sqlScript .= implode("', '", array_values($row)) . "');\n";
        }
    }

    
    
    $fileName = $database . '_backup_' . date('Y-m-d_H-i-s') . '.sql';
    file_put_contents($fileName, $sqlScript);
    
    
    
}




public static function import(){
        
            
            
        
        $tablas = file_get_contents("./sql/sql/sqlite/tablas.sql");
        $registros = file_get_contents("./sql/sql/sqlite/registros.sql");
        
        
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

