<?php

namespace Liki\Consola;
use Liki\Database\ConsultasBD;
use Liki\Consola\MySQLToSQLiteExporter;
class db{
 
public static function exportDatabase() {
   
// Configuración de la base de datos MySQL
$mysql_config = [
    'host' => '127.0.0.1',
    'user' => 'root',
    'password' => 'root',
    'database' => 'sistema_escuela'
];

// Configuración del archivo SQLite de salida
$sqlite_file = 'exported_database.sqlite';



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

}




public static function import(){
        
            
            
        
        $tablas = file_get_contents("./DataBase/sql/sqlite/tablas.sql");
        $registros = file_get_contents("./DataBase/sql/sqlite/registros.sql");
        
        
        $con = new ConsultasBD();
        
        //print($tablas);
        
        $tables = explode(';',$tablas);
        foreach($tables as $t){
            
        
        $con->ejecutarConsulta($t);
        }
        
        
        $rs = explode(';',$registros);
        foreach($rs as $rds){
            
        
        $con->ejecutarConsulta($rds);
        }
        
        
}



}

