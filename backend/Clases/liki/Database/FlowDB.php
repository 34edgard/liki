<?php
namespace Liki\Database;

use Liki\Database\ConsultasBD;
use Liki\DelegateFunction;
use Liki\Validar;
use Liki\SQL\Registrar;
use Liki\SQL\Consultar;
use Liki\SQL\Editar;
use Liki\SQL\Eliminar;
use Exception;
class FlowDB{
      protected  $tabla ;
      protected $modelo;
        public $consultaArray = [];
        public $camposValidar =[];
        
        
        private static $instance = null;  
            private  $connection = null;  
              
            public static function conf(string $modelClass): self {  
                if (self::$instance === null) {  
                    self::$instance = new self();  
                    self::$instance->connection = new ConsultasBD(); // Única conexión  
                }  
                  
                // Obtener nombre de tabla del modelo  
                self::$instance->modelo = DelegateFunction::loadModel($modelClass);  
              
                self::$instance->tabla = self::$instance->modelo->getTabla();  
                self::$instance->consultaArray['tabla'] = self::$instance->modelo->getTabla();  
                  
                return self::$instance;  
            }  
        
        
    public function __construct(?string $modelClass = null){
        $this->connection = new ConsultasBD;
        if ($modelClass !== null) {
            $this->modelo = DelegateFunction::loadModel($modelClass);
            $this->tabla  = $this->modelo->getTabla();
            $this->consultaArray['tabla'] = $this->tabla;
        }
    }
    
    public function registrar(array $datos){
      $datos['tabla'] = $this->consultaArray['tabla'] ?? $this->tabla;
      $parametrosRegistro = [];
      $sql = Registrar::generar_sql($datos,$parametrosRegistro);
     try{
         
      $this->connection->ejecutarConsulta($sql,$parametrosRegistro);
     }catch(Exception $e){
         throw new Exception("Error: ". $e->getMessage());
     }
    
    }
    
    public function consultar(array $datos){
      $datos['tabla'] = $this->consultaArray['tabla'] ?? $this->tabla;
      $parametrosConsulta = [];
      try{
       
      $sql = Consultar::generar_sql($datos,$parametrosConsulta);
    
      return  $this->connection->consultarRegistro($sql,$parametrosConsulta);
           }catch(Exception $e){
               throw new Exception("Error: ". $e->getMessage());
           }
    }
    
    public function consultarId(array $datos){
      $datos['tabla'] = $this->consultaArray['tabla'] ?? $this->tabla;
      $datos['orderBy'] = ["campo"=>$datos['campos'][0],"direccion"=>'DESC'];
      $datos['limit'] = 1;
      $parametrosConsultaId = [];
      try{
        
      $sql = Consultar::generar_sql($datos,$parametrosConsultaId);
      return $this->connection->consultarRegistro($sql,$parametrosConsultaId);
          }catch(Exception $e){
              throw new Exception("Error: ". $e->getMessage());
          }
    }
    
    public function editar(array $datos){
      $datos['tabla'] = $this->consultaArray['tabla'] ?? $this->tabla;   
      $parametrosEdicion = [];        
      
    try{
      $sql = Editar::generar_sql($datos,$parametrosEdicion);
      $this->connection->ejecutarConsulta($sql, $parametrosEdicion);
       }catch(Exception $e){
           throw new Exception("Error: ". $e->getMessage());
       }
    }
    
    
    public function eliminar(array $datos){
      $datos['tabla'] = $this->consultaArray['tabla'] ?? $this->tabla; 
      $parametrosEliminar = [];
    try{
      $sql = Eliminar::generar_sql($datos, $parametrosEliminar);
      $this->connection->ejecutarConsulta($sql, $parametrosEliminar);
      }catch(Exception $e){
          throw new Exception("Error: ". $e->getMessage());
      }

 }


public function campos(array $campos){
  if(isset($this->modelo))
      $this->modelo->validarCampos($campos);
  
    $this->consultaArray['campos'] = $campos;
    return $this;
}
public function valores(array $valores){
   if(isset($this->modelo))
       $this->modelo->validarValores($valores,$this->consultaArray['campos']);  

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
public function groupBy(string $campo){
    $this->consultaArray['groupBy'][] = $campo;
    return $this;
}

public function having(array $condiciones){
    $this->consultaArray['having'] =$this->consultaArray['having']  ?? $this->where($condiciones);
    return $this;
}


public function innerJoin(string $campo,string $where){
    $this->jion('INNER',$campo,$where);
    return $this;
}
public function leftJoin(string $campo,string $where){
    $this->jion('LEFT',$campo,$where);
    return $this;
}
public function rightJoin(string $campo,string $where){
    $this->jion('RIGHT',$campo,$where);
    return $this;
}
public function fullJoin(string $campo,string $where){
    $this->jion('FULL',$campo,$where);
    return $this;
}
public function crossJoin(string $campo,string $where){
    $this->jion('CROSS',$campo,$where);
    return $this;
}
public function selfJoin(string $campo,string $where){
    $this->jion('SELF',$campo,$where);
}
public function join(string $tipo,string $campo,string $where): object {
   $this->consultaArray['join'][] = [
        'type' => strtoupper($tipo),
        'table' => $campo,
        'on' => $where
    ];
    return $this;
}

public function reset(){
     $this->consultaArray =[
     'groupBy'=>[],
     'having'=>[],
     'join'=>[],
     'where'=>[]];
     return $this;
}
 
public function orWhere(array $where){
$this->consultaArray['where'][] = ['OR'=> $this->where($where)];
return $this;
}


private function addWhere(array $Nwhere):void{
    $whereActual = $this->consultaArray['where'] ?? [];
    $this->consultaArray['where']= array_merge($whereActual,$Nwhere);
}
public function where(array $where){
      $Nwhere =[];
    
      foreach($where as $name => $valor){
        if(is_array($valor) && count($valor) == 2 && !is_array($valor[0])){
            $Nwhere[] = [
                "campo"=>$name,
                "operador"=>$valor[0] ,
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
    $this->addWhere($Nwhere);
  $resul = $this->consultar($this->consultaArray);
$this->reset();
return $resul;
}

public function post(array $valores){
   if(isset($this->modelo))
          $this->modelo->validarValores($valores,$this->consultaArray['campos']);  
   
  $this->consultaArray['valores'] = $valores;
     
    $this->registrar($this->consultaArray);
    
    $this->reset();
}
public function head(array $where = []): bool {
   $rest = $this->campos(['COUNT(*) as num'])
    ->get($where);
   
  return  $rest[0]['num'] > 0;
}
public function put(array $where = []){
      $Nwhere = $this->where($where);
      $this->addWhere($Nwhere);
    $this->editar($this->consultaArray);
       $this->reset();
}

public function patch(array $where = [], array $campos = ['*']):array{
  $consulta = $this->consultaArray['tabla'] ?? $this->tabla;
  $this->put($where);
  return $this->tabla($consulta['tabla'])->campos($campos)->get($where);
    
}

public function delete(array $where = []){
      $Nwhere = $this->where($where);
  $this->addWhere($Nwhere);
   $this->eliminar($this->consultaArray);
       $this->reset();
}
    
}