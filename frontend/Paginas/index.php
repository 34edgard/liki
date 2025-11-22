<?php
use Liki\Plantillas\Plantilla;
$op = ["op"=>0];


$config = [
    "tituloPagina"=>"App Liki",
    "estilos"=>['bootstrap.min','estilos'],
    "estilosD"=>['estilos'],
    
    "scripts"=>['color-modes','htmx','bootstrap.bundle.min'],
    "contenidos"=>[
        ["componente"=>'estructura/Header',"configuracion"=>$op],
        ["componente"=>'Inicio',"configuracion"=>$op],
       
    ]
];


Plantilla::HTML('estructura/pagina',$config);

