<?php
namespace Liki\SQL;


class TableBuilder {  
   private $tableName;  
   private $driver;  
   private $columns = [];  
   private $primaryKey = null;  
   private $foreignKeys = [];  
   private $indexes = [];  
     
   public function __construct($tableName, $driver) {  
      $this->tableName = $tableName;  
      $this->driver = $driver;  
   }  
     
   // Tipos de columnas  
   public function integer($name, $autoIncrement = false) {  
      $type = $this->getIntegerType($autoIncrement);  
      $this->columns[] = [  
         'name' => $name,  
         'type' => $type,  
         'autoIncrement' => $autoIncrement  
      ];  
      return $this;  
   }  
     
   public function string($name, $length = 255) {  
      $this->columns[] = [  
         'name' => $name,  
         'type' => "VARCHAR({$length})"  
      ];  
      return $this;  
   }  
     
   public function text($name) {  
      $this->columns[] = [  
         'name' => $name,  
         'type' => 'TEXT'  
      ];  
      return $this;  
   }  



public function date($name) {  
      $this->columns[] = [  
         'name' => $name,  
         'type' => 'DATE'  
      ];  
      return $this;  
   }  
     
   public function datetime($name) {  
      $this->columns[] = [  
         'name' => $name,  
         'type' => 'DATETIME'  
      ];  
      return $this;  
   }  
     
   public function decimal($name, $precision = 10, $scale = 2) {  
      $this->columns[] = [  
         'name' => $name,  
         'type' => "DECIMAL({$precision},{$scale})"  
      ];  
      return $this;  
   }  
     
   public function enum($name, array $values) {  
      $type = $this->getEnumType($values);  
      $this->columns[] = [  
         'name' => $name,  
         'type' => $type,  
         'values' => $values  
      ];  
      return $this;  
   }  





     
   // Modificadores  
   public function nullable() {  
      $lastIndex = count($this->columns) - 1;  
      $this->columns[$lastIndex]['nullable'] = true;  
      return $this;  
   }  
     
   public function notNull() {  
      $lastIndex = count($this->columns) - 1;  
      $this->columns[$lastIndex]['nullable'] = false;  
      return $this;  
   }  
     
   public function default($value) {  
      $lastIndex = count($this->columns) - 1;  
      $this->columns[$lastIndex]['default'] = $value;  
      return $this;  
   }  
     
   // Constraints  
   public function primary($column) {  
      $this->primaryKey = is_array($column) ? $column : [$column];  
      return $this;  
   }  
     
   public function foreign($column, $referencedTable, $referencedColumn = 'id') {  
      $this->foreignKeys[] = [  
         'column' => $column,  
         'references' => $referencedColumn,  
         'on' => $referencedTable  
      ];  
      return $this;  
   }  
     
   public function index($columns) {  
      $this->indexes[] = is_array($columns) ? $columns : [$columns];  
      return $this;  
   }  
     
   // Generación de SQL 


public function toSQL() {  
      $sql = "CREATE TABLE {$this->tableName} (\n";  
        
      // Columnas  
      $columnDefinitions = [];  
      foreach ($this->columns as $column) {  
         $def = "  {$column['name']} {$column['type']}";  
           
         if (isset($column['autoIncrement']) && $column['autoIncrement']) {  
            $def .= $this->getAutoIncrementSyntax();  
         }  
           
         if (isset($column['nullable']) && !$column['nullable']) {  
            $def .= " NOT NULL";  
         }  
           
         if (isset($column['default'])) {  
            $def .= " DEFAULT " . $this->formatDefaultValue($column['default']);  
         }  
           
         $columnDefinitions[] = $def;  
      }  
        
      // Primary Key  
      if ($this->primaryKey) {  
         $pkColumns = implode(', ', $this->primaryKey);  
         $columnDefinitions[] = "  PRIMARY KEY ({$pkColumns})";  
      }  
        
      // Foreign Keys  
      foreach ($this->foreignKeys as $fk) {  
         $columnDefinitions[] = "  FOREIGN KEY ({$fk['column']}) REFERENCES {$fk['on']} ({$fk['references']})";  
      }  
        
      $sql .= implode(",\n", $columnDefinitions);  
      $sql .= "\n)";  
        
      // Engine específico para MySQL/MariaDB  
      if (in_array($this->driver, ['mysql', 'mariadb'])) {  
         $sql .= " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";  
      }  
        
      $sql .= ";";  
        
      return $sql;  
   }  
     
   private function getIntegerType($autoIncrement) {  
      switch ($this->driver) {  
         case 'sqlite':  
            return $autoIncrement ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INTEGER';  
         case 'mysql':  
         case 'mariadb':  
            return 'INT';  
         case 'pgsql':  
            return $autoIncrement ? 'SERIAL' : 'INTEGER';  
         default:  
            return 'INTEGER';  
      }  
   }  
     

   private function getEnumType($values) {  
      switch ($this->driver) {  
         case 'sqlite':  
            $valuesList = implode("','", $values);  
            return "TEXT CHECK( {$this->columns[count($this->columns)-1]['name']} IN ('{$valuesList}') )";  
         case 'mysql':  
         case 'mariadb':  
            $valuesList = implode("','", $values);  
            return "ENUM('{$valuesList}')";  
         case 'pgsql':  
            // PostgreSQL requiere crear un tipo ENUM personalizado  
            return "VARCHAR(50)";  
         default:  
            return "VARCHAR(50)";  
      }  
   }  
     
   private function getAutoIncrementSyntax() {  
      switch ($this->driver) {  
         case 'sqlite':  
            return ''; // Ya incluido en el tipo  
         case 'mysql':  
         case 'mariadb':  
            return ' AUTO_INCREMENT';  
         case 'pgsql':  
            return ''; // Ya incluido en SERIAL  
         default:  
            return '';  
      }  
   }  
     
   private function formatDefaultValue($value) {  
      if (is_string($value)) {  
         return "'{$value}'";  
      }  
      if (is_bool($value)) {  
         return $value ? '1' : '0';  
      }  
      return $value;  
   }  
}