<?php


namespace Liki\Database;
use PDO;
interface ConexionesBaseDatos {
    public function crearConexion(): ?PDO;
    public function validarConexion(PDO $conexion): ?PDO;
    public function cerrarConexion(PDO $conexion): void;
}

