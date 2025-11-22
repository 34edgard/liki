<?php

namespace Liki\Database;

use Liki\Database\ConsultasBD;
use Liki\SQL\Registrar;
use Liki\SQL\Editar;
use Liki\SQL\Eliminar;
use Liki\SQL\Consultar;

class Tabla{
      protected  $tabla ;
      public $Consultas_BD;
      public $consultar;
      public $registrar;
      public $editar;
      public $eliminar;
    
    public function __construct(string $tabla){
      $this->tabla = $tabla;
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
    
}