
use Liki\KitPHP\File;

$file = File::of('/tmp/datos.txt');

if ($file->exists()) {
echo $file->size() . " bytes\n";
echo $file->get();
}

File::of('/tmp/log.txt')
->put("Línea inicial\n")
->append("Segunda línea\n")
->append("Tercera línea\n");




// Contar líneas que contienen "ERROR"
$count = File::of('/var/log/app.log')->eachLine(function (string $line, int $index) {
    if (str_contains($line, 'ERROR')) {
        // hacer algo
    }
    return true; // seguir
});

echo "Procesadas: $count líneas";



// Detener el procesamiento tras la primera coincidencia
File::of('/var/log/app.log')->eachLine(function ($line, $i) {
    if (str_contains($line, 'CRITICAL')) {
        echo "Primer CRITICAL en la línea $i: $line\n";
        return false; // detiene la iteración
    }
    return true;
});


// Convertir todas las líneas a mayúsculas
File::of('/tmp/entrada.txt')
->transformLines(fn($line) => strtoupper($line));



// Eliminar líneas vacías o comentarios
File::of('/tmp/config.txt')->filterLines(
    fn($line) => trim($line) !== '' && !str_starts_with(trim($line), '#')
);



// Modo lectura: obtiene resultado sin escribir
$wordCount = File::of('/tmp/novela.txt')->pipe(function (string $content) {
    return str_word_count($content);
});
echo "Palabras: $wordCount\n";

// Modo escritura: reemplaza y guarda
File::of('/tmp/plantilla.txt')->pipe(
    fn($c) => str_replace('{{nombre}}', 'Ana', $c),
    writeBack: true
);




foreach (File::of('/tmp/app.log')->grep('/\d{4}-\d{2}-\d{2}/') as $m) {
    echo "Línea {$m['line']}: {$m['content']}\n";
}



File::of('/var/log/huge.log')->eachLineSpl(function ($line, $n) {
    // Aquí puedes escribir a otro archivo, agregar a DB, etc.
});



