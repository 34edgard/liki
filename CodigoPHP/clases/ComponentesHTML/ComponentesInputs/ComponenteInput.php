<?php

class ComponenteTexto extends ComponenteHtml {
    public function __construct(string $id, string $clase = '', string $valor = '', string $nombre = '') {
        parent::__construct('input', $clase, $id);
         // Para inputs, el contenido se maneja a través de atributos
        $this->id = 'id="' . htmlspecialchars($id) . '" ';
        $this->clase = 'class="' . htmlspecialchars($clase) . '" ';
    }

    public function generarHtml(string $contenido = ''): string {
        return "<{$this->nombre} type='text' {$this->clase} {$this->id} value='" . htmlspecialchars($contenido) . "' />";
    }
}