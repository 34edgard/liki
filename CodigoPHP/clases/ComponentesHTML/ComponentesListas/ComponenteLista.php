<?php

class ComponenteLista extends ComponenteHtml {
    private  $elementos;

    public function __construct(array $elementos, string $clase = '', string $id = '') {
        parent::__construct('ul', $clase, $id);
        $this->elementos = $elementos;
    }

    public function generarHtml(string $contenido =''): string {
      $con = $this->contenido ?? $contenido;
        $lista = "<{$this->nombre} {$this->clase} {$this->id}>";
        foreach ($this->elementos as $elemento) {
            $lista .= "<li>" . htmlspecialchars($elemento) . "</li>";
        }
        $lista .= "</{$this->nombre}>";
        return $lista;
    }
}
