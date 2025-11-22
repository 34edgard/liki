<?php


namespace Funciones;

use Liki\Database\ConsultasBD;

class BdSQLWeb{

public static function bdSQLWeb(){
    

$tablas = file_get_contents("./sql/tablas.sql");
$registros = file_get_contents("./sql/registros.sql");

//1255667
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





//$con->ejecutarConsulta("INSERT INTO tipo_parentesco (nombre) VALUES ('representante'),('madre'),('padre')");




$res = $con->consultarRegistro("SELECT name FROM sqlite_master WHERE type='table' ");
//$res = $con->consultarRegistro("SELECT COUNT(sexo) FROM estudiante WHERE sexo = 'masculino' AND  ");
/*foreach($res as $r){
    print_r($r);
    echo "<hr >";
}

*/

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