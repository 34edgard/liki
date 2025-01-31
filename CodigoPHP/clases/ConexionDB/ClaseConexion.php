<?php
/*
  public $puerto = "sql210.infinityfree.com";
  public $usuario = "if0_37533972";
  public $contrasena = "PoRkldd3vL";
  public $baseDeDatos = "if0_37533972_Proyecto_v6";
  */

interface ConexionesBaseDatos {
    public function crearConexion(): ?mysqli;
    public function validarConexion(mysqli $conexion): ?mysqli;
    public function cerrarConexion(mysqli $conexion): void;
}

class ConexionesBD implements ConexionesBaseDatos {
    protected $puerto = "0.0.0.0:3306"; // Cambia según sea necesario
    protected $usuario = "root";
    protected $contrasena = "root";
    protected $baseDeDatos = "Proyecto_v7";

    public function crearConexion(): ?mysqli {
        $conexion = mysqli_connect($this->puerto, $this->usuario, $this->contrasena);
        return $this->validarConexion($conexion);
    }

    public function validarConexion(mysqli $conexion): ?mysqli {
        if (!$conexion) {
            throw new \Exception("Error conectando a la base de datos.<br>");
            return null;
        }

        if (!mysqli_select_db($conexion, $this->baseDeDatos)) {
            throw new \Exception("Error seleccionando la base de datos.<br>");
            $this->cerrarConexion($conexion);
            return null;
        }

        return $conexion;
    }

    public function cerrarConexion(mysqli $conexion): void {
        if ($conexion) {
            mysqli_close($conexion);
        }
    }
}

class ConsultasBD extends ConexionesBD {
    public function ejecutarConsulta(string $sql): void {
        $conexion = $this->crearConexion();
        if (!$conexion) {
             throw new \Exception('Error en la conexión a la base de datos.');
            return;
        }

        $consulta = mysqli_query($conexion, $sql);
        if (!$consulta) {
          throw new \Exception("Fallo al ejecutar la consulta: " . mysqli_error($conexion));
        }

        $this->cerrarConexion($conexion); // Cerrar la conexión después de ejecutar la consulta
    }

    public function consultarRegistro(string $sql, int $logitud = 1): array {
        $conexion = $this->crearConexion();
        if (!$conexion) {
            return []; // Retorna un array vacío si no hay conexión
        }

        $result = mysqli_query($conexion, $sql);
        $arreglo = [];

        if ($result) {
            while ($row = mysqli_fetch_array($result)) {
                $arreglo[] =array_slice($row, 0, $logitud + 1); // Limitar el tamaño del array
            }
            mysqli_free_result($result);
        }

        $this->cerrarConexion($conexion); // Cerrar la conexión después de obtener los datos
        return $arreglo;
    }
}