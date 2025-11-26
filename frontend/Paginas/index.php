<?php
use Liki\Plantillas\Plantilla;
use Liki\Cache\CacheManager;

// Uso
$cache = new CacheManager();
$data = $cache->get('App liki');

if (!$data) {
    $data = obtenerUsuariosDeBD(); // Función costosa
    $cache->set('App liki', $data, 1800); // Cache por 30 min
}


$op = ["op"=>0];


$config = [
    "tituloPagina"=>"App liki",
    "estilos"=>['bootstrap.min','estilos'],
    "estilosD"=>['estilos'],
    
    "scripts"=>['color-modes','htmx','bootstrap.bundle.min'],
    "contenidos"=>[
        ["componente"=>'estructura/Header',"configuracion"=>$op],
        ["componente"=>'Inicio',"configuracion"=>$op],
       
    ]
];


Plantilla::HTML('estructura/pagina',$config);

