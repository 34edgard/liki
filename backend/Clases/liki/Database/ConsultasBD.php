<?php

namespace Liki\Database;

use Liki\Database\ConexionesBD;
use Liki\ErrorHandler;
use PDO;
use PDOException;
use Exception;
class ConsultasBD extends ConexionesBD {

    /**
     * Ejecuta una consulta SQL preparada (INSERT, UPDATE, DELETE).
     *
     * @param string $sql La consulta SQL con marcadores de posición.
     * @param array $parametros Un array asociativo de parámetros para vincular.
     * @return int El número de filas afectadas.
     * @throws Exception Si hay un error en la conexión o al ejecutar la consulta.
     */
    
    public $conexion ;
    public function __construct(){
        
        $this->conexion = $this->crearConexion();
  if (!$this->conexion) {
      throw new Exception('Error en la conexión a la base de datos.');
  } 
  }
    public function query(string $query){
       return  $this->conexion->query($query);
    }
    public function prepare(string $query){
       return  $this->conexion->prepare($query);
    }
    
    
    public function ejecutarConsulta(string $sql, array $parametros = []): int {
       // $conexion = $this->crearConexion();
        
     
        
        try {
            $stmt = $this->conexion->prepare($sql); // Prepara la consulta
            $stmt->execute($parametros); // Ejecuta la consulta con los parámetros vinculados
            return $stmt->rowCount(); // Retorna el número de filas afectadas
        } catch (PDOException $e) {
            
            ErrorHandler::getInstance()->handle(
            ErrorHandler::DB_QUERY_ERROR,
            'Error en la consulta',
            ['sql'=>$sql,'error'=>$e->getMessage()],
            500
            );
            
             } finally {
            $this->cerrarConexion($this->conexion);
        }
    }

    /**
     * Consulta registros de la base de datos usando una consulta preparada.
     *
     * @param string $sql La consulta SQL con marcadores de posición.
     * @param array $parametros Un array asociativo de parámetros para vincular.
     * @return array Un array de arrays asociativos con los resultados.
     * @throws Exception Si hay un error en la consulta.
     */
    public function consultarRegistro(string $sql, array $parametros = []): array {
      

        $arreglo = [];
        try {
            $stmt = $this->conexion->prepare($sql); // Prepara la consulta
            $stmt->execute($parametros); // Ejecuta la consulta con los parámetros vinculados
            $arreglo = $stmt->fetchAll(PDO::FETCH_ASSOC); // Obtiene todos los resultados
        } catch (PDOException $e) {
            ErrorHandler::getInstance()->handle(
            ErrorHandler::DB_QUERY_ERROR,
            'Error en la consulta',
            ['sql'=>$sql,'error'=>$e->getMessage()],
           500
           
            );
            
        } finally {
            $this->cerrarConexion($this->conexion);
        }

        return $arreglo;
    }
}


