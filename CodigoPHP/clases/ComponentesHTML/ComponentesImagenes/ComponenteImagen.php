<?php


class ComponenteImagen extends ComponenteHtml {
    public function __construct(string $src, string $clase = '', string $id = '', string $alt = '') {
        parent::__construct('img', $clase, $id);
          // Las imágenes no tienen contenido
        $this->src = htmlspecialchars($src);
        $this->alt = htmlspecialchars($alt);
    }

    public function generarHtml(string $contenido =''): string {
      $con = $this->contenido ?? $contenido;
        return "<{$this->nombre} src='{$this->src}' class='{$this->clase}' id='{$this->id}' alt='{$this->alt}' />";
    }
}