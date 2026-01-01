<?php

namespace Liki\Testing;

class TestingRutas {
    
    /**
     * Muestra todas las rutas disponibles con enlaces para probarlas
     */
    public static function mostrar_rutas_disponibles(): void {
        $rutas = \Liki\Routing\Ruta::obtener_rutas();
        
        echo "<!DOCTYPE html>
        <html lang='es'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Testing de Rutas</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
                .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .ruta-item { 
                    background: #f8f9fa; 
                    margin: 10px 0; 
                    padding: 15px; 
                    border-left: 4px solid #007bff; 
                    border-radius: 4px; 
                    cursor: pointer;
                    transition: background-color 0.2s;
                }
                .ruta-item:hover { background: #e9ecef; }
                .metodo { 
                    display: inline-block; 
                    padding: 4px 8px; 
                    border-radius: 4px; 
                    color: white; 
                    font-weight: bold; 
                    margin-right: 10px; 
                    font-size: 12px;
                }
                .GET { background: #28a745; }
                .POST { background: #007bff; }
                .PUT { background: #ffc107; color: black; }
                .DELETE { background: #dc3545; }
                .form-container { 
                    margin-top: 20px; 
                    padding: 20px; 
                    background: #f8f9fa; 
                    border-radius: 8px; 
                    border: 1px solid #dee2e6;
                }
                .form-group { margin-bottom: 15px; }
                label { display: block; margin-bottom: 5px; font-weight: bold; }
                input, textarea, select { 
                    width: 100%; 
                    padding: 8px; 
                    border: 1px solid #ced4da; 
                    border-radius: 4px; 
                    box-sizing: border-box;
                }
                button { 
                    background: #007bff; 
                    color: white; 
                    padding: 10px 20px; 
                    border: none; 
                    border-radius: 4px; 
                    cursor: pointer; 
                    margin-right: 10px;
                }
                button:hover { background: #0056b3; }
                .hidden { display: none; }
                .back-btn { background: #6c757d; }
                .back-btn:hover { background: #545b62; }
                .url-pattern { color: #6c757d; font-family: monospace; }
            </style>
        </head>
        
        <script src=\"/frontend/js/htmx.js\"></script>
        
        <body>
            <div class='container'>
                <h1>Testing de Rutas Disponibles</h1>
                <div id='lista-rutas'>";
        
        foreach ($rutas as $index => $ruta) {
            $metodo_class = strtoupper($ruta['method']);
            echo "<div class='ruta-item' hx-target=\"#lista-rutas\" hx-get=\"/testing/rutas/formulario?accion=mostrar_formulario&ruta_index=$index\">
                    <span class='metodo $metodo_class'>{$ruta['method']}</span>
                    <strong>{$ruta['url_pattern']}</strong>
                    <div class='url-pattern'>Regex: {$ruta['regex_pattern']}</div>
                  </div>";
        }
        
        echo "</div>
              <div id='formulario-test' class='form-container hidden'>
                  <!-- Aquí se cargará el formulario dinámicamente -->
              </div>
            </div>
            
            <script>
                
                
                function volverALista() {
                    document.getElementById('lista-rutas').classList.remove('hidden');
                    document.getElementById('formulario-test').classList.add('hidden');
                }
            </script>
            </body>
            </html>";
    }
    
    /**
     * Genera un formulario para probar una ruta específica
     */
    public static function generar_formulario_ruta(int $ruta_index): void {
        $rutas = \Liki\Routing\Ruta::obtener_rutas();
        
        if (!isset($rutas[$ruta_index])) {
            echo "<p>Error: Ruta no encontrada</p>";
            return;
        }
        
        $ruta = $rutas[$ruta_index];
        $metodo = strtoupper($ruta['method']);
        
        echo "<h2>Probar Ruta: <span class='metodo $metodo'>{$ruta['method']}</span> {$ruta['url_pattern']}</h2>";
        
        // Botón para volver
        echo "<button type='button' class='back-btn' onclick='volverALista()'>← Volver a la lista</button>";
        
        // Formulario principal
        echo "<form id='form-test' method='POST' action='' style='margin-top: 20px;'>";
        echo "<input type='hidden' name='accion' value='probar_ruta'>";
        echo "<input type='hidden' name='ruta_index' value='$ruta_index'>";
        
        // URL dinámica - extraer parámetros del patrón
        $parametros_url = self::extraer_parametros_url($ruta['url_pattern']);
        if (!empty($parametros_url)) {
            echo "<h3>Parámetros de URL</h3>";
            foreach ($parametros_url as $param) {
                echo "<div class='form-group'>
                        <label for='url_$param'>$param:</label>
                        <input type='text' id='url_$param' name='url_params[$param]' placeholder='Valor para $param' required>
                      </div>";
            }
        }
        
        // Parámetros esperados (GET/POST/PUT/DELETE)
        if (!empty($ruta['parametros_esperados'])) {
            echo "<h3>Parámetros de Datos</h3>";
            foreach ($ruta['parametros_esperados'] as $param) {
                echo "<div class='form-group'>
                        <label for='data_$param'>$param:</label>
                        <input type='text' id='data_$param' name='data_params[$param]' placeholder='Valor para $param'>
                      </div>";
            }
        }
        
        // Parámetros extra de función
        if (!empty($ruta['funcion_extra'])) {
            echo "<h3>Parámetros Extra de Función</h3>";
            echo "<div class='form-group'>
                    <label>Parámetros extra (array):</label>
                    <textarea name='funcion_extra' placeholder='JSON array o valores separados por coma' rows='3'>" 
                    . htmlspecialchars(json_encode($ruta['funcion_extra'])) . "</textarea>
                  </div>";
        }
        
        // Selector de método HTTP (para testing)
        echo "<div class='form-group'>
                <label for='metodo_http'>Método HTTP (para testing):</label>
                <select id='metodo_http' name='metodo_http'>
                    <option value='GET'" . ($metodo === 'GET' ? ' selected' : '') . ">GET</option>
                    <option value='POST'" . ($metodo === 'POST' ? ' selected' : '') . ">POST</option>
                    <option value='PUT'" . ($metodo === 'PUT' ? ' selected' : '') . ">PUT</option>
                    <option value='DELETE'" . ($metodo === 'DELETE' ? ' selected' : '') . ">DELETE</option>
                </select>
              </div>";
        
        echo "<button type='submit'>Probar Ruta</button>";
        echo "</form>";
        
        // Área para resultados
        echo "<div id='resultados' style='margin-top: 20px; padding: 15px; background: #e9ecef; border-radius: 4px; display: none;'>
                <h3>Resultados:</h3>
                <div id='resultado-contenido'></div>
              </div>";
        
        // JavaScript para manejar el envío del formulario via AJAX
        echo "<script>
                document.getElementById('form-test').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    
                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '', true);
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            document.getElementById('resultado-contenido').innerHTML = xhr.responseText;
                            document.getElementById('resultados').style.display = 'block';
                        }
                    };
                    xhr.send(formData);
                });
              </script>";
    }
    
    /**
     * Extrae los nombres de parámetros de un patrón de URL
     */
    private static function extraer_parametros_url(string $url_pattern): array {
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $url_pattern, $matches);
        return $matches[1] ?? [];
    }
    
    /**
     * Procesa las acciones del testing
     */
    public static function procesar_testing(): void {
        if ($_POST['accion'] ?? '' === 'mostrar_formulario') {
            self::generar_formulario_ruta((int)$_POST['ruta_index']);
            exit;
        }
        
        if ($_POST['accion'] ?? '' === 'probar_ruta') {
            self::ejecutar_prueba_ruta();
            exit;
        }
    }
    
    /**
     * Ejecuta una prueba de ruta
     */
    private static function ejecutar_prueba_ruta(): void {
        $ruta_index = (int)$_POST['ruta_index'];
        $rutas = \Liki\Routing\Ruta::obtener_rutas();
        
        if (!isset($rutas[$ruta_index])) {
            echo "Error: Ruta no encontrada";
            return;
        }
        
        $ruta = $rutas[$ruta_index];
        
        // Construir URL con parámetros dinámicos
        $url_final = $ruta['url_pattern'];
        $url_params = $_POST['url_params'] ?? [];
        
        foreach ($url_params as $param => $valor) {
            $url_final = str_replace("{{$param}}", $valor, $url_final);
        }
        
        // Preparar datos para la solicitud
        $data_params = $_POST['data_params'] ?? [];
        
        echo "<h4>Detalles de la Prueba:</h4>";
        echo "<p><strong>Método:</strong> {$_POST['metodo_http']}</p>";
        echo "<p><strong>URL:</strong> $url_final</p>";
        echo "<p><strong>Parámetros de datos:</strong> " . htmlspecialchars(json_encode($data_params)) . "</p>";
        
        echo "<h4>Resultado:</h4>";
        echo "<div style='background: white; padding: 15px; border-radius: 4px; border: 1px solid #ccc;'>";
        
        // Aquí simularías la ejecución de la ruta
        // En un entorno real, podrías usar cURL o simular la solicitud
        echo "<em>Nota: En un entorno de testing real, aquí se ejecutaría la ruta y se mostraría el resultado.</em>";
        echo "<br><br>";
        echo "Para probar completamente, necesitarías:";
        echo "<ul>
                <li>Configurar un entorno de testing</li>
                <li>Usar una librería como cURL o Guzzle</li>
                <li>Simular la superglobal \$_SERVER</li>
                <li>Capturar la salida del callback</li>
              </ul>";
        
        echo "</div>";
    }
}