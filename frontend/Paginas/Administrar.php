<?php

use Liki\Plantillas\Plantilla;
use Liki\Routing\ValidarSesion;
ValidarSesion::validar_sesion();
$op =["op"=>6];



$config = [
    "tituloPagina"=>'Administración',
    "estilos"=>['bootstrap.min','estilos'],
    "estilosD"=>['estilos'],
    "scripts"=>['color-modes','chart.umd','CambiarTitulo','htmx','bootstrap.bundle.min'],
    "contenidos"=>[
        ["componente"=>'estructura/Header',"configuracion"=>$op],
        ["componente"=>'usuario/Ajustes',"configuracion"=>$op],
        
    ]
];


Plantilla::HTML('estructura/pagina',$config);







