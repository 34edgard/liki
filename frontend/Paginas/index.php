<?php
use Liki\Plantillas\Plantilla;
use Liki\Cache\CacheManager;
use Liki\Config\ConfigManager;
// Uso


$cache = new CacheManager();
$data = $cache->get('App liki');

if (!$data) {
    $data = obtenerUsuariosDeBD(); // Función costosa
    $cache->set('App liki', $data, 1800); // Cache por 30 min
}



$config = cargarConfig::cargarConfig('Index');

Plantilla::HTML('estructura/pagina',$config);

