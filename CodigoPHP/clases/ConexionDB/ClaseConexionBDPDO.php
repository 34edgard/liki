<?php

interface ConexionesBaseDatosPDO {
    public function crearConexion(): ?PDO;
    public function validarConexion(PDO $conexion): ?PDO;
    public function cerrarConexion(PDO $conexion): void;
}

class ConexionesBDPDO implements ConexionesBaseDatosPDO {
    protected $dsn = DSN; //"mysql:host=0.0.0.0;dbname=Proyecto_v7;charset=utf8"; // Para MariaDB
    protected $usuario = "root";
    protected $contrasena = "root";

    public function crearConexion(): ?PDO {
        try {
            $conexion = new PDO($this->dsn, $this->usuario, $this->contrasena);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->validarConexion($conexion);
        } catch (PDOException $e) {
            throw new Exception("Error conectando a la base de datos: " . $e->getMessage());
        }
    }

    public function validarConexion(PDO $conexion): ?PDO {
        // En este caso, no se necesita validar la conexión, ya que PDO lanzará una excepción si hay un error.
        return $conexion;
    }

    public function cerrarConexion(PDO $conexion): void {
        $conexion = null; // Cerrar la conexión a la base de datos
    }
}

class ConsultasBDPDO extends ConexionesBDPDO {
    public function ejecutarConsulta(string $sql): void {
        $conexion = $this->crearConexion();
        if (!$conexion) {
            throw new Exception('Error en la conexión a la base de datos.');
        }

        try {
            $conexion->exec($sql);
        } catch (PDOException $e) {
            throw new Exception("Fallo al ejecutar la consulta: " . $e->getMessage());
        } finally {
            $this->cerrarConexion($conexion); // Cerrar la conexión después de ejecutar la consulta
        }
    }

    public function consultarRegistro(string $sql, int $longitud = 1): array {
        $conexion = $this->crearConexion();
        if (!$conexion) {
            return []; // Retorna un array vacío si no hay conexión
        }

        $arreglo = [];
        try {
            $stmt = $conexion->query($sql);
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $arreglo[] = array_slice($row, 0, $longitud); // Limitar el tamaño del array
            }
        } catch (PDOException $e) {
            throw new Exception("Error en la consulta: " . $e->getMessage());
        } finally {
            $this->cerrarConexion($conexion); // Cerrar la conexión después de obtener los datos
        }
        return $arreglo;
    }
}

/*Ejemplo de conexión a SQLite
class ConexionesSQLite extends ConexionesBD {
    protected $dsn = "sqlite:/CodigoPHP/Sqlite/r.db"; // Cambia la ruta según sea necesario

    public function crearConexion(): ?PDO {
        return parent::crearConexion(); // Utiliza el método de la clase padre
    }
}

// Ejemplo de uso
try {
    $consultas = new ConsultasBD();
    // Ejemplo de inyección SQL (vulnerabilidad)
    // Supongamos que $userInput proviene de una entrada del usuario sin sanitizar.
    $userInput = "'; DROP TABLE users; --"; // Esto es una inyección SQL
    $sql = "SELECT * FROM users WHERE username = '$userInput'"; // Vulnerable a inyección SQL
    $resultado = $consultas->consultarRegistro($sql);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}*/