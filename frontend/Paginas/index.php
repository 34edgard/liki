<?php
use Liki\Plantillas\Flow;

use Liki\Config\ConfigManager;
// Uso


$config = cargarConfig::cargarConfig('Index');

Flow::html('estructura/pagina',$config);

