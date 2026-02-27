<?php
namespace Liki\Database;
use Liki\SQL\TableBuilder;

class SchemaBuilder {
    private $driver;
   private $tables = [];  
     
   public function __construct($driver = null) {  
      $this->driver = $driver ?? DB_DRIVER;  
   }  
     
   public function createTable($tableName, callable $callback) {  
      $table = new TableBuilder($tableName, $this->driver);  
      $callback($table);  
      $this->tables[] = $table;  
      return $table->toSQL();  
   }  
     
   public function dropTable($tableName) {  
      return "DROP TABLE IF EXISTS {$tableName};";  
   }  
     
   public function export() {  
      $sql = "";  
      foreach ($this->tables as $table) {  
         $sql .= $table->toSQL() . "\n\n";  
      }  
      return $sql;  
   }  
}  