<?php

namespace Liki\Database;

use Liki\Database\ConsultasBD;
use Liki\SQL\Registrar;
use Liki\SQL\Editar;
use Liki\SQL\Eliminar;
use Liki\SQL\Consultar;
use Exception;
class Tabla{
      protected  $tabla ;
      public $Consultas_BD;
      public $consultar;
      public $registrar;
      public $editar;
      public $eliminar;
      public $consultaArray;
    public function __construct(string $tabla){
      $this->tabla = $tabla;
      $this->consultaArray['tabla'] = $tabla;
      $this->Consultas_BD = new ConsultasBD;
      $this->consultar = new Consultar;
      $this->registrar = new Registrar;
      $this->editar = new Editar;
      $this->eliminar = new Eliminar;
          
    }
    
    public function registrar(array $datos){
      $datos['tabla'] = $this->tabla;
      $parametrosRegistro = [];
      $sql = $this->registrar->generar_sql($datos,$parametrosRegistro);
     try{
         
      $this->Consultas_BD->ejecutarConsulta($sql,$parametrosRegistro);
     }catch(Exception $e){
         echo "Error: ". $e->getMessage();
     }
    
    }
    
    public function consultar(array $datos){
      $datos['tabla'] = $this->tabla;
      $parametrosConsulta = [];
    
      try{
      $sql = $this->consultar->generar_sql($datos,$parametrosConsulta);
    
      return  $this->Consultas_BD->consultarRegistro($sql,$parametrosConsulta);
           }catch(Exception $e){
               echo "Error: ". $e->getMessage();
           }
    }
    
    public function consultarId(array $datos){
      $datos['tabla'] = $this->tabla;
      $datos['orderBy'] = ["campo"=>$datos['campos'][0],"direccion"=>'DESC'];
      $datos['limit'] = 1;
      $parametrosConsultaId = [];
      try{
      $sql = $this->consultar->generar_sql($datos,$parametrosConsultaId);
      return $this->Consultas_BD->consultarRegistro($sql,$parametrosConsultaId);
          }catch(Exception $e){
              echo "Error: ". $e->getMessage();
          }
    }
    
    public function editar(array $datos){
      $datos['tabla'] = $this->tabla;   
      $parametrosEdicion = [];        
      
    try{
      $sql = $this->editar->generar_sql($datos,$parametrosEdicion);
      $this->Consultas_BD->ejecutarConsulta($sql, $parametrosEdicion);
       }catch(Exception $e){
           echo "Error: ". $e->getMessage();
       }
    }
    
    
    public function eliminar(array $datos){
      $datos['tabla'] = $this->tabla; 
      $parametrosEliminar = [];
    try{
      $sql = $this->eliminar->generar_sql($datos, $parametrosEliminar);
      $this->Consultas_BD->ejecutarConsulta($sql, $parametrosEliminar);
      }catch(Exception $e){
          echo "Error: ". $e->getMessage();
      }

 }


public function campos(array $campos){
    $this->consultaArray['campos'] = $campos;
    return $this;
}
public function valores(array $valores){
    $this->consultaArray['valores'] = $valores;
    return $this;
}

public function tabla(string $tabla = ''){
    if($tabla == '' ) $tabla = $this->tabla;
    $this->consultaArray['tabla'] = $tabla;
    return $this;
}

public function limit(int $limit, int $offset=0 ){
       if($limit == 0) return $this;
       $this->consultaArray['limit'] = $limit;
       $this->consultaArray['offset'] = $offset;
    
       return $this;
}

public function orderBy(string $campo ,string $direccion ='DESC' ){
    $this->consultaArray['orderBy']['campo'] = $campo;
    $this->consultaArray['orderBy']['direccion'] = $direccion;
        
   return $this;
    
}

public function join($tipo,$campo,$where){
    $this->consultar->addJoin($tipo, $campo,$where);
    return $this;
}


public function reset(){
     $tabla = $this->consultaArray['tabla'] ?? $this->tabla;
     $this->consultaArray = [];
     $this->tabla($tabla);
     return $this;
}


public function where(array $where){
      $Nwhere =[];
      foreach($where as $name => $valor){
          $Nwhere[] = [
              "campo"=>$name,
              "operador"=>'=',
              "valor"=>$valor
          ];
      }
    return $Nwhere;
}

public function get(array $where = []){
  $Nwhere = $this->where($where);
    $this->consultaArray['where'] = $Nwhere;
   // print_r($this->consultaArray);
  return  $this->consultar($this->consultaArray);
}

public function post(array $valores){
     $this->consultaArray['valores'] = $valores;
     
    $this->registrar($this->consultaArray);
}

public function put(array $where = []){
      $Nwhere = $this->where($where);
     $this->consultaArray['where'] = $Nwhere;
     
    $this->editar($this->consultaArray);
}
public function delete(array $where = []){
      $Nwhere = $this->where($where);
    $this->consultaArray['where'] = $Nwhere;
    
    
    $this->eliminar($this->consultaArray);
}
    
}