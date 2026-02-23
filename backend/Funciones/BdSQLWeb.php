<?php




namespace Funciones;

use Liki\Database\ConsultasBD;

class BdSQLWeb{

public static function bdSQLWeb(){
    $con = new ConsultasBD();


    $sql['sqlite']= "SELECT name FROM sqlite_master WHERE type='table' ";
    $sql['mysql']='SHOW TABLES';
    

$res = $con->consultarRegistro($sql[DB_DRIVER],[]);
$arrayTablas =[];
$i=1;
foreach($res as   $tabla){
    
 foreach($tabla as $t){
   echo $i.'-'.$t.'<br />';
  $arrayTablas[]=$t;
    $i++;
 }
}

//

 
 
 
foreach($arrayTablas as $tabla){
 //  foreach($tabla as $id => $nombre) 
       
   
    $registroTabla = $con->consultarRegistro('SELECT * FROM '.$tabla);
    echo "<h1>{$tabla}</h1><hr />";
    echo "<table border='1' class='table'> ";
    
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