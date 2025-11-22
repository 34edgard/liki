<?php
// Configuración de la base de datos
$host = 'localhost:3306'; // Cambia esto si tu servidor de base de datos está en otro lugar
$user = 'root'; // Tu usuario de la base de datos
$password = ''; // Tu contraseña de la base de datos
$database = 'proyecto-uruguay'; // Nombre de la base de datos que quieres exportar

// Conectar a la base de datos
$conn = new mysqli($host, $user, $password, $database);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para exportar la base de datos
function exportDatabase($conn, $database) {
    $tables = [];
    $result = $conn->query("SHOW TABLES");

    while ($row = $result->fetch_array()) {
        $tables[] = $row[0];
    }

    $sqlScript = "";
    
    foreach ($tables as $table) {
        // Obtener la estructura de la tabla
        $result = $conn->query("SHOW CREATE TABLE $table");
        $row = $result->fetch_row();
        $sqlScript .= "\n\n" . $row[1] . ";\n\n";

        // Obtener los datos de la tabla
        $result = $conn->query("SELECT * FROM $table");
        while ($row = $result->fetch_assoc()) {
            $sqlScript .= "INSERT INTO $table (";
            $sqlScript .= implode(", ", array_keys($row)) . ") VALUES ('";
            $sqlScript .= implode("', '", array_values($row)) . "');\n";
        }
    }

    return $sqlScript;
}

// Exportar la base de datos
$sqlScript = exportDatabase($conn, $database);

// Guardar el script en un archivo .sql
$fileName = $database . '_backup_' . date('Y-m-d_H-i-s') . '.sql';
file_put_contents($fileName, $sqlScript);

// Cerrar la conexión
$conn->close();

echo "La base de datos ha sido exportada a '$fileName'.";
?>
