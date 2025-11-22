
<?php
use Liki\Plantillas\Plantilla;
$op = ["op"=>9];



$config = [
    "tituloPagina"=>'APP Liki',
    "estilos"=>['bootstrap.min','estilos'],
    "estilosD"=>['estilos'],
    "scripts"=>['color-modes','script','htmx','bootstrap.bundle.min'],
    "contenidos"=>[
        ["componente"=>'estructura/Header',"configuracion"=>$op],
        ["componente"=>'sesiones/Inicio_sesion',"configuracion"=>$op],
        
    ]
];


Plantilla::HTML('estructura/pagina',$config);




