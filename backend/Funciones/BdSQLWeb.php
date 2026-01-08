<?php




namespace Funciones;

use Liki\Database\ConsultasBD;

class BdSQLWeb{

public static function bdSQLWeb(){
    $con = new ConsultasBD();


    $sql['sqlite']= "SELECT name FROM sqlite_master WHERE type='table' ";
    $sql['mysql']='SHOW TABLES';
    

$res = $con->consultarRegistro($sql[],[]);

$i=1;
foreach($res as $a => $t){
    
    echo $i.'-'.$t['name'].'<br />';
    $i++;
}

//print_r($res);

 
 
 
foreach($res as $tabla){
    
    $registroTabla = $con->consultarRegistro('SELECT * FROM '.$tabla['name']);
    echo "<h1>{$tabla['name']}</h1><hr />";
    echo "<table border='1'> ";
    
    echo "<tr>";
    //print_r($registroTabla);
    if(isset($registroTabla[0])){
        
    
        foreach($registroTabla[0] as $id => $campo){
        echo "<td>{$id}</td>";
        }
    }
    
    echo "</tr>";
     foreach($registroTabla as $registro){
        
        
         echo "<tr>";
        foreach($registro as $id => $campo){
            echo "<td>{$campo}</td>";
        }
        echo "</tr>";
     }

    echo " </table>";

    
}

}


}