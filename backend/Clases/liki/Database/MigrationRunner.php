<?php

namespace Liki\Database;


use Liki\Database\ConsultasBD;  
  
class MigrationRunner {  
    private $connection;  
    private $migrationTable;  
    private $migrationPath;  
      
    public function __construct() {  
        $this->connection = new ConsultasBD();  
        $this->migrationTable = MIGRATION_TABLE;  
        $this->migrationPath = MIGRATION_PATH;  
        $this->createMigrationsTable();  
    }  
      
    private function createMigrationsTable() {  
        $sql = "CREATE TABLE IF NOT EXISTS {$this->migrationTable} (  
            id INTEGER PRIMARY KEY AUTOINCREMENT,  
            migration VARCHAR(255) NOT NULL,  
            batch INTEGER NOT NULL,  
            executed_at DATETIME DEFAULT CURRENT_TIMESTAMP  
        )";  
          
        $this->connection->ejecutarConsulta($sql);  
    }  
      
    public function run() {  
        $migrations = $this->getPendingMigrations();  
        $batch = $this->getNextBatchNumber();  
          
        foreach ($migrations as $migration) {  
            echo "Ejecutando migración: {$migration}\n";  
            $this->executeMigration($migration, $batch);  
        }  
          
        echo "Migraciones completadas.\n";  
    }  
      


public  function rollback($steps = 1) {  
        $batches = $this->getLastBatches($steps);  
          
        foreach ($batches as $batch) {  
            $migrations = $this->getMigrationsByBatch($batch);  
              
            foreach ($migrations as $migration) {  
                echo "Revirtiendo migración: {$migration}\n";  
                $this->revertMigration($migration);  
            }  
        }  
    }  
      
    private function getPendingMigrations() {  
        $files = glob($this->migrationPath . '*.php');  
        $executed = $this->getExecutedMigrations();  
          
        $pending = [];  
        foreach ($files as $file) {  
            $name = basename($file, '.php');  
            if (!in_array($name, $executed)) {  
                $pending[] = $name;  
            }  
        }  
          
        sort($pending);  
        return $pending;  

}

private function getExecutedMigrations() {  
        $stmt = $this->connection->query(  
            "SELECT migration FROM {$this->migrationTable} ORDER BY id"  
        );  
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);  
    }  
      
    private function executeMigration($migration, $batch) {  
        $file = $this->migrationPath . $migration . '.php';  
        $instance = require $file;  
          
        if (method_exists($instance, 'up')) {  
            $instance->up();  
              
            $stmt = $this->connection->prepare(  
                "INSERT INTO {$this->migrationTable} (migration, batch) VALUES (?, ?)"  
            );  
            $stmt->execute([$migration, $batch]);  
        }  
    }  
      
    private function revertMigration($migration) {  
        $file = $this->migrationPath . $migration . '.php';  
        $instance = require $file;  
          
        if (method_exists($instance, 'down')) {  
            $instance->down();  
              
            $stmt = $this->connection->prepare(  
                "DELETE FROM {$this->migrationTable} WHERE migration = ?"  
            );  
            $stmt->execute([$migration]);  
        }  
    }  
      


private function getNextBatchNumber() {  
        $stmt = $this->connection->query(  
            "SELECT MAX(batch) as max_batch FROM {$this->migrationTable}"  
        );  
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);  
        return ($result['max_batch'] ?? 0) + 1;  
    }  
      
    private function getLastBatches($steps) {  
        $stmt = $this->connection->query(  
            "SELECT DISTINCT batch FROM {$this->migrationTable} ORDER BY batch DESC LIMIT {$steps}"  
        );  
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);  
    }  
      
    private function getMigrationsByBatch($batch) {  
        $stmt = $this->connection->prepare(  
            "SELECT migration FROM {$this->migrationTable} WHERE batch = ? ORDER BY id DESC"  
        );  
        $stmt->execute([$batch]);  
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);  
    }  
}