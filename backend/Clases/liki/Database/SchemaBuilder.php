<?php
namespace Liki\Database;
use Liki\SQL\TableBuilder;
use Liki\Database\ConsultasBD;

class SchemaBuilder {
    private $driver;
    private $tables = [];
    private $connection;

    public function __construct($driver = null) {
        $this->driver = $driver ?? DB_DRIVER;
        $this->connection = new ConsultasBD();
    }

    public function createTable($tableName, callable $callback) {
        $table = new TableBuilder($tableName, $this->driver);
        $callback($table);
        $this->tables[] = $table;

        $sql = $table->toSQL();
        $this->connection->ejecutarConsulta($sql);   // ✅ ejecuta
        return $sql;
    }

    public function dropTable($tableName) {
        $sql = "DROP TABLE IF EXISTS {$tableName};";
        $this->connection->ejecutarConsulta($sql);   // ✅ ejecuta
        return $sql;
    }

    public function export() {
        $sql = "";
        foreach ($this->tables as $table) {
            $sql .= $table->toSQL() . "\n\n";
        }
        return $sql;
    }
}