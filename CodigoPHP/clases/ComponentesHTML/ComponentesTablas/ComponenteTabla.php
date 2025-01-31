<?php
class ComponenteTabla extends ComponenteHtml {
    private  $filas;

    public function __construct(array $filas, string $clase = '', string $id = '') {
        parent::__construct('table', $clase, $id);
        $this->filas = $filas;
    }

    public function generarHtml(string $contenido= ''): string {
    //  $con = $this->contenido ?? $contenido;
        $tabla = "<{$this->nombre} {$this->clase}{$this->id}>";
        foreach ($this->filas as $fila) {
            $tabla .= "<tr>";
            foreach ($fila as $celda) {
                $tabla .= "<td>" . htmlspecialchars($celda) . "</td>";
            }
            $tabla .= "</tr>";
        }
        $tabla .= "</{$this->nombre}>";
        return $tabla;
    }
}
