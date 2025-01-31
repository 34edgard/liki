<?php
class ComponenteBoton extends ComponenteHtml {
    public function __construct(string $nombre, string $clase = '', string $id = '') {
        parent::__construct('button', $clase, $id);
        $this->contenido = htmlspecialchars($nombre);
    }
}