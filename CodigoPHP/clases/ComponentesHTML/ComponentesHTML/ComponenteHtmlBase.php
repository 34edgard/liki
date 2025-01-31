<?php


interface IcomponenteHtml {
    public function generarHtml(string $contenido): string;
}

abstract class ComponenteHtml implements IcomponenteHtml {
    protected  $nombre;
    protected $clase;
    protected $id;
    protected $contenido;
    

    public function __construct(string $nombre, string $clase = '', string $id = '') {
        $this->nombre = $nombre;
        $this->clase = $clase ? 'class="' . htmlspecialchars($clase) . '" ' : '';
        $this->id = $id ? 'id="' . htmlspecialchars($id) . '" ' : '';
        
    }
    
    public function agregarContenido(string $contenido){
      $this->contenido = $contenido;
    }
    
    public function generarHtml(string $contenido = ''): string {
        $con = $this->contenido ?? $contenido;
        return "<{$this->nombre} {$this->clase} {$this->id}>$con</{$this->nombre}>";
    }
}




class componetHTML extends ComponenteHtml{
    public function generarHtml(string $contenido =''):string{
       return parent::generarHtml($this->contenido ?? $contenido);
    }
}

// Encabezado de la página
class Pagina {
    private $titulo;
    private $cuerpo;

    public function __construct(string $titulo) {
        $this->titulo = $titulo;
        $this->cuerpo = [];
    }

    public function agregarElemento(array $componente) {
        $this->cuerpo = $componente;
    }

    public function generarHtml(): string {
        $html = "<!DOCTYPE html>\n";
        $html .= "<html lang='es'>\n";
        $html .= "<head>\n";
        $html .= "    <meta charset='UTF-8'>\n";
        $html .= "    <meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
        $html .= "<link rel='stylesheet' href='/src/css/index.css'>\n";
        $html .= "<link rel='stylesheet' href='/src/css/bootstrap.min.css'>\n";
        $html .= "    <title>{$this->titulo}</title>\n";
        $html .= "    <script>\n";
        $html .= "    function manejarEnvio(event) {\n";
        $html .= "        event.preventDefault(); // Evitar el envío del formulario\n";
        $html .= "        const nombre = document.getElementById('nombreId').value;\n";
        $html .= "        alert('Hola, ' + nombre + '!'); // Mostrar un aviso con el nombre\n";
        $html .= "    }\n";
        $html .= "    </script>\n";
        $html .= "</head>\n";
        $html .= "<body>\n";
        
        foreach ($this->cuerpo as $componente) {
            $html .= $componente . "\n";
        }

        $html .= "</body>\n";
        $html .= "</html>\n";

        return $html;
    }
}