<?php

class links extends ComponenteHtml{
  public function __construct($href="#"){
    parent::__construct('a','link-primary');
    $this->href = "href='$href'";
  }
    public function generarHtml(string $contenido =''):string{
      $con = $this->contenido ?? $contenido;
        return "<{$this->nombre} {$this->href} {$this->clase} {$this->id}>$con</{$this->nombre}>"; 
    }
}