<?php

use Liki\Plantillas\Plantilla;
use Liki\Routing\ValidarSesion;


ValidarSesion::validar_sesion();

$op = ["op"=>1];




$config = [
    "tituloPagina"=>'Inicio',
    "estilos"=>['bootstrap.min','estilos'],
    "estilosD"=>['estilos'],
    "scripts"=>['NiñosPorSexo','color-modes','chart.umd','htmx','bootstrap.bundle.min','CambiarTitulo'],
    "contenidos"=>[
        ["componente"=>'estructura/Header',"configuracion"=>$op],
        ["componente"=>'Bienvenida',"configuracion"=>$op],
        
    ]
];


Plantilla::HTML('estructura/pagina',$config);





