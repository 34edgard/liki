<?php
/**
 * Script para exportar una base de datos MySQL a SQLite
 * Uso: php mysql_to_sqlite.php
 */

// Configuración de la base de datos MySQL
$mysql_config = [
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => 'root',
    'database' => 'sistema_escuela'
];

// Configuración del archivo SQLite de salida
$sqlite_file = 'exported_database.sqlite';

// Clase principal para la conversión
class MySQLToSQLiteExporter
{
    private $mysql_conn;
    private $sqlite_conn;
    private $sqlite_file;
    
    // Tipos de datos MySQL a SQLite
    private $type_mapping = [
        'int' => 'INTEGER',
        'tinyint' => 'INTEGER',
        'smallint' => 'INTEGER',
        'mediumint' => 'INTEGER',
        'bigint' => 'INTEGER',
        'float' => 'REAL',
        'double' => 'REAL',
        'decimal' => 'REAL',
        'varchar' => 'TEXT',
        'char' => 'TEXT',
        'text' => 'TEXT',
        'tinytext' => 'TEXT',
        'mediumtext' => 'TEXT',
        'longtext' => 'TEXT',
        'date' => 'TEXT',
        'datetime' => 'TEXT',
        'timestamp' => 'TEXT',
        'time' => 'TEXT',
        'year' => 'INTEGER',
        'blob' => 'BLOB',
        'tinyblob' => 'BLOB',
        'mediumblob' => 'BLOB',
        'longblob' => 'BLOB',
        'enum' => 'TEXT',
        'set' => 'TEXT'
    ];
    
    public function __construct($mysql_config, $sqlite_file)
    {
        $this->sqlite_file = $sqlite_file;
        
        try {
            // Conectar a MySQL
            $this->mysql_conn = new mysqli(
                $mysql_config['host'],
                $mysql_config['user'],
                $mysql_config['password'],
                $mysql_config['database']
            );
            
            if ($this->mysql_conn->connect_error) {
                throw new Exception("Error de conexión MySQL: " . $this->mysql_conn->connect_error);
            }
            
            $this->mysql_conn->set_charset("utf8mb4");
            echo "✓ Conectado a MySQL\n";
            
            // Crear/abrir SQLite
            $this->sqlite_conn = new SQLite3($this->sqlite_file);
            echo "✓ Conectado a SQLite: {$this->sqlite_file}\n";
            
        } catch (Exception $e) {
            die("Error: " . $e->getMessage() . "\n");
        }
    }
    
    // Convertir tipo de dato MySQL a SQLite
    private function mapDataType($mysql_type)
    {
        $mysql_type = strtolower($mysql_type);
        
        foreach ($this->type_mapping as $mysql => $sqlite) {
            if (strpos($mysql_type, $mysql) !== false) {
                return $sqlite;
            }
        }
        
        return 'TEXT'; // Por defecto
    }
    
    // Obtener estructura de las tablas
    public function exportTables()
    {
        // Obtener todas las tablas
        $tables_result = $this->mysql_conn->query("SHOW TABLES");
        
        if (!$tables_result) {
            throw new Exception("Error obteniendo tablas");
        }
        
        $table_count = 0;
        
        while ($row = $tables_result->fetch_row()) {
            $table_name = $row[0];
            echo "\n📋 Exportando tabla: {$table_name}\n";
            
            // Obtener estructura de la tabla
            $create_table = $this->getTableStructure($table_name);
            
            // Crear tabla en SQLite
            $this->createSQLiteTable($table_name, $create_table);
            
            // Exportar datos
            $this->exportTableData($table_name);
            
            $table_count++;
        }
        
        echo "\n✅ Exportación completada. {$table_count} tabla(s) exportada(s).\n";
    }
    
    // Obtener estructura de la tabla MySQL
    private function getTableStructure($table_name)
    {
        $columns = [];
        $primary_keys = [];
        
        // Obtener información de columnas
        $columns_result = $this->mysql_conn->query("DESCRIBE `{$table_name}`");
        
        while ($col = $columns_result->fetch_assoc()) {
            $columns[] = [
                'name' => $col['Field'],
                'type' => $this->mapDataType($col['Type']),
                'nullable' => ($col['Null'] == 'YES'),
                'default' => $col['Default'],
                'primary' => ($col['Key'] == 'PRI')
            ];
            
            if ($col['Key'] == 'PRI') {
                $primary_keys[] = $col['Field'];
            }
        }
        
        return [
            'columns' => $columns,
            'primary_keys' => $primary_keys
        ];
    }
    
    // Crear tabla en SQLite
    private function createSQLiteTable($table_name, $structure)
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$table_name}` (\n";
        
        $column_defs = [];
        foreach ($structure['columns'] as $column) {
            $def = "    `{$column['name']}` {$column['type']}";
            
            if (!$column['nullable']) {
                $def .= " NOT NULL";
            }
            
            if ($column['default'] !== null && $column['default'] !== '') {
                $def .= " DEFAULT '{$column['default']}'";
            }
            
            $column_defs[] = $def;
        }
        
        // Añadir PRIMARY KEY
        if (!empty($structure['primary_keys'])) {
            $pk_columns = implode('`, `', $structure['primary_keys']);
            $column_defs[] = "    PRIMARY KEY (`{$pk_columns}`)";
        }
        
        $sql .= implode(",\n", $column_defs);
        $sql .= "\n)";
        
        $result = $this->sqlite_conn->exec($sql);
        
        if ($result === false) {
            throw new Exception("Error creando tabla {$table_name}: " . $this->sqlite_conn->lastErrorMsg());
        }
        
        echo "  ✓ Tabla creada\n";
    }
    
    // Exportar datos de una tabla
    private function exportTableData($table_name)
    {
        // Obtener datos de MySQL
        $data_result = $this->mysql_conn->query("SELECT * FROM `{$table_name}`");
        
        if (!$data_result) {
            echo "  ⚠ No se pudieron obtener datos\n";
            return;
        }
        
        if ($data_result->num_rows == 0) {
            echo "  ℹ Tabla vacía\n";
            return;
        }
        
        // Obtener nombres de columnas
        $columns = [];
        while ($field = $data_result->fetch_field()) {
            $columns[] = $field->name;
        }
        
        // Preparar INSERT statement
        $placeholders = implode(',', array_fill(0, count($columns), '?'));
        $column_names = '`' . implode('`, `', $columns) . '`';
        
        $insert_sql = "INSERT INTO `{$table_name}` ({$column_names}) VALUES ({$placeholders})";
        $stmt = $this->sqlite_conn->prepare($insert_sql);
        
        if (!$stmt) {
            echo "  ⚠ Error preparando insert:\n";
            return;
        }
        
        $row_count = 0;
        
        // Insertar datos en SQLite
        while ($row = $data_result->fetch_assoc()) {
            $values = [];
            foreach ($columns as $col) {
                $value = $row[$col];
                
                // Manejar valores NULL
                if ($value === null) {
                    $values[] = null;
                } else {
                    // Escapar caracteres especiales para SQLite
                    $values[] = $value;
                }
            }
            
            // Bind parameters
            foreach ($values as $idx => $value) {
                $param_type = SQLITE3_TEXT;
                if ($value === null) {
                    $param_type = SQLITE3_NULL;
                } elseif (is_numeric($value)) {
                    $param_type = SQLITE3_INTEGER;
                }
                
                $stmt->bindValue($idx + 1, $value, $param_type);
            }
            
            $result = $stmt->execute();
            if ($result === false) {
                echo "  ⚠ Error insertando fila: " . $this->sqlite_conn->lastErrorMsg() . "\n";
            } else {
                $row_count++;
            }
            
            $stmt->reset();
        }
        
        echo "  ✓ Datos exportados: {$row_count} fila(s)\n";
    }
    
    // Exportar índices (opcional)
    public function exportIndexes()
    {
        $tables_result = $this->mysql_conn->query("SHOW TABLES");
        
        while ($row = $tables_result->fetch_row()) {
            $table_name = $row[0];
            
            // Obtener índices de MySQL
            $indexes = $this->mysql_conn->query("SHOW INDEX FROM `{$table_name}`");
            
            $unique_indices = [];
            $non_unique_indices = [];
            
            while ($index = $indexes->fetch_assoc()) {
                if ($index['Key_name'] == 'PRIMARY') {
                    continue; // PK ya fue creada
                }
                
                if ($index['Non_unique'] == 0) {
                    $unique_indices[$index['Key_name']][] = $index['Column_name'];
                } else {
                    $non_unique_indices[$index['Key_name']][] = $index['Column_name'];
                }
            }
            
            // Crear índices únicos
            foreach ($unique_indices as $index_name => $columns) {
                $cols = implode('`, `', $columns);
                $sql = "CREATE UNIQUE INDEX IF NOT EXISTS `idx_{$table_name}_{$index_name}` ON `{$table_name}` (`{$cols}`)";
                $this->sqlite_conn->exec($sql);
            }
            
            // Crear índices no únicos
            foreach ($non_unique_indices as $index_name => $columns) {
                $cols = implode('`, `', $columns);
                $sql = "CREATE INDEX IF NOT EXISTS `idx_{$table_name}_{$index_name}` ON `{$table_name}` (`{$cols}`)";
                $this->sqlite_conn->exec($sql);
            }
            
            if (!empty($unique_indices) || !empty($non_unique_indices)) {
                echo "  ✓ Índices exportados\n";
            }
        }
    }
    
    // Cerrar conexiones
    public function close()
    {
        if ($this->mysql_conn) {
            $this->mysql_conn->close();
        }
        if ($this->sqlite_conn) {
            $this->sqlite_conn->close();
        }
    }
}

// Ejecutar exportación
try {
    echo "🚀 Iniciando exportación MySQL → SQLite\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    
    $exporter = new MySQLToSQLiteExporter($mysql_config, $sqlite_file);
    
    // Exportar tablas
    $exporter->exportTables();
    
    // Exportar índices (opcional, comentar si no se necesitan)
    echo "\n📌 Exportando índices...\n";
    $exporter->exportIndexes();
    
    $exporter->close();
    
    echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "✨ Exportación completada exitosamente!\n";
    echo "📁 Archivo SQLite: {$sqlite_file}\n";
    
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>