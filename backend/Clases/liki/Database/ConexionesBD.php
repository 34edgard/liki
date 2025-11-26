<?php

namespace Liki\Database;

use Liki\Database\ConexionesBaseDatos;
use PDO;
use PDOException;

class ConexionesBD implements ConexionesBaseDatos {
    
    public function crearConexion(): ?PDO {
        
        try {
            $conexion = new PDO(DSN, usuario_BD, contraceña_BD);
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conexion->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Desactivar la emulación de prepares para mayor seguridad
            return $this->validarConexion($conexion);
        } catch (PDOException $e) {
            throw new Exception("Error conectando a la base de datos: " . $e->getMessage());
        }
    }

    public function validarConexion(PDO $conexion): ?PDO {
        // En este caso, no se necesita validar la conexión adicionalmente.
        return $conexion;
    }

    public function cerrarConexion(PDO $conexion): void {
        $conexion = null;
    }
}

