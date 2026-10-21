<?php
namespace Liki\Database;
use Liki\Database\FlowDB;
use Liki\Database\ConsultasBD;

class BdSQLWeb{
public static function getTabla(array $p){
    $db = new FlowDB();
    
    $con = new ConsultasBD();
    
    ['tabla'=>$tabla,'campo'=>$campo,'limit'=>$limit, 'pagina' => $pagina] = $p;
    
    
         
      $td['sqlite']= "PRAGMA table_info($tabla) ";
          $td['mysql']='SHOW TABLES';
          
          
         $Tablas = $con->consultarRegistro($td[DB_DRIVER]);
        $totalPaginas = $con->consultarRegistro('SELECT COUNT(*) as num FROM '.$tabla)[0] ?? ['num' =>0];
        
     // print_r($Tablas);
    $where = [];
      //echo "--------------------";
    foreach($Tablas ?? [] as $t){
        $where[$t['name']] = ['LIKE',$campo.'%'] ;
    }
    
    $registroTabla = $db->tabla($tabla)
    ->orWhere($where)
    ->limit($limit ?? 50, $pagina ?? '')
    ->get();
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
    
    echo '<div>'; 
        $paginas = $totalPaginas['num'] / $limit;
        
        for($i = 1; $paginas > $i; $i + $limit)
        echo ' <input type="checkbox" class="btn btn-info" name="pagina" value="'.$i.'">';
    echo '</div>';
}
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
       
   
    $registroTabla = $con->consultarRegistro('SELECT * FROM '.$tabla.' LIMIT 50');
    $totalPaginas = $con->consultarRegistro('SELECT COUNT(*) as num FROM '.$tabla)[0] ?? ['num' =>0];
    
    echo "<h1>{$tabla}</h1><hr />";
    echo '<form hx-post="/bdSQLWeb/tablas" hx-target="#table_'.$tabla.'" hx-trigger="change">
    <input type="text" name="campo" />
    <input name="tabla" value="'.$tabla.'" hidden />
    ';
    
    echo '<select name="limit">
    <option value="10" >10</option>
     <option value="50" >50</option>
      <option value="100" >100</option>
    </select>';
    
    echo "<table border='1' class='table' id='table_{$tabla}'> ";
    
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
    echo '<div>'; 
    
    $paginas = $totalPaginas['num'] / 10;
    $paginas = (int)$paginas;
    print_r($paginas > 1);
    //for($i = 1; $paginas > $i; $i + 50)
    //echo ' <input type="checkbox" class="btn btn-info" name="pagina" value="'.$i.'">';
//echo ' <input type="checkbox" class="btn btn-info" name="pagina" value="">';

echo '</div>';

    echo " </table>";
echo '</form>';
 }


}


}