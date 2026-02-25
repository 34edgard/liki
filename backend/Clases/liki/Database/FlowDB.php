<?php

namespace Liki\Database;

use Liki\Database\ConsultasBD;
use Liki\DelegateFunction;
use Liki\Validar;

class FlowDB{
      protected  $tabla ;
      public $Consultas_BD;
        public $consultaArray;
        public $camposValidar =[];
        
        public array $joins = [];
        private static $instance = null;  
            private static $connection = null;  
              
            public static function conf(string $modelClass): self {  
                if (self::$instance === null) {  
                    self::$instance = new self();  
                    self::$connection = new ConsultasBD(); // Única conexión  
                }  
                  
                // Obtener nombre de tabla del modelo  
                $model = DelegateFunction::loadModel($modelClass);  
                self::$instance->camposValidar = $model->campos ?? [];  
                self::$instance->tabla = $model->tabla ?? '';  
                self::$instance->consultaArray['tabla'] = $model->tabla;  
                  
                return self::$instance;  
            }  
        
        
    public function __construct(){
      
   $this->Consultas_BD = new ConsultasBD;
   }
    
    public function registrar(array $datos){
      $datos['tabla'] = $this->consultaArray['tabla'] ?? $this->tabla;
      $parametrosRegistro = [];
      $sql = \Liki\SQL\Registrar::generar_sql($datos,$parametrosRegistro);
     try{
         
      $this->Consultas_BD->ejecutarConsulta($sql,$parametrosRegistro);
     }catch(\Exception $e){
         echo "Error: ". $e->getMessage();
     }
    
    }
    
    public function consultar(array $datos){
      $datos['tabla'] = $this->consultaArray['tabla'] ?? $this->tabla;
      $parametrosConsulta = [];
    
      try{
       \Liki\SQL\Consultar::setJoin($this->joins);
      $sql = \Liki\SQL\Consultar::generar_sql($datos,$parametrosConsulta);
    
      return  $this->Consultas_BD->consultarRegistro($sql,$parametrosConsulta);
           }catch(\Exception $e){
               echo "Error: ". $e->getMessage();
           }
    }
    
    public function consultarId(array $datos){
      $datos['tabla'] = $this->consultaArray['tabla'] ?? $this->tabla;
      $datos['orderBy'] = ["campo"=>$datos['campos'][0],"direccion"=>'DESC'];
      $datos['limit'] = 1;
      $parametrosConsultaId = [];
      try{
        
      $sql = \Liki\SQL\Consultar::generar_sql($datos,$parametrosConsultaId);
      return $this->Consultas_BD->consultarRegistro($sql,$parametrosConsultaId);
          }catch(\Exception $e){
              echo "Error: ". $e->getMessage();
          }
    }
    
    public function editar(array $datos){
      $datos['tabla'] = $this->consultaArray['tabla'] ?? $this->tabla;   
      $parametrosEdicion = [];        
      
    try{
      $sql = \Liki\SQL\Editar::generar_sql($datos,$parametrosEdicion);
      $this->Consultas_BD->ejecutarConsulta($sql, $parametrosEdicion);
       }catch(\Exception $e){
           echo "Error: ". $e->getMessage();
       }
    }
    
    
    public function eliminar(array $datos){
      $datos['tabla'] = $this->consultaArray['tabla'] ?? $this->tabla; 
      $parametrosEliminar = [];
    try{
      $sql = \Liki\SQL\Eliminar::generar_sql($datos, $parametrosEliminar);
      $this->Consultas_BD->ejecutarConsulta($sql, $parametrosEliminar);
      }catch(\Exception $e){
          echo "Error: ". $e->getMessage();
      }

 }


public function campos(array $campos){
   foreach ($campos as $id){
    Validar::isInclude($this->camposValidar,$id);
   }
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


 
   $this->joins[] = [
        'type' => strtoupper($tipo),
        'table' => $campo,
        'on' => $where
    ];
    return $this;
}


public function reset(){
     
     $this->consultaArray = [];
    $this->joins =[];
     return $this;
}


public function where(array $where){
      $Nwhere =[];
      foreach($where as $name => $valor){
        if(is_array($valor) && count($valor) == 2){
            
            $Nwhere[] = [
                "campo"=>$name,
                "operador"=>$valor[0],
                "valor"=>$valor[1]
            ];
        }else{
             $Nwhere[] = [
                 "campo"=>$name,
                 "operador"=>'=',
                 "valor"=>$valor
             ];
        }
         
      }
    return $Nwhere;
}

public function get(array $where = []){
  $Nwhere = $this->where($where);
    $this->consultaArray['where'] = $Nwhere;
  //  print_r($this->consultaArray);
  $resul =  $this->consultar($this->consultaArray);
$this->reset();
return $resul;
}

public function post(array $valores){
     $this->consultaArray['valores'] = $valores;
     
    $this->registrar($this->consultaArray);
    
    $this->reset();
}

public function put(array $where = []){
      $Nwhere = $this->where($where);
     $this->consultaArray['where'] = $Nwhere;
     
    $this->editar($this->consultaArray);
       $this->reset();
}
public function delete(array $where = []){
      $Nwhere = $this->where($where);
    $this->consultaArray['where'] = $Nwhere;
    
    
    $this->eliminar($this->consultaArray);
       $this->reset();
}
    
}